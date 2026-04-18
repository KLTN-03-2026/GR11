<template>
    <div 
      class="register-page d-flex align-items-center justify-content-center py-5 min-vh-100 position-relative overflow-hidden" 
      style="background: linear-gradient(to bottom, #fff8f4, #fff1eb);"
    >
      <div class="position-absolute rounded-circle" style="width: 260px; height: 260px; background: rgba(255, 107, 53, 0.08); top: -80px; left: -80px; animation: float 8s ease-in-out infinite;"></div>
      <div class="position-absolute rounded-circle" style="width: 160px; height: 160px; background: rgba(255, 193, 7, 0.12); right: 80px; top: 100px; animation: float 6s ease-in-out infinite;"></div>
      <div class="position-absolute rounded-circle" style="width: 220px; height: 220px; background: rgba(32, 201, 151, 0.08); bottom: -80px; right: -60px; animation: float 10s ease-in-out infinite;"></div>
  
      <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center align-items-center">
          
          <div class="col-lg-6 mb-1 mb-lg-0 pe-lg-5 text-center text-lg-start">
            <span 
              class="badge rounded-pill px-4 py-2 mb-3 shadow-sm border" 
              style="background: #fff0e8; color: #ff6b35; font-size: 1rem; border-color: #ffd8c8 !important;"
            >
              ✨ Tạo Tài Khoản Cùng EchoKids
            </span>
  
            <h1 
              class="display-4 fw-bold mb-3" 
              style="color: #0d3b66; font-family: 'Lobster Two', cursive;"
            >
              Bắt Đầu Hành Trình Học Tập Cùng EchoKids
            </h1>
  
            <p class="text-secondary fs-5 mb-4">
              Tạo tài khoản để luyện phát âm tiếng Việt, nhận huy hiệu và khám phá những bài học vui nhộn mỗi ngày.
            </p>
  
            <div class="row g-3 mb-4">
              <div v-for="stat in stats" :key="stat.label" class="col-4">
                <div 
                  class="bg-white bg-opacity-75 rounded-4 p-3 shadow-sm border-2 border text-center"
                  style="transition: transform 0.3s;"
                  onmouseover="this.style.transform='translateY(-5px)'"
                  onmouseout="this.style.transform='translateY(0)'"
                  :style="{ borderColor: stat.color + '33' }"
                >
                  <h3 class="fw-bold mb-1" :style="{ color: stat.color }">{{ stat.value }}</h3>
                  <small class="text-muted fw-bold small text-uppercase">{{ stat.label }}</small>
                </div>
              </div>
            </div>
  
            <div class="d-flex gap-3 flex-wrap justify-content-center justify-content-lg-start">
              <div v-for="feat in ['🎤 Ghi Âm', '🤖 AI Chấm Điểm', '🏆 Huy Hiệu']" :key="feat"
                class="bg-white px-3 py-2 rounded-pill shadow-sm fw-bold small" style="color: #0d3b66;">
                {{ feat }}
              </div>
            </div>
          </div>
  
          <div class="col-lg-6 col-xl-5">
            <div 
              class="card border-0 shadow-lg p-3 p-md-4 position-relative overflow-hidden" 
              style="border-radius: 36px; background: rgba(255, 255, 255, 0.94); backdrop-filter: blur(12px);"
            >
              <div class="position-absolute rounded-circle" style="width: 100px; height: 100px; background: rgba(255, 107, 53, 0.05); top: -30px; right: -30px;"></div>
  
              <div class="text-center mb-1 position-relative">
                <div 
                  class="mx-auto mb-1 d-flex align-items-center justify-content-center rounded-circle shadow" 
                  style="width: 70px; height: 70px; background: linear-gradient(135deg, #ffb18f, #ffd6c7); color: white; font-size: 28px;"
                >
                  <i class="fa fa-user-plus"></i>
                </div>
                <h2 class="fw-bold mb-1" style="color: #0d3b66">Đăng Ký</h2>
                <p class="">Tạo tài khoản để bắt đầu học cùng EchoKids</p>
              </div>
  
              <form @submit.prevent="xuLyDangKy">
                <div class="row g-2">
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary ms-2">Họ Và Tên</label>
                    <div class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm" style="border-color: #ffe5d9 !important;">
                      <span class="input-group-text bg-transparent border-0"><i class="fa fa-user text-warning"></i></span>
                      <input v-model.trim="form.ho_ten" type="text" class="form-control bg-transparent border-0 shadow-none" placeholder="Nhập họ tên" autocomplete="name">
                    </div>
                  </div>
  
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary ms-2">Ngày Sinh</label>
                    <div class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm" style="border-color: #ffe5d9 !important;">
                      <span class="input-group-text bg-transparent border-0"><i class="fa fa-calendar text-warning"></i></span>
                      <input v-model="form.ngay_sinh" type="date" class="form-control bg-transparent border-0 shadow-none text-muted">
                    </div>
                  </div>
  
                  <div class="col-12">
                    <label class="form-label fw-bold text-secondary ms-2">Email</label>
                    <div class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm" style="border-color: #ffe5d9 !important;">
                      <span class="input-group-text bg-transparent border-0"><i class="fa fa-envelope text-warning"></i></span>
                      <input v-model.trim="form.email" type="email" class="form-control bg-transparent border-0 shadow-none" placeholder="example@email.com" autocomplete="email">
                    </div>
                  </div>
  
                  <div class="col-12">
                    <label class="form-label fw-bold text-secondary ms-2">Số điện thoại <span class="text-muted fw-normal">(tùy chọn)</span></label>
                    <div class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm" style="border-color: #ffe5d9 !important;">
                      <span class="input-group-text bg-transparent border-0"><i class="fa fa-phone text-warning"></i></span>
                      <input v-model.trim="form.sdt" type="tel" maxlength="10" class="form-control bg-transparent border-0 shadow-none" placeholder="0xxxxxxxxx" autocomplete="tel">
                    </div>
                  </div>
  
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary ms-2">Mật Khẩu</label>
                    <div class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm" style="border-color: #ffe5d9 !important;">
                      <span class="input-group-text bg-transparent border-0"><i class="fa fa-lock text-warning"></i></span>
                      <input
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        class="form-control bg-transparent border-0 shadow-none"
                        placeholder="Nhập mật khẩu"
                        autocomplete="new-password"
                      >
                      <span class="input-group-text bg-transparent border-0" style="cursor: pointer" @click="showPassword = !showPassword">
                        <i :class="showPassword ? 'fa fa-eye' : 'fa fa-eye-slash'" class="text-secondary"></i>
                      </span>
                    </div>
                  </div>
  
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary ms-2">Nhập Lại Mật Khẩu</label>
                    <div class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm" style="border-color: #ffe5d9 !important;">
                      <span class="input-group-text bg-transparent border-0"><i class="fa fa-key text-warning"></i></span>
                      <input
                        v-model="form.password_confirmation"
                        :type="showPasswordConfirm ? 'text' : 'password'"
                        class="form-control bg-transparent border-0 shadow-none"
                        placeholder="Nhập lại mật khẩu"
                        autocomplete="new-password"
                      >
                      <span class="input-group-text bg-transparent border-0" style="cursor: pointer" @click="showPasswordConfirm = !showPasswordConfirm">
                        <i :class="showPasswordConfirm ? 'fa fa-eye' : 'fa fa-eye-slash'" class="text-secondary"></i>
                      </span>
                    </div>
                  </div>
                  <div class="col-12">
                     <small class="text-muted ms-1 d-block">Mật khẩu: chữ thường, chữ in hoa và ít nhất một chữ số (tối thiểu 6 ký tự).</small>
                  </div>
                </div>
  
                <div class="form-check mt-3 mb-4 ms-1">
                  <input v-model="dongYDieuKhoan" class="form-check-input shadow-none" type="checkbox" id="terms">
                  <label class="form-check-label text-muted" for="terms" style="cursor: pointer;">
                    Tôi đồng ý với <a href="#" class="text-decoration-none fw-bold" style="color: #ff6b35;">điều khoản</a> của EchoKids
                  </label>
                </div>
  
                <button 
                  type="submit" 
                  class="btn w-100 rounded-pill py-3 fw-bold border-0 text-white shadow mb-3"
                  style="background: linear-gradient(135deg, #ff6b35, #ff914d); transition: 0.3s;"
                  onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 20px rgba(255,107,53,0.3)'"
                  onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'"
                  :disabled="dangGui"
                >
                  <span v-if="dangGui"><i class="fa fa-spinner fa-spin me-2"></i> Đang tạo tài khoản...</span>
                  <span v-else>Tạo Tài Khoản</span>
                </button>
  
                <div class="text-center">
                  <span class="text-muted">Đã có tài khoản?</span>
                  <router-link to="/dang-nhap" class="ms-1 fw-bold text-decoration-none" style="color: #ff6b35;">Đăng nhập ngay</router-link>
                </div>
              </form>
            </div>
          </div>
  
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  
  const REGEX_MAT_KHAU_MANH = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/
  
  export default {
    data() {
      return {
        dangGui: false,
        dongYDieuKhoan: false,
        showPassword: false,
        showPasswordConfirm: false,
        stats: [
          { value: '10K+', label: 'Bé học', color: '#ff6b35' },
          { value: '500+', label: 'Bài học', color: '#20c997' },
          { value: '98%', label: 'Hài lòng', color: '#0d6efd' },
        ],
        form: {
          ho_ten: '',
          email: '',
          password: '',
          password_confirmation: '',
          sdt: '',
          ngay_sinh: '',
        },
      }
    },
    methods: {
      xuLyDangKy() {
        if (!this.dongYDieuKhoan) {
          this.$toast.error('Vui lòng đồng ý điều khoản sử dụng.')
          return
        }
        if (this.form.password !== this.form.password_confirmation) {
          this.$toast.error('Mật khẩu nhập lại không khớp.')
          return
        }
        if (!REGEX_MAT_KHAU_MANH.test(this.form.password)) {
          this.$toast.error(
            'Mật khẩu phải có ít nhất 6 ký tự, gồm chữ thường, chữ in hoa và ít nhất một chữ số.'
          )
          return
        }
        const sdtSo = this.form.sdt.replace(/\D/g, '').slice(0, 10)
        const payload = {
          ho_ten: this.form.ho_ten,
          email: this.form.email,
          password: this.form.password,
          password_confirmation: this.form.password_confirmation,
        }
        if (this.form.ngay_sinh) {
          payload.ngay_sinh = this.form.ngay_sinh
        }
        if (sdtSo) {
          payload.sdt = sdtSo
        }
        this.dangGui = true
        axios
          .post(`http://127.0.0.1:8000/api/dang-ky`, payload)
          .then((res) => {
            if (res.data.status) {
              this.$toast.success(res.data.message)
              this.form = {
                ho_ten: '',
                email: '',
                password: '',
                password_confirmation: '',
                sdt: '',
                ngay_sinh: '',
              }
              this.dongYDieuKhoan = false
              this.$router.push('/dang-nhap')
            } else {
              this.$toast.error(res.data.message || 'Đăng ký thất bại.')
            }
          })
          .catch((err) => {
            const errors = err.response?.data?.errors
            if (errors) {
              Object.values(errors).forEach((msg) => {
                this.$toast.error(Array.isArray(msg) ? msg[0] : msg)
              })
            } else {
              this.$toast.error(err.response?.data?.message || 'Đăng ký thất bại. Vui lòng thử lại.')
            }
          })
          .finally(() => {
            this.dangGui = false
          })
      },
    },
  }
  </script>
  
  <style scoped>
  @keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
  }
  
  .register-page {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  .input-group:focus-within {
    border-color: #ffb18f !important;
    background-color: #fff !important;
  }
  
  input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    filter: invert(48%) sepia(89%) saturate(1831%) hue-rotate(345deg) brightness(101%) contrast(101%);
  }
  </style>