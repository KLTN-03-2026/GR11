<template>
  <div class="wrapper">
    <SidebarTeach />
    <TopNavbarTeach />
    <div class="content-page">
      <div class="mt-2">
        <router-view :key="$route.fullPath" />
      </div>
    </div>
    <FooterTeach />
    <ChatBox
      v-if="$route.path !== '/teacher/chat-box'"
      mode="teacher"
      :floating="true"
    />
  </div>
</template>

<script>
import SidebarTeach from '../components/Teach/SidebarTeach.vue'
import TopNavbarTeach from '../components/Teach/TopNavbarTeach.vue'
import FooterTeach from '../components/Teach/FooterTeach.vue'
import ChatBox from '../../components/Client/ChatBox/index.vue'

// ===== CORE =====
import "../../../public/Admin/js/jquery.min.js";
import "../../../public/Admin/js/popper.min.js";
import "../../../public/Admin/js/bootstrap.min.js";

// ===== UI =====
import "../../../public/Admin/js/jquery.appear.js";
import "../../../public/Admin/js/wow.min.js";
import "../../../public/Admin/js/slick.min.js";
import "../../../public/Admin/js/select2.min.js";
import "../../../public/Admin/js/jquery.magnific-popup.min.js";
import "../../../public/Admin/js/smooth-scrollbar.js";
// ===== CHART =====
import "../../../public/Admin/js/apexcharts.js";
import "../../../public/Admin/js/morris.js";
import "../../../public/Admin/js/chartist/chartist.min.js";

// ===== AMCHART =====
import "../../../public/Admin/js/core.js";
import "../../../public/Admin/js/charts.js";
import "../../../public/Admin/js/animated.js";
import "../../../public/Admin/js/kelly.js";
import "../../../public/Admin/js/maps.js";
import "../../../public/Admin/js/worldLow.js";

// ===== EFFECT =====
import "../../../public/Admin/js/countdown.min.js";
import "../../../public/Admin/js/waypoints.min.js";
import "../../../public/Admin/js/jquery.counterup.min.js";
import "../../../public/Admin/js/lottie.js";

// ===== SYSTEM =====
import "../../../public/Admin/js/rtl.js";
import "../../../public/Admin/js/customizer.js";

// ===== CUSTOM =====
import "../../../public/Admin/js/chart-custom.js";
import "../../../public/Admin/js/custom.js";

export default {
  name: "app",

  components: {
    SidebarTeach,
    TopNavbarTeach,
    FooterTeach,
    ChatBox
  },

  mounted() {
    // fix jQuery global (quan trọng)
    window.$ = window.jQuery;
    this.khoiTaoRealtime();
  },

  beforeUnmount() {
    this.dongKenh();
  },

  methods: {
    layGiaoVienId() {
      // ID giáo viên lưu trong localStorage sau khi đăng nhập
      return parseInt(localStorage.getItem('nguoi_dung_id') || '0', 10);
    },

    khoiTaoRealtime() {
      if (!window.Echo) return;
      const giaoVienId = this.layGiaoVienId();
      if (!giaoVienId) return;

      // Cập nhật auth header với token giáo viên hiện tại
      const token = localStorage.getItem('token_teacher') || '';
      if (token && window.Echo.connector?.pusher) {
        window.Echo.connector.pusher.config.auth = {
          headers: { Authorization: 'Bearer ' + token },
        };
      }

      // Lắng nghe channel private 'teacher.{id}'
      this._teacherChannel = window.Echo.private(`teacher.${giaoVienId}`);
      this._teacherChannel.listen('.AdminDuyetBaiHoc', (data) => {
        // Phát sự kiện nội bộ để trang quản lý bài học có thể tự reload
        window.dispatchEvent(new CustomEvent('bai-hoc-duoc-duyet', { detail: data }));
      });
    },

    dongKenh() {
      const giaoVienId = this.layGiaoVienId();
      if (window.Echo && giaoVienId) {
        try { window.Echo.leave(`teacher.${giaoVienId}`); } catch (_) {}
      }
    },
  },
};
</script>
<style>
/* ===== CSS ===== */
@import "../../../public/Admin/css/bootstrap.min.css";
@import "../../../public/Admin/js/chartist/chartist.min.css";
@import "../../../public/Admin/css/typography.css";
@import "../../../public/Admin/css/style.css";
@import "../../../public/Admin/css/responsive.css";

/* ===== ICON ===== */
@import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css");
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css");

.content-page {
  margin-left: 260px !important;
  padding: 59px 18px 0 !important;
  transition: all 0.35s ease !important;
}

.iq-top-navbar {
  border-bottom: 1px solid #e5e7eb !important;
  transition: all 0.35s ease !important;
}

.iq-sidebar {
  width: 260px !important;
  transition: all 0.35s ease !important;
}

body.sidebar-main .iq-sidebar {
  width: 80px !important;
}

body.sidebar-main .content-page {
  margin-left: 80px !important;
}

body.sidebar-main .iq-top-navbar {
  left: 95px !important;
}

body.sidebar-main .iq-sidebar .iq-sidebar-logo span,
body.sidebar-main .iq-sidebar .iq-menu li a span {
  display: none !important;
}

body.sidebar-main .iq-sidebar .iq-sidebar-logo a {
  justify-content: center !important;
}

body.sidebar-main .iq-sidebar .iq-menu li a {
  justify-content: center !important;
  padding-left: 0 !important;
  padding-right: 0 !important;
}
</style>