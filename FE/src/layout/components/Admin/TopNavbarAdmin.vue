<template>
  <div class="iq-top-navbar">
    <div class="iq-navbar-custom">
      <nav class="navbar navbar-expand-lg navbar-light p-0">
        
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
            <router-link to="/thong-tin-ca-nhan" class="search-toggle iq-waves-effect d-flex align-items-center admin-user-chip">
              <div class="caption admin-user-caption">
                <span class="admin-greeting">Xin chào,</span>
                <h6 class="text-danger font-weight-bold mb-0 line-height admin-user-name">{{ user.name }}</h6>
              </div>
              <img :src="user.avatarUrl" class="img-fluid admin-avatar me-2" alt="user">
            </router-link>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>
<script>
const ANH_MAC_DINH = '/Admin/images/user/1.jpg'

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
      const source = String(raw).trim().replace(/\\/g, '/')
      if (!source) {
        return macDinh
      }
      if (source.startsWith('http://') || source.startsWith('https://') || source.startsWith('blob:')) {
        return source
      }
      const base = (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, '')
      if (source.startsWith('/storage/')) {
        return `${base}${source}`
      }
      if (source.startsWith('storage/')) {
        return `${base}/${source}`
      }
      return `${base}/storage/${source.replace(/^\//, '')}`
    },
    dongBoUserTuLocal() {
      const avatarUrl =
        localStorage.getItem('anh_dai_dien_url')
        || localStorage.getItem('anh_dai_dien_local')
        || localStorage.getItem('anh_dai_dien')
      this.user = {
        name: localStorage.getItem('ho_ten') || 'Admin',
        avatarUrl: this.duongDanAnh(avatarUrl, ANH_MAC_DINH),
      }
    },
  },
}
</script>
<style scoped>
.iq-top-navbar {
  top: 0 !important;
  left: 275px !important;
  right: 15px !important;
  width: auto !important;
  min-height: 50px !important;
  margin: 0 !important;
  padding: 0 !important;
  border-radius: 0 !important;
  overflow: hidden !important;
}

.iq-navbar-custom {
  min-height: 50px !important;
  margin: 0 !important;
  padding: 0 !important;
  border-radius: 0 !important;
}

.navbar {
  min-height: 50px !important;
  margin: 0 !important;
  padding-top: 0 !important;
  padding-bottom: 0 !important;
}

.iq-menu-bt {
  display: none !important;
}

.navbar-list {
  margin: 0 !important;
  padding-top: 0 !important;
  padding-bottom: 0 !important;
  padding-right: 0 !important;
}

.navbar-list li > a {
  min-height: 50px !important;
  line-height: 50px !important;
  padding-top: 0 !important;
  padding-bottom: 0 !important;
}

.navbar-list > li:last-child {
  margin-right: 2px !important;
  padding-right: 0 !important;
}

.admin-user-chip {
  gap: 6px;
  padding: 0 !important;
  margin: 0 !important;
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  text-decoration: none !important;
}

.admin-user-chip:hover,
.admin-user-chip:focus,
.admin-user-chip:focus-visible,
.admin-user-chip:active {
  background: transparent !important;
  outline: none !important;
  box-shadow: none !important;
}

.admin-user-chip::before,
.admin-user-chip::after {
  display: none !important;
  content: none !important;
}

.admin-bell-icon {
  font-size: 14px;
  color: #9ca3af;
}

.admin-user-caption {
  display: inline-flex;
  align-items: baseline;
  gap: 4px;
  margin: 0;
}

.admin-greeting {
  font-size: 11px;
  color: #9ca3af;
  font-weight: 500;
  line-height: 1;
}

.admin-user-name {
  font-size: 12px;
  color: #374151;
  font-weight: 700;
  line-height: 1;
}

.admin-avatar {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  object-fit: cover;
  border: 1px solid #ff7a45;
}

.admin-user-chip:focus,
.admin-user-chip:focus-visible,
.admin-user-chip:active,
.admin-user-chip *:focus,
.admin-user-chip *:focus-visible,
.admin-user-chip *:active {
  outline: none !important;
  box-shadow: none !important;
}

.admin-user-chip.iq-waves-effect .waves-ripple,
.admin-user-chip .waves-ripple {
  display: none !important;
}
</style>
