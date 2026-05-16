<template>
  <div class="container-fluid py-4 report-page">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
      <div>
        <h4 class="fw-bold mb-1 text-dark">
          <i class="fa-solid fa-chart-simple text-primary me-2"></i>
          Báo Cáo Học Viên Chuyên Sâu
        </h4>
        <p class="text-muted mb-0 small">
          Tập trung vào chất lượng từng học viên: lỗi phát âm, mức độ cần can thiệp và nhịp độ luyện tập.
        </p>
      </div>
      <div class="d-flex gap-2 align-items-center report-toolbar">
        <input
          v-model.trim="tuKhoa"
          type="text"
          class="form-control report-search-input"
          placeholder="Tìm học viên..."
        />
        <button class="btn btn-outline-primary rounded-pill report-refresh-btn" :disabled="loading" @click="taiDuLieu">
          <i class="fa-solid fa-rotate-right me-1" :class="{ 'fa-spin': loading }"></i>
          Làm mới
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-5 text-muted">
      <span class="spinner-border spinner-border-sm me-2"></span>Đang tải dữ liệu báo cáo...
    </div>

    <template v-else>
      <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3" v-for="item in tongQuanCards" :key="item.key">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="small text-muted">{{ item.label }}</div>
                  <div class="h4 fw-bold mb-0" :class="item.valueClass">{{ item.value }}</div>
                </div>
                <div class="metric-icon rounded-circle d-flex align-items-center justify-content-center" :class="item.bgClass">
                  <i :class="item.icon"></i>
                </div>
              </div>
              <div class="small text-muted mt-2">{{ item.note }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
              <h6 class="fw-bold mb-3">Top học viên mắc lỗi phát âm nhiều nhất</h6>
              <div class="chart-wrap">
                <canvas ref="barLoiHocVien"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
              <h6 class="fw-bold mb-3">Cơ cấu loại lỗi toàn lớp</h6>
              <div class="chart-wrap chart-wrap-sm">
                <canvas ref="doughnutLoaiLoi"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-12">
          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
              <h6 class="fw-bold mb-3">Danh sách học viên cần ưu tiên hỗ trợ</h6>
              <div v-if="hocVienCanCanThiep.length === 0" class="text-muted small">
                Không có học viên vượt ngưỡng cảnh báo.
              </div>
              <div v-else class="table-responsive">
                <table class="table align-middle mb-0">
                  <thead>
                    <tr>
                      <th>Học viên</th>
                      <th>Điểm TB</th>
                      <th>Tổng lỗi</th>
                      <th>Phiên luyện</th>
                      <th>Đánh giá</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="s in hocVienCanCanThiep" :key="s.id">
                      <td>
                        <div class="fw-semibold">{{ s.name }}</div>
                        <small class="text-muted">{{ s.email }}</small>
                      </td>
                      <td>{{ s.score }}/100</td>
                      <td>{{ s.tong_loi }}</td>
                      <td>{{ s.sessions || 0 }}</td>
                      <td>
                        <span class="badge rounded-pill px-3 py-2" :class="badgeCanhBaoClass(s)">
                          {{ nhanCanhBao(s) }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Bảng theo dõi học viên (lọc theo từ khóa)</h6>
          <div v-if="hocVienLoc.length === 0" class="text-muted small">Không có dữ liệu phù hợp.</div>
          <div v-else class="table-responsive">
            <table class="table align-middle mb-0">
              <thead>
                <tr>
                  <th>Học viên</th>
                  <th>Điểm TB</th>
                  <th>Số bài</th>
                  <th>Phiên luyện</th>
                  <th>Lỗi âm đầu</th>
                  <th>Lỗi vần</th>
                  <th>Lỗi thanh điệu</th>
                  <th>Tổng lỗi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="s in hocVienLoc" :key="s.id">
                  <td>
                    <div class="fw-semibold">{{ s.name }}</div>
                    <small class="text-muted">{{ s.email }}</small>
                  </td>
                  <td>{{ s.score }}/100</td>
                  <td>{{ s.so_bai || 0 }}</td>
                  <td>{{ s.sessions || 0 }}</td>
                  <td>{{ s.loi_am_dau }}</td>
                  <td>{{ s.loi_van }}</td>
                  <td>{{ s.loi_thanh_dieu }}</td>
                  <td class="fw-bold">{{ s.tong_loi }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import { API_BASE } from '../../../../api/http.js';
import axios from 'axios';
import Chart from 'chart.js/auto';

export default {
  name: 'BaoCaoThongKe',
  data() {
    return {
      apiBase: (API_BASE).replace(/\/$/, ''),
      loading: false,
      tuKhoa: '',
      hocViens: [],
      chartInstances: {
        bar: null,
        doughnut: null,
      },
    };
  },
  computed: {
    hocVienLoc() {
      const q = this.tuKhoa.toLowerCase();
      if (!q) return this.hocViens;
      return this.hocViens.filter((s) => (s.name || '').toLowerCase().includes(q) || (s.email || '').toLowerCase().includes(q));
    },
    tongQuanCards() {
      const tong = this.hocViens.length;
      const diemThap = this.hocViens.filter((s) => Number(s.score || 0) < 60).length;
      const nhieuLoi = this.hocViens.filter((s) => Number(s.tong_loi || 0) >= 10).length;
      const tongPhien = this.hocViens.reduce((sum, s) => sum + Number(s.sessions || 0), 0);
      return [
        { key: 'tong_hv', label: 'Tổng học viên theo dõi', value: tong, note: 'Nguồn: quan_he_gv_hvs + nguoi_dungs', icon: 'fa-solid fa-users', valueClass: 'text-primary', bgClass: 'bg-primary-subtle text-primary' },
        { key: 'diem_thap', label: 'Học viên điểm dưới 60', value: diemThap, note: 'Dựa trên điểm trung bình luyện tập', icon: 'fa-solid fa-triangle-exclamation', valueClass: 'text-danger', bgClass: 'bg-danger-subtle text-danger' },
        { key: 'nhieu_loi', label: 'Học viên có lỗi cao', value: nhieuLoi, note: 'Ngưỡng tổng lỗi >= 10', icon: 'fa-solid fa-volume-xmark', valueClass: 'text-warning', bgClass: 'bg-warning-subtle text-warning' },
        { key: 'tong_phien', label: 'Tổng phiên luyện tập', value: tongPhien, note: 'Tổng sessions toàn lớp', icon: 'fa-solid fa-wave-square', valueClass: 'text-success', bgClass: 'bg-success-subtle text-success' },
      ];
    },
    tongLoaiLoi() {
      return this.hocViens.reduce(
        (acc, s) => {
          acc.am_dau += Number(s.loi_am_dau || 0);
          acc.van += Number(s.loi_van || 0);
          acc.thanh_dieu += Number(s.loi_thanh_dieu || 0);
          return acc;
        },
        { am_dau: 0, van: 0, thanh_dieu: 0 }
      );
    },
    hocVienCanCanThiep() {
      return this.hocViens
        .filter((s) => Number(s.score || 0) < 60 || Number(s.tong_loi || 0) >= 10)
        .sort((a, b) => Number(b.tong_loi || 0) - Number(a.tong_loi || 0));
    },
  },
  mounted() {
    this.taiDuLieu();
  },
  beforeUnmount() {
    this.huyBieuDo();
  },
  methods: {
    authHeaders() {
      return { Authorization: 'Bearer ' + (localStorage.getItem('token_teacher') || '') };
    },
    nhanCanhBao(s) {
      const score = Number(s.score || 0);
      const loi = Number(s.tong_loi || 0);
      if (score < 60 && loi >= 10) return 'Điểm thấp & lỗi cao';
      if (score < 60) return 'Điểm thấp';
      return 'Lỗi phát âm cao';
    },
    badgeCanhBaoClass(s) {
      const score = Number(s.score || 0);
      const loi = Number(s.tong_loi || 0);
      if (score < 60 && loi >= 10) return 'bg-danger-subtle text-danger';
      if (score < 60) return 'bg-warning-subtle text-warning';
      return 'bg-info-subtle text-info';
    },
    taoMapLoi(loiPhatAmLichSu = []) {
      const m = { am_dau: 0, van: 0, thanh_dieu: 0 };
      (loiPhatAmLichSu || []).forEach((r) => {
        const k = String(r.loai_loi || '');
        if (Object.prototype.hasOwnProperty.call(m, k)) {
          m[k] += Number(r.so_lan_mac_loi || 0);
        }
      });
      return m;
    },
    huyBieuDo() {
      if (this.chartInstances.bar) {
        this.chartInstances.bar.destroy();
        this.chartInstances.bar = null;
      }
      if (this.chartInstances.doughnut) {
        this.chartInstances.doughnut.destroy();
        this.chartInstances.doughnut = null;
      }
    },
    renderCharts() {
      this.$nextTick(() => {
        const barCanvas = this.$refs.barLoiHocVien;
        const doughnutCanvas = this.$refs.doughnutLoaiLoi;
        if (!barCanvas || !doughnutCanvas) return;
        this.huyBieuDo();

        const top = [...this.hocViens]
          .sort((a, b) => Number(b.tong_loi || 0) - Number(a.tong_loi || 0))
          .slice(0, 8);

        this.chartInstances.bar = new Chart(barCanvas.getContext('2d'), {
          type: 'bar',
          data: {
            labels: top.map((s) => s.name),
            datasets: [
              {
                label: 'Tổng lỗi',
                data: top.map((s) => Number(s.tong_loi || 0)),
                backgroundColor: '#f97316',
                borderRadius: 8,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: {
              y: { beginAtZero: true, ticks: { precision: 0 } },
            },
          },
        });

        const tongLoi = this.tongLoaiLoi;
        this.chartInstances.doughnut = new Chart(doughnutCanvas.getContext('2d'), {
          type: 'doughnut',
          data: {
            labels: ['Lỗi âm đầu', 'Lỗi vần', 'Lỗi thanh điệu'],
            datasets: [
              {
                data: [tongLoi.am_dau, tongLoi.van, tongLoi.thanh_dieu],
                backgroundColor: ['#ef4444', '#f59e0b', '#06b6d4'],
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: { legend: { position: 'bottom' } },
          },
        });
      });
    },
    taiDuLieu() {
      this.loading = true;
      Promise.all([
        axios.get(this.apiBase + '/api/teacher/gv-hv/hoc-vien', { headers: this.authHeaders() }),
        axios.get(this.apiBase + '/api/teacher/gv-hv/loi-phat-am-lich-su', { headers: this.authHeaders() }),
      ])
        .then(([resHv, resLoi]) => {
          const dsHv = resHv.data?.status ? (resHv.data.data || []) : [];
          const mapLoi = resLoi.data?.status ? (resLoi.data.data || {}) : {};
          this.hocViens = dsHv.map((s) => {
            const lichSu = mapLoi[s.id] || [];
            const chiSoLoi = this.taoMapLoi(lichSu);
            return {
              ...s,
              loi_am_dau: chiSoLoi.am_dau,
              loi_van: chiSoLoi.van,
              loi_thanh_dieu: chiSoLoi.thanh_dieu,
              tong_loi: chiSoLoi.am_dau + chiSoLoi.van + chiSoLoi.thanh_dieu,
            };
          });
          this.renderCharts();
        })
        .catch(() => {
          this.hocViens = [];
          this.renderCharts();
        })
        .finally(() => {
          this.loading = false;
        });
    },
  },
};
</script>

<style scoped>
.report-page {
  background: #f8fafc;
  min-height: 100vh;
}
.metric-icon {
  width: 44px;
  height: 44px;
}
.chart-wrap {
  position: relative;
  height: 320px;
}
.chart-wrap-sm {
  height: 280px;
}
.report-toolbar {
  width: 100%;
  max-width: 440px;
}
.report-search-input {
  height: 42px;
  border-radius: 999px;
  padding-left: 14px;
  padding-right: 14px;
}
.report-refresh-btn {
  height: 42px;
  min-width: 108px;
  padding: 0 18px;
  white-space: nowrap;
}
</style>
