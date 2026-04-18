<template>
    <div class="container-fluid" style="background-color: #f8f9fa; min-height: 100vh;">
  
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 class="fw-bold mb-1" style="color: #2b3445;">Tổng Quan Giáo Viên</h4>
          <p class="text-muted mb-0" style="font-size: 0.9rem;">
            Theo dõi tình hình học tập và luyện phát âm của học viên
          </p>
        </div>
        
      </div>
  
      <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6" v-for="(stat, index) in the_tom_tat" :key="index">
          <div class="card border-0 shadow-sm rounded-3 h-100" style="background: #fff;">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
              <div>
                <small class="text-muted d-block fw-semibold mb-1">{{ stat.label }}</small>
                <h3 class="mb-0 fw-bold" :class="stat.colorClass">{{ stat.value }}</h3>
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
          <div class="card border-0 shadow-sm rounded-3 h-100" style="background: #fff;">
            <div class="card-body p-4 d-flex flex-column">
              <h6 class="fw-bold mb-4">Phân loại lỗi phát âm</h6>
              <div style="position: relative; flex-grow: 1; display: flex; justify-content: center;">
                <canvas id="errorTypeChart" style="max-height: 250px;"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-3 h-100" style="background: #fff;">
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
          <div class="card border-0 shadow-sm rounded-3 h-100" style="background: #fff;">
            <div class="card-body p-4">
              <h6 class="fw-bold mb-4">Tiến độ lớp học</h6>
  
              <div class="row g-3 mb-4">
                <div class="col-4 text-center">
                  <div class="p-3 rounded-3" style="background-color: #f8f9fa;">
                    <h4 class="fw-bold text-primary mb-1">{{ thong_ke_lop_hoc.bai_dang_giao }}</h4>
                    <small class="text-muted" style="font-size: 0.8rem;">Bài đang giao</small>
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
  
              <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded-3 border"
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
  
              <small class="text-muted mb-2 d-block">Tỉ lệ học viên chuyên cần ({{ thong_ke_lop_hoc.ti_le_chuyen_can }}%)</small>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-primary rounded-pill"
                  :style="{ width: thong_ke_lop_hoc.ti_le_chuyen_can + '%' }"></div>
              </div>
            </div>
          </div>
        </div>
  
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-3 h-100" style="background: #fff;">
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
                    <span class="fw-bold" style="font-size: 0.95rem; color: #333;">{{ hoat_dong.tieu_de }}</span>
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
  
        du_lieu_bieu_do: {
          day: { labels: [], data: [] },
          week: { labels: [], data: [] },
          month: { labels: [], data: [] },
          year: { labels: [], data: [] }
        },
  
        the_tom_tat: [
          { label: 'Học sinh tham gia', value: 0, colorClass: 'text-primary', bgColor: '#e7f0ff', icon: '👥' },
          { label: 'Bài học đang giao', value: 0, colorClass: 'text-success', bgColor: '#e6f8f0', icon: '📖' },
          { label: 'Lượt luyện tập (Tuần)', value: 0, colorClass: 'text-warning', bgColor: '#fff4e5', icon: '🎙️' },
          { label: 'Học sinh cần chú ý', value: 0, colorClass: 'text-danger', bgColor: '#ffe8e8', icon: '⚠️' }
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
        axios
          .get(this.apiBase + '/api/teacher/gv-hv/dashboard', { headers: this.authHeaders() })
          .then((res) => {
            if (!res.data.status || !res.data.data) {
              this.$toast.error(res.data.message || 'Không tải được dữ liệu tổng quan.');
              return;
            }
            const data = res.data.data;
            const tomTat = data.the_tom_tat || {};
            this.the_tom_tat = [
              { label: 'Học sinh tham gia', value: tomTat.hoc_sinh_tham_gia || 0, colorClass: 'text-primary', bgColor: '#e7f0ff', icon: '👥' },
              { label: 'Bài học đang giao', value: tomTat.bai_hoc_dang_giao || 0, colorClass: 'text-success', bgColor: '#e6f8f0', icon: '📖' },
              { label: 'Lượt luyện tập (Tuần)', value: tomTat.luot_luyen_tap_tuan || 0, colorClass: 'text-warning', bgColor: '#fff4e5', icon: '🎙️' },
              { label: 'Học sinh cần chú ý', value: tomTat.hoc_sinh_can_chu_y || 0, colorClass: 'text-danger', bgColor: '#ffe8e8', icon: '⚠️' }
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
  
      diToiDanhSachHocVien() {
        this.$router.push('/teacher/quan-ly-hoc-sinh');
      }
    }
  };
  </script>