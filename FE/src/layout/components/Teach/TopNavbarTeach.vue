<template>
  <div class="iq-top-navbar">
    <div class="iq-navbar-custom">
      <nav class="navbar navbar-expand-lg navbar-light p-0">
        <div class="navbar-left">
          <div class="iq-search-bar d-none d-md-block">
            <form action="#" class="searchbox">
              <input type="text" class="text search-input" placeholder="Type here to search...">
              <a class="search-link" href="#"><i class="ri-search-line"></i></a>
              <div class="searchbox-datalink">
                <h6 class="pl-3 pt-3 pb-3">Pages</h6>
                <ul class="m-0 pl-3 pr-3 pb-3">
                  <li class="iq-bg-primary-hover rounded"><a href="index.html"
                      class="nav-link router-link-exact-active router-link-active pr-2"><i
                        class="ri-home-4-line pr-2"></i>Dashboard</a></li>
                  <li class="iq-bg-primary-hover rounded"><a href="dashboard-1.html"
                      class="nav-link router-link-exact-active router-link-active pr-2"><i
                        class="ri-home-3-line pr-2"></i>Dashboard-1</a></li>
                  <li class="iq-bg-primary-hover rounded"><a href="chat.html" class="nav-link"><i
                        class="ri-message-line pr-2"></i>Chat</a></li>
                  <li class="iq-bg-primary-hover rounded"><a href="calendar.html" class="nav-link"><i
                        class="ri-calendar-2-line pr-2"></i>Calendar</a></li>
                  <li class="iq-bg-primary-hover rounded"><a href="profile.html" class="nav-link"><i
                        class="ri-profile-line pr-2"></i>Profile</a></li>
                  <li class="iq-bg-primary-hover rounded"><a href="todo.html" class="nav-link"><i
                        class="ri-chat-check-line pr-2"></i>Todo</a></li>
                  <li class="iq-bg-primary-hover rounded"><a href="app/index.html" class="nav-link"><i
                        class="ri-mail-line pr-2"></i>Email</a></li>
                  <li class="iq-bg-primary-hover rounded"><a href="e-commerce-product-list.html" class="nav-link"><i
                        class="ri-message-line pr-2"></i>Product Listing</a></li>
                  <li class="iq-bg-primary-hover rounded"><a href="e-commerce-product-detail.html" class="nav-link"><i
                        class="ri-file-list-line pr-2"></i>Product Details</a></li>
                  <li class="iq-bg-primary-hover rounded"><a href="pages-faq.html" class="nav-link"><i
                        class="ri-compasses-line pr-2"></i>Faq</a></li>
                  <li class="iq-bg-primary-hover rounded"><a href="form-wizard.html" class="nav-link"><i
                        class="ri-clockwise-line pr-2"></i>Form-wizard</a></li>
                </ul>
              </div>
            </form>
          </div>
        </div>
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
          <li>
            <a href="#" class="search-toggle iq-waves-effect d-flex align-items-center bg-danger rounded">
              <img :src="user.avatarUrl" class="img-fluid rounded mr-3" alt="user" style="width:40px;height:40px;object-fit:cover;">
              <div class="caption">
                <h6 class="mb-0 line-height text-white">{{ user.name }}</h6>
                <span class="font-size-12 text-white">Giáo viên</span>
              </div>
            </a>
            <div class="iq-sub-dropdown iq-user-dropdown"
              style="border-radius: 15px !important; overflow: hidden !important; border: none !important; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;">
              <div class="iq-card shadow-none m-0">
                <div class="iq-card-body p-0">

                  <a href="/thong-tin-ca-nhan" class="iq-sub-card iq-bg-danger-hover">
                    <router-link to="/thong-tin-ca-nhan" class="nav-link">
                      <div class="media align-items-center">
                        <div class="rounded iq-card-icon iq-bg-danger">
                          <i class="ri-file-user-line"></i>
                        </div>
                        <div class="media-body ml-3">
                          <h6 class="mb-0">My Profile</h6>
                          <p class="mb-0 font-size-12">Xem thông tin chi tiết hồ sơ cá nhân.</p>
                        </div>
                      </div>
                    </router-link>
                  </a>

                  <div class="w-100 text-center p-3">
                    <a class="btn btn-danger" href="#" role="button" @click.prevent="dangXuat"
                      style="background-color: #dc3545 !important; border-color: #dc3545 !important; color: #ffffff !important; box-shadow: none !important; border-radius: 10px !important;">
                      Đăng xuất <i class="ri-login-box-line ml-2"></i>
                    </a>
                  </div>

                </div>
              </div>
            </div>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>
<script>
import axios from 'axios'

const ANH_MAC_DINH = '/Admin/images/user/1.jpg'
const PROFILE_LS_KEYS = ['ho_ten', 'email', 'check_token', 'ten_vai_tro', 'anh_dai_dien']

export default {
  data() {
    return {
      user: {},
    }
  },
  mounted() {
    this.dongBoUserTuLocal()
    window.addEventListener('storage', this.dongBoUserTuLocal)
    window.addEventListener('profile-updated', this.dongBoUserTuLocal)
  },
  beforeUnmount() {
    window.removeEventListener('storage', this.dongBoUserTuLocal)
    window.removeEventListener('profile-updated', this.dongBoUserTuLocal)
  },
  methods: {
    duongDanAnh(raw, macDinh) {
      if (!raw) {
        return macDinh
      }
      if (raw.startsWith('http://') || raw.startsWith('https://')) {
        return raw
      }
      const base = (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, '')
      return `${base}/storage/${String(raw).replace(/^\//, '')}`
    },
    dongBoUserTuLocal() {
      this.user = {
        name: localStorage.getItem('ho_ten') || 'Giáo viên',
        avatarUrl: this.duongDanAnh(localStorage.getItem('anh_dai_dien'), ANH_MAC_DINH),
      }
    },
    dangXuat() {
      const token = localStorage.getItem('token_teacher')
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
            localStorage.removeItem('token_teacher')
            PROFILE_LS_KEYS.forEach((k) => localStorage.removeItem(k))
            this.$toast.success(res.data.message)
            this.$router.push('/dang-nhap')
          } else {
            this.$toast.error('Có lỗi xảy ra')
          }
        })
        .catch(() => {
          localStorage.removeItem('token_teacher')
          PROFILE_LS_KEYS.forEach((k) => localStorage.removeItem(k))
          this.$router.push('/dang-nhap')
        })
    },
  },
}
</script>
<style></style>
