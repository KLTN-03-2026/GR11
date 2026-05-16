"""
Speech-to-Text service using wav2vec2 fine-tuned on Vietnamese.

Model: nguyenvulebinh/wav2vec2-base-vietnamese-250h (~1.2 GB, downloaded on
first run from HuggingFace Hub).

The model and processor are loaded once at module import time so that
subsequent requests pay no startup cost.
"""

import logging
import numpy as np
import soundfile as sf
import torch
from transformers import Wav2Vec2ForCTC, Wav2Vec2Processor

logger = logging.getLogger(__name__)

_MODEL_ID = "nguyenvulebinh/wav2vec2-base-vietnamese-250h"
_SAMPLE_RATE = 16_000

logger.info("Loading wav2vec2 model '%s' …", _MODEL_ID)
_processor = Wav2Vec2Processor.from_pretrained(_MODEL_ID)
_model = Wav2Vec2ForCTC.from_pretrained(_MODEL_ID)
_model.eval()
logger.info("wav2vec2 model loaded.")


def transcribe(wav_path: str) -> tuple[str, float]:
    """
    Transcribe a 16 kHz mono WAV file.

    Returns:
        (transcription_text, confidence_score)
        confidence_score is the mean softmax probability of the chosen tokens
        (a rough proxy for model confidence, range 0‒1).
    """
    audio, sr = sf.read(wav_path, dtype='float32')
    if sr != _SAMPLE_RATE:
        raise ValueError(
            f"Expected {_SAMPLE_RATE} Hz audio, got {sr} Hz. "
            "Pre-process with pydub before calling transcribe()."
        )

    # Mono: average channels if stereo
    if audio.ndim > 1:
        audio = audio.mean(axis=1)

    inputs = _processor(
        audio,
        sampling_rate=_SAMPLE_RATE,
        return_tensors="pt",
        padding=True,
    )

    with torch.no_grad():
        logits = _model(**inputs).logits  # (1, T, vocab)

    # Greedy decode
    predicted_ids = torch.argmax(logits, dim=-1)
    transcription = _processor.batch_decode(predicted_ids)[0].strip().lower()

    # Confidence: mean prob of top token at each non-blank frame
    probs = torch.softmax(logits[0], dim=-1)  # (T, vocab)
    top_probs = probs.max(dim=-1).values       # (T,)
    blank_id = _processor.tokenizer.pad_token_id
    non_blank_mask = predicted_ids[0] != blank_id
    if non_blank_mask.any():
        confidence = float(top_probs[non_blank_mask].mean())
    else:
        confidence = 0.0

    return transcription, confidence
