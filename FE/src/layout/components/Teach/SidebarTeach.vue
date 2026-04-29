<template>
  <div>
    <div class="iq-sidebar">
      <div class="iq-sidebar-logo d-flex justify-content-between">
        <a href="/admin/dashboard" class="d-flex align-items-center text-danger text-nowrap"
          style="font-size: 24px; text-decoration: none;">
          <i class="fa fa-book-reader fa-xl me-2" style="position: relative; top: -2px;"></i>
          <span class="m-0 fw-bold text-uppercase text-danger" style="font-size: 18px;">
            <b>TEACHER ECHOKIDS</b>
          </span>
        </a>
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
              </router-link>
            </li>

          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
export default {};
</script>
