"""
Scoring engine: compare recognised text against the target word and produce
a pronunciation score with per-component error flags.
"""

import re
from itertools import zip_longest

from .phoneme_parser import parse, normalize_target

_WEIGHTS = {
    (True,  True):  {'am_dau': 30, 'van': 40, 'thanh': 30},
    (False, True):  {'am_dau':  0, 'van': 60, 'thanh': 40},
    (True,  False): {'am_dau': 50, 'van':  0, 'thanh': 50},
    (False, False): {'am_dau':  0, 'van':  0, 'thanh': 100},
}

_CONFIDENCE_STRONG = 0.80
_CONFIDENCE_MEDIUM = 0.65


def _tokenize(text: str) -> list[str]:
    cleaned = re.sub(r"[^\w\sđĐÀ-ỹà-ỹ]", " ", text, flags=re.UNICODE)
    return [part for part in cleaned.strip().lower().split() if part]


def _apply_confidence_penalty(raw_score: int, confidence: float) -> int:
    """
    Ưu tiên độ tin cậy cao:
      - confidence >= 0.80: giữ nguyên điểm
      - 0.65 <= confidence < 0.80: giảm nhẹ
      - confidence < 0.65: giảm mạnh để tránh chấm "ảo"
    """
    if confidence >= _CONFIDENCE_STRONG:
        return raw_score
    if confidence >= _CONFIDENCE_MEDIUM:
        return max(0, round(raw_score * 0.85))
    return max(0, round(raw_score * 0.65))


def score(tu_chuan: str, van_ban_nhan_dien: str, diem_tin_cay: float) -> dict:
    """
    Compare *van_ban_nhan_dien* (what the STT heard) against *tu_chuan*
    (the target word) and return a scoring result dict.

    Args:
        tu_chuan:           The correct Vietnamese word.
        van_ban_nhan_dien:  The word(s) returned by the STT model.
        diem_tin_cay:       Confidence score from the STT model (0‒1).

    Returns a dict compatible with the Laravel controller and DB schema:
        van_ban_nhan_dien, tu_chuan, diem, diem_tin_cay,
        loi_am_dau, loi_van, loi_thanh_dieu, chi_tiet
    """
    recognised = van_ban_nhan_dien.strip().lower()

    if not recognised:
        return {
            'van_ban_nhan_dien': '',
            'tu_chuan': tu_chuan,
            'diem': 0,
            'diem_tin_cay': diem_tin_cay,
            'loi_am_dau': True,
            'loi_van': True,
            'loi_thanh_dieu': True,
            'chi_tiet': {
                'ghi_chu': 'Khong nhan dien duoc giong noi',
            },
        }

    recognised_tokens = _tokenize(recognised)
    target_tokens = _tokenize(normalize_target(tu_chuan.strip().lower()))

    if not target_tokens:
        target_tokens = [normalize_target(tu_chuan.strip().lower())]
    if not recognised_tokens:
        recognised_tokens = ['']

    per_word = []
    tong_diem_toi_da = 0
    tong_penalty = 0
    loi_am_dau = False
    loi_van = False
    loi_thanh_dieu = False

    for target_word, recognised_word in zip_longest(target_tokens, recognised_tokens, fillvalue=''):
        parsed_chuan = parse(target_word)
        parsed_doc = parse(recognised_word)

        has_am_dau = bool(parsed_chuan['am_dau'])
        has_van = bool(parsed_chuan['van'])
        weights = _WEIGHTS[(has_am_dau, has_van)]
        word_max = weights['am_dau'] + weights['van'] + weights['thanh']
        tong_diem_toi_da += word_max

        word_loi_am_dau = has_am_dau and (parsed_chuan['am_dau'] != parsed_doc['am_dau'])
        word_loi_van = has_van and (parsed_chuan['van'] != parsed_doc['van'])
        word_loi_thanh = parsed_chuan['thanh_dieu'] != parsed_doc['thanh_dieu']

        word_penalty = 0
        if word_loi_am_dau:
            word_penalty += weights['am_dau']
        if word_loi_van:
            word_penalty += weights['van']
        if word_loi_thanh:
            word_penalty += weights['thanh']
        tong_penalty += word_penalty

        loi_am_dau = loi_am_dau or word_loi_am_dau
        loi_van = loi_van or word_loi_van
        loi_thanh_dieu = loi_thanh_dieu or word_loi_thanh

        per_word.append({
            'tu_chuan': target_word,
            'tu_doc': recognised_word,
            'am_dau_chuan': parsed_chuan['am_dau'],
            'am_dau_doc': parsed_doc['am_dau'],
            'van_chuan': parsed_chuan['van'],
            'van_doc': parsed_doc['van'],
            'thanh_chuan': parsed_chuan['thanh_dieu'],
            'thanh_doc': parsed_doc['thanh_dieu'],
            'loi_am_dau': word_loi_am_dau,
            'loi_van': word_loi_van,
            'loi_thanh_dieu': word_loi_thanh,
            'diem_tu': max(0, word_max - word_penalty),
            'diem_toi_da_tu': word_max,
        })

    tong_diem_toi_da = max(1, tong_diem_toi_da)
    raw_score = max(0, round((tong_diem_toi_da - tong_penalty) * 100 / tong_diem_toi_da))
    diem = _apply_confidence_penalty(raw_score, float(diem_tin_cay))

    first_target = target_tokens[0] if target_tokens else ''
    first_recognised = recognised_tokens[0] if recognised_tokens else ''
    parsed_chuan = parse(first_target)
    parsed_doc = parse(first_recognised)

    return {
        'van_ban_nhan_dien': " ".join(recognised_tokens),
        'tu_chuan': tu_chuan,
        'diem': diem,
        'diem_tin_cay': round(diem_tin_cay, 4),
        'loi_am_dau': loi_am_dau,
        'loi_van': loi_van,
        'loi_thanh_dieu': loi_thanh_dieu,
        'chi_tiet': {
            'am_dau_chuan': parsed_chuan['am_dau'],
            'am_dau_doc': parsed_doc['am_dau'],
            'van_chuan': parsed_chuan['van'],
            'van_doc': parsed_doc['van'],
            'thanh_chuan': parsed_chuan['thanh_dieu'],
            'thanh_doc': parsed_doc['thanh_dieu'],
            'raw_score': raw_score,
            'confidence_adjusted': diem != raw_score,
            'so_tu_muc_tieu': len(target_tokens),
            'so_tu_nhan_dien': len(recognised_tokens),
            'theo_tu': per_word,
        },
    }
