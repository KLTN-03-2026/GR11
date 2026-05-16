import logging
import os
import re
import tempfile
import unicodedata

import numpy as np
import torch
import soundfile as sf
from contextlib import asynccontextmanager

from fastapi import FastAPI, File, Form, HTTPException, UploadFile
from fastapi.responses import JSONResponse
from pydub import AudioSegment

from services.phoneme_parser import _PHONETIC_EXCEPTIONS, parse, normalize_target
from services.scoring_engine import score
from services import stt_service

# ─── Logging ─────────────────────────────────────────────────────────────────
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s  %(levelname)-8s  %(name)s — %(message)s",
)
logger = logging.getLogger(__name__)

# ─── Hằng số ─────────────────────────────────────────────────────────────────
_SAMPLE_RATE          = 16_000
_TOP_K                = 5      # Số ứng viên phiên âm xem xét
_BLANK_CONF_THRESHOLD = 0.40   # Ngưỡng confidence thấp → cảnh báo không rõ

# Đảo ngược _PHONETIC_EXCEPTIONS: dạng STT hay nhầm → dạng ưu tiên từ tu_vungs
# Ví dụ: 'á' → 'ă',  'ớ' → 'â', 'ờ' → 'ơ', ...
_STT_TO_PREFERRED: dict[str, str] = {v: k for k, v in _PHONETIC_EXCEPTIONS.items()}


def _tokenize_phrase(text: str) -> list[str]:
    cleaned = re.sub(r"[^\w\sđĐÀ-ỹà-ỹ]", " ", text, flags=re.UNICODE)
    return [part for part in cleaned.strip().lower().split() if part]


# ─── Lifespan ────────────────────────────────────────────────────────────────
@asynccontextmanager
async def lifespan(app: FastAPI):
    logger.info("AI Pronunciation Service v2 khởi động — model sẵn sàng.")
    yield
    logger.info("AI Pronunciation Service tắt.")


app = FastAPI(
    title="AI Pronunciation Scoring Service",
    version="2.0.0",
    lifespan=lifespan,
)


# ═══════════════════════════════════════════════════════════════════════════════
# TẦNG 1 — Top-K candidates từ logits
# ═══════════════════════════════════════════════════════════════════════════════

def _decode_topk_candidates(logits: torch.Tensor, k: int = _TOP_K) -> list[str]:
    """
    Từ logits (1, T, vocab), sinh k ứng viên phiên âm:
      - Ứng viên #0: greedy decode (argmax mỗi frame)
      - Ứng viên #1..k-1: thay thế token tại các frame có ENTROPY CAO nhất
        (entropy cao = model phân vân nhất tại frame đó) bằng token top-k tiếp theo.

    Entropy cao là dấu hiệu model không chắc — đây chính là những frame
    dễ nhầm nhất, nơi lỗi phát âm thường xuất hiện.
    Không cần dependency mới — chỉ dùng torch + transformers đã có.
    """
    processor = stt_service._processor
    blank_id  = processor.tokenizer.pad_token_id
    logits_2d = logits[0]                           # (T, vocab)
    probs     = torch.softmax(logits_2d, dim=-1)    # (T, vocab)

    # Greedy (ứng viên #0)
    greedy_ids  = torch.argmax(probs, dim=-1)       # (T,)
    greedy_text = processor.batch_decode(greedy_ids.unsqueeze(0))[0].strip().lower()
    candidates  = [greedy_text]

    # Entropy mỗi frame → chọn frame không chắc nhất để thay thế
    entropy    = -(probs * (probs + 1e-9).log()).sum(dim=-1)   # (T,)
    top_frames = torch.topk(entropy, min(k - 1, len(entropy))).indices

    for frame_idx in top_frames:
        if len(candidates) >= k:
            break
        fidx       = frame_idx.item()
        top_tokens = torch.topk(probs[fidx], k).indices        # top-k token tại frame này

        for alt_token in top_tokens[1:]:   # bỏ qua greedy token (đã có)
            alt_id = alt_token.item()
            if alt_id == blank_id:
                continue
            alt_ids  = greedy_ids.clone()
            alt_ids[fidx] = alt_id
            alt_text = processor.batch_decode(alt_ids.unsqueeze(0))[0].strip().lower()
            if alt_text and alt_text not in candidates:
                candidates.append(alt_text)
            if len(candidates) >= k:
                break

    return candidates[:k]


# ═══════════════════════════════════════════════════════════════════════════════
# TẦNG 2 — Per-component confidence từ logits
# ═══════════════════════════════════════════════════════════════════════════════

def _tinh_confidence_theo_thanh_phan(
    logits: torch.Tensor,
    tu_chuan: str,
) -> dict[str, float]:
    """
    Tính confidence riêng cho âm đầu, vần, thanh điệu.

    Âm đầu & vần:
        Chia các non-blank frame theo tỷ lệ độ dài ký tự âm đầu / vần
        trong tu_chuan. Mean softmax prob của token được chọn trong mỗi
        vùng = confidence thành phần đó.

    Thanh điệu:
        wav2vec2 CTC không tách thanh trực tiếp. Dùng NGHỊCH ĐẢO ENTROPY
        trung bình của toàn bộ non-blank frame làm proxy:
          entropy thấp → model chắc chắn về chuỗi phát âm → conf thanh cao
          entropy cao  → model phân vân → conf thanh thấp → khả năng sai thanh
        Chuẩn hoá về [0, 1] bằng max_entropy = ln(vocab_size).
    """
    processor = stt_service._processor
    blank_id  = processor.tokenizer.pad_token_id

    logits_2d = logits[0]                          # (T, vocab)
    probs     = torch.softmax(logits_2d, dim=-1)   # (T, vocab)
    top_probs = probs.max(dim=-1).values           # (T,)
    pred_ids  = torch.argmax(probs, dim=-1)        # (T,)

    nb_mask       = pred_ids != blank_id
    nb_top_probs  = top_probs[nb_mask]             # prob của frame có nội dung

    if nb_top_probs.numel() == 0:
        return {'conf_am_dau': 0.0, 'conf_van': 0.0, 'conf_thanh_dieu': 0.0}

    # Hỗ trợ cả cụm từ nhiều tiếng: cộng chiều dài âm đầu/vần trên từng tiếng.
    target_tokens = _tokenize_phrase(normalize_target(tu_chuan.strip().lower()))
    if not target_tokens:
        target_tokens = [normalize_target(tu_chuan.strip().lower())]

    tong_am_dau = 0
    tong_van = 0
    for token in target_tokens:
        parsed = parse(token)
        tong_am_dau += len(parsed['am_dau'])
        tong_van += len(parsed['van'])
    total_len = max(tong_am_dau + tong_van, 1)

    # Tỷ lệ frame dành cho âm đầu
    am_dau_ratio = tong_am_dau / total_len if tong_am_dau else 0.0
    n_frames     = nb_top_probs.numel()
    split_idx    = max(1, round(n_frames * am_dau_ratio)) if tong_am_dau else 0

    frames_am_dau = nb_top_probs[:split_idx]
    frames_van    = nb_top_probs[split_idx:]

    conf_am_dau = float(frames_am_dau.mean()) if frames_am_dau.numel() > 0 and tong_am_dau else 1.0
    conf_van    = float(frames_van.mean())    if frames_van.numel() > 0 else 1.0

    # Thanh điệu: nghịch đảo entropy chuẩn hoá
    entropy      = -(probs * (probs + 1e-9).log()).sum(dim=-1)   # (T,)
    vocab_size   = probs.shape[-1]
    max_entropy  = float(np.log(vocab_size))
    mean_entropy = float(entropy[nb_mask].mean())
    conf_thanh   = max(0.0, 1.0 - (mean_entropy / max_entropy))

    return {
        'conf_am_dau':     round(conf_am_dau, 4),
        'conf_van':        round(conf_van,    4),
        'conf_thanh_dieu': round(conf_thanh,  4),
    }


# ═══════════════════════════════════════════════════════════════════════════════
# TẦNG 3 — Diacritic-priority correction
# ═══════════════════════════════════════════════════════════════════════════════

def _nfc(text: str) -> str:
    return unicodedata.normalize("NFC", text)


def _levenshtein(a: str, b: str) -> int:
    a, b = a.lower(), b.lower()
    if a == b:  return 0
    if not a:   return len(b)
    if not b:   return len(a)
    prev = list(range(len(b) + 1))
    for ca in a:
        curr = [prev[0] + 1]
        for j, cb in enumerate(b, 1):
            curr.append(min(prev[j] + 1, curr[j-1] + 1, prev[j-1] + (ca != cb)))
        prev = curr
    return prev[-1]


def _pick_best_candidate(candidates: list[str], tu_chuan: str, confidence: float) -> str:
    """
    Chọn candidate theo ưu tiên:
      1) Điểm âm vị cao nhất (score engine)
      2) Nếu bằng điểm, chọn candidate gần tu_chuan nhất theo Levenshtein
    """
    if not candidates:
        return ""
    ref = _nfc(tu_chuan.strip().lower())
    ranked = []
    for candidate in candidates[:_TOP_K]:
        scoring = score(
            tu_chuan=tu_chuan,
            van_ban_nhan_dien=candidate,
            diem_tin_cay=confidence,
        )
        ranked.append((
            int(scoring.get("diem", 0)),
            -_levenshtein(_nfc(candidate), ref),
            candidate,
        ))
    ranked.sort(reverse=True)
    return ranked[0][2]


def _apply_diacritic_priority(stt_word: str, tu_chuan: str) -> str:
    """
    Ưu tiên ký tự trong tu_chuan (lấy từ tu_vungs) hơn dạng STT trả về.

    Case A — STT trả về dạng phát âm tương đương:
        tu_chuan='ăn', STT='án'  →  'á' được map về 'ă'

    Case B — STT bỏ mất dấu hình hoàn toàn:
        tu_chuan='ăn', STT='an'  →  'a' (base của 'ă') được map về 'ă'

    Chỉ áp dụng khi tu_chuan thực sự chứa ký tự ưu tiên — tránh sửa sai.
    """
    tu_chuan_nfc = _nfc(tu_chuan.strip().lower())
    stt_nfc      = _nfc(stt_word.strip().lower())

    # Bộ ký tự ưu tiên xuất hiện trong tu_chuan
    preferred_chars: set[str] = {ch for ch in tu_chuan_nfc if ch in _PHONETIC_EXCEPTIONS}
    if not preferred_chars:
        return stt_nfc

    # Xây map: base (bỏ mọi dấu) → ký tự ưu tiên (Case B)
    base_to_preferred: dict[str, str] = {}
    for pref_char in preferred_chars:
        nfd  = unicodedata.normalize("NFD", pref_char)
        base = unicodedata.normalize("NFC",
               "".join(ch for ch in nfd if unicodedata.category(ch) != "Mn"))
        base_to_preferred[base] = pref_char

    corrected = []
    for ch in stt_nfc:
        # Case A: ký tự là dạng phát âm tương đương của ký tự ưu tiên
        preferred_via_a = _STT_TO_PREFERRED.get(ch)
        if preferred_via_a and preferred_via_a in preferred_chars:
            corrected.append(preferred_via_a)
            continue
        # Case B: ký tự là base (không dấu) của ký tự ưu tiên
        if ch in base_to_preferred:
            corrected.append(base_to_preferred[ch])
            continue
        corrected.append(ch)

    return "".join(corrected)


# ═══════════════════════════════════════════════════════════════════════════════
# Audio
# ═══════════════════════════════════════════════════════════════════════════════

def _convert_to_wav(src: str, dst: str) -> None:
    """Chuyển audio bất kỳ → 16 kHz mono WAV bằng pydub/ffmpeg."""
    seg = AudioSegment.from_file(src)
    seg.set_frame_rate(_SAMPLE_RATE).set_channels(1).export(dst, format="wav")


# ═══════════════════════════════════════════════════════════════════════════════
# Cảnh báo ngôn ngữ tự nhiên
# ═══════════════════════════════════════════════════════════════════════════════

def _tao_canh_bao(
    loi_am_dau: bool,
    loi_van: bool,
    loi_thanh_dieu: bool,
    conf: dict,
) -> list[str]:
    """
    Sinh cảnh báo ngôn ngữ tự nhiên dựa trên lỗi + mức confidence.
    Phân biệt 2 mức độ cho mỗi thành phần:
      - Confidence thấp (< ngưỡng): phát âm không rõ ràng
      - Confidence bình thường nhưng sai: phát âm rõ nhưng nhầm
    """
    msgs = []

    if loi_am_dau:
        if conf['conf_am_dau'] < _BLANK_CONF_THRESHOLD:
            msgs.append("Âm đầu không rõ ràng — bé cần phát âm rõ phụ âm đầu hơn.")
        else:
            msgs.append("Âm đầu bị sai — kiểm tra vị trí miệng/lưỡi khi bắt đầu từ.")

    if loi_van:
        if conf['conf_van'] < _BLANK_CONF_THRESHOLD:
            msgs.append("Phần vần không rõ — bé cần mở miệng và kéo dài nguyên âm hơn.")
        else:
            msgs.append("Vần bị sai — kiểm tra nguyên âm chính và âm cuối của từ.")

    if loi_thanh_dieu:
        if conf['conf_thanh_dieu'] < _BLANK_CONF_THRESHOLD:
            msgs.append("Thanh điệu không ổn định — bé cần giữ đều cao độ giọng.")
        else:
            msgs.append("Sai thanh điệu — chú ý dấu (huyền/sắc/hỏi/ngã/nặng/ngang).")

    if not msgs:
        msgs.append("Phát âm chính xác! 🎉")

    return msgs


# ═══════════════════════════════════════════════════════════════════════════════
# Routes
# ═══════════════════════════════════════════════════════════════════════════════

@app.get("/health")
def health() -> dict:
    return {"status": "ok"}


@app.post("/analyze")
async def analyze(
    audio:    UploadFile = File(..., description="File ghi âm (webm/ogg/wav/mp3)"),
    tu_chuan: str        = Form(..., description="Từ chuẩn lấy từ bảng tu_vungs"),
) -> JSONResponse:
    tu_chuan = tu_chuan.strip()
    if not tu_chuan:
        raise HTTPException(status_code=422, detail="tu_chuan không được để trống.")

    raw_path = wav_path = None
    try:
        # B1: Lưu file tạm
        ext = os.path.splitext(audio.filename or "audio")[1] or ".webm"
        raw_fd, raw_path = tempfile.mkstemp(suffix=ext)
        with os.fdopen(raw_fd, "wb") as fh:
            fh.write(await audio.read())

        # B2: Chuyển về 16 kHz mono WAV
        _, wav_path = tempfile.mkstemp(suffix=".wav")
        try:
            _convert_to_wav(raw_path, wav_path)
        except Exception as e:
            logger.error("Lỗi chuyển đổi audio: %s", e)
            raise HTTPException(status_code=422, detail=f"Không thể xử lý file audio: {e}")

        # B3: Đọc WAV + chạy wav2vec2 → logits thô
        audio_data, sr = sf.read(wav_path, dtype="float32")
        if sr != _SAMPLE_RATE:
            raise HTTPException(status_code=422, detail=f"Sample rate không hợp lệ: {sr} Hz")
        if audio_data.ndim > 1:
            audio_data = audio_data.mean(axis=1)

        inputs = stt_service._processor(
            audio_data,
            sampling_rate=_SAMPLE_RATE,
            return_tensors="pt",
            padding=True,
        )
        with torch.no_grad():
            logits = stt_service._model(**inputs).logits   # (1, T, vocab)

        # Confidence tổng (tương thích schema DB: diem_tin_cay)
        probs_all  = torch.softmax(logits[0], dim=-1)
        pred_ids   = torch.argmax(probs_all, dim=-1)
        blank_id   = stt_service._processor.tokenizer.pad_token_id
        nb_mask    = pred_ids != blank_id
        confidence = float(probs_all.max(dim=-1).values[nb_mask].mean()) if nb_mask.any() else 0.0

        greedy_text = stt_service._processor.batch_decode(
            pred_ids.unsqueeze(0)
        )[0].strip().lower()
        logger.info("STT greedy='%s'  conf=%.4f  tu_chuan='%s'",
                    greedy_text, confidence, tu_chuan)

        # B4: Tầng 1 — Top-K candidates
        candidates = _decode_topk_candidates(logits, k=_TOP_K)
        logger.info("Candidates: %s", candidates)

        # B5: Chọn candidate tốt nhất
        best_word = _pick_best_candidate(candidates, tu_chuan, confidence)

        # B6: Tầng 3 — Diacritic-priority correction
        if best_word:
            best_word = _apply_diacritic_priority(best_word, tu_chuan)
        logger.info("Từ sau hiệu chỉnh: '%s'", best_word)

        # B7: Tầng 2 — Per-component confidence
        conf_dict = _tinh_confidence_theo_thanh_phan(logits, tu_chuan)

        # B8: Chấm điểm âm vị (scoring_engine nhận best_word — 1 từ duy nhất)
        result = score(
            tu_chuan          = tu_chuan,
            van_ban_nhan_dien = best_word,
            diem_tin_cay      = confidence,
        )

        # B9: Bổ sung chi tiết per-component vào response
        if isinstance(result.get("chi_tiet"), dict):
            result["chi_tiet"].update({
                "conf_am_dau":      conf_dict["conf_am_dau"],
                "conf_van":         conf_dict["conf_van"],
                "conf_thanh_dieu":  conf_dict["conf_thanh_dieu"],
                "candidates":       candidates[:_TOP_K],
                "canh_bao":         _tao_canh_bao(
                    result["loi_am_dau"],
                    result["loi_van"],
                    result["loi_thanh_dieu"],
                    conf_dict,
                ),
            })

        return JSONResponse(content=result, status_code=200)

    finally:
        for path in (raw_path, wav_path):
            if path and os.path.exists(path):
                try:
                    os.remove(path)
                except OSError:
                    pass
