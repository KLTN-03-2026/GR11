<template>
  <div class="page-full bg-light min-vh-100">
    <div class="content-wrapper py-5">

      <!-- Loading -->
      <div v-if="loading" class="text-center py-5 text-muted">Đang tải dữ liệu tiến độ...</div>

      <!-- Lỗi -->
      <div v-else-if="errorMessage" class="alert alert-danger rounded-4 shadow-sm">
        {{ errorMessage }}
      </div>

      <template v-else>
        <!-- Chưa bắt đầu học -->
        <div v-if="!hasStarted" class="empty-state bg-white rounded-5 shadow-sm p-5 text-center">
          <div class="empty-icon mb-3">📚</div>
          <h4 class="fw-bold mb-2">Bé chưa bắt đầu bài học nào</h4>
          <p class="text-muted mb-4">
            Hãy chọn một bài học để bắt đầu luyện tập, hệ thống sẽ tự động ghi nhận tiến độ.
          </p>
          <button
            class="btn rounded-pill px-4 py-3 fw-bold text-white"
            style="background: linear-gradient(135deg, #ff8c42, #ff6b35);"
            @click="goToLessons"
          >
            Đi Tới Danh Sách Bài Học
          </button>
        </div>

        <template v-else>
          <!-- Header -->
          <div
            class="rounded-5 p-4 mb-4 position-relative overflow-hidden shadow-sm"
            style="background: linear-gradient(135deg, #ff8c42, #ff6b35);"
          >
            <div
              class="position-absolute rounded-circle"
              style="width:180px;height:180px;background:rgba(255,255,255,0.08);top:-50px;right:-50px;"
            ></div>

            <div class="row align-items-center position-relative">
              <div class="col-lg-8">
                <span class="badge bg-white text-dark rounded-pill px-3 py-2 mb-3">
                  Tiến Độ Học Tập
                </span>

                <h2
                  class="fw-bold text-white mb-2"
                  style="font-size:40px;font-family:'Lobster Two',cursive;"
                >
                  Bé Đang Tiến Bộ Mỗi Ngày 🎉
                </h2>

                <p class="text-white mb-3" style="max-width:600px;">
                  Theo dõi số bài học đã hoàn thành, điểm phát âm và chuỗi ngày học của bé.
                </p>

                <div class="d-flex flex-wrap gap-2">
                  <div class="rounded-4 px-3 py-2 progress-top-box">
                    <h6 class="fw-bold mb-0 text-white">
                      {{ overview.tong_bai_hoan_thanh || 0 }} Bài Học
                    </h6>
                  </div>
                 
                  <div class="rounded-4 px-3 py-2 progress-top-box">
                    <h6 class="fw-bold mb-0 text-white">
                      {{ Number(overview.phan_tram_phat_am || 0).toFixed(0) }}% Phát Âm
                    </h6>
                  </div>
                  <div class="rounded-4 px-3 py-2 progress-top-box">
                    <h6 class="fw-bold mb-0 text-white">
                      {{ overview.streak_hien_tai || 0 }}🔥 Streak
                    </h6>
                  </div>
                </div>
              </div>

              <div class="col-lg-4 text-center mt-4 mt-lg-0">
                <div
                  class="rounded-circle d-inline-flex align-items-center justify-content-center"
                  style="width:140px;height:140px;background:rgba(255,255,255,0.15);border:8px solid rgba(255,255,255,0.2);box-shadow:0 15px 35px rgba(0,0,0,0.12);"
                >
                  <span style="font-size:60px;">📈</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Thống kê 4 thẻ -->
          <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
              <div class="bg-white rounded-5 shadow-sm p-3 h-100 progress-card" style="border-top:4px solid #ff8c42;">
                <div class="d-flex align-items-center gap-3">
                  <div class="stat-icon-box" style="background:#fff1eb;">
                    <span style="font-size:30px;">📚</span>
                  </div>
                  <div>
                    <h4 class="fw-bold mb-1" style="color:#0d3b66;">
                      {{ overview.tong_bai_hoan_thanh || 0 }}
                    </h4>
                    <p class="text-muted mb-0 small">Bài Học Đã Hoàn Thành</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="bg-white rounded-5 shadow-sm p-3 h-100 progress-card" style="border-top:4px solid #67d11f;">
                <div class="d-flex align-items-center gap-3">
                  <div class="stat-icon-box" style="background:#fff8e6;">
                    <span style="font-size:30px;">🔥</span>
                  </div>
                  <div>
                    <h4 class="fw-bold mb-1" style="color:#0d3b66;">
                      {{ overview.streak_hien_tai || 0 }} Ngày
                    </h4>
                    <p class="text-muted mb-0 small">Chuỗi Học Liên Tiếp</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="bg-white rounded-5 shadow-sm p-3 h-100 progress-card" style="border-top:4px solid #36a2eb;">
                <div class="d-flex align-items-center gap-3">
                  <div class="stat-icon-box" style="background:#eaf8f0;">
                    <span style="font-size:30px;">⭐</span>
                  </div>
                  <div>
                    <h4 class="fw-bold mb-1" style="color:#0d3b66;">
                      {{ formatNumber(overview.diem_tich_luy) }}
                    </h4>
                    <p class="text-muted mb-0 small">Điểm Tích Lũy</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="bg-white rounded-5 shadow-sm p-3 h-100 progress-card" style="border-top:4px solid #9b5de5;">
                <div class="d-flex align-items-center gap-3">
                  <div class="stat-icon-box" style="background:#f3ebff;">
                    <span style="font-size:30px;">🎯</span>
                  </div>
                  <div>
                    <h4 class="fw-bold mb-1" style="color:#0d3b66;">
                      {{ latestLesson ? latestLesson.tieu_de : '—' }}
                    </h4>
                    <p class="text-muted mb-0 small">Bài Gần Nhất</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tiến độ theo lộ trình -->
          <div class="bg-white rounded-5 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
              <div>
                <h3 class="fw-bold mb-1" style="color:#0d3b66;">Tiến Độ Lộ Trình</h3>
                <p class="text-muted mb-0">Các lộ trình bé đã học và mức độ hoàn thành</p>
              </div>
              <button
                class="btn rounded-pill px-4 py-2 fw-bold text-white"
                style="background:linear-gradient(135deg,#ff8c42,#ff6b35);box-shadow:0 10px 20px rgba(255,107,53,0.2);"
                @click="goToLessons"
              >
                Xem Bài Học
              </button>
            </div>

           
            <div v-if="roadmap.danh_sach.length === 0" class="text-muted py-4 text-center">
              Chưa có dữ liệu lộ trình.
            </div>

            <div v-else class="row g-4">
              <div
                class="col-lg-6"
                v-for="(item, idx) in roadmap.danh_sach"
                :key="item.id"
              >
               
                <div
                  class="rounded-5 p-4 h-100 topic-card"
                  :style="{
                    background: `linear-gradient(135deg, ${topicPreset(idx).bgLight}, #fff)`,
                    border: `2px solid ${topicPreset(idx).border}`
                  }"
                >
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-3">
                      <div
                        class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                        :style="{ width:'70px', height:'70px', background: topicPreset(idx).iconBg, fontSize:'30px' }"
                      >
                        {{ topicPreset(idx).icon }}
                      </div>
                      <div>
                       
                        <h5 class="fw-bold mb-1" style="color:#0d3b66;">{{ item.ten_lo_trinh }}</h5>
                        <small class="text-muted" v-if="item.can_hoc">
                          {{ item.so_tu_da_hoc }}/{{ item.tong_tu_vung }} từ vựng
                        </small>
                        <small class="text-danger d-block mt-1" v-else-if="item.can_mua">
                          Lộ trình trả phí — {{ formatVnd(item.gia) }}. Thanh toán từ ví để xem bài theo thứ tự.
                        </small>
                        <small class="text-warning d-block mt-1" v-else-if="item.la_tra_phi && !item.tra_phi_da_duyet">
                          Giá lộ trình đang chờ duyệt. Vui lòng quay lại sau.
                        </small>
                      </div>
                    </div>

                    <div
                      class="topic-percent"
                      :style="{ color: topicPreset(idx).color, border: `2px solid ${topicPreset(idx).border}` }"
                    >
                      {{ Number(item.tien_do || 0).toFixed(0) }}%
                    </div>
                  </div>

                  <div class="progress rounded-pill" style="height:16px;background:#f1f1f1;">
                    <div
                      class="progress-bar rounded-pill"
                      role="progressbar"
                      :style="{ width: `${Number(item.tien_do || 0)}%`, background: topicPreset(idx).progressGradient }"
                    ></div>
                  </div>
                  <div v-if="item.can_mua" class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                    <button
                      type="button"
                      class="btn rounded-pill px-4 py-2 fw-bold text-white"
                      :disabled="muaLoTrinhId === item.id"
                      style="background: linear-gradient(135deg,#10b981,#059669);"
                      @click="moModalThanhToanLoTrinh(item)"
                    >
                      {{ muaLoTrinhId === item.id ? 'Đang xử lý...' : 'Thanh toán & mở lộ trình' }}
                    </button>
                    <router-link to="/profile" class="btn btn-outline-secondary rounded-pill btn-sm">Nạp tiền ví</router-link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Nút xem thêm lộ trình -->
            <div v-if="roadmap.phan_trang.co_them" class="text-center mt-4">
              <button
                class="btn rounded-pill px-4 py-2 fw-bold"
                style="border:2px solid #ff8c42;color:#ff8c42;"
                @click="xemThemLoTrinh"
              >
                Xem Thêm Lộ Trình
              </button>
            </div>
          </div>

          <!-- Tiến độ bài học -->
          <div class="bg-white rounded-5 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
              <div>
                <h3 class="fw-bold mb-1" style="color:#0d3b66;">Tiến Độ Bài Học</h3>
                <p class="text-muted mb-0">Các bài học bé đã luyện tập gần đây</p>
              </div>
              <span class="badge rounded-pill px-4 py-2 fw-bold"
                style="background:linear-gradient(135deg,#36a2eb,#4cc9f0);color:#fff;font-size:13px;">
                {{ aiSuggestions.length }} bài học
              </span>
            </div>

            <div v-if="aiSuggestions.length === 0" class="text-muted py-4 text-center">
              Bé chưa luyện tập bài học nào.
            </div>

            <div v-else class="row g-4">
              <div
                class="col-lg-4 col-md-6"
                v-for="(item, idx) in aiSuggestions"
                :key="item.bai_hoc_id"
              >
                <div
                  class="rounded-5 p-4 h-100 skill-card"
                  :style="{
                    background: `linear-gradient(135deg, ${skillPreset(idx).bgLight}, #fff)`,
                    border: `2px solid ${skillPreset(idx).border}`
                  }"
                >
                  <div
                    class="rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm"
                    :style="{ width:'75px', height:'75px', background: skillPreset(idx).iconBg, fontSize:'34px' }"
                  >
                    📖
                  </div>

                  <div class="mb-3">
                    <span
                      class="badge rounded-pill px-3 py-2"
                      :style="{ background: skillPreset(idx).badgeBg, color: skillPreset(idx).color }"
                    >
                      {{ Number(item.tien_do || 0).toFixed(0) }}% hoàn thành
                    </span>
                  </div>

                  <h5 class="fw-bold mb-2" style="color:#0d3b66;">{{ item.tieu_de }}</h5>

                  <p class="text-muted mb-3">
                    Đã học <strong>{{ item.so_tu_da_hoc }}/{{ item.tong_tu_vung }}</strong> từ vựng
                  </p>

                  <div class="progress rounded-pill mb-3" style="height:12px;background:#f1f1f1;">
                    <div
                      class="progress-bar rounded-pill"
                      role="progressbar"
                      :style="{ width: `${Number(item.tien_do || 0)}%`, background: skillPreset(idx).progressGradient }"
                    ></div>
                  </div>

                  <button
                    class="btn rounded-pill px-4 py-2 fw-bold text-white"
                    :style="{ background: skillPreset(idx).progressGradient, boxShadow: `0 10px 20px ${skillPreset(idx).shadow}` }"
                    @click="goToPractice(item)"
                  >
                    Luyện Ngay
                  </button>
                </div>
              </div>
            </div>
          </div>
        </template>
      </template>

    </div>

    <!-- Modal xác nhận thanh toán phí lộ trình -->
    <div
      v-if="modalThanhToanLoTrinh.mo"
      class="modal-thanhtoan-backdrop position-fixed top-0 start-0 end-0 bottom-0 d-flex align-items-center justify-content-center p-3"
      style="z-index:1050;background:rgba(15,23,42,0.45);"
      tabindex="-1"
      @click.self="dongModalThanhToanLoTrinh"
    >
      <div class="bg-white rounded-5 shadow-lg p-4 modal-thanhtoan-card" role="dialog" aria-modal="true" @click.stop>
        <h5 class="fw-bold mb-2" style="color:#0d3b66;">Xác nhận thanh toán</h5>
        <p class="text-muted mb-3 small" v-if="modalThanhToanLoTrinh.item">
          Bạn sẽ thanh toán <strong>{{ formatVnd(modalThanhToanLoTrinh.item.gia) }}</strong>
          để mở lộ trình
          « <strong>{{ modalThanhToanLoTrinh.item.ten_lo_trinh }}</strong> »
          từ ví EchoKids của bạn.
        </p>
        <div v-if="soDuViHienTai !== null" class="mb-3 p-3 rounded-4 bg-light small">
          <span class="text-muted">Số dư ví hiện tại:</span>
          <span class="fw-bold ms-1" :class="(!modalThanhToanLoTrinh.item || soDuViHienTai < Number(modalThanhToanLoTrinh.item.gia)) ? 'text-danger' : ''">
            {{ formatVnd(soDuViHienTai) }}
          </span>
          <router-link v-if="modalThanhToanLoTrinh.item && soDuViHienTai < Number(modalThanhToanLoTrinh.item.gia)" to="/profile" class="ms-2 d-inline-block">
            Nạp tiền
          </router-link>
        </div>
        <p class="small text-muted mb-3">
          Giảng viên nhận phần chia sau khi trừ hoa hồng nền tảng (theo cấu hình). Phần còn lại là phí vận hành EchoKids.
        </p>
        <div class="d-flex gap-2 justify-content-end flex-wrap">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" :disabled="muaLoTrinhId" @click="dongModalThanhToanLoTrinh">
            Hủy
          </button>
          <button
            type="button"
            class="btn rounded-pill px-4 fw-bold text-white"
            style="background: linear-gradient(135deg,#10b981,#059669);"
            :disabled="!!muaLoTrinhId || !modalThanhToanLoTrinh.item || (soDuViHienTai !== null && modalThanhToanLoTrinh.item && soDuViHienTai < Number(modalThanhToanLoTrinh.item.gia))"
            @click="xacNhanThanhToanLoTrinh"
          >
            {{ muaLoTrinhId === modalThanhToanLoTrinh.item?.id ? 'Đang xử lý...' : 'Đồng ý thanh toán' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

const TOPIC_PRESETS = [
  { icon: "🐶", iconBg: "#ffe4d9", bgLight: "#fff8f5", border: "#ffe4d9", color: "#ff6b35", progressGradient: "linear-gradient(135deg,#ff8c42,#ff6b35)" },
  { icon: "🍎", iconBg: "#dff6e7", bgLight: "#eefaf3", border: "#dff6e7", color: "#27ae60", progressGradient: "linear-gradient(135deg,#67d11f,#4caf50)" },
  { icon: "🔤", iconBg: "#dbeeff", bgLight: "#eef5ff", border: "#dbeeff", color: "#0984e3", progressGradient: "linear-gradient(135deg,#36a2eb,#4cc9f0)" },
  { icon: "🚗", iconBg: "#eadcff", bgLight: "#f7f0ff", border: "#eadcff", color: "#9b5de5", progressGradient: "linear-gradient(135deg,#9b5de5,#b388ff)" },
  { icon: "🎨", iconBg: "#fce4ec", bgLight: "#fff0f4", border: "#fce4ec", color: "#e91e63", progressGradient: "linear-gradient(135deg,#e91e63,#f48fb1)" },
  { icon: "🌈", iconBg: "#fff8e6", bgLight: "#fffdf0", border: "#fff0c2", color: "#f4a100", progressGradient: "linear-gradient(135deg,#f4a100,#ffc107)" },
];

const SKILL_PRESETS = [
  { iconBg: "#ffe4d9", bgLight: "#fff8f5", border: "#ffe4d9", color: "#ff6b35", badgeBg: "#fff1eb", progressGradient: "linear-gradient(135deg,#ff8c42,#ff6b35)", shadow: "rgba(255,107,53,0.2)" },
  { iconBg: "#dff6e7", bgLight: "#eefaf3", border: "#dff6e7", color: "#27ae60", badgeBg: "#eaf8f0", progressGradient: "linear-gradient(135deg,#67d11f,#4caf50)", shadow: "rgba(76,175,80,0.2)" },
  { iconBg: "#dbeeff", bgLight: "#eef5ff", border: "#dbeeff", color: "#0984e3", badgeBg: "#eef5ff", progressGradient: "linear-gradient(135deg,#36a2eb,#4cc9f0)", shadow: "rgba(54,162,235,0.2)" },
  { iconBg: "#eadcff", bgLight: "#f7f0ff", border: "#eadcff", color: "#9b5de5", badgeBg: "#f3ebff", progressGradient: "linear-gradient(135deg,#9b5de5,#b388ff)", shadow: "rgba(155,93,229,0.2)" },
  { iconBg: "#fce4ec", bgLight: "#fff0f4", border: "#fce4ec", color: "#e91e63", badgeBg: "#fce4ec", progressGradient: "linear-gradient(135deg,#e91e63,#f48fb1)", shadow: "rgba(233,30,99,0.2)" },
];

export default {
  name: "TienDoPage",
  data() {
    return {
      loading: true,
      hasStarted: false,
      errorMessage: "",
      overview: {
        tong_bai_hoan_thanh: 0,
        phan_tram_phat_am: 0,
        streak_hien_tai: 0,
        diem_tich_luy: 0,
      },
      roadmap: {
        danh_sach: [],
        phan_trang: {
          tong_so: 0,
          co_them: false,
        },
      },
      loTrinhOffset: 0,
      loTrinhLimit: 3,
      baiHocOffset: 0,
      baiHocLimit: 5,
      aiSuggestions: [],
      loadingMore: false,
      muaLoTrinhId: null,
      apiBase: (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, ''),
      modalThanhToanLoTrinh: { mo: false, item: null },
      soDuViHienTai: null,
    };
  },
  mounted() {
    this.loadProgress();
  },
  methods: {
    authHeaders() {
      const token = localStorage.getItem("token_nguoi_dung");
      return { Authorization: `Bearer ${token}` };
    },

    async loadProgress() {
      this.loading = true;
      this.errorMessage = "";
      try {
        const { data: res } = await axios.get(
          this.apiBase + "/api/tien-do-bai-hoc/tong-quan",
          {
            headers: this.authHeaders(),
            params: {
              lo_trinh_limit: this.loTrinhLimit,
              lo_trinh_offset: 0,
              bai_hoc_limit: this.baiHocLimit,
              bai_hoc_offset: 0,
            },
          }
        );

        if (!res.status) {
          this.errorMessage = "Không thể tải dữ liệu tiến độ.";
          return;
        }

        const d = res.data;

        this.overview = {
          tong_bai_hoan_thanh: d.so_bai_hoc || 0,
          phan_tram_phat_am: d.phan_tram_phat_am || 0,
          streak_hien_tai: d.streak_hien_tai || 0,
          diem_tich_luy: d.diem_tich_luy || 0,
        };

        this.roadmap = {
          danh_sach: d.lo_trinhs.danh_sach || [],
          phan_trang: {
            tong_so: d.lo_trinhs.tong || 0,
            co_them: (d.lo_trinhs.con_lai || 0) > 0,
          },
        };
        this.loTrinhOffset = d.lo_trinhs.danh_sach.length;

        this.aiSuggestions = d.bai_hocs.danh_sach || [];
        this.baiHocOffset = d.bai_hocs.danh_sach.length;

        this.hasStarted =
          d.so_bai_hoc > 0 ||
          !!d.bai_gan_nhat ||
          d.lo_trinhs.tong > 0;
      } catch (e) {
        if (e.response && e.response.status === 401) {
          this.errorMessage = "Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại.";
        } else {
          this.errorMessage = "Đã xảy ra lỗi khi tải dữ liệu tiến độ.";
        }
      } finally {
        this.loading = false;
      }
    },

    async xemThemLoTrinh() {
      if (this.loadingMore) return;
      this.loadingMore = true;
      try {
        const { data: res } = await axios.get(
          this.apiBase + "/api/tien-do-bai-hoc/tong-quan",
          {
            headers: this.authHeaders(),
            params: {
              lo_trinh_limit: this.loTrinhLimit,
              lo_trinh_offset: this.loTrinhOffset,
              bai_hoc_limit: 0,
              bai_hoc_offset: 0,
            },
          }
        );
        if (res.status) {
          const newItems = res.data.lo_trinhs.danh_sach || [];
          this.roadmap.danh_sach.push(...newItems);
          this.loTrinhOffset += newItems.length;
          this.roadmap.phan_trang.co_them = (res.data.lo_trinhs.con_lai || 0) > 0;
        }
      } catch (_) {
        // silent
      } finally {
        this.loadingMore = false;
      }
    },

    topicPreset(idx) {
      return TOPIC_PRESETS[idx % TOPIC_PRESETS.length];
    },

    skillPreset(idx) {
      return SKILL_PRESETS[idx % SKILL_PRESETS.length];
    },

    formatNumber(n) {
      if (!n) return "0";
      return Number(n).toLocaleString("vi-VN");
    },

    goToLessons() {
      this.$router.push("/bai-hoc");
    },

    goToLessonDetail(lessonId) {
      if (!lessonId) return;
      this.$router.push(`/chi-tiet-bai-hoc/${lessonId}`);
    },

    goToPractice(item) {
      this.goToLessonDetail(item?.bai_hoc_id);
    },

    formatVnd(n) {
      if (!n) return "0 đ";
      return Number(n).toLocaleString("vi-VN") + " đ";
    },

    moModalThanhToanLoTrinh(item) {
      if (!item?.id) return;
      this.modalThanhToanLoTrinh = { mo: true, item };
      this.taiSoDuViChoModal();
    },
    dongModalThanhToanLoTrinh() {
      if (this.muaLoTrinhId) return;
      this.modalThanhToanLoTrinh = { mo: false, item: null };
      this.soDuViHienTai = null;
    },
    async taiSoDuViChoModal() {
      this.soDuViHienTai = null;
      try {
        const { data } = await axios.get(this.apiBase + "/api/vi/so-du", {
          headers: this.authHeaders(),
        });
        if (data.status) {
          this.soDuViHienTai = typeof data.so_du === "number" ? data.so_du : parseInt(data.so_du, 10) || 0;
        }
      } catch (_) {
        this.soDuViHienTai = null;
      }
    },
    async xacNhanThanhToanLoTrinh() {
      const item = this.modalThanhToanLoTrinh.item;
      if (!item) return;
      const ok = await this.thucHienMuaLoTrinh(item);
      if (ok) {
        this.modalThanhToanLoTrinh = { mo: false, item: null };
        this.soDuViHienTai = null;
      }
    },

    async thucHienMuaLoTrinh(item) {
      if (!item?.id) return false;
      this.muaLoTrinhId = item.id;
      try {
        const { data: res } = await axios.post(
          this.apiBase + "/api/hoc-vien/lo-trinh-ca-nhan/" + item.id + "/mua",
          {},
          { headers: this.authHeaders() }
        );
        if (res.status) {
          if (this.$toast) this.$toast.success(res.message || "Đã mở lộ trình.");
          await this.loadProgress();
          return true;
        }
        if (this.$toast) this.$toast.error(res.message || "Không thanh toán được.");
        return false;
      } catch (e) {
        const msg = e.response?.data?.message || "Không thanh toán được.";
        if (this.$toast) this.$toast.error(msg);
        return false;
      } finally {
        this.muaLoTrinhId = null;
      }
    },
  },
};
</script>

<style scoped>
.content-wrapper { width: 92%; max-width: 1650px; margin: 0 auto; }
.page-full { width: 100vw; margin-left: calc(-50vw + 50%); }

.rounded-5 { border-radius: 28px !important; }

/* Empty state */
.empty-state { background: linear-gradient(135deg, #ffffff, #fff7f2); }
.empty-icon { font-size: 44px; }

/* Progress top badges */
.progress-top-box {
  background: rgba(255,255,255,0.18);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.2);
  min-width: 130px;
}

/* 4 stat cards */
.progress-card {
  transition: all 0.3s ease;
  border: 2px solid transparent;
}
.progress-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 18px 35px rgba(0,0,0,0.12) !important;
  border-color: #ffe3d4;
}

.stat-icon-box {
  width: 65px;
  height: 65px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Topic progress cards */
.topic-card {
  transition: all 0.3s ease;
  box-shadow: 0 10px 25px rgba(0,0,0,0.06);
}
.topic-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 18px 35px rgba(0,0,0,0.12);
}
.topic-percent {
  min-width: 75px;
  text-align: center;
  padding: 8px 12px;
  border-radius: 999px;
  font-weight: 700;
  background: rgba(255,255,255,0.8);
}

/* Skill cards */
.skill-card {
  transition: all 0.3s ease;
  box-shadow: 0 10px 25px rgba(0,0,0,0.06);
}
.skill-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 18px 35px rgba(0,0,0,0.12);
}

@media (max-width: 768px) {
  .progress-top-box { min-width: unset; }
  .stat-icon-box { width: 52px; height: 52px; }
}

.modal-thanhtoan-card {
  max-width: 440px;
  width: 100%;
}
</style>