<template>
  <div class="page-full bg-light min-vh-100">
    <div class="content-wrapper py-5">
      <div
        v-if="token && baiKiemTraId"
        class="text-center mb-4"
      >
        <h2
          class="fw-bold mb-2 quiz-page-title quiz-page-title--compact"
        >
          Làm Bài Kiểm Tra
        </h2>
        <button
          type="button"
          class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold"
          @click="veDanhSachKiemTra"
        >
          <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </button>
      </div>

      <div
        v-if="!token"
        class="bg-white rounded-5 shadow-sm p-5 text-center"
      >
        <p class="fw-bold text-secondary mb-0 fs-5">
          Bạn cần đăng nhập để làm bài kiểm tra.
        </p>
      </div>

      <template v-else-if="!baiKiemTraId">
        <div class="text-center mb-5">
          <h2
            class="fw-bold mb-3 quiz-page-title"
          >
            Bài Kiểm Tra
          </h2>
          <p class="text-secondary fs-5">
            Chọn bài kiểm tra theo bài học bạn đã học — ôn phát âm và củng cố từ vựng trước khi làm bài nhé.
          </p>
        </div>

        <div class="bg-white rounded-5 shadow-sm p-4 mb-5">
          <p class="text-muted mb-0 text-center">
            Các đề hiển thị khi bạn đã có tiến độ bài học hoặc đã từng làm kiểm tra.
          </p>
        </div>

        <div
          v-if="loadingList"
          class="text-center py-5"
        >
          <div
            class="spinner-border text-primary"
            role="status"
            style="width: 3rem; height: 3rem;"
          >
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-3 mb-0 text-muted fs-5">
            Đang tải danh sách…
          </p>
        </div>

        <div
          v-else-if="listError"
          class="bg-white rounded-5 shadow-sm p-4"
        >
          <div class="alert alert-danger mb-0 rounded-4">
            {{ listError }}
          </div>
        </div>

        <div
          v-else-if="!quizList.length"
          class="text-center py-5"
        >
          <template v-if="!coGiaoVienKetNoi">
            <div
              class="mb-3"
              style="font-size: 64px;"
            >
              👋
            </div>
            <h5
              class="fw-bold mb-2"
              style="color: #0d3b66;"
            >
              Kết nối với giáo viên
            </h5>
            <p class="text-muted mb-4 px-md-5 mx-auto" style="max-width: 520px;">
              Bạn chưa kết nối với giáo viên nào. Hãy gửi yêu cầu kết nối để bắt đầu làm bài kiểm tra từ giáo viên của bạn.
            </p>
            <button
              type="button"
              class="btn btn-primary rounded-pill px-4 py-2 fw-bold"
              @click="$router.push('/')"
            >
              Về trang chủ
            </button>
          </template>
          <template v-else>
            <div
              class="mb-3"
              style="font-size: 64px;"
            >
              🔍
            </div>
            <h5
              class="fw-bold mb-2"
              style="color: #0d3b66;"
            >
              Chưa có bài kiểm tra
            </h5>
            <p class="text-muted mb-4">
              Hãy học ít nhất một bài học có đề kiểm tra (đã xuất bản) để danh sách hiển thị tại đây.
            </p>
            <button
              type="button"
              class="btn btn-primary rounded-pill px-4 py-2 fw-bold"
              @click="$router.push('/bai-hoc')"
            >
              Đến danh sách bài học
            </button>
          </template>
        </div>

        <div
          v-else
          class="row g-4"
        >
          <div
            v-for="(q, idx) in quizList"
            :key="q.bai_kiem_tra_id"
            class="col-xl-3 col-lg-4 col-md-6"
          >
            <div class="card border-0 rounded-5 shadow-sm overflow-hidden lesson-card h-100">
              <div class="position-relative">
                <img
                  :src="quizPreset(idx).image"
                  class="card-img-top"
                  style="height: 220px; object-fit: cover;"
                  alt=""
                >
                <span
                  class="position-absolute top-0 start-0 badge rounded-pill px-3 py-2 m-3"
                  :style="{
                    backgroundColor: quizPreset(idx).topicColor,
                    color: '#fff',
                  }"
                >
                  Kiểm tra
                </span>
              </div>
              <div class="card-body p-4">
                <div
                  class="rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3"
                  :style="{
                    width: '70px',
                    height: '70px',
                    backgroundColor: quizPreset(idx).iconBg,
                  }"
                >
                  <span style="font-size: 34px;">{{ quizPreset(idx).icon }}</span>
                </div>
                <h4
                  class="fw-bold mb-2"
                  style="color: #0d3b66;"
                >
                  {{ q.bai_hoc_tieu_de || 'Bài học #' + q.bai_hoc_id }}
                </h4>
                <p class="fw-semibold mb-2 text-muted small">
                  {{ q.tieu_de || 'Đề kiểm tra' }}
                </p>
                <div
                  v-if="q.giao_vien"
                  class="d-flex align-items-center gap-2 mb-2"
                >
                  <img
                    v-if="teacherAvatarUrl(q)"
                    :src="teacherAvatarUrl(q)"
                    alt=""
                    class="rounded-circle border flex-shrink-0"
                    style="width: 40px; height: 40px; object-fit: cover;"
                  >
                  <div
                    v-else
                    class="rounded-circle bg-light border d-flex align-items-center justify-content-center flex-shrink-0 text-secondary"
                    style="width: 40px; height: 40px;"
                  >
                    <i class="bi bi-person-fill fs-5"></i>
                  </div>
                  <span class="small text-muted text-truncate">{{ q.giao_vien.ho_ten || 'Giáo viên' }}</span>
                </div>
                <p
                  class="text-muted mb-4 small"
                  style="display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;"
                >
                  {{ q.mo_ta_huong_dan || '—' }}
                </p>
                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Chuẩn bị</small>
                    <small
                      class="fw-bold"
                      :style="{ color: quizPreset(idx).topicColor }"
                    >
                      {{ quizProgressPercent(q) }}%
                    </small>
                  </div>
                  <div
                    class="progress rounded-pill"
                    style="height: 10px; background-color: #f1f1f1;"
                  >
                    <div
                      class="progress-bar rounded-pill"
                      role="progressbar"
                      :style="{
                        width: quizProgressPercent(q) + '%',
                        backgroundColor: quizPreset(idx).topicColor,
                      }"
                    ></div>
                  </div>
                </div>
                <div class="small text-muted mb-3">
                  <span v-if="q.thoi_gian_gioi_han_giay"><i class="bi bi-clock me-1"></i>{{ Math.floor(q.thoi_gian_gioi_han_giay / 60) }} phút</span>
                  <span
                    v-if="q.diem_toi_thieu != null"
                    class="ms-2"
                  ><i class="bi bi-flag me-1"></i>Đạt tối thiểu: {{ q.diem_toi_thieu }}</span>
                  <span
                    v-if="Number(q.tong_diem_toi_da) > 0"
                    class="ms-2"
                  ><i class="bi bi-trophy me-1"></i>Tối đa: {{ q.tong_diem_toi_da }}</span>
                </div>
                <div
                  v-if="q.diem_kiem_tra_tot_nhat != null || q.phien_gan_nhat"
                  class="small mb-3"
                >
                  <span
                    v-if="q.diem_kiem_tra_tot_nhat != null"
                    class="fw-bold"
                    :style="{ color: quizPreset(idx).topicColor }"
                  >Điểm tốt nhất: {{ q.diem_kiem_tra_tot_nhat }}</span>
                  <span
                    v-if="q.phien_gan_nhat && q.phien_gan_nhat.trang_thai === 0"
                    class="badge bg-warning text-dark ms-1"
                  >Đang làm dở</span>
                  <small class="d-block text-muted mt-1">Điểm tốt nhất tính chung cho bài học (gộp các đề).</small>
                </div>
                <button
                  type="button"
                  class="btn w-100 rounded-pill py-3 fw-bold text-white border-0"
                  :style="{ backgroundColor: quizPreset(idx).topicColor }"
                  @click="chonBaiKiemTra(q.bai_kiem_tra_id)"
                >
                  Làm Ngay
                </button>
              </div>
            </div>
          </div>
        </div>
      </template>

      <div
        v-else-if="loading"
        class="bg-white rounded-5 shadow-sm p-5 text-center"
      >
        <div
          class="spinner-border text-primary"
          role="status"
        >
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 mb-0 text-muted fs-5">
          Đang tải đề…
        </p>
      </div>

      <div
        v-else-if="errorMessage"
        class="bg-white rounded-5 shadow-sm p-4 text-center text-danger"
      >
        {{ errorMessage }}
      </div>

      <template v-else-if="result">
        <div class="bg-white rounded-5 shadow-sm p-4 text-center mb-3">
          <h2
            class="fw-bold mb-3 quiz-page-title quiz-page-title--compact"
          >
            Kết quả bài kiểm tra
          </h2>
          <p class="fs-4 mb-1">
            Điểm: <span class="fw-bold text-primary">{{ result.tong_diem }}</span>
            / {{ tongDiemToiDaResult }}
          </p>
          <p
            class="fw-bold mb-3"
            :class="result.qua ? 'text-success' : 'text-danger'"
          >
            {{ result.qua ? 'Đạt yêu cầu' : 'Chưa đạt điểm tối thiểu' }}
          </p>
          <p class="text-muted small mb-0">
            Điểm tối thiểu: {{ result.diem_toi_thieu }} — Trạng thái nộp: {{ trangThaiLabel(result.trang_thai) }}
          </p>
        </div>
      </template>

      <template v-else-if="exam && currentCau">
        <div class="bg-white rounded-5 shadow-sm p-3 mb-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span
              class="fw-bold"
              style="color: #0d3b66; font-size: 20px;"
            >
              Câu {{ currentIndex + 1 }} / {{ exam.cau_hoi.length }}
            </span>
            <span
              class="fw-bold text-danger"
              style="font-size: 22px;"
            >
              ⏰ {{ formatMmSs(timerRemaining) }}
            </span>
          </div>
          <div
            class="progress rounded-pill mb-2"
            style="height: 16px; width: 100%; background: #f1f1f1;"
          >
            <div
              class="progress-bar rounded-pill"
              role="progressbar"
              :style="{ width: progressPercent + '%', background: 'linear-gradient(135deg, #ff6b35, #ff8c42)' }"
            ></div>
          </div>
          <div class="text-end">
            <span
              class="fw-bold"
              style="color: #ff6b35; font-size: 16px;"
            >
              Hoàn thành {{ Math.round(progressPercent) }}%
            </span>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-lg-7">
            <div class="bg-white rounded-5 shadow-sm p-3 h-100">
              <div class="text-center mb-3">
                <h3
                  class="fw-bold mb-1"
                  style="color: #0d3b66; font-size: 28px;"
                >
                  {{ exam.bai_hoc?.tieu_de || 'Bài kiểm tra' }}
                </h3>
                <p class="text-muted mb-0">
                  {{ exam.bai_kiem_tra?.mo_ta_huong_dan || exam.bai_kiem_tra?.tieu_de || 'Làm bài theo hướng dẫn.' }}
                </p>
              </div>

              <div
                v-if="currentCau.loai === 'phat_am' && currentCau.tu_vung?.hinh_anh_url"
                class="position-relative mb-3"
              >
                <img
                  :src="resolveUrl(currentCau.tu_vung.hinh_anh_url)"
                  class="img-fluid rounded-5 shadow-sm"
                  style="height: 380px; width: 100%; object-fit: cover;"
                  alt=""
                />
              </div>

              <div
                v-if="currentCau.loai === 'phat_am' && currentCau.tu_vung?.tu_chuan"
                class="tu-chuan-box rounded-5 shadow-sm mb-3 d-flex align-items-center justify-content-center text-center"
              >
                <span class="tu-chuan-text fw-bold">
                  {{ currentCau.tu_vung.tu_chuan }}
                </span>
              </div>

              <div
                v-if="currentCau.loai === 'mcq'"
                class="mb-3"
              >
                <p
                  class="fw-bold fs-5"
                  style="color: #0d3b66;"
                >
                  {{ currentCau.noi_dung_cau }}
                </p>
              </div>

              <div class="row g-2">
                <div
                  v-if="currentCau.loai === 'phat_am' && currentCau.tu_vung?.am_thanh_mau_url"
                  class="col-6"
                >
                  <button
                    type="button"
                    class="btn btn-outline-primary rounded-pill py-3 fw-bold w-100"
                    @click="playMau"
                  >
                    🔊 Nghe Mẫu
                  </button>
                </div>
                <div :class="currentCau.loai === 'phat_am' && currentCau.tu_vung?.am_thanh_mau_url ? 'col-6' : 'col-12'">
                  <button
                    type="button"
                    class="btn btn-outline-secondary rounded-pill py-3 fw-bold w-100"
                    :disabled="busy"
                    @click="skipCau"
                  >
                    🔄 Bỏ qua
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-5">
            <div class="bg-white rounded-5 shadow-sm p-3 h-100 d-flex flex-column justify-content-between">
              <div v-if="currentCau.loai === 'mcq'">
                <p class="text-center fw-bold text-secondary mb-3">
                  Chọn đáp án đúng
                </p>
                <div class="d-grid gap-2">
                  <button
                    v-for="opt in currentCau.lua_chon"
                    :key="opt.id"
                    type="button"
                    class="rounded-pill py-3 fw-bold text-center mcq-option-btn"
                    :class="mcqOptionClass(opt.id)"
                    :disabled="busy || mcqAnswered"
                    @click="chonMcq(opt.id)"
                  >
                    <span
                      v-if="mcqFeedback && opt.id === mcqFeedback.selectedId"
                      class="me-2"
                    >{{ mcqFeedback.dung ? '✓' : '✗' }}</span>
                    <span
                      v-else-if="mcqFeedback && !mcqFeedback.dung && opt.id === mcqFeedback.dapAnDungId"
                      class="me-2"
                    >✓</span>
                    {{ opt.noi_dung }}
                  </button>
                </div>
                <div
                  v-if="mcqFeedback"
                  class="alert border-0 rounded-4 mt-3 mb-0 py-3"
                  :class="mcqFeedback.dung ? 'alert-success' : 'alert-danger'"
                  role="status"
                >
                  <span class="fw-bold">{{ mcqFeedback.dung ? 'Chính xác!' : 'Chưa đúng.' }}</span>
                  <span class="ms-1">Điểm câu này: {{ mcqFeedback.diemCau }} / {{ mcqFeedback.diemToiDa }}.</span>
                  <span
                    v-if="!mcqFeedback.dung"
                    class="d-block small mt-1 mb-0 opacity-90"
                  >Đáp án đúng đã được đánh dấu màu xanh.</span>
                </div>
              </div>

              <div v-else>
                <div class="text-center mb-3">
                  <span class="badge bg-danger rounded-pill px-4 py-2 mb-3">
                    Thu âm giọng nói
                  </span>
                  <button
                    type="button"
                    class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 microphone-box border-0"
                    :disabled="busy"
                    :class="{ 'recording-active': isRecording }"
                    @click="toggleRecording"
                  >
                    <span
                      v-if="!isRecording"
                      style="font-size: 70px;"
                    >🎤</span>
                    <div
                      v-else
                      class="d-flex justify-content-center align-items-end gap-2"
                      style="height: 70px;"
                    >
                      <div class="sound-bar"></div>
                      <div class="sound-bar bar-2"></div>
                      <div class="sound-bar bar-3"></div>
                      <div class="sound-bar bar-4"></div>
                      <div class="sound-bar bar-5"></div>
                    </div>
                  </button>
                  <p
                    class="fw-bold mb-2"
                    style="color: #ff6b35;"
                  >
                    {{ isRecording ? 'Đang ghi âm…' : 'Nhấn micro để bắt đầu' }}
                  </p>
                  <p class="text-muted mb-0 small">
                    Đọc rõ từ theo hình (tối đa 10 giây)
                  </p>
                </div>
              </div>

              <div
                v-if="pronunciationResult"
                class="result-box rounded-5 p-3 mt-2"
              >
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h5
                    class="fw-bold mb-0"
                    style="color: #0d3b66;"
                  >
                    Kết quả AI
                  </h5>
                  <div class="rounded-circle d-flex align-items-center justify-content-center score-circle">
                    <span class="fw-bold fs-5 text-success">{{ pronunciationResult.diem }}</span>
                  </div>
                </div>
                <p class="small text-muted mb-2">
                  {{ pronunciationResult.van_ban_nhan_dien }}
                </p>
              </div>

              <div class="mt-3">
                <button
                  v-if="isLastCau"
                  type="button"
                  class="btn btn-success rounded-pill px-4 py-3 fw-bold w-100"
                  :disabled="busy"
                  @click="submitQuiz"
                >
                  Nộp bài
                </button>
                <button
                  v-else
                  type="button"
                  class="btn btn-success rounded-pill px-4 py-3 fw-bold w-100"
                  :disabled="busy || !canNext"
                  @click="nextCau"
                >
                  Câu tiếp theo
                </button>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import { API_BASE } from '../../../api/http.js';
import axios from 'axios'

const QUIZ_STYLE_PRESETS = [
  {
    image: 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=800&q=80',
    icon: '📝',
    topicColor: '#ff6b35',
    iconBg: '#fff1eb',
  },
  {
    image: 'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=800&q=80',
    icon: '✅',
    topicColor: '#20c997',
    iconBg: '#e8faf5',
  },
  {
    image: 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?auto=format&fit=crop&w=800&q=80',
    icon: '🎯',
    topicColor: '#f4a100',
    iconBg: '#fff8e6',
  },
  {
    image: 'https://images.unsplash.com/photo-1494256997604-768d1f608cac?auto=format&fit=crop&w=800&q=80',
    icon: '⭐',
    topicColor: '#ff4d6d',
    iconBg: '#ffeaf0',
  },
  {
    image: 'https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&w=800&q=80',
    icon: '📚',
    topicColor: '#6f42c1',
    iconBg: '#f3ebff',
  },
  {
    image: 'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=800&q=80',
    icon: '🏆',
    topicColor: '#4d96ff',
    iconBg: '#eaf3ff',
  },
]

export default {
  name: 'BaiKiemTraPage',
  data() {
    return {
      loading: true,
      busy: false,
      errorMessage: '',
      exam: null,
      phienKiemTraId: null,
      currentIndex: 0,
      timerRemaining: 0,
      _timerId: null,
      _mediaRecorder: null,
      _audioChunks: [],
      _recordingTimer: null,
      isRecording: false,
      pronunciationResult: null,
      mcqAnswered: false,
      mcqFeedback: null,
      result: null,
      _mauAudio: null,
      loadingList: false,
      quizList: [],
      listError: '',
      coGiaoVienKetNoi: true,
    }
  },
  computed: {
    apiOrigin() {
      const u = (API_BASE).replace(/\/$/, '')
      return u.endsWith('/api') ? u.slice(0, -4) : u
    },
    apiBase() {
      return `${this.apiOrigin}/api`
    },
    token() {
      return typeof localStorage !== 'undefined' ? localStorage.getItem('token_nguoi_dung') : null
    },
    baiKiemTraId() {
      const q = this.$route?.query?.bai_kiem_tra_id
      const n = Number(q)
      return Number.isFinite(n) && n > 0 ? n : null
    },
    currentCau() {
      if (!this.exam?.cau_hoi?.length) return null
      return this.exam.cau_hoi[this.currentIndex] || null
    },
    isLastCau() {
      if (!this.exam?.cau_hoi?.length) return false
      return this.currentIndex >= this.exam.cau_hoi.length - 1
    },
    progressPercent() {
      if (!this.exam?.cau_hoi?.length) return 0
      return ((this.currentIndex + 1) / this.exam.cau_hoi.length) * 100
    },
    maxDiemTong() {
      if (!this.exam?.cau_hoi?.length) return 0
      return this.exam.cau_hoi.reduce((s, c) => s + (Number(c.diem_toi_da) || 0), 0)
    },
    tongDiemToiDaResult() {
      if (!this.result) return 0
      const t = this.result.tong_diem_toi_da
      if (t != null && Number(t) > 0) return Number(t)
      return this.maxDiemTong
    },
    canNext() {
      if (!this.currentCau) return false
      if (this.currentCau.loai === 'mcq') return this.mcqAnswered
      return !!this.pronunciationResult
    },
  },
  watch: {
    '$route.query.bai_kiem_tra_id'() {
      if (this.baiKiemTraId) {
        this.reload()
      } else {
        this.clearExamSession()
        if (this.token) {
          this.fetchQuizList()
        }
      }
    },
  },
  mounted() {
    if (this.baiKiemTraId) {
      this.reload()
    } else if (this.token) {
      this.fetchQuizList()
    } else {
      this.loading = false
    }
  },
  beforeUnmount() {
    this.clearTimer()
    this.stopMau()
    if (this._recordingTimer) clearTimeout(this._recordingTimer)
  },
  methods: {
    veDanhSachKiemTra() {
      this.$router.push({ path: '/bai-kiem-tra' })
    },
    mcqOptionClass(optId) {
      if (!this.mcqAnswered) {
        return 'btn-outline-dark border-2'
      }
      const fb = this.mcqFeedback
      if (!fb) {
        return 'btn-outline-secondary text-muted border-0'
      }
      if (optId === fb.selectedId) {
        return fb.dung ? 'btn-success text-white border-0' : 'btn-danger text-white border-0'
      }
      if (!fb.dung && fb.dapAnDungId && optId === fb.dapAnDungId) {
        return 'btn-outline-success fw-bold border border-success border-2 bg-white text-success'
      }
      return 'btn-light text-muted border-0'
    },
    quizPreset(index) {
      return QUIZ_STYLE_PRESETS[index % QUIZ_STYLE_PRESETS.length]
    },
    quizProgressPercent(q) {
      if (Number(q.qua_kiem_tra) === 1) {
        return 100
      }
      const d = Number(q.diem_kiem_tra_tot_nhat) || 0
      if (d > 0) {
        return Math.min(95, 35 + Math.min(60, Math.round(d)))
      }
      if (q.phien_gan_nhat && Number(q.phien_gan_nhat.trang_thai) === 0) {
        return 40
      }
      return 18
    },
    chonBaiKiemTra(baiKiemTraId) {
      this.$router.push({
        path: '/bai-kiem-tra',
        query: { bai_kiem_tra_id: String(baiKiemTraId) },
      })
    },
    clearExamSession() {
      this.clearTimer()
      this.stopMau()
      if (this._recordingTimer) clearTimeout(this._recordingTimer)
      this.loading = false
      this.busy = false
      this.errorMessage = ''
      this.exam = null
      this.phienKiemTraId = null
      this.currentIndex = 0
      this.timerRemaining = 0
      this.pronunciationResult = null
      this.mcqAnswered = false
      this.mcqFeedback = null
      this.result = null
      this.isRecording = false
    },
    async fetchQuizList() {
      if (!this.token) {
        return
      }
      this.loadingList = true
      this.listError = ''
      try {
        const res = await axios.get(`${this.apiBase}/hoc-vien/bai-kiem-tra`, {
          headers: this.authHeaders(),
        })
        if (!res.data?.status) {
          this.listError = res.data?.message || 'Không tải được danh sách.'
          this.quizList = []
          return
        }
        this.quizList = Array.isArray(res.data.data) ? res.data.data : []
        const m = res.data?.meta
        this.coGiaoVienKetNoi =
          m && typeof m.co_giao_vien_ket_noi === 'boolean' ? m.co_giao_vien_ket_noi : true
      } catch (e) {
        this.listError = e.response?.data?.message || e.message || 'Lỗi tải danh sách.'
        this.quizList = []
      } finally {
        this.loadingList = false
      }
    },
    teacherAvatarUrl(q) {
      const u = q?.giao_vien?.anh_dai_dien
      if (!u) return ''
      return this.resolveUrl(u)
    },
    authHeaders() {
      const t = this.token
      return t ? { Authorization: `Bearer ${t}` } : {}
    },
    resolveUrl(url) {
      if (!url) return ''
      if (url.startsWith('http://') || url.startsWith('https://')) return url
      return `${this.apiOrigin.replace(/\/$/, '')}/${String(url).replace(/^\//, '')}`
    },
    formatMmSs(sec) {
      const s = Math.max(0, Math.floor(Number(sec) || 0))
      const m = Math.floor(s / 60)
      const r = s % 60
      return `${String(m).padStart(2, '0')}:${String(r).padStart(2, '0')}`
    },
    trangThaiLabel(v) {
      if (v === 1) return 'Đã nộp'
      if (v === 2) return 'Hết giờ'
      return 'Đang làm'
    },
    clearTimer() {
      if (this._timerId) {
        clearInterval(this._timerId)
        this._timerId = null
      }
    },
    startTimer() {
      this.clearTimer()
      const limit = Number(this.exam?.bai_kiem_tra?.thoi_gian_gioi_han_giay) || 900
      this.timerRemaining = limit
      this._timerId = setInterval(() => {
        if (this.timerRemaining <= 0) {
          this.clearTimer()
          if (!this.result) this.submitQuiz()
          return
        }
        this.timerRemaining -= 1
      }, 1000)
    },
    async reload() {
      this.clearTimer()
      this.stopMau()
      this.loading = true
      this.errorMessage = ''
      this.exam = null
      this.phienKiemTraId = null
      this.currentIndex = 0
      this.pronunciationResult = null
      this.mcqAnswered = false
      this.mcqFeedback = null
      this.result = null

      if (!this.baiKiemTraId || !this.token) {
        this.loading = false
        return
      }

      try {
        const res = await axios.get(`${this.apiBase}/bai-kiem-tra/${this.baiKiemTraId}/lam-bai`, {
          headers: this.authHeaders(),
        })
        if (!res.data?.status) {
          this.errorMessage = res.data?.message || 'Không tải được đề.'
          return
        }
        this.exam = res.data.data
        const startRes = await axios.post(
          `${this.apiBase}/phien-kiem-tra/start`,
          { bai_kiem_tra_id: this.baiKiemTraId },
          { headers: this.authHeaders() }
        )
        if (!startRes.data?.status) {
          this.errorMessage = startRes.data?.message || 'Không tạo được phiên làm bài.'
          return
        }
        this.phienKiemTraId = startRes.data.data.phien_kiem_tra_id
        this.startTimer()
        this.resetCauState()
      } catch (e) {
        const msg = e.response?.data?.message
        this.errorMessage = msg || e.message || 'Lỗi tải đề.'
      } finally {
        this.loading = false
      }
    },
    resetCauState() {
      this.mcqAnswered = false
      this.mcqFeedback = null
      this.pronunciationResult = null
    },
    playMau() {
      const u = this.currentCau?.tu_vung?.am_thanh_mau_url
      if (!u) return
      this.stopMau()
      this._mauAudio = new Audio(this.resolveUrl(u))
      this._mauAudio.play().catch(() => {})
    },
    stopMau() {
      if (this._mauAudio) {
        this._mauAudio.pause()
        this._mauAudio.src = ''
        this._mauAudio = null
      }
    },
    async chonMcq(luaChonId) {
      if (!this.phienKiemTraId || !this.currentCau || this.busy) return
      this.busy = true
      try {
        const res = await axios.post(
          `${this.apiBase}/phien-kiem-tra/luu-cau`,
          {
            phien_kiem_tra_id: this.phienKiemTraId,
            cau_hoi_kiem_tra_id: this.currentCau.id,
            lua_chon_id: luaChonId,
          },
          { headers: this.authHeaders() }
        )
        if (!res.data?.status) {
          alert(res.data?.message || 'Không lưu được câu trả lời.')
          return
        }
        const d = res.data.data || {}
        this.mcqFeedback = {
          selectedId: luaChonId,
          dung: !!d.chon_dung,
          dapAnDungId: Number(d.dap_an_dung_id) || null,
          diemCau: Number(d.diem_cau) || 0,
          diemToiDa: Number(d.diem_toi_da) || Number(this.currentCau.diem_toi_da) || 0,
        }
        this.mcqAnswered = true
      } catch (e) {
        alert(e.response?.data?.message || e.message || 'Lỗi lưu câu.')
      } finally {
        this.busy = false
      }
    },
    async toggleRecording() {
      if (this.isRecording) {
        this.stopRecording()
        return
      }
      if (!this.currentCau || this.currentCau.loai !== 'phat_am' || this.busy) return
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true })
        this._audioChunks = []
        const mimeType = MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : ''
        this._mediaRecorder = new MediaRecorder(stream, mimeType ? { mimeType } : {})
        this._mediaRecorder.ondataavailable = (e) => {
          if (e.data.size > 0) this._audioChunks.push(e.data)
        }
        this._mediaRecorder.onstop = () => {
          stream.getTracks().forEach((t) => t.stop())
          const blob = new Blob(this._audioChunks, {
            type: this._mediaRecorder.mimeType || 'audio/webm',
          })
          this.submitPhatAm(blob)
        }
        this._mediaRecorder.start()
        this.isRecording = true
        this.pronunciationResult = null
        this._recordingTimer = setTimeout(() => {
          if (this.isRecording) this.stopRecording()
        }, 10000)
      } catch (e) {
        console.error(e)
        alert('Không thể truy cập microphone.')
      }
    },
    stopRecording() {
      if (this._recordingTimer) {
        clearTimeout(this._recordingTimer)
        this._recordingTimer = null
      }
      if (this._mediaRecorder && this._mediaRecorder.state !== 'inactive') {
        this._mediaRecorder.stop()
      }
      this.isRecording = false
    },
    async submitPhatAm(blob) {
      if (!this.phienKiemTraId || !this.currentCau) return
      this.busy = true
      try {
        const ext = blob.type.includes('ogg') ? 'ogg' : 'webm'
        const form = new FormData()
        form.append('phien_kiem_tra_id', String(this.phienKiemTraId))
        form.append('cau_hoi_kiem_tra_id', String(this.currentCau.id))
        form.append('audio', blob, `quiz.${ext}`)
        const res = await axios.post(`${this.apiBase}/phien-kiem-tra/cham-phat-am`, form, {
          headers: { ...this.authHeaders() },
        })
        if (!res.data?.status) {
          alert(res.data?.message || 'Chấm điểm thất bại.')
          return
        }
        const d = res.data.data || {}
        this.pronunciationResult = {
          diem: d.diem ?? 0,
          van_ban_nhan_dien: d.van_ban_nhan_dien || '',
        }
      } catch (e) {
        alert(e.response?.data?.message || e.message || 'Lỗi gửi âm thanh.')
      } finally {
        this.busy = false
      }
    },
    skipCau() {
      if (this.currentCau?.loai === 'mcq') {
        this.mcqAnswered = true
      } else {
        this.pronunciationResult = { diem: 0, van_ban_nhan_dien: '(Bỏ qua)' }
      }
    },
    nextCau() {
      if (!this.isLastCau && !this.canNext) return
      if (this.currentIndex < (this.exam.cau_hoi.length - 1)) {
        this.currentIndex += 1
        this.resetCauState()
      }
    },
    async submitQuiz() {
      if (!this.phienKiemTraId || this.busy) return
      this.busy = true
      this.clearTimer()
      try {
        const res = await axios.post(
          `${this.apiBase}/phien-kiem-tra/nop-bai`,
          { phien_kiem_tra_id: this.phienKiemTraId },
          { headers: this.authHeaders() }
        )
        if (!res.data?.status) {
          alert(res.data?.message || 'Nộp bài thất bại.')
          return
        }
        this.result = res.data.data
      } catch (e) {
        alert(e.response?.data?.message || e.message || 'Lỗi nộp bài.')
      } finally {
        this.busy = false
      }
    },
  },
}
</script>

<style scoped>
.quiz-page-title {
  color: #0d3b66;
  font-size: 48px;
}

.quiz-page-title--compact {
  font-size: 36px;
}

.lesson-card {
  transition: all 0.3s ease;
}

.lesson-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 18px 35px rgba(0, 0, 0, 0.12) !important;
}

.lesson-card img {
  transition: all 0.4s ease;
}

.lesson-card:hover img {
  transform: scale(1.05);
}

.mcq-option-btn {
  transition:
    background-color 0.2s ease,
    color 0.2s ease,
    border-color 0.2s ease,
    transform 0.15s ease;
}

.mcq-option-btn:not(:disabled):hover {
  transform: translateY(-1px);
}

.page-full {
  width: 100vw;
  margin-left: calc(-50vw + 50%);
  background: #f8fafc;
}

.content-wrapper {
  width: 92%;
  max-width: 1650px;
  margin: 0 auto;
}

.tu-chuan-box {
  min-height: 180px;
  padding: 24px 16px;
  background: linear-gradient(135deg, #fff8e6, #fff1eb);
  border: 2px dashed #ffd6b8;
}

.tu-chuan-text {
  color: #0d3b66;
  font-size: 110px;
  line-height: 1;
  letter-spacing: 2px;
}

.microphone-box {
  width: 150px;
  height: 150px;
  background: linear-gradient(135deg, #fff1eb, #ffe4d9);
  border: 6px solid #ff8c42;
  box-shadow:
    0 0 0 8px rgba(255, 140, 66, 0.25),
    0 15px 35px rgba(255, 107, 53, 0.15);
  transition: all 0.3s ease;
}

.microphone-box:hover:not(:disabled) {
  transform: scale(1.05);
}

.recording-active {
  background: linear-gradient(135deg, #ff6b35, #ff8c42);
  box-shadow:
    0 0 0 10px rgba(255, 107, 53, 0.2),
    0 20px 40px rgba(255, 107, 53, 0.35);
  animation: pulseRecord 1.5s infinite;
}

.score-circle {
  width: 70px;
  height: 70px;
  background: #e8faf5;
  border: 5px solid #d4f5e8;
}

.result-box {
  background: #f8fafc;
}

.sound-bar {
  width: 7px;
  height: 28px;
  background: #ffffff;
  border-radius: 20px;
  animation: soundWave 1s infinite ease-in-out;
}

.bar-2 {
  height: 45px;
  animation-delay: 0.15s;
}

.bar-3 {
  height: 60px;
  animation-delay: 0.3s;
}

.bar-4 {
  height: 45px;
  animation-delay: 0.45s;
}

.bar-5 {
  height: 28px;
  animation-delay: 0.6s;
}

@keyframes soundWave {
  0% {
    transform: scaleY(1);
    opacity: 0.7;
  }
  50% {
    transform: scaleY(1.8);
    opacity: 1;
  }
  100% {
    transform: scaleY(1);
    opacity: 0.7;
  }
}

@keyframes pulseRecord {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.06);
  }
  100% {
    transform: scale(1);
  }
}
</style>
