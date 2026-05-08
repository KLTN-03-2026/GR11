<template>
  <div v-if="isReady" class="container-xxl bg-white p-0">
    <div id="spinner"
      class="bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
      <div class="spinner-border text-primary"></div>
    </div>
<!-- Bỏ trong này sẽ container -->
  </div>
      <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top px-4 px-lg-5 py-lg-0">
     <NavbarClient />
    </nav>
   <router-view />
  <FooterClient v-if="isReady" />
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { createToaster } from '@meforma/vue-toaster'
import NavbarClient from '../components/Client/NavbarClient.vue';
import FooterClient from '../components/Client/FooterClient.vue';

const isReady = ref(false)
const toast = createToaster({ position: "top-right" })
let _studentChannel = null

const loadCSS = (href) => {
  if (!document.querySelector(`link[href="${href}"]`)) {
    const link = document.createElement('link')
    link.rel = 'stylesheet'
    link.href = href
    link.className = 'client-css-file' 
    document.head.appendChild(link)
  }
}

const loadScript = (src) => {
  return new Promise((resolve, reject) => {
    if (document.querySelector(`script[src="${src}"]`)) {
      resolve();
      return;
    }
    const script = document.createElement('script')
    script.src = src
    script.onload = resolve
    script.onerror = reject
    document.body.appendChild(script)
  })
}

function khoiTaoStudentChannel() {
  if (!window.Echo) return
  const studentId = parseInt(localStorage.getItem('nguoi_dung_id') || '0', 10)
  if (!studentId) return

  // Cập nhật auth header với token học viên
  const token = localStorage.getItem('token_nguoi_dung') || ''
  if (token && window.Echo.connector?.pusher) {
    window.Echo.connector.pusher.config.auth = {
      headers: { Authorization: 'Bearer ' + token },
    }
  }

  // Lắng nghe channel private 'student.{id}'
  _studentChannel = window.Echo.private(`student.${studentId}`)
  _studentChannel.listen('.GiaoVienGuiGoiY', (data) => {
    const uuTienLabel = data.uu_tien === 'cao' ? '🔴 Ưu tiên cao' : '🟡 Bình thường'
    let msg = `📚 Giáo viên ${data.ten_giao_vien} gợi ý bài "${data.tieu_de}". [${uuTienLabel}]`
    if (data.loi_nhan) msg += ` — ${data.loi_nhan}`
    toast.info(msg, { duration: 10000 })

    // Phát sự kiện nội bộ để ChatBox hoặc trang chat có thể cập nhật
    window.dispatchEvent(new CustomEvent('goi-y-bai-hoc-moi', { detail: data }))
  })
}

onMounted(async () => {
  loadCSS("https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@600&family=Lobster+Two:wght@700&display=swap");
  loadCSS("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css");
  loadCSS("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css");
  
  loadCSS("/Client/lib/animate/animate.min.css");
  loadCSS("/Client/lib/owlcarousel/assets/owl.carousel.min.css");
  loadCSS("/Client/css/bootstrap.min.css");
  loadCSS("/Client/css/style.css");

  await loadScript('https://code.jquery.com/jquery-3.4.1.min.js')
  await loadScript('https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js')
  
  await loadScript('/Client/lib/wow/wow.min.js')
  await loadScript('/Client/lib/easing/easing.min.js')
  await loadScript('/Client/lib/waypoints/waypoints.min.js')
  await loadScript('/Client/lib/owlcarousel/owl.carousel.min.js')
  isReady.value = true
  await nextTick()
  await loadScript('/Client/js/main.js')
  setTimeout(() => {
    if (window.WOW) new WOW().init()
    if (window.$ && $('.owl-carousel').length) {
      $('.owl-carousel').owlCarousel()
    }
    window.dispatchEvent(new Event('resize'))
  }, 300)

  // Khởi tạo kênh real-time cho học viên
  khoiTaoStudentChannel()
})

onBeforeUnmount(() => {
  const studentId = parseInt(localStorage.getItem('nguoi_dung_id') || '0', 10)
  if (window.Echo && studentId) {
    try { window.Echo.leave(`student.${studentId}`) } catch (_) {}
  }
})
</script>


<style>
</style>