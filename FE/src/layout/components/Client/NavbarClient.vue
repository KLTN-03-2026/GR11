<template>
  <div ref="navbarRoot" class="container-fluid d-flex flex-wrap align-items-center py-lg-0">
  <!-- Logo -->
  <router-link to="/" class="navbar-brand d-flex align-items-center">
    <h1 class="m-0 text-primary">
      <i :class="branding.logo_icon"></i>{{ branding.site_name }}
    </h1>
  </router-link>

  <!-- Toggle -->
  <button
    type="button"
    class="navbar-toggler"
    data-bs-toggle="collapse"
    data-bs-target="#navbarCollapse"
  >
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarCollapse">
    <div class="navbar-nav mx-auto align-items-lg-center">
      <!-- Trang chủ -->
      <router-link
        to="/"
        class="nav-item nav-link px-3 rounded-pill mx-1"
        active-class="active"
      >
        🏠 Trang Chủ
      </router-link>

      <!-- Học tập -->
      <div class="nav-item dropdown">
        <a
          href="#"
          class="nav-link dropdown-toggle px-3 rounded-pill mx-1"
          data-bs-toggle="dropdown"
        >
          📚 Học Tập
        </a>

        <div class="dropdown-menu rounded-4 border-0 shadow-sm m-0 p-2">
          <router-link
            to="/chu-de"
            class="dropdown-item rounded-3 py-2"
          >
            🎯 Chọn Chủ Đề
          </router-link>

          <router-link
            to="/bai-hoc"
            class="dropdown-item rounded-3 py-2"
          >
            📖 Chọn Bài Học
          </router-link>

          <router-link
            to="/luyen-tap"
            class="dropdown-item rounded-3 py-2"
          >
            🎤 Luyện Tập
          </router-link>

          <router-link
            to="/bai-kiem-tra"
            class="dropdown-item rounded-3 py-2"
          >
            📝 Kiểm Tra
          </router-link>

          <router-link
            to="/on-tap-loi"
            class="dropdown-item rounded-3 py-2"
          >
            ♻️ Ôn Tập Lỗi
          </router-link>
        </div>
      </div>

      <!-- Tiến độ -->
      <router-link
        to="/tien-do"
        class="nav-item nav-link px-3 rounded-pill mx-1"
        active-class="active"
      >
        ⭐ Tiến Độ
      </router-link>

      <!-- Chuỗi ngày học -->
      <router-link
        to="/chuoi-ngay-hoc"
        class="nav-item nav-link px-3 rounded-pill mx-1"
        active-class="active"
      >
        🔥 Chuỗi Ngày Học
      </router-link>

      <!-- Bảng xếp hạng -->
      <router-link
        to="/xep-hang"
        class="nav-item nav-link px-3 rounded-pill mx-1"
        active-class="active"
      >
        🏅 Xếp Hạng
      </router-link>

      <!-- Đăng nhập (mobile) -->
      <router-link
        v-if="!daDangNhap"
        to="/dang-nhap"
        class="nav-item nav-link px-3 rounded-pill mx-1 d-lg-none"
        active-class="active"
      >
        🔑 Đăng nhập
      </router-link>
    </div>

    <!-- Chưa đăng nhập — desktop -->
    <router-link
      v-if="!daDangNhap"
      to="/dang-nhap"
      class="link-dang-nhap ms-lg-auto d-none d-lg-inline-flex"
      active-class="link-dang-nhap--active"
    >
      Đăng nhập
    </router-link>

    <!-- Đã đăng nhập — User Dropdown -->
    <div
      v-else
      class="user-dropdown-wrapper ms-lg-auto d-none d-lg-block"
    >
      <div
        class="user-toggle"
        :class="{ 'user-toggle--no-avatar': !user.avatarDisplayUrl }"
        @click="showUserMenu = !showUserMenu"
      >
        <img
          v-if="user.avatarDisplayUrl"
          :src="user.avatarDisplayUrl"
          class="user-avatar"
          alt=""
        />

        <div class="user-info">
          <h6 class="mb-0">{{ user.name }}</h6>
          <small>{{ phuDeNguoiDung }}</small>
        </div>
      </div>

      <transition name="dropdown-fade">
        <div
          v-if="showUserMenu"
          class="user-dropdown-menu"
        >
          <router-link
            to="/profile"
            class="user-menu-item"
          >
            <i class="fa fa-user-circle"></i>
            Profile
          </router-link>

          <div class="dropdown-divider my-2"></div>

          <a
            href="#"
            class="user-menu-item logout-item"
            @click.prevent="dangXuat"
          >
            <i class="fa fa-sign-out-alt"></i>
            Đăng Xuất
          </a>
        </div>
      </transition>
    </div>
  </div>
  </div>
</template>

<script>
import axios from "axios";

const PROFILE_LS_KEYS = ["ho_ten", "email", "check_token", "ten_vai_tro", "anh_dai_dien", "anh_dai_dien_url", "anh_dai_dien_local"];

export default {

  data() {
    return {
      showUserMenu: false,
      user: {},
      daDangNhap: false,
      branding: {
        logo_icon: "fa fa-book-reader me-3",
        site_name: "EchoKids",
      },
    };
  },

  computed: {
    phuDeNguoiDung() {
      return this.daDangNhap ? "Bạn học" : "Khách";
    },
  },

  watch: {
    $route() {
      this.showUserMenu = false;
      this.dongBoUserTuLocal();
    },
  },

  mounted() {
    document.addEventListener("click", this.handleClickOutside);
    this.dongBoUserTuLocal();
    this.taiCauHinhChung();
    window.addEventListener("storage", this.dongBoUserTuLocal);
    window.addEventListener("profile-updated", this.dongBoUserTuLocal);
  },

  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
    window.removeEventListener("storage", this.dongBoUserTuLocal);
    window.removeEventListener("profile-updated", this.dongBoUserTuLocal);
  },

  methods: {
    taiCauHinhChung() {
      axios
        .get("http://127.0.0.1:8000/api/admin/cau-hinh/chung/data")
        .then((res) => {
          if (res.data?.status && res.data?.data) {
            this.branding.logo_icon =
              res.data.data.logo_icon || this.branding.logo_icon;
            this.branding.site_name =
              res.data.data.site_name || this.branding.site_name;
          }
        })
        .catch(() => {});
    },
    duongDanAnh(raw, macDinh = "") {
      if (!raw) {
        return macDinh;
      }
      const source = String(raw).trim().replace(/\\/g, "/");
      if (!source) {
        return macDinh;
      }
      if (source.startsWith("http://") || source.startsWith("https://") || source.startsWith("blob:")) {
        return source;
      }
      const base = (import.meta.env.VITE_API_URL || "http://127.0.0.1:8000").replace(/\/$/, "");
      if (source.startsWith("/storage/")) {
        return `${base}${source}`;
      }
      if (source.startsWith("storage/")) {
        return `${base}/${source}`;
      }
      return `${base}/storage/${source.replace(/^\//, "")}`;
    },
    dongBoUserTuLocal() {
      const token = localStorage.getItem("token_nguoi_dung");
      const rawAnhUrl = localStorage.getItem("anh_dai_dien_url");
      const rawAnhLocal = localStorage.getItem("anh_dai_dien_local");
      const rawAnh = localStorage.getItem("anh_dai_dien");
      const daDangNhap = !!token;
      const avatarRaw = rawAnhUrl || rawAnhLocal || rawAnh;
      const coAnhThat = !!(avatarRaw && String(avatarRaw).trim());
      this.daDangNhap = daDangNhap;
      this.user = {
        name: localStorage.getItem("ho_ten") || (daDangNhap ? "Bạn học" : "Khách"),
        avatarDisplayUrl:
          daDangNhap && coAnhThat ? this.duongDanAnh(avatarRaw) : null,
      };
    },
    dangXuat() {
      this.showUserMenu = false;
      const token = localStorage.getItem("token_nguoi_dung");
      axios
        .post(
          "http://127.0.0.1:8000/api/dang-xuat",
          {},
          {
            headers: {
              Authorization: "Bearer " + token,
            },
          }
        )
        .then((res) => {
          if (res.data.status) {
            localStorage.removeItem("token_nguoi_dung");
            PROFILE_LS_KEYS.forEach((k) => localStorage.removeItem(k));
            this.$toast.success(res.data.message);
            this.dongBoUserTuLocal();
            this.$router.push("/dang-nhap");
          } else {
            this.$toast.error("Có lỗi xảy ra");
          }
        })
        .catch(() => {
          localStorage.removeItem("token_nguoi_dung");
          PROFILE_LS_KEYS.forEach((k) => localStorage.removeItem(k));
          this.dongBoUserTuLocal();
          this.$router.push("/dang-nhap");
        });
    },
    handleClickOutside(event) {
      const root = this.$refs.navbarRoot;
      if (!root?.querySelector) {
        return;
      }
      const dropdown = root.querySelector(".user-dropdown-wrapper");
      if (dropdown && !dropdown.contains(event.target)) {
        this.showUserMenu = false;
      }
    },
  },
};
</script>

<style scoped>
.navbar {
  padding-top: 14px;
  padding-bottom: 14px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(12px);
}

.navbar-brand h1 {
  font-size: 34px;
  font-weight: 800;
  color: #ff6b35 !important;
  font-family: 'Lobster Two', cursive;
  transition: all 0.3s ease;
}

.navbar-brand h1:hover {
  transform: scale(1.05);
}

.navbar-brand i {
  background: linear-gradient(135deg, #ff6b35, #ff8c42);
  color: #fff;
  width: 52px;
  height: 52px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 25px rgba(255, 107, 53, 0.25);
  transition: all 0.3s ease;
}

.navbar-brand:hover i {
  transform: rotate(-8deg) scale(1.08);
}

.navbar-nav {
  gap: 8px;
}

.navbar .nav-link {
  position: relative;
  font-weight: 700;
  font-size: 18px;
  color: #0d3b66 !important;
  padding: 14px 22px !important;
  border-radius: 999px;
  transition: all 0.3s ease;
  overflow: hidden;
}

.navbar .nav-link::before {
  content: "";
  position: absolute;
  width: 0;
  height: 100%;
  left: 0;
  top: 0;
  border-radius: 999px;
  background: linear-gradient(135deg, #fff3ef, #ffe7db);
  transition: all 0.3s ease;
  z-index: -1;
}

.navbar .nav-link:hover::before {
  width: 100%;
}

.navbar .nav-link:hover {
  color: #ff6b35 !important;
  transform: translateY(-2px);
}

.navbar .nav-link.router-link-active {
  background: linear-gradient(135deg, #ff6b35, #ff8c42);
  color: #fff !important;
  box-shadow: 0 10px 20px rgba(255, 107, 53, 0.22);
}

.dropdown-menu {
  border: none;
  border-radius: 20px;
  padding: 1px;
  margin-top: 55px !important;
  min-width: 200px;
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.08);
  left: 50% !important;
  transform: translateX(-50%);
}
.nav-item.dropdown {
  position: relative;
}

.nav-item.dropdown .dropdown-menu {
  top: 100%;
}
.dropdown-item {
  padding: 16px 20px;
  border-radius: 16px;
  font-weight: 700;
  font-size: 17px;
  color: #0d3b66;
  transition: all 0.3s ease;
}

.dropdown-item:hover {
  background: linear-gradient(135deg, #fff3ef, #ffe7db);
  color: #ff6b35;
  transform: translateX(5px);
}

.link-dang-nhap {
  align-items: center;
  justify-content: center;
  padding: 12px 24px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 16px;
  color: #fff !important;
  text-decoration: none;
  background: linear-gradient(135deg, #ff6b35, #ff8c42);
  box-shadow: 0 10px 20px rgba(255, 107, 53, 0.22);
  transition: all 0.3s ease;
  white-space: nowrap;
}

.link-dang-nhap:hover {
  color: #fff !important;
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(255, 107, 53, 0.28);
}

.link-dang-nhap--active {
  opacity: 0.95;
}

.user-dropdown-wrapper {
  position: relative;
}

.user-toggle {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #fff;
  border: 2px solid #ffe2d6;
  border-radius: 999px;
  padding: 5px 12px 5px 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 8px 18px rgba(255, 107, 53, 0.1);
}

.user-toggle--no-avatar {
  padding-left: 14px;
}

.user-toggle:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 22px rgba(255, 107, 53, 0.16);
}

.user-avatar {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #fff3ef;
}

.user-info h6 {
  color: #0d3b66;
  font-weight: 700;
  font-size: 14px;
}

.user-info small {
  color: #ff8c42;
  font-weight: 600;
  font-size: 12px;
}

.user-arrow {
  color: #ff6b35;
  font-size: 11px;
  margin-left: 2px;
}

.user-dropdown-menu {
  position: absolute;
  top: 62px;
  right: 0;
  width: 180px;
  background: #fff;
  border-radius: 24px;
  padding: 12px;
  box-shadow: 0 18px 40px rgba(0, 0, 0, 0.1);
  border: 1px solid #f4f4f4;
  z-index: 1000;
}

.user-menu-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 12px;
  border-radius: 14px;
  text-decoration: none;
  color: #0d3b66;
  font-weight: 600;
  font-size: 15px;
  transition: all 0.3s ease;
}

.user-menu-item i {
  width: 18px;
  color: #ff8c42;
}

.user-menu-item:hover {
  background: linear-gradient(135deg, #fff3ef, #ffe7db);
  color: #ff6b35;
  transform: translateX(4px);
}

.logout-item {
  color: #e74c3c;
}

.logout-item i {
  color: #e74c3c;
}

.dropdown-fade-enter-active,
.dropdown-fade-leave-active {
  transition: all 0.25s ease;
}

.dropdown-fade-enter-from,
.dropdown-fade-leave-to {
  opacity: 0;
  transform: translateY(10px) scale(0.96);
}

.navbar-toggler {
  border: none;
  box-shadow: none !important;
  background: #fff3ef;
  border-radius: 14px;
  padding: 10px 14px;
}

@media (max-width: 991px) {
  .user-dropdown-wrapper {
    display: none;
  }
}
</style>