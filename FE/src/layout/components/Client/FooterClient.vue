<template>
  <!-- Footer Start -->
  <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
      <div class="row g-5">
        <div class="col-lg-3 col-md-6">
          <h3 class="text-white mb-4">Liên Hệ</h3>
          <p class="mb-2">
            <i class="fa fa-map-marker-alt me-3"></i>TP. Đà Nẵng, Việt Nam
          </p>
          <p class="mb-2">
            <i class="fa fa-phone-alt me-3"></i>{{ footer.hotline || '0123 456 789' }}
          </p>
          <p class="mb-2">
            <i class="fa fa-envelope me-3"></i>{{ footer.support_email || 'supportechokids@gmail.com' }}
          </p>

          <div class="d-flex pt-2">
            <a class="btn btn-outline-light btn-social" :href="footer.facebook_url || '#'" target="_blank"
              rel="noopener noreferrer">
              <i class="fab fa-facebook-f"></i>
            </a>

            <a class="btn btn-outline-light btn-social" href="">
              <i class="fab fa-youtube"></i>
            </a>

            <a class="btn btn-outline-light btn-social" href="">
              <i class="fab fa-instagram"></i>
            </a>

            <a class="btn btn-outline-light btn-social" href="">
              <i class="fab fa-tiktok"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <h3 class="text-white mb-4">Liên Kết Nhanh</h3>
          <a class="btn btn-link text-white-50" href="">Giới Thiệu</a>
          <a class="btn btn-link text-white-50" href="">Bài Học</a>
          <a class="btn btn-link text-white-50" href="">Luyện Tập</a>
          <a class="btn btn-link text-white-50" href="">Bảng Xếp Hạng</a>
          <a class="btn btn-link text-white-50" href="">Liên Hệ</a>
        </div>

        <div class="col-lg-3 col-md-6">
          <h3 class="text-white mb-4">Hình Ảnh Nổi Bật</h3>

          <div class="row g-2 pt-2">
            <div class="col-4">
              <img class="img-fluid rounded bg-light p-1" src="/Client/images/classes-1.jpg" alt="" />
            </div>

            <div class="col-4">
              <img class="img-fluid rounded bg-light p-1" src="/Client/images/classes-2.jpg" alt="" />
            </div>

            <div class="col-4">
              <img class="img-fluid rounded bg-light p-1" src="/Client/images/classes-3.jpg" alt="" />
            </div>

            <div class="col-4">
              <img class="img-fluid rounded bg-light p-1" src="/Client/images/classes-4.jpg" alt="" />
            </div>

            <div class="col-4">
              <img class="img-fluid rounded bg-light p-1" src="/Client/images/classes-5.jpg" alt="" />
            </div>

            <div class="col-4">
              <img class="img-fluid rounded bg-light p-1" src="/Client/images/classes-6.jpg" alt="" />
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <h3 class="text-white mb-4">Đăng Ký Nhận Tin</h3>
          <p>
            Nhận thông báo về bài học mới, mẹo luyện phát âm và hoạt động nổi
            bật từ EchoKids.
          </p>

          <div class="position-relative mx-auto" style="max-width: 400px;">
            <input class="form-control bg-transparent w-100 py-3 ps-4 pe-5" type="text"
              placeholder="Nhập email của bạn" />
            <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">
              <router-link to="/dang-ky" class="text-white">
                Đăng Ký
              </router-link>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="copyright">
        <div class="row">
          <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
            &copy;
            <a class="border-bottom" href="#">{{ footer.site_name || 'EchoKids' }}</a>, Hệ thống hỗ trợ
            luyện phát âm tiếng Việt cho trẻ em.

            <br />
            Đồng hành cùng phụ huynh, giáo viên và chuyên gia ngôn ngữ.
          </div>

          <div class="col-md-6 text-center text-md-end">
            <div class="footer-menu">
              <router-link to="/">Trang Chủ</router-link>
              <router-link to="/chinh-sach">Chính Sách</router-link>
              <a href="">Hỗ Trợ</a>
              <router-link to="/faq">FAQ</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Footer End -->
</template>

<script>
import { API_BASE } from '../../../api/http.js';
import axios from "axios";

export default {
  data() {
    return {
      footer: {
        site_name: "EchoKids",
        hotline: "0123 456 789",
        support_email: "supportechokids@gmail.com",
        facebook_url: "",
      },
    };
  },
  mounted() {
    this.taiCauHinhFooter();
  },
  methods: {
    taiCauHinhFooter() {
      axios
        .get(`${API_BASE}/api/cau-hinh/footer/data`)
        .then((res) => {
          if (res.data?.status && res.data?.data) {
            this.footer = { ...this.footer, ...res.data.data };
          }
        })
        .catch(() => { });
    },
  },
};
</script>

<style>
.footer {
  background: linear-gradient(135deg, #0d3b66, #102542) !important;
  position: relative;
  overflow: hidden;
}

.footer::before {
  content: "";
  position: absolute;
  width: 320px;
  height: 320px;
  background: rgba(255, 255, 255, 0.04);
  border-radius: 50%;
  top: -120px;
  left: -120px;
}

.footer::after {
  content: "";
  position: absolute;
  width: 260px;
  height: 260px;
  background: rgba(255, 107, 53, 0.08);
  border-radius: 50%;
  bottom: -100px;
  right: -100px;
}

.footer h3 {
  font-weight: 700;
  color: #fff !important;
  margin-bottom: 24px !important;
  position: relative;
  display: inline-block;
}

.footer h3::after {
  content: "";
  position: absolute;
  width: 50px;
  height: 4px;
  border-radius: 999px;
  background: linear-gradient(135deg, #ff6b35, #ff8c42);
  left: 0;
  bottom: -10px;
}

.footer p,
.footer a,
.footer small {
  position: relative;
  z-index: 2;
}

.footer .btn-link {
  display: block;
  color: rgba(255, 255, 255, 0.7) !important;
  text-decoration: none;
  padding: 6px 0;
  transition: all 0.3s ease;
}

.footer .btn-link:hover {
  color: #ff6b35 !important;
  transform: translateX(6px);
}

.btn-social {
  width: 46px;
  height: 46px;
  border-radius: 50% !important;
  border: none !important;
  background: rgba(255, 255, 255, 0.08) !important;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.btn-social:hover {
  background: linear-gradient(135deg, #ff6b35, #ff8c42) !important;
  color: #fff !important;
  transform: translateY(-5px) rotate(8deg);
  box-shadow: 0 12px 24px rgba(255, 107, 53, 0.25);
}

.footer img {
  transition: all 0.3s ease;
  border-radius: 18px !important;
}

.footer img:hover {
  transform: scale(1.08) rotate(-2deg);
}

.footer input.form-control {
  height: 58px;
  border-radius: 999px;
  border: 2px solid rgba(255, 255, 255, 0.15);
  color: #fff;
}

.footer input.form-control:focus {
  border-color: #ff6b35;
  box-shadow: none;
  background: rgba(255, 255, 255, 0.05);
  color: #fff;
}

.footer input.form-control::placeholder {
  color: rgba(255, 255, 255, 0.5);
}

.footer .btn-primary {
  border-radius: 999px;
  background: linear-gradient(135deg, #ff6b35, #ff8c42);
  border: none;
  padding-left: 22px;
  padding-right: 22px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.footer .btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 24px rgba(255, 107, 53, 0.25);
}

.copyright {
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  padding-top: 24px;
  padding-bottom: 12px;
  position: relative;
  z-index: 2;
}

.copyright a {
  color: #ff8c42 !important;
  text-decoration: none;
  transition: all 0.3s ease;
}

.copyright a:hover {
  color: #fff !important;
}

.footer-menu a {
  margin-left: 16px;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  transition: all 0.3s ease;
}

.footer-menu a:hover {
  color: #ff6b35;
}
</style>