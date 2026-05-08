<template>
  <div class="login-page d-flex align-items-center py-5 justify-content-center position-relative overflow-hidden"
    style="background-color: #fff9f5;">
    <div class="position-absolute rounded-circle"
      style="width: 300px; height: 250px; background: rgba(255,107,53,0.08); top: -80px; left: -80px; animation: float 8s ease-in-out infinite;">
    </div>
    <div class="position-absolute rounded-circle"
      style="width: 180px; height: 180px; background: rgba(255,193,7,0.12); right: 80px; top: 100px; animation: float 6s ease-in-out infinite;">
    </div>
    <div class="position-absolute rounded-circle"
      style="width: 250px; height: 250px; background: rgba(32,201,151,0.08); bottom: -80px; right: -60px; animation: float 10s ease-in-out infinite;">
    </div>

    <div class="container position-relative" style="z-index: 10;">
      <div class="row justify-content-center align-items-center">
        <div class="col-lg-6 mb-5 mb-lg-0 pe-lg-5">
          <span class="badge rounded-pill px-4 mb-4 shadow-sm"
            style="background: #fff0e8; color: #ff6b35; font-size: 1.1rem; border: 1px solid #ffd8c8;">
            ✨ Chào Mừng Đến Với EchoKids
          </span>

          <h1 class="display-3 fw-bold mb-4" style="color: #0d3b66; font-family: 'Lobster Two', cursive;">
            Cùng EchoKids Khám Phá Mỗi Ngày
          </h1>

          <p class="text-secondary fs-5 mb-5">
            Luyện phát âm tiếng Việt với hình ảnh sinh động, ghi âm trực tiếp và AI chấm điểm vui nhộn.
          </p>

          <div class="row g-3 mb-5">
            <div class="col-4" v-for="(stat, index) in stats" :key="index">
              <div class="p-3 shadow-sm rounded-4 text-center border-2 border bg-white"
                :style="{ borderColor: stat.color + '44', transition: '0.3s' }">
                <h3 class="fw-bold mb-1" :style="{ color: stat.color }">{{ stat.value }}</h3>
                <small class="fw-bold text-uppercase" style="font-size: 0.75rem;">{{ stat.label }}</small>
              </div>
            </div>
          </div>

          <div class="d-flex gap-3 flex-wrap">
            <div v-for="feat in ['🎤 Ghi Âm', '🤖 AI Chấm Điểm', '🏆 Huy Hiệu']" :key="feat"
              class="px-4 py-2 rounded-pill bg-white shadow-sm fw-bold" style="color: #0d3b66;">
              {{ feat }}
            </div>
          </div>
        </div>

        <div class="col-lg-5 col-xl-5">
          <div class="card border-0 shadow-lg p-md-5 position-relative overflow-hidden"
            style="border-radius: 40px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
            <div class="position-absolute rounded-circle"
              style="width: 100px; height: 100px; background: rgba(255,107,53,0.05); top: -20px; right: -20px;"></div>

            <div class="text-center position-relative">
              <div class="mx-auto mb-3 text-center">
                <img v-if="branding.logo_url" :src="branding.logo_url" alt="Logo" class="rounded-circle shadow" style="width: 80px; height: 80px; object-fit: cover;" />
                <i v-else class="fa fa-book-reader" style="font-size: 64px; color: #FF7B39;"></i>
              </div>
              <h2 class="fw-bold" style="color: #0d3b66">Đăng Nhập {{ branding.site_name ? 'vào ' + branding.site_name : '' }}</h2>
              <p class="">Bé hãy đăng nhập để tiếp tục học nhé</p>
            </div>

            <form @submit.prevent="dangNhap">
              <div class="mb-1">
                <label class="form-label fw-bold text-secondary ms-2">Email</label>
                <div class="input-group p-1 bg-light border-0 rounded-4 shadow-sm"
                  style="border: 2px solid #ffe5d9 !important;">
                  <span class="input-group-text bg-transparent border-0"><i
                      class="fa fa-envelope text-warning"></i></span>
                  <input v-model="tai_Khoan.email" type="email" class="form-control bg-transparent border-0 shadow-none"
                    placeholder="example@email.com" />
                </div>
              </div>

              <div class="mb-1">
                <label class="form-label fw-bold text-secondary ms-2">Mật khẩu</label>
                <div class="input-group p-1 bg-light border-0 rounded-4 shadow-sm"
                  style="border: 2px solid #ffe5d9 !important;">
                  <span class="input-group-text bg-transparent border-0"><i class="fa fa-lock text-warning"></i></span>
                  <input v-model="tai_Khoan.password" :type="showPassword ? 'text' : 'password'"
                    class="form-control bg-transparent border-0 shadow-none" placeholder="Nhập mật khẩu"
                    @keyup.enter="dangNhap" />
                  <span class="input-group-text bg-transparent border-0" style="cursor: pointer"
                    @click="showPassword = !showPassword">
                    <i :class="showPassword ? 'fa fa-eye' : 'fa fa-eye-slash'" class="text-secondary"></i>
                  </span>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-1 px-2">
                <div class="form-check shadow-none">
                  <input v-model="tai_Khoan.remember" class="form-check-input shadow-none" type="checkbox"
                    id="rememberMe" />
                  <label class="form-check-label" for="rememberMe">Ghi nhớ</label>
                </div>
                <router-link to="/quen-mat-khau" class="fw-bold text-decoration-none" style="color: #ff6b35;">Quên
                  mật khẩu?</router-link>
              </div>
              <div class="mb-1">
                <label class="mb-1">Xác thực</label>
                <div v-if="recaptchaSiteKey" class="d-flex justify-content-center">
                  <div ref="recaptcha"></div>
                </div>
              </div>

              <button type="submit" class="btn btn-login-primary w-100 rounded-pill fw-bold mt-1 mb-1 border-0 text-white shadow"
                style="background: linear-gradient(135deg, #ff6b35, #ff914d);" :disabled="isLoading">
                <span v-if="isLoading"><i class="fa fa-spinner fa-spin me-2"></i> Đang vào lớp...</span>
                <span v-else>Đăng Nhập</span>
              </button>

              <div
                ref="googleLoginOuter"
                class="google-login-outer w-100 mt-1 mb-1"
                :style="{ minHeight: googleOuterHeightPx + 'px' }"
              >
                <div class="google-login-scaler" :style="{ height: googleOuterHeightPx + 'px' }">
                  <div class="google-login-scaler-inner" :style="googleLoginScalerStyle">
                    <GoogleLogin
                      :client-id="googleClientId"
                      :callback="callback"
                      :button-config="googleButtonConfig"
                    />
                  </div>
                </div>
              </div>

              <div class="text-center mt-3">
                <span class="text-muted">Chưa có tài khoản?</span>
                <router-link to="/dang-ky" class="ms-1 fw-bold text-decoration-none" style="color: #ff6b35;">Đăng ký
                  ngay</router-link>
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
import { GoogleLogin } from 'vue3-google-login'
export default {
  components: { GoogleLogin },
  computed: {
    recaptchaSiteKey() {
      return this.$publicKeys?.recaptchaSiteKey || '';
    },
    googleClientId() {
      return this.$publicKeys?.googleClientId || '';
    },
    googleLoginScalerStyle() {
      const w = this.googleButtonConfig.width || 400
      const s = this.googleScaleUniform
      return {
        width: `${w}px`,
        transform: `scale(${s})`,
        transformOrigin: 'center center',
      }
    },
  },
  data() {
    return {
      recaptchaWidgetId: null,
      isLoading: false,
      showPassword: false,
      stats: [
        { value: '10K+', label: 'Bé Học', color: '#ff6b35' },
        { value: '500+', label: 'Bài Học', color: '#20c997' },
        { value: '98%', label: 'Hài Lòng', color: '#0d6efd' },
      ],
      googleButtonConfig: {
        type: 'standard',
        theme: 'outline',
        size: 'large',
        text: 'signin_with',
        shape: 'pill',
        logo_alignment: 'left',
        width: 400,
      },
      googleScaleUniform: 1,
      googleOuterHeightPx: 52,
      googleResizeObserver: null,
      googleBtnNaturalHeightPx: 44,
      tai_Khoan: {
        'email': "",
        'password': "",
        'remember': false,
      },
      branding: {
        logo_icon: "fa fa-book-reader",
        logo_url: null,
        site_name: "",
      }
    }
  },
  mounted() {
    this.taiCauHinhChung();
    if (localStorage.getItem('remember') === 'true') {
      this.tai_Khoan.email = localStorage.getItem('remember_email') || '';
      this.tai_Khoan.password = localStorage.getItem('remember_password') || '';
      this.tai_Khoan.remember = true;
    }
    this.kiemTraDangNhap();
    this.loadRecaptcha();
    this.$nextTick(() => this.initGoogleButtonScale());
  },
  beforeUnmount() {
    this.teardownGoogleButtonScale();
  },
  methods: {
    getBlockedAccountMessage() {
      return 'Tài khoản của bạn đã vi phạm chính sách bảo mật của chúng tôi';
    },
    taiCauHinhChung() {
      axios
        .get("http://127.0.0.1:8000/api/cau-hinh/footer/data")
        .then((res) => {
          if (res.data?.status && res.data?.data) {
            this.branding.logo_icon = res.data.data.logo_icon || this.branding.logo_icon;
            this.branding.site_name = res.data.data.site_name || this.branding.site_name;
            this.branding.logo_url = res.data.data.logo_url || null;
          }
        })
        .catch(() => {});
    },
    initGoogleButtonScale() {
      this._onWindowResizeGoogle = () => this.syncGoogleButtonScale();
      window.addEventListener('resize', this._onWindowResizeGoogle);
      const outer = this.$refs.googleLoginOuter;
      if (outer && typeof ResizeObserver !== 'undefined') {
        this.googleResizeObserver = new ResizeObserver(() => this.syncGoogleButtonScale());
        this.googleResizeObserver.observe(outer);
      }
      this.syncGoogleButtonScale();
      [200, 500, 1200].forEach((ms) => setTimeout(() => this.syncGoogleButtonScale(), ms));
    },
    teardownGoogleButtonScale() {
      this.googleResizeObserver?.disconnect();
      this.googleResizeObserver = null;
      if (this._onWindowResizeGoogle) {
        window.removeEventListener('resize', this._onWindowResizeGoogle);
      }
    },
    syncGoogleButtonScale() {
      const outer = this.$refs.googleLoginOuter;
      if (!outer || outer.clientWidth < 8) return;
      const nw = this.googleButtonConfig.width || 400;
      const iframe = outer.querySelector('iframe');
      const nh =
        iframe && iframe.offsetHeight > 20 ? iframe.offsetHeight : this.googleBtnNaturalHeightPx;
      const cw = outer.clientWidth;
      const s = cw / nw;
      this.googleScaleUniform = s;
      this.googleOuterHeightPx = Math.max(52, Math.ceil(nh * s) + 10);
    },
    loadRecaptcha() {
      if (!this.recaptchaSiteKey) return;
      const initWidget = () => {
        this.$nextTick(() => {
          const el = this.$refs.recaptcha;
          if (!el || !window.grecaptcha || this.recaptchaWidgetId != null) return;
          window.grecaptcha.ready(() => {
            try {
              this.recaptchaWidgetId = window.grecaptcha.render(el, {
                sitekey: this.recaptchaSiteKey,
              });
            } catch (e) {
              this.$toast.error('Không khởi tạo được Recaptcha.');
            }
          });
        });
      };
      if (window.grecaptcha) {
        initWidget();
        return;
      }
      let script = document.querySelector('script[src*="google.com/recaptcha/api.js"]');
      if (script) {
        if (window.grecaptcha) initWidget();
        else script.addEventListener('load', initWidget, { once: true });
        return;
      }
      script = document.createElement('script');
      script.src = 'https://www.google.com/recaptcha/api.js';
      script.async = true;
      script.onload = initWidget;
      script.onerror = () => {
        this.$toast.error('Không thể tải Recaptcha. Vui lòng thử lại sau.');
      };
      document.head.appendChild(script);
    },
    resetRecaptcha() {
      if (this.recaptchaWidgetId != null && window.grecaptcha?.reset) {
        window.grecaptcha.reset(this.recaptchaWidgetId);
      }
    },
    luuThongTinTuApi(data) {
      localStorage.setItem('ho_ten', data.ho_ten ?? '')
      localStorage.setItem('email', data.email ?? '')
      if (data.id != null && data.id !== '') {
        localStorage.setItem('nguoi_dung_id', String(data.id))
      } else {
        localStorage.removeItem('nguoi_dung_id')
      }
      localStorage.setItem('check_token', String(data.status))
      if (data.ten_vai_tro != null && data.ten_vai_tro !== '') {
        localStorage.setItem('ten_vai_tro', String(data.ten_vai_tro))
      } else {
        localStorage.removeItem('ten_vai_tro')
      }
      if (data.anh_dai_dien) {
        localStorage.setItem('anh_dai_dien', String(data.anh_dai_dien))
      } else {
        localStorage.removeItem('anh_dai_dien')
      }
    },
    xoaProfileLocal() {
      ['ho_ten', 'email', 'check_token', 'ten_vai_tro', 'anh_dai_dien', 'nguoi_dung_id'].forEach((k) =>
        localStorage.removeItem(k)
      )
    },
    dangNhap() {
      let code = '';
      if (this.recaptchaWidgetId != null && window.grecaptcha?.getResponse) {
        code = window.grecaptcha.getResponse(this.recaptchaWidgetId);
      } else if (window.grecaptcha?.getResponse) {
        code = window.grecaptcha.getResponse();
      }
      if (!code) {
        this.$toast.error("Bạn chưa chọn recaptcha");
      }
      else {
        this.isLoading = true;
        this.tai_Khoan.code = code;
        axios
          .post("http://127.0.0.1:8000/api/dang-nhap", this.tai_Khoan)
          .then((res) => {
            if (res.data.status == 1) {
              this.$toast.success(res.data.message);
              if (this.tai_Khoan.remember) {
                localStorage.setItem('remember_email', this.tai_Khoan.email);
                localStorage.setItem('remember_password', this.tai_Khoan.password);
                localStorage.setItem('remember', 'true');
              } else {
                localStorage.removeItem('remember_email');
                localStorage.removeItem('remember_password');
                localStorage.removeItem('remember');
              }
              this.tai_Khoan = {
                'email': "",
                'password': "",
                'remember': false,
              };
              this.luuTokenTheoVaiTro(res.data.vai_tro_id ?? 3, res.data.token);
              this.luuThongTinTuApi(res.data);
              this.chuyenTrangTheoVaiTro(res.data.vai_tro_id ?? 3);
            }
            else {
              this.$toast.error(res.data.message);
              this.resetRecaptcha();
            }
          })
          .catch((res) => {
            if (res.response?.status === 403) {
              this.$toast.error(res.response?.data?.message || this.getBlockedAccountMessage());
              this.resetRecaptcha();
              return;
            }
            const list = Object.values(res.response?.data?.errors || {});
            if (list.length) {
              list.forEach((v) => {
                this.$toast.error(v[0]);
              });
            } else {
              this.$toast.error(res.response?.data?.message || "Đăng nhập thất bại");
            }
            this.resetRecaptcha();
          })
          .finally(() => {
            this.isLoading = false;
          })
      }

    },
    luuTokenTheoVaiTro(vaiTroId, token) {
      localStorage.removeItem("token_admin");
      localStorage.removeItem("token_teacher");
      localStorage.removeItem("token_nguoi_dung");
      if (vaiTroId === 1) {
        localStorage.setItem("token_admin", token);
      } else if (vaiTroId === 2) {
        localStorage.setItem("token_teacher", token);
      } else {
        localStorage.setItem("token_nguoi_dung", token);
      }
    },
    chuyenTrangTheoVaiTro(vaiTroId) {
      if (vaiTroId === 1) {
        this.$router.push("/admin/dashboard");
      } else if (vaiTroId === 2) {
        this.$router.push("/teacher/dashboard");
      } else {
        this.$router.push("/");
      }
    },
    kiemTraDangNhap() {
      const token =
        localStorage.getItem("token_admin") ||
        localStorage.getItem("token_teacher") ||
        localStorage.getItem("token_nguoi_dung");
      if (!token) {
        return;
      }
      axios
        .get("http://127.0.0.1:8000/api/check-token", {
          headers: {
            Authorization: "Bearer " + token,
          },
        })
        .then((res) => {
          if (res.data.status) {
            this.luuThongTinTuApi(res.data);
            this.chuyenTrangTheoVaiTro(res.data.vai_tro_id ?? 3);
          }
        })
        .catch((err) => {
          if (err.response?.status === 401 || err.response?.status === 403) {
            if (err.response?.status === 403) {
              this.$toast.error(err.response?.data?.message || this.getBlockedAccountMessage());
            }
            localStorage.removeItem("token_admin");
            localStorage.removeItem("token_teacher");
            localStorage.removeItem("token_nguoi_dung");
            this.xoaProfileLocal();
          }
        });
    },
    callback(res) {
      let jwt = ''
      if (res && typeof res === 'object') {
        jwt = res.credential || res.id_token || ''
      } else if (typeof res === 'string') {
        jwt = res
      }
      jwt = String(jwt).trim()
      if (!jwt) {
        this.$toast.error('Không nhận được mã từ Google. Vui lòng thử lại.')
        return
      }
      axios
        .post('http://127.0.0.1:8000/api/login-google', { id_token: jwt, credential: jwt })
        .then((res) => {
          if (res.data.status == 1 || res.data.status === true) {
            this.$toast.success(res.data.message);
            this.luuTokenTheoVaiTro(res.data.vai_tro_id ?? 3, res.data.token);
            this.luuThongTinTuApi(res.data);
            this.chuyenTrangTheoVaiTro(res.data.vai_tro_id ?? 3);
          }
          else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((res) => {
          if (res.response?.status === 403) {
            this.$toast.error(res.response?.data?.message || this.getBlockedAccountMessage());
            return;
          }
          this.$toast.error(res.response?.data?.message || "Đăng nhập Google thất bại");
        })
    }
  },
}
</script>

<style scoped>
@keyframes float {

  0%,
  100% {
    transform: translateY(0px);
  }

  50% {
    transform: translateY(-20px);
  }
}

.col-4 div:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}

.form-control:focus {
  background-color: #fff !important;
}

.btn-login-primary {
  padding-top: 0.85rem;
  padding-bottom: 0.85rem;
  min-height: 3.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.google-login-outer {
  width: 100%;
  overflow: visible;
}

.google-login-scaler {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: visible;
}

.google-login-scaler-inner {
  flex: 0 0 auto;
}
</style>