<template>
  <div class="teacher-dashboard">
    <div class="header-card mb-4">
      <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
          <h4 class="fw-bold mb-1">Tổng Quan Giáo Viên</h4>
          <p class="text-muted mb-0">
            Xin chào <span class="fw-bold text-danger">{{ ten_gv_hien_thi || "Giáo viên" }}</span>
          </p>
        </div>
        <div class="btn-group">
          <button
            v-for="opt in tuy_chon_ky"
            :key="opt.value"
            type="button"
            class="btn btn-sm"
            :class="chu_ky_chon === opt.value ? 'btn-primary' : 'btn-outline-secondary'"
            @click="doiChuKyDashboard(opt.value)"
          >
            {{ opt.label }}
          </button>
        </div>
      </div>
      <small class="text-muted d-block mt-2">Kỳ đang xem: {{ nhan_ky_hien_tai }}</small>
    </div>

    <div v-if="loading" class="text-center py-5 text-muted">
      <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
      Đang tải dữ liệu...
    </div>

    <template v-else>
      <div class="row g-3 mb-4">
        <div v-for="(stat, idx) in the_tom_tat" :key="idx" class="col-md-6 col-xl-3">
          <div class="kpi-card h-100">
            <small class="text-muted">{{ stat.label }}</small>
            <h3 class="fw-bold mb-1" :class="stat.colorClass">{{ stat.value }}</h3>
            <small :class="layMauXuHuong(stat.trend)">
              {{ hienThiXuHuong(stat.trend, stat.label === 'Học viên phụ trách') }}
            </small>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-8">
          <div class="panel-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="fw-bold mb-0">Lượt luyện tập theo thời gian</h6>
              <div class="btn-group btn-group-sm">
                <button
                  v-for="g in tuy_chon_khung_bieu_do"
                  :key="g.value"
                  class="btn"
                  :class="khung_bieu_do === g.value ? 'btn-primary' : 'btn-outline-primary'"
                  @click="doiKhungBieuDo(g.value)"
                >
                  {{ g.label }}
                </button>
              </div>
            </div>
            <div class="chart-wrap">
              <canvas id="activityChart"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="panel-card h-100">
            <h6 class="fw-bold mb-3">Phân loại lỗi phát âm</h6>
            <div class="chart-wrap">
              <canvas id="errorTypeChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-6">
          <div class="panel-card h-100">
            <h6 class="fw-bold mb-3">Tình hình trong kỳ</h6>
            <div class="row g-2 mb-3">
              <div class="col-4 text-center">
                <div class="mini-stat">
                  <div class="fw-bold text-primary">{{ thong_ke_lop_hoc.bai_dang_giao }}</div>
                  <small class="text-muted">Bài đã tạo</small>
                </div>
              </div>
              <div class="col-4 text-center">
                <div class="mini-stat">
                  <div class="fw-bold text-success">{{ thong_ke_lop_hoc.luot_nop_bai }}</div>
                  <small class="text-muted">Lượt trong kỳ</small>
                </div>
              </div>
              <div class="col-4 text-center">
                <div class="mini-stat">
                  <div class="fw-bold text-warning">{{ thong_ke_lop_hoc.diem_trung_binh }}</div>
                  <small class="text-muted">Điểm TB</small>
                </div>
              </div>
            </div>
            <small class="text-muted">Lỗi phổ biến: <span class="fw-semibold text-danger">{{ thong_ke_lop_hoc.loi_pho_thong }}</span></small>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="panel-card h-100">
            <h6 class="fw-bold mb-3">Top học viên (theo kỳ)</h6>
            <div v-if="!top_hoc_sinh_noi_bat.length" class="text-muted small">Chưa có dữ liệu.</div>
            <div v-for="(hv, index) in top_hoc_sinh_noi_bat" :key="hv.id || index" class="d-flex justify-content-between py-2 border-bottom">
              <div>
                <span class="fw-bold me-2">#{{ index + 1 }}</span>
                <span>{{ hv.ho_ten }}</span>
              </div>
              <span class="fw-bold text-success">{{ hv.diem_trung_binh }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="panel-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0">Nhật ký hoạt động gần đây</h6>
          <a href="#" class="text-decoration-none small" @click.prevent="diToiDanhSachHocVien">Quản lý học viên</a>
        </div>
        <div v-if="!danh_sach_hoat_dong.length" class="text-muted small">Chưa có hoạt động gần đây.</div>
        <div v-for="item in danh_sach_hoat_dong" :key="item.id" class="d-flex gap-2 py-2 border-bottom">
          <div>{{ item.icon }}</div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <span class="fw-semibold">{{ item.tieu_de }}</span>
              <small class="text-muted">{{ item.thoi_gian }}</small>
            </div>
            <small class="text-muted">{{ item.mo_ta }}</small>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import axios from 'axios';
import Chart from 'chart.js/auto';

export default {
  data() {
    return {
      apiBase: (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, ''),
      loading: false,
      chu_ky_chon: 'week',
      khung_bieu_do: 'day',
      ho_ten_giao_vien: '',
      dashboard_meta: { label_ky: '' },
      tuy_chon_ky: [
        { value: 'week', label: 'Tuần' },
        { value: 'month', label: 'Tháng' },
        { value: 'quarter', label: 'Quý' },
        { value: 'all', label: 'Toàn TG' },
      ],
      tuy_chon_khung_bieu_do: [
        { value: 'day', label: 'Ngày' },
        { value: 'week', label: 'Tuần' },
        { value: 'month', label: 'Tháng' },
        { value: 'year', label: 'Năm' },
      ],
      du_lieu_bieu_do: { day: { labels: [], data: [] }, week: { labels: [], data: [] }, month: { labels: [], data: [] }, year: { labels: [], data: [] } },
      du_lieu_loi_phat_am: { labels: ['Sai thanh điệu', 'Sai âm đầu', 'Sai vần'], data: [0, 0, 0] },
      thong_ke_lop_hoc: { bai_dang_giao: 0, luot_nop_bai: 0, diem_trung_binh: 0, loi_pho_thong: 'Chưa ghi nhận' },
      the_tom_tat: [],
      danh_sach_hoat_dong: [],
      top_hoc_sinh_noi_bat: [],
      doi_tuong_bieu_do: {},
    };
  },
  computed: {
    ten_gv_hien_thi() {
      return (this.ho_ten_giao_vien || '').trim();
    },
    nhan_ky_hien_tai() {
      return this.dashboard_meta.label_ky || '—';
    },
  },
  mounted() {
    this.ho_ten_giao_vien = localStorage.getItem('ho_ten') || '';
    this.taiTongQuan();
  },
  beforeUnmount() {
    if (this.doi_tuong_bieu_do.hoat_dong) this.doi_tuong_bieu_do.hoat_dong.destroy();
    if (this.doi_tuong_bieu_do.loi_phat_am) this.doi_tuong_bieu_do.loi_phat_am.destroy();
  },
  methods: {
    getAuthToken() {
      return localStorage.getItem('token_teacher') || '';
    },
    authHeaders() {
      return { Authorization: 'Bearer ' + this.getAuthToken() };
    },
    doiChuKyDashboard(value) {
      if (this.chu_ky_chon === value) return;
      this.chu_ky_chon = value;
      this.taiTongQuan();
    },
    doiKhungBieuDo(value) {
      this.khung_bieu_do = value;
      this.capNhatBieuDo();
    },
    taiTongQuan() {
      this.loading = true;
      axios.get(this.apiBase + '/api/teacher/gv-hv/dashboard', {
        headers: this.authHeaders(),
        params: { chu_ky: this.chu_ky_chon },
      }).then((res) => {
        const data = res.data?.data || {};
        const tomTat = data.the_tom_tat || {};
        const xuHuong = data.xu_huong_tom_tat || {};
        this.dashboard_meta = data.meta || {};
        this.the_tom_tat = [
          { label: 'Học viên phụ trách', value: tomTat.hoc_sinh_tham_gia || 0, colorClass: 'text-primary', trend: xuHuong.hoc_sinh_tham_gia || 0 },
          { label: 'Bài học đã tạo', value: tomTat.bai_hoc_da_tao || 0, colorClass: 'text-success', trend: xuHuong.bai_hoc_da_tao || 0 },
          { label: 'Lượt luyện trong kỳ', value: tomTat.luot_luyen_tap_tuan || 0, colorClass: 'text-warning', trend: xuHuong.luot_luyen_tap_tuan || 0 },
          { label: 'Học viên cần chú ý', value: tomTat.hoc_sinh_can_chu_y || 0, colorClass: 'text-danger', trend: xuHuong.hoc_sinh_can_chu_y || 0 },
        ];
        this.thong_ke_lop_hoc = { ...this.thong_ke_lop_hoc, ...(data.thong_ke_lop_hoc || {}) };
        this.du_lieu_bieu_do = { ...this.du_lieu_bieu_do, ...(data.du_lieu_bieu_do || {}) };
        this.du_lieu_loi_phat_am = data.du_lieu_loi_phat_am || this.du_lieu_loi_phat_am;
        this.danh_sach_hoat_dong = data.danh_sach_hoat_dong || [];
        this.top_hoc_sinh_noi_bat = data.top_hoc_sinh_noi_bat || [];
      }).finally(() => {
        this.loading = false;
        this.$nextTick(() => {
          if (this.doi_tuong_bieu_do.hoat_dong) {
            this.capNhatBieuDo();
            this.capNhatBieuDoLoiPhatAm();
          } else {
            this.khoiTaoBieuDo();
          }
        });
      });
    },
    duLieuBieuDoHienTai() {
      return this.du_lieu_bieu_do[this.khung_bieu_do] || { labels: [], data: [] };
    },
    khoiTaoBieuDo() {
      const act = document.getElementById('activityChart');
      const err = document.getElementById('errorTypeChart');
      if (!act || !err) return;
      const cur = this.duLieuBieuDoHienTai();
      this.doi_tuong_bieu_do.hoat_dong = new Chart(act.getContext('2d'), {
        type: 'line',
        data: { labels: cur.labels, datasets: [{ label: 'Số phiên luyện', data: cur.data, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.12)', fill: true, tension: 0.35 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true } } },
      });
      this.doi_tuong_bieu_do.loi_phat_am = new Chart(err.getContext('2d'), {
        type: 'doughnut',
        data: { labels: this.du_lieu_loi_phat_am.labels, datasets: [{ data: this.du_lieu_loi_phat_am.data, backgroundColor: ['#dc2626', '#ca8a04', '#0891b2'] }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '62%' },
      });
    },
    capNhatBieuDo() {
      const chart = this.doi_tuong_bieu_do.hoat_dong;
      if (!chart) return;
      const cur = this.duLieuBieuDoHienTai();
      chart.data.labels = cur.labels;
      chart.data.datasets[0].data = cur.data;
      chart.update();
    },
    capNhatBieuDoLoiPhatAm() {
      const chart = this.doi_tuong_bieu_do.loi_phat_am;
      if (!chart) return;
      chart.data.labels = this.du_lieu_loi_phat_am.labels || [];
      chart.data.datasets[0].data = this.du_lieu_loi_phat_am.data || [];
      chart.update();
    },
    layMauXuHuong(value) {
      if (value > 0) return 'text-success';
      if (value < 0) return 'text-danger';
      return 'text-secondary';
    },
    hienThiXuHuong(value, hide) {
      if (hide) return '—';
      if (value === 0) return 'Không đổi';
      return `${value > 0 ? '+' : ''}${value}% so với kỳ trước`;
    },
    diToiDanhSachHocVien() {
      this.$router.push('/teacher/quan-ly-hoc-sinh');
    },
  },
};
</script>

<style scoped>
.teacher-dashboard { max-width: 1320px; margin: 0 auto; padding-bottom: 2rem; }
.header-card, .panel-card, .kpi-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; box-shadow: 0 4px 18px rgba(15,23,42,.04); }
.header-card { padding: 1rem 1.25rem; }
.kpi-card { padding: 1rem; }
.panel-card { padding: 1rem 1.2rem; }
.chart-wrap { position: relative; height: 280px; }
.mini-stat { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: .75rem; }
</style>
