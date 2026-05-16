<template>
<div class="page-full bg-light min-vh-100">
    <div class="content-wrapper py-5">
      <!-- Summary -->
      <div class="streak-header rounded-5 shadow-sm overflow-hidden mb-4">
        <div class="row align-items-center g-4">
          <div class="col-lg-5">
            <div class="d-flex align-items-center gap-4">
              <div class="streak-fire-wrap">
                <div class="streak-fire">
                  🔥
                </div>
              </div>

              <div>
                <span class="badge rounded-pill px-3 py-2 mb-3 streak-badge">
                  Chuỗi học hiện tại
                </span>

                <h2 class="fw-bold mb-2" style="color: #0d3b66;">
                  {{ thong_tin.streak_hien_tai || 0 }} Ngày Liên Tiếp
                </h2>

                <p class="text-muted mb-0">
                  Ngày học cuối: {{ thong_tin.ngay_hoc_cuoi_cung || "—" }}
                </p>
              </div>
            </div>
          </div>

          <div class="col-lg-7">
            <div class="row g-3">
              <div class="col-md-4">
                <div class="mini-card orange-card">
                  <div class="mini-icon orange-bg">⭐</div>
                  <div>
                    <h5 class="fw-bold mb-1">{{ thong_tin.diem_tich_luy || 0 }} XP</h5>
                    <small>Điểm thưởng</small>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="mini-card blue-card">
                  <div class="mini-icon blue-bg">🏆</div>
                  <div>
                    <h5 class="fw-bold mb-1">{{ currentRankDisplay }}</h5>
                    <small>Hạng hiện tại</small>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="mini-card pink-card">
                  <div class="mini-icon pink-bg">🎁</div>
                  <div>
                    <h5 class="fw-bold mb-1">{{ phien_id ? "Sẵn sàng" : "—" }}</h5>
                    <small>Hoàn thành phiên</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Điểm danh — luôn ở đầu trang -->
      <div class="bg-white rounded-5 shadow-sm p-4 mb-4 attendance-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div>
            <h5 class="fw-bold mb-1" style="color: #0d3b66;">Điểm danh hôm nay</h5>
            <p class="text-muted mb-0 small">
              <span v-if="daDiemDanhHomNay">Đã ghi nhận — duy trì chuỗi bằng hoạt động học mỗi ngày.</span>
              <span v-else>Điểm danh để +15 XP và cập nhật chuỗi học.</span>
            </p>
          </div>
          <div class="d-flex flex-wrap gap-2">
            <button
              type="button"
              class="btn rounded-pill px-4 fw-semibold"
              :class="daDiemDanhHomNay ? 'btn-success' : 'btn-primary'"
              :disabled="actionLoading || daDiemDanhHomNay"
              @click="diemDanh"
            >
              <span v-if="actionLoading" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else-if="daDiemDanhHomNay" class="fa-solid fa-check me-2"></i>
              <i v-else class="fa-solid fa-calendar-check me-2"></i>
              {{ daDiemDanhHomNay ? 'Đã điểm danh' : 'Điểm danh hôm nay' }}
            </button>
            <button
              v-if="phien_id"
              type="button"
              class="btn btn-outline-success rounded-pill px-4 fw-semibold"
              :disabled="actionLoading"
              @click="markCompleted"
            >
              Hoàn thành phiên
            </button>
          </div>
        </div>
      </div>

      <!-- Week Streak -->
      <div class="bg-white rounded-5 shadow-sm p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
          <div>
            <h4 class="fw-bold mb-1" style="color: #0d3b66;">
              Chuỗi Học Tuần Này
            </h4>
            <p class="text-muted mb-0">
              Bé đã học đều đặn mỗi ngày
            </p>
          </div>

          <button class="btn btn-outline-primary rounded-pill px-4">
            Hôm Nay
          </button>
        </div>

        <div class="row g-3">
          <div
            class="col"
            v-for="day in streakDays"
            :key="day.id"
          >
            <div
              class="day-card text-center"
              :class="{
                active: day.active,
                today: day.today
              }"
            >
              <small class="fw-bold d-block mb-2" style="font-size: 18px;">
                {{ day.label }}
              </small>

              <div class="day-icon" >
                {{ day.icon }}
              </div>

              <small class="d-block mt-2"  style="font-size: 18px;">
                {{ day.date }}
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- Badges -->
      <div class="bg-white rounded-5 shadow-sm p-4">
        <div class="mb-4">
          <h4 class="fw-bold mb-1" style="color: #0d3b66;">
            Bảng Xếp Hạng
          </h4>
          <p class="text-muted mb-0">
            Top theo điểm tích lũy
          </p>
        </div>

        <div v-if="loadingLeaderboard" class="text-center py-3 text-muted">
          Đang tải…
        </div>
        <div v-else class="row g-3">
          <div
            class="col-lg-3 col-md-6"
            v-for="(u, idx) in leaderboard"
            :key="u.nguoi_dung_id"
          >
            <div class="badge-card text-center">
              <div class="badge-icon" >
                {{ idx + 1 }}
              </div>

              <h6 class="fw-bold mb-2" style="color: #0d3b66;font-size: 18px;">
                {{ u.ho_ten }}
              </h6>

              <small class="text-muted" style="font-size: 18px;">
                {{ u.diem_tich_luy }} điểm
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "StreakPage",

  data() {
    return {
      apiBase: (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, ''),
      phien_id : localStorage.getItem("last_phien_id"),
      thong_tin : {},
      leaderboard : [],
      currentUserRank: null,
      loading : true,
      loadingLeaderboard : true,
      actionLoading : false,
      streakDays: []
    };
  },
  computed: {
    currentRankDisplay() {
      return this.currentUserRank ? `#${this.currentUserRank}` : "—";
    },
    daDiemDanhHomNay() {
      if (this.thong_tin?.da_diem_danh_hom_nay === true) return true;
      const raw = this.thong_tin?.ngay_hoc_cuoi_cung;
      if (!raw) return false;
      const today = new Date().toISOString().slice(0, 10);
      return String(raw).slice(0, 10) === today;
    },
  },
  mounted() {
    this.buildStreakDays();
    this.loadThongTin();
    this.loadLeaderboard();
  },
  methods: {
    authHeaders() {
      const token = localStorage.getItem('token_nguoi_dung');
      return token ? { Authorization: 'Bearer ' + token } : {};
    },
    apDungDuLieuStreak(data) {
      if (!data) return;
      this.thong_tin.streak_hien_tai = data.streak_hien_tai;
      this.thong_tin.diem_tich_luy = data.diem_tich_luy;
      this.thong_tin.ngay_hoc_cuoi_cung = data.ngay_hoc_cuoi_cung;
      this.thong_tin.da_diem_danh_hom_nay = data.da_diem_danh_hom_nay;
      this.buildStreakDays();
    },
    buildStreakDays() {
      const labels = ["CN", "T2", "T3", "T4", "T5", "T6", "T7"];
      const now = new Date();
      const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      const dayOfWeek = today.getDay();
      const daysFromMonday = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
      const monday = new Date(today);
      monday.setDate(today.getDate() - daysFromMonday);

      const practicedDates = new Set();
      const streak = Number(this.thong_tin?.streak_hien_tai || 0);
      const lastDateRaw = this.thong_tin?.ngay_hoc_cuoi_cung;
      const lastDate = lastDateRaw ? new Date(lastDateRaw + "T00:00:00") : null;

      if (streak > 0 && lastDate && !isNaN(lastDate.getTime())) {
        for (let i = 0; i < streak; i++) {
          const d = new Date(lastDate);
          d.setDate(lastDate.getDate() - i);
          practicedDates.add(d.toISOString().slice(0, 10));
        }
      }

      this.streakDays = Array.from({ length: 7 }, (_, i) => {
        const d = new Date(monday);
        d.setDate(monday.getDate() + i);
        const iso = d.toISOString().slice(0, 10);
        const active = practicedDates.has(iso);
        return {
          id: i + 1,
          label: labels[d.getDay()],
          date: d.toLocaleDateString("vi-VN", { day: "2-digit", month: "2-digit" }),
          icon: active ? "🔥" : "",
          active,
          today: d.getTime() === today.getTime(),
        };
      });
    },
    loadThongTin(){
      const token = localStorage.getItem("token_nguoi_dung");
      if (!token) {
        this.thong_tin = {};
        this.buildStreakDays();
        this.updateCurrentUserRank();
        this.loading = false;
        return;
      }
      this.loading = true;
      axios.get(this.apiBase + '/api/thong-tin-hoc-vien/me', {
        headers: this.authHeaders(),
      })
      .then((res) => {
        if(res.data.status){
          this.thong_tin = res.data.data || {};
        } else {
          this.thong_tin = {};
        }
        this.buildStreakDays();
        this.updateCurrentUserRank();
      })
      .catch(() => {
        this.thong_tin = {};
        this.buildStreakDays();
        this.updateCurrentUserRank();
      })
      .finally(() => {
        this.loading = false;
      });
    },
    loadLeaderboard(){
      this.loadingLeaderboard = true;
      axios.get(this.apiBase + '/api/leaderboard?type=points&limit=1000')
      .then((res) => {
        if(res.data.status){
          this.leaderboard = res.data.data || [];
        } else {
          this.leaderboard = [];
        }
        this.updateCurrentUserRank();
      })
      .catch(() => {
        this.leaderboard = [];
        this.updateCurrentUserRank();
      })
      .finally(() => {
        this.loadingLeaderboard = false;
      });
    },
    updateCurrentUserRank() {
      const userId = Number(this.thong_tin?.nguoi_dung_id || 0);
      if (!userId || !Array.isArray(this.leaderboard) || this.leaderboard.length === 0) {
        this.currentUserRank = null;
        return;
      }
      const idx = this.leaderboard.findIndex((u) => Number(u.nguoi_dung_id) === userId);
      this.currentUserRank = idx >= 0 ? idx + 1 : null;
    },
    diemDanh() {
      const token = localStorage.getItem('token_nguoi_dung');
      if (!token) {
        this.$toast.error('Bạn chưa đăng nhập.');
        return;
      }
      this.actionLoading = true;
      axios
        .post(this.apiBase + '/api/thong-tin-hoc-vien/diem-danh', {}, { headers: this.authHeaders() })
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message || 'Điểm danh thành công.');
            this.apDungDuLieuStreak(res.data.data);
            this.loadLeaderboard();
          } else {
            this.$toast.error(res.data.message || 'Không thể điểm danh.');
          }
        })
        .catch(() => {
          this.$toast.error('Lỗi kết nối đến server.');
        })
        .finally(() => {
          this.actionLoading = false;
        });
    },
    markCompleted(){
      if(!this.phien_id){
        this.$toast.error('Không có phien_id để đánh dấu hoàn thành.');
        return;
      }
      const token = localStorage.getItem("token_nguoi_dung");
      if (!token) {
        this.$toast.error('Bạn chưa đăng nhập.');
        return;
      }
      this.actionLoading = true;
      const payload = {
        phien_id: this.phien_id,
        tong_diem: 0
      };
      axios.post(this.apiBase + '/api/phien-luyen-taps/hoan-thanh', payload, {
        headers: this.authHeaders(),
      })
      .then((res) => {
        if(res.data.status){
          this.$toast.success(res.data.message || 'Hoàn thành phiên thành công.');
          this.apDungDuLieuStreak(res.data.data);
          localStorage.removeItem("last_phien_id");
          this.phien_id = null;
          this.loadLeaderboard();
        } else {
          this.$toast.error(res.data.message || 'Không thể cập nhật.');
        }
      })
      .catch((e) => {
        if (e?.response?.status === 401) {
          this.$toast.error('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
          return;
        }
        this.$toast.error('Lỗi kết nối đến server.');
      })
      .finally(() => {
        this.actionLoading = false;
      });
    }
  }
};
</script>

<style scoped>
.streak-header {
  background: linear-gradient(135deg, #fff7f2, #ffffff);
  padding: 30px;
  position: relative;
}

.streak-header::before {
  content: "";
  position: absolute;
  top: -60px;
  right: -60px;
  width: 180px;
  height: 180px;
  border-radius: 50%;
  background: rgba(255, 183, 71, 0.12);
}

.streak-badge {
  background: #ffe6d8;
  color: #ff6b35;
  font-weight: 600;
}

.streak-fire-wrap {
  width: 120px;
  height: 120px;
  border-radius: 35px;
  background: rgba(255, 107, 53, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
}

.streak-fire {
  width: 90px;
  height: 90px;
  border-radius: 30px;
  background: linear-gradient(135deg, #ff6b35, #ffb347);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 42px;
  color: white;
  box-shadow: 0 12px 25px rgba(255, 107, 53, 0.2);
}

.mini-card {
  border-radius: 24px;
  padding: 18px;
  display: flex;
  align-items: center;
  gap: 14px;
  height: 100%;
  transition: all 0.3s ease;
  border: 1px solid transparent;
}

.mini-card:hover {
  transform: translateY(-5px);
}

.orange-card {
  background: #fff7f2;
  border-color: #ffe1d0;
}

.blue-card {
  background: #f4f8ff;
  border-color: #dfeaff;
}

.pink-card {
  background: #fff5f8;
  border-color: #ffe1ec;
}

.mini-icon {
  width: 58px;
  height: 58px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
}

.orange-bg {
  background: #ffe9dd;
}

.blue-bg {
  background: #e6f0ff;
}

.pink-bg {
  background: #ffe7f0;
}

.day-card {
  background: #f8f9fb;
  border-radius: 24px;
  padding: 18px 8px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.day-card:hover {
  transform: translateY(-4px);
}

.day-card.active {
  background: linear-gradient(135deg,#fceabb, #f8b9d4);
  color: rgb(37, 0, 124);
  box-shadow: 0 10px 20px rgba(255, 107, 53, 0.18);
}

.day-card.today {
  border-color: #0d3b66;
}

.day-icon {
  font-size: 24px;
}

.badge-card {
  background: #fffaf7;
  border-radius: 28px;
  padding: 24px 18px;
  transition: all 0.3s ease;
  height: 100%;
  border: 1px solid #fff0e8;
}

.badge-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
}

.badge-icon {
  width: 78px;
  height: 78px;
  background: linear-gradient(135deg, #ff6b35, #ffb347);
  border-radius: 50%;
  margin: 0 auto 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  color: white;
  box-shadow: 0 10px 20px rgba(255, 107, 53, 0.15);
}

@media (max-width: 768px) {
  .streak-header {
    padding: 20px;
  }

  .streak-fire-wrap {
    width: 90px;
    height: 90px;
    border-radius: 28px;
  }

  .streak-fire {
    width: 70px;
    height: 70px;
    border-radius: 22px;
    font-size: 32px;
  }

  .mini-card {
    padding: 14px;
  }

  .mini-icon {
    width: 50px;
    height: 50px;
    font-size: 20px;
  }

  .day-card {
    padding: 14px 4px;
  }

  .day-icon {
    font-size: 20px;
  }
}
.content-wrapper { width: 92%; max-width: 1650px; margin: 0 auto; } .page-full { width: 100vw; margin-left: calc(-50vw + 50%); }
</style>