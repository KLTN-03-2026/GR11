<template>
    <div class="container-fluid dashboard-wrap">
  
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 class="fw-bold mb-1 title-main">Tổng Quan Giáo Viên</h4>
          <p class="text-muted mb-0 subtitle-main">
            Theo dõi tình hình học tập và luyện phát âm của học viên
          </p>
        </div>
        
      </div>

      <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
          <div class="row g-3 align-items-end">
            <div class="col-xl-3 col-md-6">
              <label class="form-label fw-semibold mb-1 form-label-dashboard">Chu kỳ</label>
              <select class="form-select form-select-sm" v-model="bo_loc_tong_quan.chu_ky" @change="apDungBoLocTongQuan">
                <option value="week">Tuần này</option>
                <option value="month">Tháng này</option>
                <option value="quarter">Quý này</option>
              </select>
            </div>
            <div class="col-xl-3 col-md-6">
              <label class="form-label fw-semibold mb-1 form-label-dashboard">Lớp</label>
              <select class="form-select form-select-sm" v-model="bo_loc_tong_quan.lop_hoc" @change="apDungBoLocTongQuan">
                <option value="all">Tất cả lớp</option>
                <option v-for="lop in danh_sach_lop_hoc" :key="lop.id" :value="lop.id">{{ lop.ten_lop }}</option>
              </select>
            </div>
            <div class="col-xl-2 col-md-6">
              <label class="form-label fw-semibold mb-1 form-label-dashboard">Từ ngày</label>
              <input type="date" class="form-control form-control-sm" v-model="bo_loc_tong_quan.tu_ngay" />
            </div>
            <div class="col-xl-2 col-md-6">
              <label class="form-label fw-semibold mb-1 form-label-dashboard">Đến ngày</label>
              <input type="date" class="form-control form-control-sm" v-model="bo_loc_tong_quan.den_ngay" />
            </div>
            <div class="col-xl-2 col-md-12">
              <button class="btn btn-sm btn-primary w-100" @click="apDungBoLocTongQuan">Áp dụng bộ lọc</button>
            </div>
          </div>
        </div>
      </div>
  
      <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6" v-for="(stat, index) in the_tom_tat" :key="index">
          <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
              <div>
                <small class="text-muted d-block fw-semibold mb-1 label-strong">{{ stat.label }}</small>
                <h3 class="mb-0 fw-bold" :class="stat.colorClass">{{ stat.value }}</h3>
                <small class="d-inline-flex align-items-center mt-2 trend-text" :class="layMauXuHuong(stat.trend)">
                  <span class="me-1">{{ layIconXuHuong(stat.trend) }}</span>
                  {{ hienThiXuHuong(stat.trend) }}
                </small>
              </div>
              <div class="p-3 rounded-circle" :style="`background-color: ${stat.bgColor};`">
                <span style="font-size: 1.2rem;">{{ stat.icon }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <div class="row g-3 mb-4">
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-4 d-flex flex-column">
              <h6 class="fw-bold mb-4">Phân loại lỗi phát âm</h6>
              <div style="position: relative; flex-grow: 1; display: flex; justify-content: center;">
                <canvas id="errorTypeChart" style="max-height: 250px;"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0">Thống kê lượt luyện tập</h6>
                <select class="form-select form-select-sm w-auto shadow-sm" v-model="bo_loc_bieu_do"
                  @change="capNhatBieuDo" style="cursor: pointer;">
                  <option value="day">Theo Ngày (Tuần qua)</option>
                  <option value="week">Theo Tuần (Tháng qua)</option>
                  <option value="month">Theo Tháng (6 tháng qua)</option>
                  <option value="year">Theo Năm</option>
                </select>
              </div>
              <div style="position: relative; height: 300px;">
                <canvas id="activityChart"></canvas>
              </div>
            </div>
          </div>
        </div>
  
        
      </div>
  
      <div class="row g-3">
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0">Tiến độ lớp học</h6>
                <button class="btn btn-sm btn-outline-primary" @click="hien_chi_tiet_tien_do = !hien_chi_tiet_tien_do">
                  {{ hien_chi_tiet_tien_do ? 'Ẩn chi tiết' : 'Xem chi tiết' }}
                </button>
              </div>
  
              <div class="row g-3 mb-4">
                <div class="col-4 text-center">
                  <div class="p-3 rounded-3" style="background-color: #f8f9fa;">
                    <h4 class="fw-bold text-primary mb-1">{{ thong_ke_lop_hoc.bai_dang_giao }}</h4>
                    <small class="text-muted" style="font-size: 0.8rem;">Bài đã tạo</small>
                  </div>
                </div>
                <div class="col-4 text-center">
                  <div class="p-3 rounded-3" style="background-color: #f8f9fa;">
                    <h4 class="fw-bold text-success mb-1">{{ thong_ke_lop_hoc.luot_nop_bai }}</h4>
                    <small class="text-muted" style="font-size: 0.8rem;">Lượt nộp bài</small>
                  </div>
                </div>
                <div class="col-4 text-center">
                  <div class="p-3 rounded-3" style="background-color: #f8f9fa;">
                    <h4 class="fw-bold text-warning mb-1">{{ thong_ke_lop_hoc.diem_trung_binh }}</h4>
                    <small class="text-muted" style="font-size: 0.8rem;">Điểm TB</small>
                  </div>
                </div>
              </div>
  
              <div v-if="hien_chi_tiet_tien_do" class="d-flex justify-content-between align-items-center mb-3 p-3 rounded-3 border"
                style="background-color: #fffaf0; border-color: #ffe6b3 !important;">
                <div>
                  <small class="text-muted d-block mb-1">Âm lỗi phổ biến nhất</small>
                  <h5 class="fw-bold text-danger mb-0">{{ thong_ke_lop_hoc.loi_pho_thong }}</h5>
                </div>
                <div class="text-end">
                  <small class="text-muted d-block mb-1">Mức độ cải thiện</small>
                  <h5 class="fw-bold text-success mb-0">+{{ thong_ke_lop_hoc.ti_le_cai_thien }}%</h5>
                </div>
              </div>
  
              <small class="text-muted mb-2 d-block">Tỉ lệ học viên chuyên cần</small>
              <div class="d-flex justify-content-between mb-2">
                <small class="text-muted">{{ thong_ke_lop_hoc.ti_le_chuyen_can }}%</small>
                <small class="text-muted">{{ bo_loc_tong_quan.chu_ky === 'week' ? 'Tuần' : bo_loc_tong_quan.chu_ky === 'month' ? 'Tháng' : 'Quý' }}</small>
              </div>
              <div class="progress" style="height: 9px;">
                <div class="progress-bar bg-primary rounded-pill"
                  :style="{ width: thong_ke_lop_hoc.ti_le_chuyen_can + '%' }"></div>
              </div>
            </div>
          </div>
        </div>
  
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0">Top học sinh nổi bật</h6>
                <span class="badge text-bg-light px-3 py-2">Top {{ top_hoc_sinh_noi_bat.length }}</span>
              </div>

              <div
                class="d-flex justify-content-between align-items-center p-3 rounded-3 border mb-3"
                v-for="(hoc_sinh, index) in top_hoc_sinh_noi_bat"
                :key="hoc_sinh.id || index"
              >
                <div class="d-flex align-items-center">
                  <div class="me-3 rank-badge">{{ index + 1 }}</div>
                  <div>
                    <p class="mb-1 fw-semibold text-dark">{{ hoc_sinh.ho_ten }}</p>
                    <small class="text-muted">Đã hoàn thành {{ hoc_sinh.so_bai_da_hoc }} bài học</small>
                  </div>
                </div>
                <div class="text-end">
                  <p class="mb-0 fw-bold text-success">{{ hoc_sinh.diem_trung_binh }}</p>
                  <small class="text-muted">điểm trung bình</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mt-1">
        <div class="col-12">
          <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0">Hoạt động của học viên</h6>
                <a href="#" class="text-decoration-none text-primary" style="font-size: 0.85rem;"
                  @click.prevent="diToiDanhSachHocVien">Xem tất cả</a>
              </div>
  
              <div class="d-flex align-items-start mb-3" v-for="hoat_dong in danh_sach_hoat_dong" :key="hoat_dong.id">
                <div class="p-2 rounded-circle me-3" style="background-color: #e9ecef;">
                  <span style="font-size: 1rem;">{{ hoat_dong.icon }}</span>
                </div>
                <div class="flex-grow-1 border-bottom pb-3">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ hoat_dong.tieu_de }}</span>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ hoat_dong.thoi_gian }}</small>
                  </div>
                  <p class="text-muted mb-0" style="font-size: 0.85rem;">{{ hoat_dong.mo_ta }}</p>
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
  import Chart from 'chart.js/auto';
  
  export default {
    data() {
      return {
        apiBase: (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, ''),
        bo_loc_bieu_do: 'week',
        loading: false,
      hien_chi_tiet_tien_do: false,
      bo_loc_tong_quan: {
        chu_ky: 'week',
        lop_hoc: 'all',
        tu_ngay: '',
        den_ngay: '',
      },
      danh_sach_lop_hoc: [],
  
        du_lieu_bieu_do: {
          day: { labels: [], data: [] },
          week: { labels: [], data: [] },
          month: { labels: [], data: [] },
          year: { labels: [], data: [] }
        },
  
        the_tom_tat: [
          { label: 'Học sinh tham gia', value: 0, colorClass: 'text-primary', bgColor: '#e7f0ff', icon: '👥', trend: 0 },
          { label: 'Bài học đã tạo', value: 0, colorClass: 'text-success', bgColor: '#e6f8f0', icon: '📚', trend: 0 },
          { label: 'Lượt luyện tập (Tuần)', value: 0, colorClass: 'text-warning', bgColor: '#fff4e5', icon: '🎙️', trend: 0 },
          { label: 'Học sinh cần chú ý', value: 0, colorClass: 'text-danger', bgColor: '#ffe8e8', icon: '⚠️', trend: 0 }
        ],
  
        thong_ke_lop_hoc: {
          bai_dang_giao: 0,
          luot_nop_bai: 0,
          diem_trung_binh: 0,
          loi_pho_thong: 'Chưa ghi nhận',
          ti_le_cai_thien: 0,
          ti_le_chuyen_can: 0
        },
  
        du_lieu_loi_phat_am: { labels: ['Sai thanh điệu', 'Sai âm đầu', 'Sai vần'], data: [0, 0, 0] },
        danh_sach_hoat_dong: [],
      top_hoc_sinh_noi_bat: [],
  
        doi_tuong_bieu_do: {}
      };
    },
    mounted() {
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
      taiTongQuan() {
        this.loading = true;
        const params = {
          chu_ky: this.bo_loc_tong_quan.chu_ky,
          lop_hoc: this.bo_loc_tong_quan.lop_hoc,
          tu_ngay: this.bo_loc_tong_quan.tu_ngay || undefined,
          den_ngay: this.bo_loc_tong_quan.den_ngay || undefined,
        };
        axios
          .get(this.apiBase + '/api/teacher/gv-hv/dashboard', { headers: this.authHeaders(), params })
          .then((res) => {
            if (!res.data.status || !res.data.data) {
              this.$toast.error(res.data.message || 'Không tải được dữ liệu tổng quan.');
              return;
            }
            const data = res.data.data;
            const tomTat = data.the_tom_tat || {};
            const xuHuong = data.xu_huong_tom_tat || {};
            this.the_tom_tat = [
              {
                label: 'Học sinh tham gia',
                value: tomTat.hoc_sinh_tham_gia || 0,
                colorClass: 'text-primary',
                bgColor: '#e7f0ff',
                icon: '👥',
                trend: xuHuong.hoc_sinh_tham_gia || 0
              },
              {
                label: 'Bài học đã tạo',
                value: tomTat.bai_hoc_da_tao || tomTat.bai_hoc_dang_giao || 0,
                colorClass: 'text-success',
                bgColor: '#e6f8f0',
                icon: '📚',
                trend: xuHuong.bai_hoc_da_tao || xuHuong.bai_hoc_dang_giao || 0
              },
              {
                label: 'Lượt luyện tập (Tuần)',
                value: tomTat.luot_luyen_tap_tuan || 0,
                colorClass: 'text-warning',
                bgColor: '#fff4e5',
                icon: '🎙️',
                trend: xuHuong.luot_luyen_tap_tuan || 0
              },
              {
                label: 'Học sinh cần chú ý',
                value: tomTat.hoc_sinh_can_chu_y || 0,
                colorClass: 'text-danger',
                bgColor: '#ffe8e8',
                icon: '⚠️',
                trend: xuHuong.hoc_sinh_can_chu_y || 0
              }
            ];
            this.thong_ke_lop_hoc = {
              ...this.thong_ke_lop_hoc,
              ...(data.thong_ke_lop_hoc || {}),
            };
            this.du_lieu_bieu_do = {
              ...this.du_lieu_bieu_do,
              ...(data.du_lieu_bieu_do || {}),
            };
            this.du_lieu_loi_phat_am = data.du_lieu_loi_phat_am || this.du_lieu_loi_phat_am;
            this.danh_sach_hoat_dong = data.danh_sach_hoat_dong || [];
            this.danh_sach_lop_hoc = data.danh_sach_lop_hoc || [];
            this.top_hoc_sinh_noi_bat = data.top_hoc_sinh_noi_bat || this.taoTopHocSinhMacDinh();
            if (this.doi_tuong_bieu_do.hoat_dong || this.doi_tuong_bieu_do.loi_phat_am) {
              this.capNhatBieuDo();
              this.capNhatBieuDoLoiPhatAm();
            } else {
              this.khoiTaoBieuDo();
            }
          })
          .catch((err) => {
            if (err.response && err.response.status === 401) {
              this.$toast.error('Vui lòng đăng nhập lại.');
              this.$router.push('/dang-nhap');
              return;
            }
            if (err.response && err.response.status === 403) {
              this.$toast.error('Bạn không có quyền truy cập.');
              return;
            }
            this.$toast.error('Không thể kết nối máy chủ.');
          })
          .finally(() => {
            this.loading = false;
          });
      },
      khoiTaoBieuDo() {
        const ctxActivity = document.getElementById('activityChart').getContext('2d');
        const duLieuBanDau = this.du_lieu_bieu_do[this.bo_loc_bieu_do];
  
        this.doi_tuong_bieu_do.hoat_dong = new Chart(ctxActivity, {
          type: 'line',
          data: {
            labels: duLieuBanDau.labels,
            datasets: [{
              label: 'Lượt nộp bài/luyện tập',
              data: duLieuBanDau.data,
              borderColor: '#0d6efd',
              backgroundColor: 'rgba(13, 110, 253, 0.1)',
              borderWidth: 2,
              tension: 0.4,
              fill: true
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
              y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
              x: { grid: { display: false } }
            }
          }
        });
  
        const ctxErrorType = document.getElementById('errorTypeChart').getContext('2d');
        this.doi_tuong_bieu_do.loi_phat_am = new Chart(ctxErrorType, {
          type: 'doughnut',
          data: {
            labels: this.du_lieu_loi_phat_am.labels || ['Sai thanh điệu', 'Sai âm đầu', 'Sai vần'],
            datasets: [{
              data: this.du_lieu_loi_phat_am.data || [0, 0, 0],
              backgroundColor: ['#dc3545', '#ffc107', '#17a2b8'],
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
              legend: { position: 'bottom' }
            }
          }
        });
      },
  
      capNhatBieuDo() {
        const bieuDo = this.doi_tuong_bieu_do.hoat_dong;
        if (!bieuDo) return;
        const duLieuMoi = this.du_lieu_bieu_do[this.bo_loc_bieu_do];
  
        bieuDo.data.labels = duLieuMoi.labels;
        bieuDo.data.datasets[0].data = duLieuMoi.data;
        bieuDo.update();
      },
      capNhatBieuDoLoiPhatAm() {
        const bieuDo = this.doi_tuong_bieu_do.loi_phat_am;
        if (!bieuDo) return;
        bieuDo.data.labels = this.du_lieu_loi_phat_am.labels || [];
        bieuDo.data.datasets[0].data = this.du_lieu_loi_phat_am.data || [];
        bieuDo.update();
      },
      apDungBoLocTongQuan() {
        this.taiTongQuan();
      },
      taoTopHocSinhMacDinh() {
        return [
          { id: 1, ho_ten: 'Học sinh A', diem_trung_binh: '0.0/10', so_bai_da_hoc: 0 },
          { id: 2, ho_ten: 'Học sinh B', diem_trung_binh: '0.0/10', so_bai_da_hoc: 0 },
          { id: 3, ho_ten: 'Học sinh C', diem_trung_binh: '0.0/10', so_bai_da_hoc: 0 },
        ];
      },
      layIconXuHuong(giaTri) {
        if (giaTri > 0) return '↗';
        if (giaTri < 0) return '↘';
        return '→';
      },
      layMauXuHuong(giaTri) {
        if (giaTri > 0) return 'text-success';
        if (giaTri < 0) return 'text-danger';
        return 'text-secondary';
      },
      hienThiXuHuong(giaTri) {
        if (giaTri === 0) return 'Không đổi so với kỳ trước';
        const dau = giaTri > 0 ? '+' : '';
        return `${dau}${giaTri}% so với kỳ trước`;
      },
  
      diToiDanhSachHocVien() {
        this.$router.push('/teacher/quan-ly-hoc-sinh');
      }
    }
  };
  </script>

<style scoped>
.dashboard-wrap {
  background-color: #f8f9fa;
  min-height: 100vh;
}

.title-main {
  color: #2b3445;
}

.subtitle-main {
  font-size: 0.92rem;
  color: #697586 !important;
}

.form-label-dashboard {
  color: #344054;
  font-size: 0.82rem;
}

.label-strong {
  color: #475467 !important;
}

.trend-text {
  font-size: 0.78rem;
  font-weight: 600;
}

.rank-badge {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #eef2ff;
  color: #364fc7;
  font-weight: 700;
  font-size: 0.85rem;
}
</style>