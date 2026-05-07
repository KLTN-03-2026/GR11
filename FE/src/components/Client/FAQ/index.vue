<template>
  <div class="faq-page">
    <!-- Header -->
    <div class="faq-header">
      <div class="faq-header-inner">
        <div class="faq-icon"><i class="fa-solid fa-circle-question"></i></div>
        <h1>Câu Hỏi Thường Gặp</h1>
        <p>Tổng hợp các thắc mắc phổ biến về nền tảng EchoKids</p>
      </div>
    </div>

    <div class="faq-body">

      <!-- Nhóm 1 -->
      <div class="faq-group">
        <div class="group-title">
          <i class="fa-solid fa-user-plus"></i>
          <span>Tài khoản &amp; Đăng ký</span>
        </div>
        <div class="faq-list">
          <div v-for="(item, i) in group1" :key="i" class="faq-item" :class="{ open: open1 === i }" @click="open1 = open1 === i ? null : i">
            <div class="faq-q">
              <span>{{ item.q }}</span>
              <i class="fa-solid fa-chevron-down faq-arrow"></i>
            </div>
            <div class="faq-a">{{ item.a }}</div>
          </div>
        </div>
      </div>

      <!-- Nhóm 2 -->
      <div class="faq-group">
        <div class="group-title">
          <i class="fa-solid fa-book-open"></i>
          <span>Sử dụng &amp; Học tập</span>
        </div>
        <div class="faq-list">
          <div v-for="(item, i) in group2" :key="i" class="faq-item" :class="{ open: open2 === i }" @click="open2 = open2 === i ? null : i">
            <div class="faq-q">
              <span>{{ item.q }}</span>
              <i class="fa-solid fa-chevron-down faq-arrow"></i>
            </div>
            <div class="faq-a">{{ item.a }}</div>
          </div>
        </div>
      </div>

      <!-- Nhóm 3 -->
      <div class="faq-group">
        <div class="group-title">
          <i class="fa-solid fa-credit-card"></i>
          <span>Thanh toán &amp; Dịch vụ</span>
        </div>
        <div class="faq-list">
          <div v-for="(item, i) in group3" :key="i" class="faq-item" :class="{ open: open3 === i }" @click="open3 = open3 === i ? null : i">
            <div class="faq-q">
              <span>{{ item.q }}</span>
              <i class="fa-solid fa-chevron-down faq-arrow"></i>
            </div>
            <div class="faq-a">{{ item.a }}</div>
          </div>
        </div>
      </div>

      <!-- Nhóm 4 -->
      <div class="faq-group">
        <div class="group-title">
          <i class="fa-solid fa-shield-halved"></i>
          <span>Dữ liệu &amp; Quyền riêng tư</span>
        </div>
        <div class="faq-list">
          <div v-for="(item, i) in group4" :key="i" class="faq-item" :class="{ open: open4 === i }" @click="open4 = open4 === i ? null : i">
            <div class="faq-q">
              <span>{{ item.q }}</span>
              <i class="fa-solid fa-chevron-down faq-arrow"></i>
            </div>
            <div class="faq-a">{{ item.a }}</div>
          </div>
        </div>
      </div>

      <!-- Liên hệ -->
      <div class="faq-contact">
        <i class="fa-solid fa-headset"></i>
        <div>
          <p>Không tìm thấy câu trả lời bạn cần? Liên hệ với chúng tôi qua email:</p>
          <a href="mailto:supportechokids@gmail.com">supportechokids@gmail.com</a>
        </div>
      </div>

      <!-- Nút quay về -->
      <div class="faq-back">
        <router-link to="/" class="back-btn">
          <i class="fa-solid fa-arrow-left"></i> Quay về Trang Chủ
        </router-link>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  name: 'FAQ',
  data() {
    return {
      open1: null,
      open2: null,
      open3: null,
      open4: null,
      group1: [
        {
          q: 'Tôi cần cung cấp những thông tin gì khi đăng ký tài khoản?',
          a: 'Bạn bắt buộc cần cung cấp Họ tên và Địa chỉ Email. Số điện thoại và Ngày sinh là thông tin tùy chọn, không bắt buộc.',
        },
        {
          q: 'Trẻ em có thể tự tạo tài khoản không?',
          a: 'Nền tảng được thiết kế cho trẻ em sử dụng trực tiếp, nhưng việc đăng ký tài khoản yêu cầu phải có sự đồng ý và giám sát của phụ huynh hoặc người giám hộ hợp pháp.',
        },
      ],
      group2: [
        {
          q: 'EchoKids là ứng dụng cần tải về máy hay sử dụng trên trình duyệt?',
          a: 'EchoKids hoạt động trực tiếp trên nền tảng website, không cần tải ứng dụng về máy.',
        },
        {
          q: 'Hệ thống đánh giá phát âm của trẻ bằng cách nào?',
          a: 'EchoKids thu âm giọng nói của người dùng và sử dụng thuật toán Trí tuệ nhân tạo (AI) để phân tích, đánh giá mức độ chính xác của phát âm.',
        },
      ],
      group3: [
        {
          q: 'Làm thế nào để thanh toán nâng cấp các tính năng trả phí?',
          a: 'Bạn có thể thanh toán bằng cách quét mã QR hiển thị trên hệ thống. Vui lòng đảm bảo ghi đúng nội dung chuyển khoản theo yêu cầu.',
        },
        {
          q: 'Có thể chia sẻ tài khoản cho người khác cùng học không?',
          a: 'Không nên. Việc chia sẻ tài khoản sẽ làm sai lệch dữ liệu lịch sử ghi âm và hệ thống AI sẽ đánh giá sai tiến độ học tập của người học chính.',
        },
      ],
      group4: [
        {
          q: 'Dữ liệu âm thanh và thông tin của tôi được lưu trữ ở đâu?',
          a: 'Toàn bộ dữ liệu được mã hóa và lưu trữ an toàn trên nền tảng điện toán đám mây (Cloud).',
        },
        {
          q: 'Dữ liệu giọng nói của con tôi có bị lộ hoặc chia sẻ cho bên khác không?',
          a: 'Không. EchoKids cam kết tuyệt đối không bán, trao đổi hoặc chia sẻ dữ liệu âm thanh của người dùng cho bất kỳ bên thứ ba nào.',
        },
        {
          q: 'Tôi có thể xóa một bản ghi âm cụ thể trong lịch sử học tập không?',
          a: 'Không. Để hệ thống có thể đánh giá chính xác sự tiến bộ qua thời gian, bạn không thể tự xóa từng bản ghi âm riêng lẻ.',
        },
        {
          q: 'Làm sao để xóa hoàn toàn dữ liệu của tôi khỏi hệ thống?',
          a: 'Bạn có quyền yêu cầu xóa toàn bộ tài khoản. Khi đó, toàn bộ dữ liệu cá nhân và lịch sử âm thanh sẽ được hủy bỏ vĩnh viễn khỏi hệ thống.',
        },
      ],
    };
  },
  created() {
    document.title = 'FAQ – EchoKids';
  },
};
</script>

<style scoped>
.faq-page {
  background: linear-gradient(135deg, #f0f4ff 0%, #fdf0f8 100%);
  padding-bottom: 60px;
}

/* Header */
.faq-header {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  padding: 56px 16px 48px;
  text-align: center;
  color: #fff;
}

.faq-header-inner {
  max-width: 640px;
  margin: 0 auto;
}

.faq-icon {
  font-size: 52px;
  margin-bottom: 14px;
}

.faq-header h1 {
  font-size: 28px;
  font-weight: 800;
  margin: 0 0 8px;
}

.faq-header p {
  font-size: 15px;
  opacity: 0.85;
  margin: 0;
}

/* Body */
.faq-body {
  max-width: 820px;
  margin: 0 auto;
  padding: 40px 16px 0;
}

/* Group */
.faq-group {
  margin-bottom: 32px;
}

.group-title {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 17px;
  font-weight: 700;
  color: #ef4444;
  margin-bottom: 14px;
  padding-left: 4px;
}

.group-title i {
  font-size: 18px;
}

/* FAQ item */
.faq-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.faq-item {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(99,102,241,0.07);
  overflow: hidden;
  cursor: pointer;
  transition: box-shadow 0.2s;
}

.faq-item:hover {
  box-shadow: 0 4px 20px rgba(239,68,68,0.12);
}

.faq-q {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  padding: 18px 20px;
  font-size: 15px;
  font-weight: 600;
  color: #1e293b;
}

.faq-arrow {
  color: #ef4444;
  font-size: 13px;
  flex-shrink: 0;
  transition: transform 0.25s;
}

.faq-item.open .faq-arrow {
  transform: rotate(180deg);
}

.faq-a {
  max-height: 0;
  overflow: hidden;
  font-size: 14px;
  color: #475569;
  line-height: 1.7;
  padding: 0 20px;
  transition: max-height 0.3s ease, padding 0.3s ease;
}

.faq-item.open .faq-a {
  max-height: 300px;
  padding: 0 20px 18px;
}

/* Contact */
.faq-contact {
  display: flex;
  align-items: center;
  gap: 16px;
  background: #fef2f2;
  border: 1.5px solid #fecaca;
  border-radius: 14px;
  padding: 20px 24px;
  margin-top: 8px;
  margin-bottom: 32px;
}

.faq-contact > i {
  font-size: 28px;
  color: #ef4444;
  flex-shrink: 0;
}

.faq-contact p {
  margin: 0 0 4px;
  font-size: 14px;
  color: #374151;
}

.faq-contact a {
  font-size: 14px;
  font-weight: 600;
  color: #ef4444;
  text-decoration: none;
}

.faq-contact a:hover { text-decoration: underline; }

/* Back */
.faq-back {
  text-align: center;
}

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #ef4444;
  color: #fff;
  padding: 11px 26px;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.2s;
}

.back-btn:hover { background: #dc2626; color: #fff; }
</style>
