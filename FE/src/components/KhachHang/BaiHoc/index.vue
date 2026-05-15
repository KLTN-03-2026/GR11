<template>
    <div class="page-full bg-light min-vh-100">
       <div class="content-wrapper py-5">
        <!-- Tiêu đề -->
        <div class="text-center mb-5">
          <h2
            class="fw-bold mb-3"
            style="
              color: #0d3b66;
              font-size: 48px;
  
            "
          >
            Danh Sách Bài Học
          </h2>
  
          <p class="text-secondary fs-5">
            Bé có thể chọn bài học yêu thích để luyện phát âm và học từ vựng.
          </p>
  
          <p
            v-if="chuDeTen"
            class="fw-semibold mt-2 mb-0"
            style="color: #0d3b66;"
          >
            Đang xem chủ đề: {{ chuDeTen }}
          </p>
        </div>
  
        <!-- Bộ lọc -->
        <div class="bg-white rounded-5 shadow-sm p-4 mb-5">
          <div class="row g-3 align-items-center">
            <!-- Thanh tìm kiếm -->
            <div class="col-lg-5">
              <div class="position-relative">
                <i
                  class="bi bi-search position-absolute top-50 translate-middle-y ms-3"
                  style="color: #adb5bd; font-size: 16px; left: 4px;"
                ></i>
                <input
                  type="text"
                  class="form-control rounded-pill border-0 bg-light py-3 ps-5 pe-4"
                  placeholder="Tìm bài học, chủ đề..."
                  v-model="tuKhoa"
                />
              </div>
            </div>

            <!-- Lọc cấp độ -->
            <div class="col-lg-7">
              <div class="d-flex flex-wrap gap-2 justify-content-lg-end align-items-center">
                <span class="text-muted small me-1">Cấp độ:</span>
                <button
                  v-for="cd in danhSachCapDo"
                  :key="cd.value"
                  type="button"
                  class="btn rounded-pill px-3 py-2"
                  :class="capDoChon === cd.value ? cd.classActive : cd.classOutline"
                  @click="chonCapDo(cd.value)"
                >
                  {{ cd.label }}
                </button>
              </div>
            </div>
          </div>

          <!-- Lọc chủ đề -->
          <div class="row mt-3">
            <div class="col-12">
              <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted small me-1">Chủ đề:</span>
                <button
                  type="button"
                  class="btn rounded-pill px-4"
                  :class="danhMucChon === null ? 'btn-primary' : 'btn-outline-primary'"
                  @click="chonDanhMuc(null)"
                >
                  Tất Cả
                </button>
                <button
                  v-for="(dm, idx) in danhSachChuDe"
                  :key="dm.id"
                  type="button"
                  class="btn rounded-pill px-4"
                  :class="danhMucChon === dm.id
                    ? TOPIC_BTN_ACTIVE[idx % TOPIC_BTN_ACTIVE.length]
                    : TOPIC_BTN_OUTLINE[idx % TOPIC_BTN_OUTLINE.length]"
                  @click="chonDanhMuc(dm.id)"
                >
                  {{ dm.ten_danh_muc }}
                </button>
              </div>
            </div>
          </div>

          <!-- Kết quả tìm kiếm -->
          <div v-if="tuKhoa || danhMucChon || capDoChon" class="row mt-3">
            <div class="col-12">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <small class="text-muted">Đang lọc:</small>
                <span v-if="tuKhoa" class="badge bg-light text-dark border rounded-pill px-3 py-2">
                  <i class="bi bi-search me-1"></i>{{ tuKhoa }}
                  <button type="button" class="btn-close ms-2" style="font-size:10px;" @click="tuKhoa = ''"></button>
                </span>
                <span v-if="danhMucChon" class="badge bg-light text-dark border rounded-pill px-3 py-2">
                  <i class="bi bi-bookmark me-1"></i>{{ tenDanhMucChon }}
                  <button type="button" class="btn-close ms-2" style="font-size:10px;" @click="chonDanhMuc(null)"></button>
                </span>
                <span v-if="capDoChon" class="badge bg-light text-dark border rounded-pill px-3 py-2">
                  <i class="bi bi-bar-chart me-1"></i>{{ tenCapDoChon }}
                  <button type="button" class="btn-close ms-2" style="font-size:10px;" @click="chonCapDo('')"></button>
                </span>
                <button
                  type="button"
                  class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                  @click="xoaBoLoc"
                >
                  Xóa tất cả
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Lộ trình được gán (học viên đăng nhập) -->
        <div
          v-if="hienThiKhoiLoTrinh"
          class="bg-white rounded-5 shadow-sm p-4 mb-5 border border-light"
        >
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
              <h3 class="fw-bold mb-1" style="color: #0d3b66;">Lộ Trình Của Bé</h3>
              <p class="text-muted small mb-0">Bài học theo thứ tự giáo viên đã sắp xếp</p>
            </div>
            <div style="min-width: 220px; max-width: 100%;">
              <select
                v-model.number="loTrinhChonId"
                class="form-select rounded-pill border-0 bg-light py-2 px-3"
                :disabled="dangTaiLoTrinh || danhSachLoTrinh.length === 0"
                @change="loadChiTietLoTrinh"
              >
                <option
                  v-for="lt in danhSachLoTrinh"
                  :key="lt.id"
                  :value="lt.id"
                >
                  {{ lt.ten_lo_trinh }}{{ lt.can_mua ? ' (cần thanh toán)' : '' }}
                </option>
              </select>
            </div>
          </div>

          <div v-if="dangTaiLoTrinh" class="text-muted small py-2">Đang tải lộ trình...</div>

          <template v-else-if="danhSachLoTrinh.length > 0 && loTrinhHienTai">
            <div v-if="loTrinhError" class="alert alert-warning rounded-4 mb-0 py-2 small">{{ loTrinhError }}</div>

            <div v-else-if="loTrinhHienTai.can_mua" class="rounded-4 p-3" style="background: #fff8f5; border: 1px solid #ffe4d9;">
              <p class="mb-2 small text-dark">
                Lộ trình trả phí — <strong>{{ formatVnd(loTrinhHienTai.gia) }}</strong>. Thanh toán từ trang tiến độ để xem bài theo thứ tự.
              </p>
              <button
                type="button"
                class="btn rounded-pill px-4 py-2 fw-bold text-white"
                style="background: linear-gradient(135deg, #10b981, #059669);"
                @click="goToTienDo"
              >
                Đi tới trang Tiến độ
              </button>
            </div>

            <div
              v-else-if="loTrinhHienTai.la_tra_phi && !loTrinhHienTai.tra_phi_da_duyet"
              class="alert alert-warning rounded-4 mb-0 py-2 small"
            >
              Giá lộ trình đang chờ duyệt. Vui lòng quay lại sau.
            </div>

            <div v-else-if="loTrinhHienTai.can_hoc">
              <div v-if="dangTaiChiTietLoTrinh" class="text-muted small py-3">Đang tải danh sách bài...</div>
              <div v-else-if="chiTietLoTrinh.length === 0" class="text-muted small py-2">
                Chưa có bài học trong lộ trình này.
              </div>
              <ol v-else class="list-group list-group-numbered list-group-flush rounded-4 overflow-hidden border">
                <li
                  v-for="(row) in chiTietLoTrinh"
                  :key="row.bai_hoc_id"
                  class="list-group-item d-flex flex-wrap align-items-center justify-content-between gap-2 py-3"
                >
                  <div class="flex-grow-1" style="min-width: 200px;">
                    <span class="fw-semibold" style="color: #0d3b66;">{{ row.tieu_de || 'Bài học' }}</span>
                    <p v-if="row.ghi_chu_gv" class="small text-muted mb-0 mt-1">{{ row.ghi_chu_gv }}</p>
                  </div>
                  <button
                    type="button"
                    class="btn btn-sm rounded-pill px-3 fw-bold text-white shrink-0"
                    style="background: linear-gradient(135deg, #ff6b35, #ff8c42);"
                    @click="vaoChiTietBaiHoc(row.bai_hoc_id)"
                  >
                    Học ngay
                  </button>
                </li>
              </ol>
            </div>
          </template>
        </div>
  
        <!-- Danh sách bài học -->
        <div v-if="lessons.length === 0 && !dangTai" class="text-center py-5">
          <div class="mb-3" style="font-size: 64px;">🔍</div>
          <h5 class="fw-bold" style="color: #0d3b66;">Không tìm thấy bài học</h5>
          <p class="text-muted">Thử thay đổi từ khóa hoặc bộ lọc để xem nhiều bài học hơn.</p>
          <button class="btn btn-primary rounded-pill px-4" @click="xoaBoLoc">Xem tất cả bài học</button>
        </div>

        <div class="row g-4">
          <div
            class="col-xl-3 col-lg-4 col-md-6"
            v-for="lesson in lessons"
            :key="lesson.id"
          >
            <div class="card border-0 rounded-5 shadow-sm overflow-hidden lesson-card h-100">
              <div class="position-relative">
                <img
                  :src="lesson.image"
                  class="card-img-top"
                  style="height: 220px; object-fit: cover;"
                  alt=""
                />
  
                <span
                  class="position-absolute top-0 start-0 badge rounded-pill px-3 py-2 m-3"
                  :style="{
                    backgroundColor: lesson.topicColor,
                    color: '#fff'
                  }"
                >
                  {{ lesson.topic }}
                </span>
              </div>
  
              <div class="card-body p-4">
                <div
                  class="rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3"
                  :style="{
                    width: '70px',
                    height: '70px',
                    backgroundColor: lesson.iconBg
                  }"
                >
                  <span style="font-size: 34px;">
                    {{ lesson.icon }}
                  </span>
                </div>
  
                <h4 class="fw-bold mb-2" style="color: #0d3b66;">
                  {{ lesson.title }}
                </h4>
  
                <p class="text-muted mb-4">
                  {{ lesson.description }}
                </p>
  
                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Tiến Độ</small>
                    <small class="fw-bold" :style="{ color: lesson.topicColor }">
                      {{ lesson.progress }}%
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
                        width: lesson.progress + '%',
                        backgroundColor: lesson.topicColor
                      }"
                    ></div>
                  </div>
                </div>
  
                <button
                  type="button"
                  class="btn w-100 rounded-pill py-3 fw-bold text-white"
                  :style="{ backgroundColor: lesson.topicColor }"
                  @click="vaoChiTietBaiHoc(lesson.id)"
                >
                  Học Ngay
                </button>
              </div>
            </div>
          </div>
        </div>
  <div class="text-center mt-5" v-if="coThemBaiHoc || dangTaiThem">
    <p class="text-muted mb-3">
      Bé muốn khám phá thêm nhiều bài học thú vị hơn?
    </p>

    <button
      class="btn rounded-pill px-5 py-3 fw-bold text-white shadow-sm"
      style="background: linear-gradient(135deg, #ff6b35, #ff8c42); transition: all 0.3s ease;"
      :disabled="dangTaiThem"
      @click="xemThem"
      @mouseover="$event.currentTarget.style.transform='translateY(-4px)'"
      @mouseout="$event.currentTarget.style.transform='translateY(0px)'"
    >
      <span v-if="dangTaiThem">
        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
        Đang tải...
      </span>
      <span v-else>
        Xem Thêm Bài Học
        <i class="bi bi-arrow-down-circle ms-2"></i>
      </span>
    </button>
  </div>
        <!-- Banner -->
  <div class="position-relative overflow-hidden rounded-5 shadow-sm bg-white mt-5">
    <div class="row align-items-center">
      <div class="col-lg-6 p-4 p-lg-5">
        <span class="badge bg-warning text-dark rounded-pill px-4 py-2 mb-3 fs-6">
          Kho Bài Học EchoKids
        </span>
  
        <h1
          class="fw-bold mb-4"
          style="
            color: #0d3b66;
            font-size: 58px;
            line-height: 1.2;
            font-family: 'Lobster Two', cursive;
          "
        >
          Chọn Bài Học<br />
          Bé Yêu Thích
        </h1>
  
        <p class="text-secondary fs-5 mb-4" style="max-width: 600px;">
          Hàng trăm bài học thú vị đang chờ bé khám phá với hình ảnh sinh động,
          âm thanh vui nhộn và nhiều chủ đề hấp dẫn mỗi ngày.
        </p>
  
        <div class="d-flex flex-wrap gap-3 mb-4">
          <button class="btn start-btn rounded-pill px-4 py-3 fw-bold text-white">
            Học Ngay
          </button>
  
          <button class="btn btn-light border rounded-pill px-4 py-3 fw-bold shadow-sm">
            Xem Tiến Độ
          </button>
        </div>
  
  
      </div>
  
      <div class="col-lg-6 position-relative text-center py-5">
        <div class="position-relative d-inline-block">
          <!-- vòng nền -->
          <div
            class="position-absolute top-50 start-50 translate-middle rounded-circle"
            style="
              width: 460px;
              height: 460px;
              background: #fff3ef;
              z-index: 1;
            "
          ></div>
  
          <!-- ảnh chính -->
          <div
            class="position-relative rounded-circle overflow-hidden shadow-lg"
            style="
              width: 370px;
              height: 370px;
              border: 10px solid #fff;
              z-index: 2;
            "
          >
            <img
              src="https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=900&q=80"
              class="w-100 h-100"
              style="object-fit: cover;"
              alt=""
            />
          </div>
  
  <!-- card nhỏ 1 -->
  <div
    class="position-absolute bg-white rounded-circle shadow-sm d-flex flex-column align-items-center justify-content-center text-center"
    style="
      top: 25px;
      left: -20px;
      width: 110px;
      height: 110px;
      z-index: 3;
      border: 6px solid #fff1eb;
    "
  >
    <div
      class="rounded-circle d-flex align-items-center justify-content-center mb-2"
      style="
        width: 42px;
        height: 42px;
        background: #fff1eb;
      "
    >
      <span style="font-size: 20px;">🔤</span>
    </div>
    <p
      class="mb-0 fw-bold"
      style="
        font-size: 13px;
        color: #0d3b66;
        line-height: 1.3;
      "
    >
      Bảng Chữ Cái
    </p>
  </div>
  
  <!-- card nhỏ 2 -->
  <div
    class="position-absolute bg-white rounded-circle shadow-sm d-flex flex-column align-items-center justify-content-center text-center"
    style="
      bottom: -10px;
      left: -15px;
      width: 105px;
      height: 105px;
      z-index: 3;
      border: 6px solid #e8faf5;
    "
  >
    <div
      class="rounded-circle d-flex align-items-center justify-content-center mb-2"
      style="
        width: 40px;
        height: 40px;
        background: #e8faf5;
      "
    >
      <span style="font-size: 20px;">🐶</span>
    </div>
    <p
      class="mb-0 fw-bold"
      style="
        font-size: 13px;
        color: #0d3b66;
        line-height: 1.3;
      "
    >
      Động Vật
    </p>
  </div>
  
  <!-- card nhỏ 3 -->
  <div
    class="position-absolute bg-white rounded-circle shadow-sm d-flex flex-column align-items-center justify-content-center text-center"
    style="
      top: 190px;
      right: -35px;
      width: 105px;
      height: 105px;
      z-index: 4;
      border: 6px solid #fff8e6;
    "
  >
    <div
      class="rounded-circle d-flex align-items-center justify-content-center mb-2"
      style="
        width: 40px;
        height: 40px;
        background: #fff8e6;
      "
    >
      <span style="font-size: 20px;">🍎</span>
    </div>
    <p
      class="mb-0 fw-bold"
      style="
        font-size: 13px;
        color: #0d3b66;
        line-height: 1.3;
      "
    >
      Trái Cây
    </p>
  </div>
        </div>
      </div>
    </div>
  </div>
      </div>
      
    </div>
  </template>
  
<script>
import axios from 'axios';

const LESSON_STYLE_PRESETS = [
    {
        image: 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=800&q=80',
        icon: '🔤',
        topicColor: '#ff6b35',
        iconBg: '#fff1eb',
    },
    {
        image: 'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=800&q=80',
        icon: '🐶',
        topicColor: '#20c997',
        iconBg: '#e8faf5',
    },
    {
        image: 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?auto=format&fit=crop&w=800&q=80',
        icon: '🍎',
        topicColor: '#f4a100',
        iconBg: '#fff8e6',
    },
    {
        image: 'https://images.unsplash.com/photo-1494256997604-768d1f608cac?auto=format&fit=crop&w=800&q=80',
        icon: '🎨',
        topicColor: '#ff4d6d',
        iconBg: '#ffeaf0',
    },
    {
        image: 'https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&w=800&q=80',
        icon: '👨‍👩‍👧',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=800&q=80',
        icon: '🚗',
        topicColor: '#4d96ff',
        iconBg: '#eaf3ff',
    },
];

const TOPIC_BTN_OUTLINE = [
    'btn-outline-warning',
    'btn-outline-success',
    'btn-outline-danger',
    'btn-outline-info',
    'btn-outline-secondary',
];
const TOPIC_BTN_ACTIVE = [
    'btn-warning',
    'btn-success',
    'btn-danger',
    'btn-info',
    'btn-secondary',
];

export default {
    name: 'LessonPage',
    data() {
        return {
            apiBase: (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, ''),
            lessons: [],
            chuDeTen: '',
            tuKhoa: '',
            capDoChon: '',
            danhMucChon: null,
            danhSachChuDe: [],
            dangTai: false,
            dangTaiThem: false,
            coThemBaiHoc: false,
            trangHienTai: 1,
            debounceTimer: null,
            abortController: null,
            danhSachLoTrinh: [],
            loTrinhChonId: null,
            chiTietLoTrinh: [],
            loTrinhError: '',
            dangTaiLoTrinh: false,
            dangTaiChiTietLoTrinh: false,
            TOPIC_BTN_OUTLINE,
            TOPIC_BTN_ACTIVE,
            danhSachCapDo: [
                { value: '', label: 'Tất Cả', classOutline: 'btn-outline-primary', classActive: 'btn-primary' },
                { value: 'basic', label: 'Cơ Bản', classOutline: 'btn-outline-success', classActive: 'btn-success' },
                { value: 'intermediate', label: 'Trung Cấp', classOutline: 'btn-outline-warning', classActive: 'btn-warning text-white' },
                { value: 'advanced', label: 'Nâng Cao', classOutline: 'btn-outline-danger', classActive: 'btn-danger' },
            ],
        };
    },
    computed: {
        tenDanhMucChon() {
            const dm = this.danhSachChuDe.find((d) => d.id === this.danhMucChon);
            return dm?.ten_danh_muc || '';
        },
        tenCapDoChon() {
            const cd = this.danhSachCapDo.find((c) => c.value === this.capDoChon);
            return cd?.label || '';
        },
        coTokenHocVien() {
            return !!localStorage.getItem('token_nguoi_dung');
        },
        hienThiKhoiLoTrinh() {
            return this.coTokenHocVien && (this.dangTaiLoTrinh || this.danhSachLoTrinh.length > 0);
        },
        loTrinhHienTai() {
            if (!this.loTrinhChonId) return null;
            return this.danhSachLoTrinh.find((l) => Number(l.id) === Number(this.loTrinhChonId)) || null;
        },
    },
    mounted() {
        const idFromRoute = this.$route.query.danh_muc_id;
        if (idFromRoute) {
            this.danhMucChon = Number(idFromRoute);
        }
        this.fetchDanhMuc();
        this.fetchLessons(false);
        this.fetchDanhSachLoTrinh();
    },
    beforeUnmount() {
        clearTimeout(this.debounceTimer);
        if (this.abortController) {
            this.abortController.abort();
        }
    },
    watch: {
        '$route.query.danh_muc_id'(newVal) {
            this.danhMucChon = newVal ? Number(newVal) : null;
            this.fetchLessons(false);
        },
        tuKhoa() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.fetchLessons(false);
            }, 400);
        },
    },
    methods: {
        authHeaders() {
            const token = localStorage.getItem('token_nguoi_dung');
            return token ? { Authorization: `Bearer ${token}` } : {};
        },
        formatVnd(n) {
            if (n == null || n === '') return '0 đ';
            return `${Number(n).toLocaleString('vi-VN')} đ`;
        },
        goToTienDo() {
            this.$router.push('/tien-do');
        },
        async fetchDanhSachLoTrinh() {
            const token = localStorage.getItem('token_nguoi_dung');
            if (!token) {
                this.danhSachLoTrinh = [];
                this.loTrinhChonId = null;
                this.chiTietLoTrinh = [];
                return;
            }
            this.dangTaiLoTrinh = true;
            this.loTrinhError = '';
            try {
                const { data: res } = await axios.get(`${this.apiBase}/api/hoc-vien/lo-trinh-ca-nhan`, {
                    headers: this.authHeaders(),
                });
                if (res.status && Array.isArray(res.data)) {
                    this.danhSachLoTrinh = res.data;
                    const stillValid = this.danhSachLoTrinh.some(
                        (l) => Number(l.id) === Number(this.loTrinhChonId)
                    );
                    if (!stillValid) {
                        this.loTrinhChonId = this.danhSachLoTrinh.length ? this.danhSachLoTrinh[0].id : null;
                    }
                    await this.loadChiTietLoTrinh();
                } else {
                    this.danhSachLoTrinh = [];
                    this.loTrinhChonId = null;
                    this.chiTietLoTrinh = [];
                }
            } catch (err) {
                if (err.response?.status === 401) {
                    this.danhSachLoTrinh = [];
                    this.loTrinhChonId = null;
                    this.chiTietLoTrinh = [];
                } else {
                    this.loTrinhError = 'Không tải được danh sách lộ trình.';
                    this.danhSachLoTrinh = [];
                    this.loTrinhChonId = null;
                    this.chiTietLoTrinh = [];
                }
            } finally {
                this.dangTaiLoTrinh = false;
            }
        },
        async loadChiTietLoTrinh() {
            this.loTrinhError = '';
            const lt = this.loTrinhHienTai;
            if (!lt || !lt.can_hoc) {
                this.chiTietLoTrinh = [];
                this.dangTaiChiTietLoTrinh = false;
                return;
            }
            this.dangTaiChiTietLoTrinh = true;
            this.chiTietLoTrinh = [];
            try {
                const { data: res } = await axios.get(
                    `${this.apiBase}/api/hoc-vien/lo-trinh-ca-nhan/${lt.id}`,
                    { headers: this.authHeaders() }
                );
                if (res.status && res.data?.chi_tiet) {
                    const raw = Array.isArray(res.data.chi_tiet) ? res.data.chi_tiet : [];
                    this.chiTietLoTrinh = [...raw].sort(
                        (a, b) => Number(a.thu_tu_uu_tien || 0) - Number(b.thu_tu_uu_tien || 0)
                    );
                }
            } catch (err) {
                if (err.response?.status === 403 || err.response?.data?.code === 'REQUIRES_PURCHASE') {
                    this.chiTietLoTrinh = [];
                } else if (err.response?.status === 401) {
                    this.chiTietLoTrinh = [];
                } else {
                    this.loTrinhError = 'Không tải được chi tiết lộ trình.';
                }
            } finally {
                this.dangTaiChiTietLoTrinh = false;
            }
        },
        mapBaiHocToLesson(row, globalIndex) {
            const style = LESSON_STYLE_PRESETS[globalIndex % LESSON_STYLE_PRESETS.length];
            const dm = row.danh_muc;
            return {
                id: row.id,
                title: row.tieu_de,
                description: row.mo_ta || '',
                image: style.image,
                topic: dm?.ten_danh_muc || 'Bài học',
                icon: style.icon,
                progress: 0,
                topicColor: style.topicColor,
                iconBg: style.iconBg,
            };
        },
        async fetchLessonProgress() {
            const token = localStorage.getItem('token_nguoi_dung');
            if (!token || this.lessons.length === 0) {
                this.lessons = this.lessons.map((lesson) => ({ ...lesson, progress: 0 }));
                return;
            }

            try {
                const { data: res } = await axios.get(
                    `${this.apiBase}/api/tien-do-bai-hoc/tong-quan`,
                    {
                        headers: this.authHeaders(),
                        params: {
                            bai_hoc_limit: 1000,
                            bai_hoc_offset: 0,
                        },
                    }
                );

                const danhSachTienDo = Array.isArray(res?.data?.bai_hocs?.danh_sach)
                    ? res.data.bai_hocs.danh_sach
                    : [];

                const progressMap = new Map(
                    danhSachTienDo.map((item) => [
                        Number(item.bai_hoc_id),
                        Number(item.tien_do || 0),
                    ])
                );

                this.lessons = this.lessons.map((lesson) => ({
                    ...lesson,
                    progress: progressMap.get(Number(lesson.id)) || 0,
                }));
            } catch (err) {
                if (err.response?.status === 401) {
                    this.lessons = this.lessons.map((lesson) => ({ ...lesson, progress: 0 }));
                    return;
                }
                console.error(err);
            }
        },
        chonDanhMuc(id) {
            this.danhMucChon = id;
            this.fetchLessons(false);
        },
        chonCapDo(value) {
            this.capDoChon = value;
            this.fetchLessons(false);
        },
        xoaBoLoc() {
            this.tuKhoa = '';
            this.capDoChon = '';
            this.danhMucChon = null;
            this.$router.push({ path: '/bai-hoc' });
            this.fetchLessons(false);
        },
        xemTatCaBaiHoc() {
            this.xoaBoLoc();
        },
        xemThem() {
            if (!this.coThemBaiHoc || this.dangTaiThem) return;
            this.fetchLessons(true);
        },
        vaoChiTietBaiHoc(id) {
            this.$router.push({ path: `/chi-tiet-bai-hoc/${id}` });
        },
        fetchDanhMuc() {
            axios
                .get(`${this.apiBase}/api/danh-muc-bai-hoc`)
                .then((res) => {
                    this.danhSachChuDe = Array.isArray(res.data?.data) ? res.data.data : [];
                })
                .catch((err) => {
                    console.error(err);
                    this.danhSachChuDe = [];
                });
        },
        fetchLessons(themVao) {
            // Hủy request đang chạy nếu có
            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();

            if (themVao) {
                this.trangHienTai += 1;
                this.dangTaiThem = true;
            } else {
                this.trangHienTai = 1;
                this.dangTai = true;
            }

            const params = { page: this.trangHienTai };
            if (this.danhMucChon) params.danh_muc_id = this.danhMucChon;
            if (this.tuKhoa.trim()) params.tim_kiem = this.tuKhoa.trim();
            if (this.capDoChon) params.cap_do = this.capDoChon;

            axios
                .get(`${this.apiBase}/api/bai-hoc`, {
                    params,
                    signal: this.abortController.signal,
                })
                .then((res) => {
                    const data = res.data;
                    const rows = Array.isArray(data?.data) ? data.data : [];
                    const startIndex = themVao ? this.lessons.length : 0;
                    const mapped = rows.map((row, i) => this.mapBaiHocToLesson(row, startIndex + i));

                    if (themVao) {
                        this.lessons = [...this.lessons, ...mapped];
                    } else {
                        this.lessons = mapped;
                    }

                    this.coThemBaiHoc = data?.pagination?.con_trang_tiep ?? false;
                    this.chuDeTen = data?.meta?.danh_muc?.ten_danh_muc || '';
                    this.fetchLessonProgress();
                })
                .catch((err) => {
                    if (err.code === 'ERR_CANCELED') return;
                    console.error(err);
                    if (!themVao) {
                        this.lessons = [];
                        this.chuDeTen = '';
                    }
                    this.coThemBaiHoc = false;
                })
                .finally(() => {
                    this.dangTai = false;
                    this.dangTaiThem = false;
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
  .content-wrapper { width: 92%; max-width: 1650px; margin: 0 auto; } .page-full { width: 100vw; margin-left: calc(-50vw + 50%); }
  </style>