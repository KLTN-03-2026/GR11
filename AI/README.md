# AI Pronunciation Scoring Service

FastAPI microservice that scores Vietnamese pronunciation by:

1. Converting uploaded audio to 16 kHz mono WAV (pydub / ffmpeg)
2. Running Speech-to-Text with `nguyenvulebinh/wav2vec2-base-vietnamese-250h`
3. Parsing recognised and target words into phoneme components (âm đầu, vần, thanh điệu)
4. Returning a score (0–100) with per-component error flags

## Requirements

- Python 3.10+
- **ffmpeg** installed and on `PATH` (required by pydub for format conversion)

## Setup

```bash
cd AI/
python -m venv venv
# Windows
venv\Scripts\activate
# Linux / macOS
source venv/bin/activate

pip install -r requirements.txt
```

> **Note:** On first run, the wav2vec2 model (~1.2 GB) will be downloaded
> automatically from HuggingFace Hub. Run `uvicorn main:app ...` once before
> a demo so the model is cached.

## Running

```bash
uvicorn main:app --host 0.0.0.0 --port 8001
```

The service listens on `http://127.0.0.1:8001` by default.
Laravel reads `PYTHON_AI_URL` from `.env` to reach it.

## Endpoint

### `POST /analyze`

**Form fields:**

| Field      | Type       | Description                     |
|------------|------------|---------------------------------|
| `audio`    | file       | Audio recording (webm/ogg/wav…) |
| `tu_chuan` | string     | Target Vietnamese word          |

**Response JSON:**

```json
{
  "van_ban_nhan_dien": "choi",
  "tu_chuan": "troi",
  "diem": 70,
  "diem_tin_cay": 0.85,
  "loi_am_dau": true,
  "loi_van": false,
  "loi_thanh_dieu": false,
  "chi_tiet": {
    "am_dau_chuan": "tr",
    "am_dau_doc": "ch",
    "van_chuan": "oi",
    "van_doc": "oi",
    "thanh_chuan": "huyen",
    "thanh_doc": "huyen"
  }
}
```

### `GET /health`

Returns `{"status": "ok"}`.
