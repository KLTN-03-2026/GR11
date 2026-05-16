<template>
  <div>
    <div class="iq-sidebar">
      <div class="iq-sidebar-logo d-flex justify-content-between">
        <router-link
          to="/teacher/dashboard"
          class="d-flex align-items-center text-danger text-nowrap"
          style="font-size: 24px; text-decoration: none"
        >
          <img v-if="branding.logo_url" :src="branding.logo_url" alt="Logo" class="me-2" style="height: 32px; width: 32px; object-fit: cover; border-radius: 50%;">
          <i v-else class="fa fa-book-reader fa-xl me-2" style="position: relative; top: -2px"></i>
          <span
            class="m-0 fw-bold text-uppercase text-danger"
            style="font-size: 18px"
          >
            <b>TEACHER ECHOKIDS</b>
          </span>
        </router-link>
        <div class="iq-menu-bt-sidebar">
          <div class="iq-menu-bt align-self-center">
            <div class="wrapper-menu sidebar-collapse-toggle">
              <div class="sidebar-toggle-icon">
                <i class="fa-solid fa-chevron-right"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="sidebar-scrollbar">
        <nav class="iq-sidebar-menu">
          <ul class="iq-menu">
            <li :class="{ active: $route.path === '/teacher/dashboard' }">
              <router-link to="/teacher/dashboard" class="iq-waves-effect mt-1">
                <i class="ri-home-4-line"></i>
                <span>Tổng quan</span>
              </router-link>
            </li>
            <li
              :class="{ active: $route.path === '/teacher/quan-ly-hoc-sinh' }"
            >
              <router-link
                to="/teacher/quan-ly-hoc-sinh"
                class="iq-waves-effect mt-1"
              >
                <i class="fa-solid fa-user-graduate"></i>
                <span>Quản Lý Học Sinh</span>
              </router-link>
            </li>
            <li
              :class="{ active: $route.path === '/teacher/quan-ly-lo-trinh' }"
            >
              <router-link
                to="/teacher/quan-ly-lo-trinh"
                class="iq-waves-effect mt-1"
              >
                <i class="fa-solid fa-route"></i>
                <span>Lộ trình cá nhân</span>
              </router-link>
            </li>
            <li :class="{ active: $route.path === '/teacher/quan-ly-bai-hoc' }">
              <router-link
                to="/teacher/quan-ly-bai-hoc"
                class="iq-waves-effect mt-1"
              >
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>Quản Lý Bài Học</span>
              </router-link>
            </li>
            <li
              :class="{
                active:
                  $route.path === '/teacher/quan-ly-bai-kiem-tra' ||
                  $route.path.startsWith('/teacher/quan-ly-bai-kiem-tra/'),
              }"
            >
              <router-link
                to="/teacher/quan-ly-bai-kiem-tra"
                class="iq-waves-effect mt-1"
              >
                <i class="fa-solid fa-clipboard-question"></i>
                <span>Bài kiểm tra</span>
              </router-link>
            </li>

            <li
              :class="{ active: $route.path === '/teacher/bao-cao-thong-ke' }"
            >
              <router-link
                to="/teacher/bao-cao-thong-ke"
                class="iq-waves-effect mt-1"
              >
                <i class="fa-solid fa-chart-line"></i>
                <span>Báo Cáo Thống Kê</span>
              </router-link>
            </li>
            <li class="logout-item">
              <a
                href="#"
                class="iq-waves-effect logout-link"
                @click.prevent="showLogoutModal = true"
              >
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Đăng xuất</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
    <div
      v-if="showLogoutModal"
      class="logout-modal-overlay"
      @click.self="closeLogoutModal"
    >
      <div class="logout-modal-card">
        <h5>Bạn có muốn đăng xuất không?</h5>
        <div class="logout-modal-actions">
          <button
            type="button"
            class="btn-cancel"
            @click="closeLogoutModal"
            :disabled="isLoggingOut"
          >
            Không
          </button>
          <button
            type="button"
            class="btn-logout"
            @click="dangXuat"
            :disabled="isLoggingOut"
          >
            {{ isLoggingOut ? "Đang đăng xuất..." : "Đăng xuất" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

const PROFILE_LS_KEYS = [
  "ho_ten",
  "email",
  "check_token",
  "ten_vai_tro",
  "anh_dai_dien",
  "anh_dai_dien_url",
  "anh_dai_dien_local",
  "nguoi_dung_id",
];

export default {
  data() {
    return {
      showLogoutModal: false,
      isLoggingOut: false,
      branding: {
        logo_icon: "fa fa-book-reader",
        logo_url: null,
        site_name: "TEACHER ECHOKIDS",
      },
    };
  },
  mounted() {
    this.taiCauHinhChung();
  },
  methods: {
    taiCauHinhChung() {
      axios
        .get("http://127.0.0.1:8000/api/admin/cau-hinh/chung/data", {
          headers: {
            Authorization: "Bearer " + (localStorage.getItem("token_teacher") || "")
          }
        })
        .then((res) => {
          if (res.data?.status && res.data?.data) {
            this.branding.logo_icon = res.data.data.logo_icon || this.branding.logo_icon;
            this.branding.site_name = res.data.data.site_name || this.branding.site_name;
            this.branding.logo_url = res.data.data.logo_url || null;
          }
        })
        .catch(() => {});
    },
    closeLogoutModal() {
      if (this.isLoggingOut) return;
      this.showLogoutModal = false;
    },
    dangXuat() {
      this.isLoggingOut = true;
      const token = localStorage.getItem("token_teacher");

      console.log("1. Bắt đầu gọi API đăng xuất..."); // Kiểm tra xem đã bấm nút thành công chưa

      axios
        .post(
          "http://127.0.0.1:8000/api/dang-xuat",
          {},
          {
            headers: {
              Authorization: "Bearer " + token,
            },
          },
        )
        .then((res) => {
          localStorage.removeItem("token_teacher");
          PROFILE_LS_KEYS.forEach((k) => localStorage.removeItem(k));
          this.$router.push("/dang-nhap");
        })
        .catch((error) => {
          localStorage.removeItem("token_teacher");
          PROFILE_LS_KEYS.forEach((k) => localStorage.removeItem(k));
          this.$router.push("/dang-nhap");
        })
        .finally(() => {
          this.isLoggingOut = false;
          this.showLogoutModal = false;
        });
    },
  },
};
</script>

<style scoped>
.iq-sidebar-logo {
  position: relative;
  overflow: hidden;
  width: 100% !important;
  min-height: 56px !important;
  padding: 0 8px 0 10px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: flex-start !important;
}

.iq-sidebar-logo > a {
  height: 100% !important;
  margin: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  line-height: 1 !important;
}

.iq-menu-bt-sidebar {
  margin-left: 4px !important;
  position: static !important;
  transform: none !important;
  z-index: 3 !important;
  flex: 0 0 auto;
}

.iq-menu-bt-sidebar .iq-menu-bt,
.iq-menu-bt-sidebar .wrapper-menu {
  position: static !important;
  transform: none !important;
  margin: 0 !important;
  transition: none !important;
}

.sidebar-collapse-toggle,
.sidebar-collapse-toggle:hover,
.sidebar-collapse-toggle:focus,
.sidebar-collapse-toggle:active {
  position: static !important;
  width: auto !important;
  height: auto !important;
  border-radius: 0 !important;
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  transform: none !important;
  margin: 0 !important;
  padding: 0 !important;
  transition: none !important;
  outline: none !important;
}

.sidebar-collapse-toggle.open {
  transform: none !important;
}

.sidebar-collapse-toggle {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
}

.sidebar-toggle-icon {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 14px !important;
  height: 14px !important;
  line-height: 1 !important;
  transform: none !important;
  margin: 0 !important;
  padding: 0 !important;
}

.sidebar-toggle-icon i {
  font-size: 14px !important;
  color: #ef4444 !important;
  line-height: 1 !important;
  margin: 0 !important;
  padding: 0 !important;
  transform: none !important;
  transition: none !important;
}

body.sidebar-main .sidebar-collapse-toggle {
  width: auto !important;
  height: auto !important;
  border-radius: 0 !important;
  margin-left: 0 !important;
  background: transparent !important;
  box-shadow: none !important;
  transform: none !important;
}

body.sidebar-main .iq-sidebar-logo {
  width: 80px !important;
  min-height: 56px !important;
  padding-right: 0 !important;
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
}

body.sidebar-main .iq-sidebar-logo > a {
  margin-right: 0 !important;
  position: relative !important;
  left: -8px !important;
  z-index: 5 !important;
}

body.sidebar-main .iq-sidebar-logo > a i {
  display: inline-flex !important;
  opacity: 1 !important;
  visibility: visible !important;
}

body.sidebar-main .iq-sidebar-logo > a img {
  display: inline-flex !important;
  opacity: 1 !important;
  visibility: visible !important;
}

body.sidebar-main .iq-sidebar-logo .iq-menu-bt-sidebar {
  position: absolute !important;
  right: 10px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  z-index: 20 !important;
}

body.sidebar-main .iq-sidebar .iq-menu-bt-sidebar,
body.sidebar-main .iq-sidebar .iq-menu-bt-sidebar .iq-menu-bt,
body.sidebar-main .iq-sidebar .iq-menu-bt-sidebar .wrapper-menu {
  display: inline-flex !important;
  opacity: 1 !important;
  visibility: visible !important;
}

body.sidebar-main .sidebar-toggle-icon i {
  font-size: 14px !important;
  margin: 0 !important;
}

.sidebar-collapse-toggle::before,
.sidebar-collapse-toggle::after,
.sidebar-toggle-icon::before,
.sidebar-toggle-icon::after {
  content: none !important;
  display: none !important;
}

.logout-item {
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid #e5e7eb;
}

.logout-link {
  color: #dc2626 !important;
  font-weight: 700;
}

.logout-link i,
.logout-link span {
  color: inherit !important;
}

.logout-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.logout-modal-card {
  width: min(420px, 92vw);
  background: #fff;
  border-radius: 14px;
  padding: 18px;
  box-shadow: 0 14px 40px rgba(0, 0, 0, 0.2);
}

.logout-modal-card h5 {
  margin: 0 0 16px;
  font-size: 18px;
  font-weight: 700;
  color: #111827;
}

.logout-modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.btn-cancel,
.btn-logout {
  border: none;
  border-radius: 10px;
  padding: 9px 14px;
  font-weight: 700;
}

.btn-cancel {
  background: #e5e7eb;
  color: #111827;
}

.btn-logout {
  background: #dc2626;
  color: #fff;
}
</style>
