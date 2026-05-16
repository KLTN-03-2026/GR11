"""
Vietnamese phoneme parser: splits a word into (am_dau, van, thanh_dieu).
"""

import unicodedata
import re

# Tone mark → tone name mapping (based on combining diacritics)
_TONE_MARKS = {
    '\u0300': 'huyen',   # ` grave
    '\u0301': 'sac',     # ´ acute
    '\u0303': 'nga',     # ~ tilde
    '\u0309': 'hoi',     # ̉ hook above
    '\u0323': 'nang',    # . dot below
}

# Ordered by length (longest first) so multi-char initials match first
_INITIALS = [
    'ngh', 'gh', 'gi', 'ch', 'kh', 'ng', 'nh', 'ph', 'qu', 'th', 'tr',
    'b', 'c', 'd', 'đ', 'g', 'h', 'k', 'l', 'm', 'n', 'p', 'r', 's',
    't', 'v', 'x',
]

_PHONETIC_EXCEPTIONS: dict[str, str] = {
    'ă': 'á',
    'â': 'ớ',
    'ê': 'ê',
    'ô': 'ô',
    'ơ': 'ờ',
    'ư': 'ư',
}


def normalize_target(word: str) -> str:
    """Chuyển tên chữ cái về dạng âm thanh thực trước khi so sánh."""
    key = word.strip().lower()
    return _PHONETIC_EXCEPTIONS.get(key, key)


def _strip_tone(text: str) -> tuple[str, str]:
    """Return (base_string_without_tone, tone_name)."""
    nfd = unicodedata.normalize('NFD', text)
    tone = 'ngang'
    clean_chars = []
    for ch in nfd:
        if ch in _TONE_MARKS:
            tone = _TONE_MARKS[ch]
        else:
            clean_chars.append(ch)
    base = unicodedata.normalize('NFC', ''.join(clean_chars))
    return base, tone


def parse(word: str) -> dict:
    """
    Parse a Vietnamese word into its components.

    Returns:
        {
            'am_dau': str,       # initial consonant ('' if none)
            'van': str,          # rhyme / vần
            'thanh_dieu': str,   # tone name
        }
    """
    word = word.strip().lower()
    if not word:
        return {'am_dau': '', 'van': '', 'thanh_dieu': 'ngang'}

    base, tone = _strip_tone(word)

    am_dau = ''
    for initial in _INITIALS:
        if base.startswith(initial):
            # Avoid matching 'g' when base starts with 'gh' or 'gi' (already handled by ordering)
            am_dau = initial
            break

    van = base[len(am_dau):]

    return {
        'am_dau': am_dau,
        'van': van,
        'thanh_dieu': tone,
    }
