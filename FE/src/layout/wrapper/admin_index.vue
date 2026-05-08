<template>
  <div class="wrapper">
    <!-- Layout -->
    <SidebarAdmin />
    <TopNavbarAdmin />
    <div class="content-page">
      <div class="container-fluid">
        <router-view></router-view>
      </div>
    </div>
    <FooterAdmin />
    <!-- Float Menu -->
    <nav class="iq-float-menu">
      <input type="checkbox" class="iq-float-menu-open" id="menu-open" />

      <label class="iq-float-menu-open-button" for="menu-open">
        <span class="lines line-1"></span>
        <span class="lines line-2"></span>
        <span class="lines line-3"></span>
      </label>

      <button class="iq-float-menu-item bg-info" title="Direction Mode">
        <i class="ri-text-direction-r"></i>
      </button>

      <button class="iq-float-menu-item bg-danger" id="dark-mode">
        <i class="ri-sun-line"></i>
      </button>

      <button class="iq-float-menu-item bg-warning">
        <i class="ri-palette-line"></i>
      </button>
    </nav>
  </div>
</template>
<script>
import SidebarAdmin from "../components/Admin/SidebarAdmin.vue";
import TopNavbarAdmin from "../components/Admin/TopNavbarAdmin.vue";
import FooterAdmin from "../components/Admin/FooterAdmin.vue";

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
  name: "AdminLayout",

  components: {
    SidebarAdmin,
    TopNavbarAdmin,
    FooterAdmin
  },

  mounted() {
    // fix jQuery global (quan trọng)
    window.$ = window.jQuery;
  }
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