<template>
    <div class="page-full bg-light min-vh-100">
      
       <div class="content-wrapper py-5">
        <!-- Tiêu đề -->
        <div class="text-center mb-5">
          <h2
            class="fw-bold mb-3"
            style="
              color: #0d3b66;
              font-size: 48px;
  
            "
          >
            Danh Sách Chủ Đề
          </h2>
  
          <p class="text-secondary fs-5">
            Bé có thể chọn chủ đề yêu thích để luyện phát âm và học từ vựng.
          </p>
        </div>
  
        <!-- Card chủ đề -->
        <div class="row g-4">
          <div
            class="col-xl-3 col-lg-4 col-md-6"
            v-for="topic in topics"
            :key="topic.id"
          >
            <div class="card border-0 rounded-5 shadow-sm h-100 overflow-hidden topic-card">
              <div class="position-relative">
                <img
                  :src="topic.image"
                  class="card-img-top"
                  style="height: 220px; object-fit: cover;"
                  alt=""
                />
  
                <span
                  class="position-absolute top-0 end-0 badge rounded-pill px-3 py-2 m-3"
                  :style="{
                    backgroundColor: topic.badgeColor,
                    color: '#fff'
                  }"
                >
                  {{ topic.totalLesson }} bài học
                </span>
              </div>
  
              <div class="card-body p-4 text-center">
                <div
                  class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 shadow-sm"
                  :style="{
                    width: '80px',
                    height: '80px',
                    backgroundColor: topic.iconBg
                  }"
                >
                  <span style="font-size: 36px;">
                    {{ topic.icon }}
                  </span>
                </div>
  
                <h4 class="fw-bold mb-2" style="color: #0d3b66;">
                  {{ topic.name }}
                </h4>
  
                <p class="text-muted mb-4">
                  {{ topic.description }}
                </p>
  
                <router-link
                  :to="{
                    path: '/bai-hoc',
                    query: { danh_muc_id: String(topic.id) },
                  }"
                  class="btn rounded-pill px-4 py-2 fw-bold text-white text-decoration-none d-inline-block"
                  :style="{ backgroundColor: topic.badgeColor }"
                >
                  Khám Phá
                </router-link>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Banner -->
              <div class="bg-white rounded-5 shadow-sm overflow-hidden p-4 p-lg-5 mt-5">
          <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
              <span class="badge bg-warning text-dark rounded-pill px-4 py-2 mb-3 fs-6">
                Chủ đề luyện phát âm
              </span>
  
              <h1
                class="fw-bold mb-4"
                style="
                  color: #0d3b66;
                  font-size: 52px;
                  line-height: 1.2;
            
                "
              >
                Khám Phá Các Chủ Đề Cùng EchoKids
              </h1>
  
              <p class="text-secondary fs-5 mb-4">
                Bé có thể lựa chọn chủ đề yêu thích để luyện nghe, luyện nói và học thêm nhiều từ vựng thú vị mỗi ngày.
              </p>
  
              <div class="row g-3 mb-4">
                <div class="col-4">
                  <div
                    class="bg-light rounded-4 text-center py-3 shadow-sm"
                    style="border: 2px solid #ffe8e3;"
                  >
                    <h3 class="fw-bold mb-1" style="color: #ff6b35;">20+</h3>
                    <small class="text-muted">Chủ Đề</small>
                  </div>
                </div>
  
                <div class="col-4">
                  <div
                    class="bg-light rounded-4 text-center py-3 shadow-sm"
                    style="border: 2px solid #e3f7ee;"
                  >
                    <h3 class="fw-bold mb-1" style="color: #198754;">150+</h3>
                    <small class="text-muted">Bài Học</small>
                  </div>
                </div>
  
                <div class="col-4">
                  <div
                    class="bg-light rounded-4 text-center py-3 shadow-sm"
                    style="border: 2px solid #fff3cd;"
                  >
                    <h3 class="fw-bold mb-1" style="color: #f4a100;">100%</h3>
                    <small class="text-muted">Vui Nhộn</small>
                  </div>
                </div>
              </div>
  
              <button class="btn rounded-pill px-5 py-3 fw-bold text-white shadow-sm topic-btn">
                Khám Phá Ngay
              </button>
            </div>
  
            <div class="col-lg-6 text-center position-relative">
              <div class="position-relative d-inline-block">
                <!-- ảnh lớn -->
                <div
                  class="rounded-circle overflow-hidden shadow-lg mx-auto"
                  style="
                    width: 430px;
                    height: 430px;
                    border: 12px solid #fff4ef;
                  "
                >
                  <img
                    src="https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=800&q=80"
                    class="w-100 h-100"
                    style="object-fit: cover;"
                    alt=""
                  />
                </div>
  
                <!-- ảnh nhỏ trái -->
                <div
                  class="position-absolute top-0 start-0 bg-white rounded-circle shadow p-2"
                  style="width: 120px; height: 120px;"
                >
                  <img
                    src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=300&q=80"
                    class="w-100 h-100 rounded-circle"
                    style="object-fit: cover;"
                    alt=""
                  />
                </div>
  
                <!-- ảnh nhỏ phải -->
                <div
                  class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow p-2"
                  style="width: 110px; height: 110px;"
                >
                  <img
                    src="https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=300&q=80"
                    class="w-100 h-100 rounded-circle"
                    style="object-fit: cover;"
                    alt=""
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  const TOPIC_STYLE_PRESETS = [
      {
          image:
              'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=800&q=80',
          icon: '📚',
          badgeColor: '#ff6b35',
          iconBg: '#fff1eb',
      },
      {
          image:
              'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?auto=format&fit=crop&w=800&q=80',
          icon: '🍎',
          badgeColor: '#f4a100',
          iconBg: '#fff8e6',
      },
      {
          image:
              'https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&w=800&q=80',
          icon: '👨‍👩‍👧',
          badgeColor: '#198754',
          iconBg: '#eaf8f0',
      },
      {
          image:
              'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=800&q=80',
          icon: '🚗',
          badgeColor: '#4d96ff',
          iconBg: '#eaf3ff',
      },
      {
          image:
              'https://images.unsplash.com/photo-1494256997604-768d1f608cac?auto=format&fit=crop&w=800&q=80',
          icon: '🎨',
          badgeColor: '#ff4d6d',
          iconBg: '#ffeaf0',
      },
      {
          image:
              'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=800&q=80',
          icon: '🏫',
          badgeColor: '#6f42c1',
          iconBg: '#f3ebff',
      },
      {
          image:
              'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
          icon: '🍔',
          badgeColor: '#fd7e14',
          iconBg: '#fff1e8',
      },
      {
          image:
              'https://images.unsplash.com/photo-1519345182560-3f2917c472ef?auto=format&fit=crop&w=800&q=80',
          icon: '🖐️',
          badgeColor: '#20c997',
          iconBg: '#e8faf5',
      },
  ];
  
  export default {
      name: 'TopicPage',
      data() {
          return {
              topics: [],
          };
      },
      mounted() {
          this.fetchTopics();
      },
      methods: {
          mapDanhMucToTopic(row, index) {
              const style =
                  TOPIC_STYLE_PRESETS[index % TOPIC_STYLE_PRESETS.length];
              return {
                  id: row.id,
                  slug: row.slug_danh_muc,
                  name: row.ten_danh_muc,
                  description: row.mo_ta || '',
                  totalLesson: row.so_luong_bai_hoc ?? 0,
                  image: style.image,
                  icon: style.icon,
                  badgeColor: style.badgeColor,
                  iconBg: style.iconBg,
              };
          },
          fetchTopics() {
              axios
                  .get('http://127.0.0.1:8000/api/danh-muc-bai-hoc')
                  .then((res) => {
                      const rows = Array.isArray(res.data?.data)
                          ? res.data.data
                          : [];
                      this.topics = rows.map((row, i) =>
                          this.mapDanhMucToTopic(row, i)
                      );
                  })
                  .catch((err) => {
                      console.error(err);
                      this.topics = [];
                  });
          },
      },
  };
  </script>
  
  <style scoped>
  .topic-btn {
    background: linear-gradient(135deg, #ff6b35, #ff8c42);
    transition: all 0.3s ease;
  }
  
  .topic-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(255, 107, 53, 0.25);
  }
  
  .topic-card {
    transition: all 0.3s ease;
  }
  
  .topic-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 35px rgba(0, 0, 0, 0.12) !important;
  }
  
  .topic-card img {
    transition: all 0.4s ease;
  }
  
  .topic-card:hover img {
    transform: scale(1.05);
  }
  .content-wrapper { width: 92%; max-width: 1650px; margin: 0 auto; } .page-full { width: 100vw; margin-left: calc(-50vw + 50%); }
  </style>