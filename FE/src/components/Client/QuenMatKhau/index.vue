<template>
    <div 
      class="forgot-page d-flex align-items-center justify-content-center position-relative overflow-hidden" 
      style="background: linear-gradient(to bottom, #fff8f4, #fff1eb); min-height: 100vh;"
    >
      <div class="position-absolute rounded-circle" style="width: 260px; height: 260px; background: rgba(255,107,53,0.08); top: -80px; left: -80px; animation: float 8s ease-in-out infinite;"></div>
      <div class="position-absolute rounded-circle" style="width: 160px; height: 160px; background: rgba(255,193,7,0.12); right: 80px; top: 100px; animation: float 6s ease-in-out infinite;"></div>
      <div class="position-absolute rounded-circle" style="width: 220px; height: 220px; background: rgba(32,201,151,0.08); bottom: -80px; right: -60px; animation: float 10s ease-in-out infinite;"></div>
  
      <div class="container position-relative" style="z-index: 5;">
        <div class="row justify-content-center">
          <div class="col-lg-6 col-md-8 col-sm-11">
            <div 
              class="card border-0 shadow-lg p-4 position-relative overflow-hidden" 
              style="border-radius: 32px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);"
            >
              <div class="position-absolute rounded-circle" style="width: 90px; height: 90px; background: rgba(255,107,53,0.06); top: -20px; right: -20px;"></div>
              <div class="position-absolute rounded-circle" style="width: 70px; height: 70px; background: rgba(255,193,7,0.08); bottom: -15px; left: -15px;"></div>
  
              <div class="text-center mb-2 position-relative">
                <div 
                  class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow" 
                  style="width: 70px; height: 70px; background: linear-gradient(135deg, #ffb18f, #ffd6c7); color: white; font-size: 28px;"
                >
                  <i class="fa fa-envelope-open-text"></i>
                </div>
                <h2 class="fw-bold" style="color: #0d3b66;">Xác Nhận Email</h2>
                <p class="">Nhập email để nhận mã xác thực đặt lại mật khẩu</p>
              </div>
  
              <form @submit.prevent="datLaiMatKhau">
                <div class="mb-2">
                  <label class="form-label fw-bold text-secondary ms-2">Email Đã Đăng Ký</label>
                  <div class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm" style="border-color: #ffe5d9 !important;">
                    <span class="input-group-text bg-transparent border-0"><i class="fa fa-envelope text-warning"></i></span>
                    <input v-model="payload.email" type="email" class="form-control bg-transparent border-0 shadow-none" placeholder="Nhập email của bạn" required>
                  </div>
                </div>
  
                <div class="p-3 mb-4 rounded-4" style="background: #fff8f4; border: 2px dashed #ffd8c8;">
                  <label class="form-label fw-bold text-secondary mb-3 d-block text-center">Nhập Mã Xác Nhận</label>
                  <div class="d-flex justify-content-center gap-2">
                    <input 
                      v-for="(val, index) in otpArray" 
                      :key="index" 
                      v-model="otpArray[index]"
                      ref="otpInputs"
                      @input="focusNext(index, $event)"
                      @keydown.delete="focusPrev(index, $event)"
                      type="text" 
                      maxlength="1" 
                      class="form-control text-center fw-bold shadow-none" 
                      style="width: 40px; height: 50px; border-radius: 12px; border: 2px solid #ffe0d1; color: #0d3b66;"
                    >
                  </div>
                  <div class="text-center mt-3">
                    <span v-if="isSendingOTP" class="fw-bold text-secondary">
                      <i class="fa fa-spinner fa-spin me-1"></i> Đang gửi...
                    </span>
                    <span 
                      v-else-if="otpSecondsLeft > 0"
                      class="fw-bold text-secondary d-block"
                    >
                      Mã có hiệu lực trong
                      <span class="text-danger">{{ countdownDisplay }}</span>
                    </span>
                    <a
                      v-else
                      href="#"
                      class="fw-bold text-decoration-none"
                      style="color: #ff6b35;"
                      @click.prevent="guiMaXacNhan"
                    >
                      {{ hasRequestedOtp ? 'Gửi lại mã' : 'Gửi mã xác nhận' }}
                    </a>
                  </div>
                </div>
  
                <div class="mb-3">
                  <label class="form-label fw-bold text-secondary ms-2">Mật Khẩu Mới</label>
                  <div class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm" style="border-color: #ffe5d9 !important;">
                    <span class="input-group-text bg-transparent border-0"><i class="fa fa-lock text-warning"></i></span>
                    <input
                      v-model="payload.new_password"
                      :type="showPasswordMoi ? 'text' : 'password'"
                      class="form-control bg-transparent border-0 shadow-none"
                      placeholder="Ít nhất 6 ký tự, có hoa, thường và số"
                      required
                    >
                    <span class="input-group-text bg-transparent border-0" style="cursor: pointer" @click="showPasswordMoi = !showPasswordMoi">
                      <i :class="showPasswordMoi ? 'fa fa-eye' : 'fa fa-eye-slash'" class="text-secondary"></i>
                    </span>
                  </div>
                  <small class="ms-2 d-block mt-1">Mật khẩu: chữ thường, chữ in hoa và ít nhất một chữ số (tối thiểu 6 ký tự).</small>
                </div>
  
                <div class="mb-4">
                  <label class="form-label fw-bold text-secondary ms-2">Xác Nhận Mật Khẩu</label>
                  <div class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm" style="border-color: #ffe5d9 !important;">
                    <span class="input-group-text bg-transparent border-0"><i class="fa fa-key text-warning"></i></span>
                    <input
                      v-model="payload.confirm_password"
                      :type="showPasswordXacNhan ? 'text' : 'password'"
                      class="form-control bg-transparent border-0 shadow-none"
                      placeholder="Nhập lại mật khẩu"
                      required
                    >
                    <span class="input-group-text bg-transparent border-0" style="cursor: pointer" @click="showPasswordXacNhan = !showPasswordXacNhan">
                      <i :class="showPasswordXacNhan ? 'fa fa-eye' : 'fa fa-eye-slash'" class="text-secondary"></i>
                    </span>
                  </div>
                </div>
  
                <button 
                  type="submit" 
                  class="btn w-100 rounded-pill py-3 fw-bold text-white shadow-sm mb-4 border-0" 
                  style="background: linear-gradient(135deg, #ff6b35, #ff914d);"
                  :disabled="isLoading"
                >
                  <span v-if="isLoading"><i class="fa fa-spinner fa-spin me-2"></i> Đang xử lý...</span>
                  <span v-else>Đặt Lại Mật Khẩu</span>
                </button>
  
                <div class="text-center">
                  <router-link to="/dang-nhap" class="text-decoration-none fw-bold" style="color: #ff6b35;">
                    <i class="fa fa-arrow-left me-1"></i> Quay Lại Đăng Nhập
                  </router-link>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
import { API_BASE } from '../../../api/http.js';

  import axios from 'axios';
  
  /** Khớp rule backend: chữ thường (a-z), chữ in hoa (A-Z), số, tối thiểu 6 ký tự */
  const REGEX_MAT_KHAU_MANH = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/
  /** Đồng bộ với backend resetPassword: mã OTP 5 phút */
  const OTP_HIEU_LUC_GIAY = 5 * 60
  
  export default {
    data() {
      return {
        payload: {
          email: "",
          new_password: "",
          confirm_password: ""
        },
        otpArray: ["", "", "", "", "", ""],
        isSendingOTP: false,
        isLoading: false,
        otpSecondsLeft: 0,
        hasRequestedOtp: false,
        otpIntervalId: null,
        showPasswordMoi: false,
        showPasswordXacNhan: false,
      }
    },
    computed: {
      countdownDisplay() {
        const s = this.otpSecondsLeft
        const m = Math.floor(s / 60)
        const sec = s % 60
        return `${String(m).padStart(2, '0')}:${String(sec).padStart(2, '0')}`
      },
    },
    beforeUnmount() {
      this.clearOtpCountdown()
    },
    methods: {
      clearOtpCountdown() {
        if (this.otpIntervalId != null) {
          clearInterval(this.otpIntervalId)
          this.otpIntervalId = null
        }
      },
      batDauDemNguocOtp() {
        this.clearOtpCountdown()
        this.otpSecondsLeft = OTP_HIEU_LUC_GIAY
        this.otpIntervalId = setInterval(() => {
          if (this.otpSecondsLeft <= 1) {
            this.otpSecondsLeft = 0
            this.clearOtpCountdown()
          } else {
            this.otpSecondsLeft -= 1
          }
        }, 1000)
      },
      // Tự động nhảy sang ô tiếp theo khi nhập OTP
      focusNext(index, event) {
        // Chỉ lấy số
        this.otpArray[index] = event.target.value.replace(/[^0-9]/g, '');
        
        if (this.otpArray[index] && index < 5) {
          this.$refs.otpInputs[index + 1].focus();
        }
      },
      // Tự động lùi về ô trước đó khi bấm nút xóa (Backspace)
      focusPrev(index, event) {
        if (!event.target.value && index > 0) {
          this.$refs.otpInputs[index - 1].focus();
        }
      },
  
      // Hàm gọi API gửi mã về email
      guiMaXacNhan() {
        if (!this.payload.email) {
          this.$toast.error("Vui lòng nhập email của bạn trước!");
          return;
        }
  
        this.isSendingOTP = true;
        axios
          .post(`${API_BASE}/api/quen-mat-khau`, { email: this.payload.email })
          .then((res) => {
            if (res.data.status) {
              this.$toast.success(res.data.message);
              this.hasRequestedOtp = true;
              this.batDauDemNguocOtp();
            } else {
              this.$toast.error(res.data.message);
            }
          })
          .catch((res) => {
            const data = res.response?.data;
            if (data?.errors) {
              Object.values(data.errors).forEach((v) =>
                this.$toast.error(Array.isArray(v) ? v[0] : v)
              );
            } else {
              this.$toast.error(
                data?.message || "Lỗi kết nối máy chủ. Vui lòng thử lại."
              );
            }
          })
          .finally(() => {
            this.isSendingOTP = false;
          });
      },
  
      // Hàm gọi API đổi mật khẩu
      datLaiMatKhau() {
        if (this.payload.new_password !== this.payload.confirm_password) {
          this.$toast.error("Mật khẩu xác nhận không trùng khớp!");
          return;
        }
        if (!REGEX_MAT_KHAU_MANH.test(this.payload.new_password)) {
          this.$toast.error(
            "Mật khẩu phải có ít nhất 6 ký tự, gồm chữ thường, chữ in hoa và ít nhất một chữ số."
          );
          return;
        }
  
        // Gom 6 ô OTP lại thành 1 chuỗi
        const otpCode = this.otpArray.join('');
        if (otpCode.length < 6) {
          this.$toast.error("Vui lòng nhập đủ 6 số mã xác nhận!");
          return;
        }
  
        this.isLoading = true;
        
        // 3. Chuẩn bị dữ liệu gửi đi
        const dataToSend = {
          email: this.payload.email,
          otp: otpCode,
          new_password: this.payload.new_password,
          new_password_confirmation: this.payload.confirm_password,
        };
  
        axios
          .post(`${API_BASE}/api/dat-lai-mat-khau`, dataToSend)
          .then((res) => {
            if (res.data.status) {
              this.clearOtpCountdown();
              this.otpSecondsLeft = 0;
              this.$toast.success(res.data.message);
              setTimeout(() => {
                this.$router.push('/dang-nhap');
              }, 1500);
            } else {
              const msg = res.data.message || '';
              if (msg.includes('hết hạn') || msg.includes('5 phút')) {
                this.clearOtpCountdown();
                this.otpSecondsLeft = 0;
              }
              this.$toast.error(msg || 'Đặt lại mật khẩu thất bại.');
            }
          })
          .catch((res) => {
            const msg = res.response?.data?.message || '';
            if (msg.includes('hết hạn') || msg.includes('5 phút')) {
              this.clearOtpCountdown();
              this.otpSecondsLeft = 0;
            }
            if (res.response?.data?.errors) {
              const list = Object.values(res.response.data.errors);
              list.forEach((v) => this.$toast.error(Array.isArray(v) ? v[0] : v));
            } else {
              this.$toast.error(msg || 'Không thể đặt lại mật khẩu. Vui lòng thử lại.');
            }
          })
          .finally(() => {
            this.isLoading = false;
          });
      }
    }
  }
  </script>
  
  <style scoped>
  @keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
  }
  
  .input-group:focus-within {
    border-color: #ffb18f !important;
    box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.08) !important;
  }
  
  input:focus {
    border-color: #ff6b35 !important;
    outline: none;
  }
  
  @media (max-width: 576px) {
    .form-control {
      width: 35px !important;
      height: 45px !important;
      font-size: 20px;
    }
  }
  </style>