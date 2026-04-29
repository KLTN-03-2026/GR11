<template>
  <div>
    <div class="iq-sidebar">
      <div class="iq-sidebar-logo d-flex justify-content-between">
        <router-link to="/teacher/dashboard" class="d-flex align-items-center text-danger text-nowrap"
          style="font-size: 24px; text-decoration: none;">
          <i class="fa fa-book-reader fa-xl me-2" style="position: relative; top: -2px;"></i>
          <span class="m-0 fw-bold text-uppercase text-danger" style="font-size: 18px;">
            <b>TEACHER ECHOKIDS</b>
          </span>
        </router-link>
        <div class="iq-menu-bt-sidebar">
          <div class="iq-menu-bt align-self-center">
            <div class="wrapper-menu">
              <div class="main-circle">
                <i class="ri-arrow-left-s-line"></i>
              </div>
              <div class="hover-circle">
                <i class="ri-arrow-right-s-line"></i>
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
            <li :class="{ active: $route.path === '/teacher/quan-ly-hoc-sinh' }">
              <router-link to="/teacher/quan-ly-hoc-sinh" class="iq-waves-effect mt-1">
                <i class="fa-solid fa-user-graduate"></i>
                <span>Quản Lý Học Sinh</span>
              </router-link>
            </li>
            <li :class="{ active: $route.path === '/teacher/quan-ly-bai-hoc' }">
              <router-link to="/teacher/quan-ly-bai-hoc" class="iq-waves-effect mt-1">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>Quản Lý Bài Học</span>
              </router-link>
            </li>
            
            <li :class="{ active: $route.path === '/teacher/bao-cao-thong-ke' }">
              <router-link to="/teacher/bao-cao-thong-ke" class="iq-waves-effect mt-1">
                <i class="fa-solid fa-chart-line"></i>
                <span>Báo Cáo Thống Kê</span>
              </router-link>
            </li>
            <li :class="{ active: $route.path === '/teacher/chat-box' }">
              <router-link to="/teacher/chat-box" class="iq-waves-effect mt-1">
                <i class="fa-solid fa-comments"></i>
                <span>Tin nhắn</span>
                <span v-if="unreadCount > 0" class="chat-unread-badge">
                  {{ unreadCount > 99 ? '99+' : unreadCount }}
                </span>
              </router-link>
            </li>

          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      unreadCount: 0,
      echoChannelName: null,
    }
  },
  mounted() {
    this.fetchUnreadCount()
    this.subscribeRealtimeUnread()
  },
  beforeUnmount() {
    this.unsubscribeRealtimeUnread()
  },
  methods: {
    fetchUnreadCount() {
      const token = localStorage.getItem('token_teacher')
      if (!token) {
        this.unreadCount = 0
        return
      }

      axios
        .get('http://127.0.0.1:8000/api/teacher/chat/unread-count', {
          headers: { Authorization: 'Bearer ' + token },
        })
        .then((res) => {
          this.unreadCount = Number(res?.data?.unread_count || 0)
        })
        .catch(() => {
          this.unreadCount = 0
        })
    },
    subscribeRealtimeUnread() {
      const token = localStorage.getItem('token_teacher')
      if (!token || !window.Echo) {
        return
      }

      axios
        .get('http://127.0.0.1:8000/api/user', {
          headers: { Authorization: 'Bearer ' + token },
        })
        .then((res) => {
          const teacherId = Number(res?.data?.id || 0)
          if (!teacherId || !window.Echo) {
            return
          }

          this.echoChannelName = `teacher.${teacherId}`
          window.Echo.private(this.echoChannelName).listen('.StudentSentMessage', () => {
            this.unreadCount += 1
          })
        })
        .catch(() => {})
    },
    unsubscribeRealtimeUnread() {
      if (window.Echo && this.echoChannelName) {
        window.Echo.leave(`private-${this.echoChannelName}`)
      }
      this.echoChannelName = null
    },
  },
}
</script>

<style scoped>
.chat-unread-badge {
  margin-left: auto;
  min-width: 20px !important;
  height: 20px !important;
  border-radius: 999px;
  background: #f9f8f8;
  color: #730c0c;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  text-align: center !important;
  padding: 0 5px !important;
  box-sizing: border-box !important;
  font-size: 17px;
  font-weight: 700;
  letter-spacing: 0 !important;
  text-indent: 0 !important;
  line-height: normal !important;
}
</style>
