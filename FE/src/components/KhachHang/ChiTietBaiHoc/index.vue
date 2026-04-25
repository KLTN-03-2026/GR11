<template>
     <div class="content-wrapper py-5">
      <!-- Bảng chữ cái + luyện đọc -->
      <div class="row g-4 mt-5">
        <!-- Cột trái -->
        <div class="col-lg-7">
          <div class="bg-white rounded-5 shadow-sm p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div>
                <h2 class="fw-bold mb-1" style="color: #0d3b66;">
                  {{ panelTitle }}
                </h2>
                <p class="text-muted mb-0">
                  {{ panelSubtitle }}
                </p>
              </div>

              <span class="badge bg-primary rounded-pill px-3 py-2">
                {{ panelCountLabel }}
              </span>
            </div>

            <div v-if="vocabLoading" class="text-center py-5 text-muted">
              Đang tải từ vựng…
            </div>

            <div v-else class="row g-3">
              <div
                class="col-lg-2 col-md-3 col-sm-4 col-4"
                v-for="item in practiceList"
                :key="item.key"
              >
                <div
                  class="alphabet-card rounded-4 text-center py-3 px-2 h-100"
                  :class="{ active: selectedLetter.key === item.key }"
                  @click="selectedLetter = item"
                >
                  <div
                    class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 overflow-hidden"
                    style="
                      width: 55px;
                      height: 55px;
                      background: #fff3ef;
                    "
                  >
                    <img
                      v-if="item.image"
                      :src="item.image"
                      alt=""
                      class="w-100 h-100"
                      style="object-fit: cover;"
                    />
                    <h3
                      v-else
                      class="fw-bold mb-0 px-1"
                      style="color: #ff6b35; font-size: 1.15rem;"
                    >
                      {{ item.letter }}
                    </h3>
                  </div>

                  <p class="fw-bold mb-1 small" style="color: #0d3b66;">
                    {{ item.word }}
                  </p>

                  <small class="text-muted">{{ item.icon }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>

<!-- Cột phải -->
<div class="col-lg-5">
  <div class="bg-white rounded-5 shadow-sm p-4 h-100 practice-panel">
    <div class="text-center mb-4 position-relative">
      <div
        class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4 practice-letter-circle"
        style="
          width: 160px;
          height: 160px;
          background: linear-gradient(135deg, #fff1eb, #ffe4d6);
          border: 10px solid #fff3ef;
        "
      >
        <img
          v-if="selectedLetter.image"
          :src="selectedLetter.image"
          alt=""
          class="rounded-circle"
          style="width: 130px; height: 130px; object-fit: cover;"
        />
        <h1
          v-else
          class="fw-bold mb-0 practice-main-title"
          :class="{ 'practice-main-title--long': isLongMainTitle }"
          style="
            color: #ff6b35;
            font-family: 'Lobster Two', cursive;
          "
        >
          {{ selectedLetter.letter }}
        </h1>
      </div>

      <h2
        class="fw-bold mb-2 position-relative"
        style="color: #0d3b66; z-index: 2;"
      >
        {{ selectedLetter.word }}
      </h2>

      <p
        class="text-muted mb-4 position-relative"
        style="z-index: 2;"
      >
        Bé hãy nghe phát âm và đọc lại theo nhé
      </p>
    </div>

    <div class="bg-light rounded-4 p-3 mb-4 text-center practice-icon-box">
      <div style="font-size: 52px;" class="mb-2">
        <img
          v-if="selectedLetter.image"
          :src="selectedLetter.image"
          alt=""
          class="rounded-3"
          style="max-height: 120px; max-width: 100%; object-fit: contain;"
        />
        <span v-else>{{ selectedLetter.icon }}</span>
      </div>

      <h5 class="fw-bold mb-1">{{ selectedLetter.word }}</h5>

      <small class="text-muted">
        Chạm để nghe cách phát âm chuẩn
      </small>
    </div>

    <div class="d-grid gap-3 mb-4 position-relative" style="z-index: 2;">
      <button
        type="button"
        class="btn btn-warning rounded-pill py-3 fw-bold practice-btn"
        :disabled="ttsLoading"
        @click="playSampleTts"
      >
        <i class="fa fa-volume-up me-2"></i>
        Nghe mẫu
      </button>

      <button class="btn btn-danger rounded-pill py-3 fw-bold text-white practice-btn">
        <i class="fa fa-microphone me-2"></i>
        Ghi Âm Kiểm Tra
      </button>
    </div>

    <div class="bg-light rounded-4 p-4 mb-4 position-relative" style="z-index: 2;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="fw-bold">Điểm AI</span>
        <span class="fw-bold" style="color: #20c997;">8.5 / 10</span>
      </div>

      <div
        class="progress rounded-pill mb-3 ai-progress"
        style="height: 10px;"
      >
        <div
          class="progress-bar rounded-pill"
          role="progressbar"
          style="
            width: 85%;
            background: linear-gradient(135deg, #20c997, #4dd4ac);
          "
        ></div>
      </div>

      <div
        class="rounded-4 p-3 ai-result-box"
        style="background: #eefbf5;"
      >
        <div class="d-flex align-items-center gap-2 mb-2">
          <i class="fa fa-check-circle text-success"></i>
          <p class="text-success fw-bold mb-0">
            Gần Đúng
          </p>
        </div>

        <small class="text-muted">
          Bé phát âm khá tốt, hãy đọc to và rõ hơn ở cuối âm.
        </small>
      </div>
    </div>

    <button class="btn btn-primary rounded-pill py-3 fw-bold w-100 test-btn">
      <i class="fa fa-file-alt me-2"></i>
      Làm Bài Kiểm Tra
    </button>
  </div>
</div>
      </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            ttsLoading: false,
            vocabLoading: false,
            lessonFetchDone: false,
            tuVungList: [],
            baiHocDetail: null,
            phien_id: null,

            selectedLetter: {
                key: "a-A",
                letter: "A",
                word: "Na",
                icon: "🍚",
                image: null,
            },

            alphabetList: [
                { letter: "A", word: "Na", icon: "🍚" },
                { letter: "Ă", word: "Ăn", icon: "🍜" },
                { letter: "Â", word: "Âm", icon: "🎵" },
                { letter: "B", word: "Bò", icon: "🐄" },
                { letter: "C", word: "Cá", icon: "🐟" },
                { letter: "D", word: "Dê", icon: "🐐" },
                { letter: "Đ", word: "Đèn", icon: "💡" },
                { letter: "E", word: "Em", icon: "👧" },
                { letter: "Ê", word: "Ếch", icon: "🐸" },
                { letter: "G", word: "Gà", icon: "🐔" },
                { letter: "H", word: "Hoa", icon: "🌸" },
                { letter: "I", word: "In", icon: "🖨️" },
                { letter: "K", word: "Kem", icon: "🍦" },
                { letter: "L", word: "Lá", icon: "🍃" },
                { letter: "M", word: "Mèo", icon: "🐱" },
                { letter: "N", word: "Nai", icon: "🦌" },
                { letter: "O", word: "Ong", icon: "🐝" },
                { letter: "Ô", word: "Ô", icon: "☂️" },
                { letter: "Ơ", word: "Ớt", icon: "🌶️" },
                { letter: "P", word: "Phở", icon: "🍜" },
                { letter: "Q", word: "Quả", icon: "🍎" },
                { letter: "R", word: "Rổ", icon: "🧺" },
                { letter: "S", word: "Sách", icon: "📚" },
                { letter: "T", word: "Táo", icon: "🍎" },
                { letter: "U", word: "Uống", icon: "🥤" },
                { letter: "Ư", word: "Ước", icon: "⭐" },
                { letter: "V", word: "Voi", icon: "🐘" },
                { letter: "X", word: "Xe", icon: "🚗" },
                { letter: "Y", word: "Yêu", icon: "❤️" },
            ],
        };
    },

    computed: {
        alphabetPracticeList() {
            return this.alphabetList.map((a) => ({
                key: `a-${a.letter}`,
                letter: a.letter,
                word: a.word,
                icon: a.icon,
                image: null,
            }));
        },
        practiceList() {
            if (!this.$route.params.id) {
                return this.alphabetPracticeList;
            }
            if (!this.lessonFetchDone) {
                return [];
            }
            if (this.tuVungList.length > 0) {
                return this.tuVungList.map((tv) => ({
                    key: `v-${tv.id}`,
                    letter: tv.tu_chuan,
                    word: tv.phien_am || tv.tu_chuan,
                    icon: "✨",
                    image: tv.hinh_anh_url || null,
                }));
            }
            return this.alphabetPracticeList;
        },
        panelTitle() {
            return this.tuVungList.length > 0 ? "Danh sách từ vựng" : "Danh Sách Chữ Cái";
        },
        panelSubtitle() {
            if (this.tuVungList.length > 0 && this.baiHocDetail?.tieu_de) {
                return this.baiHocDetail.tieu_de;
            }
            return "Chọn chữ cái để nghe phát âm và luyện đọc";
        },
        panelCountLabel() {
            if (this.vocabLoading || !this.lessonFetchDone) {
                return "…";
            }
            const n = this.practiceList.length;
            return this.tuVungList.length > 0 ? `${n} từ` : `${n} Chữ`;
        },
        isLongMainTitle() {
            return String(this.selectedLetter.letter || "").length > 10;
        },
    },

    mounted() {
        this.loadBaiHocTuVung();
    },

    watch: {
        "$route.params.id"() {
            // Kết thúc phiên cũ nếu có
            try { this.endSession(); } catch (e) {}

            this.lessonFetchDone = false;
            this.tuVungList = [];
            this.baiHocDetail = null;
            this.loadBaiHocTuVung();
        },
    },

    beforeUnmount() {
        this.stopTtsSample();
        try { this.endSession(); } catch (e) {}
    },

    methods: {
        loadBaiHocTuVung() {
            const id = this.$route.params.id;
            if (!id) {
                this.lessonFetchDone = true;
                return;
            }
            this.vocabLoading = true;
            axios
                .get(`http://127.0.0.1:8000/api/bai-hoc/${id}`)
                .then((res) => {
                    const d = res.data?.data;
                    if (!d) {
                        this.tuVungList = [];
                        this.baiHocDetail = null;
                    } else {
                        this.baiHocDetail = {
                            tieu_de: d.tieu_de,
                            mo_ta: d.mo_ta,
                        };
                        this.tuVungList = Array.isArray(d.tu_vungs) ? d.tu_vungs : [];
                    }
                })
                .catch((e) => {
                    console.error(e);
                    this.tuVungList = [];
                    this.baiHocDetail = null;
                })
                .finally(() => {
                    this.lessonFetchDone = true;
                    this.vocabLoading = false;
                    this.$nextTick(() => {
                        const first = this.practiceList[0];
                        if (first) {
                            this.selectedLetter = { ...first };
                        }
                    });

                    // Tạo phiên luyện tập khi load bài xong
                    this.startSession();
                });
        },

        stopTtsSample() {
            if (this._ttsAudio) {
                this._ttsAudio.pause();
                this._ttsAudio.src = "";
                this._ttsAudio = null;
            }
            if (this._ttsBlobUrl) {
                URL.revokeObjectURL(this._ttsBlobUrl);
                this._ttsBlobUrl = null;
            }
        },

        startSession() {
            const id = this.$route.params.id;
            if (!id) return;
            if (this.phien_id) return; // đã có phiên
            const token = localStorage.getItem("token_nguoi_dung");
            if (!token) return; // chưa đăng nhập

            axios.post('http://127.0.0.1:8000/api/phien-luyen-taps/start', { bai_hoc_id: id }, {
                headers: {
                    Authorization: 'Bearer ' + token
                }
            })
            .then((res) => {
                if (res.data.status) {
                    this.phien_id = res.data.data.phien_id;
                    localStorage.setItem('last_phien_id', String(this.phien_id));
                }
            })
            .catch((e) => {
                console.error('startSession error', e);
            });
        },

        endSession() {
            if (!this.phien_id) return;
            const token = localStorage.getItem("token_nguoi_dung");
            if (!token) { this.phien_id = null; return; }

            axios.post('http://127.0.0.1:8000/api/phien-luyen-taps/end', { phien_id: this.phien_id }, {
                headers: {
                    Authorization: 'Bearer ' + token
                }
            })
            .then(() => {
                this.phien_id = null;
            })
            .catch((e) => {
                console.error('endSession error', e);
                this.phien_id = null;
            });
        },

        playSampleTts() {
            const text = String(this.selectedLetter.word || "").trim();
            if (!text || this.ttsLoading) {
                return;
            }
            this.ttsLoading = true;
            this.stopTtsSample();

            axios
                .get(`http://127.0.0.1:8000/api/tts-vi`, {
                    params: { t: text },
                    responseType: "blob",
                })
                .then((res) => {
                    const blobUrl = URL.createObjectURL(res.data);
                    this._ttsBlobUrl = blobUrl;
                    const audio = new Audio(blobUrl);
                    this._ttsAudio = audio;
                    audio.onended = () => this.stopTtsSample();
                    audio.onerror = () => this.stopTtsSample();
                    audio.play();
                })
                .catch((e) => {
                    console.error(e);
                    this.stopTtsSample();
                })
                .finally(() => {
                    this.ttsLoading = false;
                });
        },
    },
};
</script>

<style scoped>
.start-btn {
  background: linear-gradient(135deg, #ff6b35, #ff8c42);
  transition: all 0.3s ease;
}

.start-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 25px rgba(255, 107, 53, 0.25);
}

.alphabet-card {
  background: #fff;
  border: 2px solid #f5f5f5;
  cursor: pointer;
  transition: all 0.3s ease;
}

.alphabet-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
}

.alphabet-card.active {
  border: 2px solid #ff6b35;
  background: #fff7f2;
  transform: translateY(-4px);
}

.start-btn {
  background: linear-gradient(135deg, #ff6b35, #ff8c42);
  transition: all 0.3s ease;
}

.start-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 25px rgba(255, 107, 53, 0.25);
}

.alphabet-card {
  background: #fff;
  border: 2px solid #f5f5f5;
  cursor: pointer;
  transition: all 0.3s ease;
}

.alphabet-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
}

.alphabet-card.active {
  border: 2px solid #ff6b35;
  background: #fff7f2;
  transform: translateY(-4px);
}

.practice-panel {
  position: sticky;
  top: 30px;
  overflow: hidden;
}

.practice-panel::before {
  content: "";
  position: absolute;
  width: 220px;
  height: 220px;
  background: rgba(255, 107, 53, 0.08);
  border-radius: 50%;
  top: -80px;
  right: -80px;
}

.practice-panel::after {
  content: "";
  position: absolute;
  width: 180px;
  height: 180px;
  background: rgba(77, 150, 255, 0.08);
  border-radius: 50%;
  bottom: -60px;
  left: -60px;
}

.practice-letter-circle {
  position: relative;
  z-index: 2;
  animation: floatCircle 3s ease-in-out infinite;
  box-shadow: 0 20px 40px rgba(255, 107, 53, 0.15);
}

/* .practice-main-title {
  font-size: 80px;
}

.practice-main-title--long {
  font-size: 1.65rem;
  line-height: 1.25;
  font-family: inherit !important;
  max-width: 100%;
  padding: 0 0.5rem;
} */

.practice-icon-box {
  transition: all 0.3s ease;
  border: 2px dashed #ffe1d6;
  position: relative;
  z-index: 2;
}

.practice-icon-box:hover {
  transform: translateY(-5px);
  background: #fff7f2 !important;
}

.practice-btn {
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.practice-btn:hover {
  transform: translateY(-3px);
}

.practice-btn::before {
  content: "";
  position: absolute;
  top: 0;
  left: -120%;
  width: 100%;
  height: 100%;
  background: rgba(255,255,255,0.2);
  transform: skewX(-20deg);
  transition: 0.5s;
}

.practice-btn:hover::before {
  left: 120%;
}

.ai-result-box {
  border: 2px dashed #d9f8ea;
  transition: all 0.3s ease;
  position: relative;
  z-index: 2;
}

.ai-result-box:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(32, 201, 151, 0.12);
}

.ai-progress {
  overflow: hidden;
}

.ai-progress .progress-bar {
  animation: progressMove 2s ease;
}

.test-btn {
  position: relative;
  z-index: 2;
  transition: all 0.3s ease;
}

.test-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 14px 28px rgba(13, 110, 253, 0.2);
}

@keyframes floatCircle {
  0% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-8px);
  }
  100% {
    transform: translateY(0px);
  }
}

@keyframes progressMove {
  from {
    width: 0;
  }
}
</style>