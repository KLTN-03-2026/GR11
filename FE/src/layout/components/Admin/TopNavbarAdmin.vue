<template>
  <div class="iq-top-navbar">
    <div class="iq-navbar-custom">
      <nav class="navbar navbar-expand-lg navbar-light p-0">
        
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-label="Toggle navigation">
          <i class="ri-menu-3-line"></i>
        </button>
        <div class="iq-menu-bt align-self-center">
          <div class="wrapper-menu">
            <div class="main-circle"><i class="ri-arrow-left-s-line"></i></div>
            <div class="hover-circle"><i class="ri-arrow-right-s-line"></i></div>
          </div>
        </div>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ml-auto navbar-list">
          </ul>
        </div>
        <ul class="navbar-list">
          <!-- Bell Notification -->
          <li class="notif-wrapper">
            <button class="notif-bell-btn" @click.stop="showNotifications = !showNotifications" :class="{ active: showNotifications }">
              <i class="fa-regular fa-bell notif-bell-icon"></i>
              <span v-if="unreadCount > 0" class="notif-badge">{{ unreadCount > 9 ? '9+' : unreadCount }}</span>
            </button>

            <transition name="notif-drop">
              <div v-if="showNotifications" class="notif-panel" @click.stop>
                <div class="notif-panel-header">
                  <div class="notif-panel-title">
                    Thông báo
                    <span v-if="unreadCount > 0" class="notif-count-chip">{{ unreadCount }}</span>
                  </div>
                  <button v-if="unreadCount > 0" class="notif-clear-btn" @click.prevent="clearNotifications">
                    <i class="fa-solid fa-check-double me-1"></i>Đọc tất cả
                  </button>
                </div>
                <div class="notif-list">
                  <div v-if="notifications.length === 0" class="notif-empty">
                    <div class="notif-empty-icon"><i class="fa-regular fa-bell-slash"></i></div>
                    <p class="notif-empty-text">Không có thông báo mới</p>
                    <small class="notif-empty-sub">Bạn đã xem hết các thông báo</small>
                  </div>
                  <div v-for="(notif, idx) in notifications" :key="notif.id || idx" class="notif-item" :class="[notif.isRead ? 'read' : 'unread', 'notif-type-' + (notif.type || 'info')]" @click="handleNotifClick(notif, idx)">
                    <div class="notif-item-icon"><i :class="notifIcon(notif)"></i></div>
                    <div class="notif-item-body">
                      <div class="notif-item-message" v-html="notif.message"></div>
                      <div class="notif-item-time"><i class="fa-regular fa-clock me-1"></i>{{ notif.time }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </transition>
          </li>
          <li>
            <router-link to="/admin/thong-tin-ca-nhan" class="search-toggle iq-waves-effect d-flex align-items-center admin-user-chip">
              <div class="caption admin-user-caption">
                <span class="admin-greeting">Xin chào,</span>
                <h6 class="text-danger font-weight-bold mb-0 line-height admin-user-name">{{ user.name }}</h6>
              </div>
              <img :src="user.avatarUrl" class="img-fluid admin-avatar me-2" alt="user">
            </router-link>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>
<script>
import { API_BASE } from '../../../api/http.js';

const ANH_MAC_DINH = '/Admin/images/user/1.jpg'

export default {
  data() {
    return {
      user: {},
      notifications: [],
      showNotifications: false,
    }
  },
  computed: {
    unreadCount() {
      return this.notifications.filter(n => !n.isRead).length;
    }
  },
  mounted() {
    this.dongBoUserTuLocal()
    this.loadNotifications()
    window.addEventListener('storage', this.dongBoUserTuLocal)
    window.addEventListener('profile-updated', this.dongBoUserTuLocal)
    window.addEventListener('ho-so-giao-vien-moi', this.handleHoSoMoi)
    window.addEventListener('bai-hoc-cho-duyet-moi', this.handleBaiHocMoi)
    document.addEventListener('click', this.handleClickOutside)
  },
  beforeUnmount() {
    window.removeEventListener('storage', this.dongBoUserTuLocal)
    window.removeEventListener('profile-updated', this.dongBoUserTuLocal)
    window.removeEventListener('ho-so-giao-vien-moi', this.handleHoSoMoi)
    window.removeEventListener('bai-hoc-cho-duyet-moi', this.handleBaiHocMoi)
    document.removeEventListener('click', this.handleClickOutside)
  },
  methods: {
    duongDanAnh(raw, macDinh) {
      if (!raw) {
        return macDinh
      }
      const source = String(raw).trim().replace(/\\/g, '/')
      if (!source) {
        return macDinh
      }
      if (source.startsWith('http://') || source.startsWith('https://') || source.startsWith('blob:')) {
        return source
      }
      const base = (API_BASE).replace(/\/$/, '')
      if (source.startsWith('/storage/')) {
        return `${base}${source}`
      }
      if (source.startsWith('storage/')) {
        return `${base}/${source}`
      }
      return `${base}/storage/${source.replace(/^\//, '')}`
    },
    dongBoUserTuLocal() {
      const avatarUrl =
        localStorage.getItem('anh_dai_dien_url')
        || localStorage.getItem('anh_dai_dien_local')
        || localStorage.getItem('anh_dai_dien')
      this.user = {
        name: localStorage.getItem('ho_ten') || 'Admin',
        avatarUrl: this.duongDanAnh(avatarUrl, ANH_MAC_DINH),
      }
    },
    handleHoSoMoi(e) {
      const data = e.detail;
      this.addNotification({
        message: `📋 Hồ sơ mới: <b>${data.ho_ten}</b> vừa đăng ký làm giáo viên.`,
        link: '/admin/quan-ly-ho-so-giao-vien',
        type: 'warning'
      });
    },
    handleBaiHocMoi(e) {
      const data = e.detail;
      this.addNotification({
        message: `📝 Bài học mới chờ duyệt: Giáo viên <b>${data.teacherName}</b> vừa nộp bài "${data.baiHoc.tieu_de}".`,
        link: '/admin/kiem-duyet',
        type: 'warning'
      });
    },
    addNotification(payload) {
      const now = new Date();
      const time = now.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) + ' ' + now.toLocaleDateString('vi-VN');
      
      const notif = typeof payload === 'string' ? { message: payload } : payload;
      notif.time = time;
      notif.isRead = false;
      notif.id = Date.now() + Math.random();

      this.notifications.unshift(notif);
      this.saveNotifications();
    },
    loadNotifications() {
      const saved = localStorage.getItem('admin_notifications');
      if (saved) {
        try {
          this.notifications = JSON.parse(saved);
        } catch(e) {}
      }
    },
    saveNotifications() {
      localStorage.setItem('admin_notifications', JSON.stringify(this.notifications.slice(0, 20)));
    },
    clearNotifications() {
      this.notifications.forEach(n => n.isRead = true);
      this.saveNotifications();
    },
    handleNotifClick(notif, idx) {
      notif.isRead = true;
      this.saveNotifications();
      if (notif.link) {
         this.$router.push(notif.link);
         this.showNotifications = false;
      }
    },
    handleClickOutside(e) {
      if (this.showNotifications && !this.$el.contains(e.target)) {
        this.showNotifications = false;
      }
    },
    notifIcon(notif) {
      const msg = (notif.message || '').toLowerCase();
      if (msg.includes('✅') || msg.includes('duyệt')) return 'fa-solid fa-circle-check';
      if (msg.includes('❌') || msg.includes('từ chối')) return 'fa-solid fa-circle-xmark';
      if (msg.includes('hồ sơ') || msg.includes('đăng ký')) return 'fa-solid fa-user-tie';
      if (msg.includes('bài học') || msg.includes('nộp bài')) return 'fa-solid fa-book-open';
      return 'fa-solid fa-bell';
    },
  },
}
</script>
<style scoped>
.notif-item.read { opacity: 0.6; }
.iq-top-navbar {
  top: 0 !important;
  left: 275px !important;
  right: 15px !important;
  width: auto !important;
  min-height: 50px !important;
  margin: 0 !important;
  padding: 0 !important;
  border-radius: 0 !important;
  overflow: visible !important;
}

.iq-navbar-custom {
  min-height: 50px !important;
  margin: 0 !important;
  padding: 0 !important;
  border-radius: 0 !important;
  overflow: visible !important;
}

.navbar {
  min-height: 50px !important;
  margin: 0 !important;
  padding-top: 0 !important;
  padding-bottom: 0 !important;
  overflow: visible !important;
}

.iq-menu-bt { display: none !important; }

.navbar-list {
  margin: 0 !important;
  padding: 0 !important;
  display: flex;
  align-items: center;
  overflow: visible !important;
}

.navbar-list li > a {
  min-height: 50px !important;
  line-height: 50px !important;
  padding-top: 0 !important;
  padding-bottom: 0 !important;
}

.navbar-list > li:last-child { margin-right: 2px !important; padding-right: 0 !important; }

/* ===== NOTIFICATION ===== */
.notif-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  margin-right: 14px;
}

.notif-bell-btn {
  position: relative;
  background: transparent;
  border: none;
  outline: none;
  cursor: pointer;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  transition: background 0.2s, color 0.2s;
}

.notif-bell-btn:hover, .notif-bell-btn.active {
  background: #f3f4f6;
  color: #2563eb;
}

.notif-bell-icon { font-size: 17px; }

.notif-badge {
  position: absolute;
  top: 2px;
  right: 2px;
  background: #ef4444;
  color: #fff;
  font-size: 9px;
  font-weight: 700;
  min-width: 16px;
  height: 16px;
  border-radius: 999px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 3px;
  line-height: 1;
  border: 1.5px solid #fff;
}

.notif-panel {
  position: absolute;
  right: -8px;
  top: calc(100% + 10px);
  width: 340px;
  max-height: 460px;
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.13), 0 1px 6px rgba(0,0,0,0.07);
  z-index: 9999;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  border: 1px solid #e5e7eb;
}

.notif-panel::before {
  content: '';
  position: absolute;
  top: -7px;
  right: 16px;
  width: 14px;
  height: 14px;
  background: #fff;
  border-left: 1px solid #e5e7eb;
  border-top: 1px solid #e5e7eb;
  transform: rotate(45deg);
  border-radius: 2px;
  z-index: 1;
}

.notif-panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px 12px;
  border-bottom: 1px solid #f1f3f5;
  flex-shrink: 0;
  background: #fff;
  position: relative;
  z-index: 2;
}

.notif-panel-title {
  font-size: 15px;
  font-weight: 700;
  color: #111827;
  display: flex;
  align-items: center;
  gap: 8px;
}

.notif-count-chip {
  background: #2563eb;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  border-radius: 999px;
  padding: 1px 7px;
  line-height: 1.6;
}

.notif-clear-btn {
  background: none;
  border: none;
  outline: none;
  cursor: pointer;
  font-size: 12px;
  color: #2563eb;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 6px;
  transition: background 0.15s;
}
.notif-clear-btn:hover { background: #eff6ff; }

.notif-list { overflow-y: auto; flex: 1; padding: 6px 0; }
.notif-list::-webkit-scrollbar { width: 4px; }
.notif-list::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

.notif-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 36px 20px;
  text-align: center;
}

.notif-empty-icon {
  width: 56px;
  height: 56px;
  background: #f3f4f6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  color: #9ca3af;
  margin-bottom: 12px;
}

.notif-empty-text { font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 4px; }
.notif-empty-sub { font-size: 12px; color: #9ca3af; }

.notif-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 11px 16px;
  cursor: pointer;
  transition: background 0.15s;
  border-bottom: 1px solid #f9fafb;
}
.notif-item:last-child { border-bottom: none; }
.notif-item:hover { background: #f8faff; }

.notif-item-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  flex-shrink: 0;
  background: #eff6ff;
  color: #2563eb;
}

.notif-type-success .notif-item-icon { background: #f0fdf4; color: #16a34a; }
.notif-type-danger .notif-item-icon { background: #fef2f2; color: #dc2626; }
.notif-type-warning .notif-item-icon { background: #fffbeb; color: #d97706; }

.notif-item-body { flex: 1; min-width: 0; }
.notif-item-message { font-size: 13px; color: #1f2937; line-height: 1.5; margin-bottom: 4px; }
.notif-item-time { font-size: 11px; color: #9ca3af; }

.notif-drop-enter-active { transition: opacity 0.18s ease, transform 0.18s ease; }
.notif-drop-leave-active { transition: opacity 0.14s ease, transform 0.14s ease; }
.notif-drop-enter-from { opacity: 0; transform: translateY(-8px) scale(0.97); }
.notif-drop-leave-to { opacity: 0; transform: translateY(-6px) scale(0.97); }

/* ===== USER CHIP ===== */
.admin-user-chip {
  gap: 6px;
  padding: 0 !important;
  margin: 0 !important;
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  text-decoration: none !important;
}

.admin-user-chip:hover, .admin-user-chip:focus, .admin-user-chip:focus-visible, .admin-user-chip:active {
  background: transparent !important;
  outline: none !important;
  box-shadow: none !important;
}

.admin-user-chip::before, .admin-user-chip::after { display: none !important; content: none !important; }

.admin-user-caption { display: inline-flex; align-items: baseline; gap: 4px; margin: 0; }
.admin-greeting { font-size: 11px; color: #9ca3af; font-weight: 500; line-height: 1; }
.admin-user-name { font-size: 12px; color: #374151; font-weight: 700; line-height: 1; }

.admin-avatar {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  object-fit: cover;
  border: 1px solid #ff7a45;
}

.admin-user-chip:focus, .admin-user-chip:focus-visible, .admin-user-chip:active,
.admin-user-chip *:focus, .admin-user-chip *:focus-visible, .admin-user-chip *:active {
  outline: none !important;
  box-shadow: none !important;
}

.admin-user-chip.iq-waves-effect .waves-ripple, .admin-user-chip .waves-ripple { display: none !important; }
</style>
