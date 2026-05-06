<template>
  <div>
    <div class="iq-sidebar">
      <div class="iq-sidebar-logo d-flex justify-content-between">
        <router-link to="/admin/dashboard" class="d-flex align-items-center text-danger text-nowrap"
          style="font-size: 24px; text-decoration: none;">
          <i class="fa fa-book-reader fa-xl me-2" style="position: relative; top: -2px;"></i>
          <span class="m-0 fw-bold text-uppercase text-danger" style="font-size: 18px;">
            <b>ADMIN ECHOKIDS</b>
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

            <li :class="{ active: $route.path === '/admin/dashboard' && (!$route.query.tab || $route.query.tab === 'monitoring') }">
              <router-link to="/admin/dashboard" class="iq-waves-effect mt-1">
                <i class="ri-dashboard-line"></i>
                <span>Tổng quan</span>
              </router-link>
            </li>

            <li :class="{ active: $route.path === '/admin/quan-ly-tai-khoan' }">
              <router-link to="/admin/quan-ly-tai-khoan" class="iq-waves-effect mt-1">
                <i class="ri-group-line"></i>
                <span>Quản lý Tài khoản</span>
              </router-link>
            </li>

            <li :class="{ active: $route.path === '/admin/kiem-duyet' }">
              <router-link to="/admin/kiem-duyet" class="iq-waves-effect mt-1">
                <i class="ri-book-open-line"></i>
                <span>Kiểm Duyệt</span>
              </router-link>
            </li>

            <li :class="{ active: ($route.path === '/admin/dashboard' && $route.query.tab === 'reports') || $route.path === '/admin/thong-ke' }">
              <router-link :to="{ path: '/admin/dashboard', query: { tab: 'reports' } }" class="iq-waves-effect mt-1">
                <i class="ri-bar-chart-box-line"></i>
                <span>Báo cáo Thống kê</span>
              </router-link>
            </li>

            <li :class="{ active: $route.path === '/admin/phan-quyen' }">
              <router-link to="/admin/phan-quyen" class="iq-waves-effect mt-1">
                <i class="ri-shield-user-line"></i>
                <span>Phân quyền</span>
              </router-link>
            </li>
            <li :class="{ active: $route.path === '/admin/quan-ly-cau-hinh' }">
              <router-link to="/admin/quan-ly-cau-hinh" class="iq-waves-effect mt-1">
                <i class="ri-shield-user-line"></i>
                <span>Quản lý cấu hình</span>
              </router-link>
            </li>
            <li class="logout-item">
              <a href="#" class="iq-waves-effect logout-link" @click.prevent="showLogoutModal = true">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Đăng xuất</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
    <div v-if="showLogoutModal" class="logout-modal-overlay" @click.self="closeLogoutModal">
      <div class="logout-modal-card">
        <h5>Bạn có muốn đăng xuất không?</h5>
        <div class="logout-modal-actions">
          <button type="button" class="btn-cancel" @click="closeLogoutModal" :disabled="isLoggingOut">Không</button>
          <button type="button" class="btn-logout" @click="dangXuat" :disabled="isLoggingOut">
            {{ isLoggingOut ? 'Đang đăng xuất...' : 'Đăng xuất' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

const PROFILE_LS_KEYS = ['ho_ten', 'email', 'check_token', 'ten_vai_tro', 'anh_dai_dien', 'anh_dai_dien_url', 'anh_dai_dien_local', 'nguoi_dung_id']

export default {
  data() {
    return {
      showLogoutModal: false,
      isLoggingOut: false,
    }
  },
  methods: {
    closeLogoutModal() {
      if (this.isLoggingOut) return
      this.showLogoutModal = false
    },
    dangXuat() {
      this.isLoggingOut = true
      const token = localStorage.getItem('token_admin')
      axios
        .post(
          'http://127.0.0.1:8000/api/dang-xuat',
          {},
          {
            headers: {
              Authorization: 'Bearer ' + token,
            },
          }
        )
        .then((res) => {
          if (res.data.status) {
            localStorage.removeItem('token_admin')
            PROFILE_LS_KEYS.forEach((k) => localStorage.removeItem(k))
            this.$toast.success(res.data.message)
            this.$router.push('/dang-nhap')
          } else {
            this.$toast.error('Có lỗi xảy ra')
          }
        })
        .catch(() => {
          localStorage.removeItem('token_admin')
          PROFILE_LS_KEYS.forEach((k) => localStorage.removeItem(k))
          this.$router.push('/dang-nhap')
        })
        .finally(() => {
          this.isLoggingOut = false
          this.showLogoutModal = false
        })
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
.sidebar-collapse-toggle:active,
.sidebar-collapse-toggle.open {
  position: static !important;
  width: auto !important;
  height: auto !important;
  border-radius: 0 !important;
  background: transparent !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  box-shadow: none !important;
  border: none !important;
  transform: none !important;
  margin: 0 !important;
  padding: 0 !important;
  cursor: pointer !important;
  transition: none !important;
  outline: none !important;
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
  flex: 0 0 auto;
  position: relative !important;
  left: -8px !important;
  z-index: 5 !important;
}

body.sidebar-main .iq-sidebar-logo > a i.fa-book-reader {
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

body.sidebar-main .sidebar-collapse-toggle {
  width: auto !important;
  height: auto !important;
  border-radius: 0 !important;
  margin-left: 0 !important;
  background: transparent !important;
  box-shadow: none !important;
  transform: none !important;
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

/* --- STYLES MENU & LOGOUT GIỮ NGUYÊN --- */
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