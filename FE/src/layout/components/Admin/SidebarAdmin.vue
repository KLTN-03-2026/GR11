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
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
export default {};
</script>