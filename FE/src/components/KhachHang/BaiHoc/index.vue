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
            Danh Sách Bài Học
          </h2>
  
          <p class="text-secondary fs-5">
            Bé có thể chọn bài học yêu thích để luyện phát âm và học từ vựng.
          </p>
  
          <p
            v-if="chuDeTen"
            class="fw-semibold mt-2 mb-0"
            style="color: #0d3b66;"
          >
            Đang xem chủ đề: {{ chuDeTen }}
          </p>
        </div>
  
        <!-- Bộ lọc -->
        <div class="bg-white rounded-5 shadow-sm p-4 mb-5">
          <div class="row g-3 align-items-center">
            <!-- Thanh tìm kiếm -->
            <div class="col-lg-5">
              <div class="position-relative">
                <i
                  class="bi bi-search position-absolute top-50 translate-middle-y ms-3"
                  style="color: #adb5bd; font-size: 16px; left: 4px;"
                ></i>
                <input
                  type="text"
                  class="form-control rounded-pill border-0 bg-light py-3 ps-5 pe-4"
                  placeholder="Tìm bài học, chủ đề..."
                  v-model="tuKhoa"
                />
              </div>
            </div>

            <!-- Lọc cấp độ -->
            <div class="col-lg-7">
              <div class="d-flex flex-wrap gap-2 justify-content-lg-end align-items-center">
                <span class="text-muted small me-1">Cấp độ:</span>
                <button
                  v-for="cd in danhSachCapDo"
                  :key="cd.value"
                  type="button"
                  class="btn rounded-pill px-3 py-2"
                  :class="capDoChon === cd.value ? cd.classActive : cd.classOutline"
                  @click="chonCapDo(cd.value)"
                >
                  {{ cd.label }}
                </button>
              </div>
            </div>
          </div>

          <!-- Lọc chủ đề -->
          <div class="row mt-3">
            <div class="col-12">
              <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted small me-1">Chủ đề:</span>
                <button
                  type="button"
                  class="btn rounded-pill px-4"
                  :class="danhMucChon === null ? 'btn-primary' : 'btn-outline-primary'"
                  @click="chonDanhMuc(null)"
                >
                  Tất Cả
                </button>
                <button
                  v-for="(dm, idx) in danhSachChuDe"
                  :key="dm.id"
                  type="button"
                  class="btn rounded-pill px-4"
                  :class="danhMucChon === dm.id
                    ? TOPIC_BTN_ACTIVE[idx % TOPIC_BTN_ACTIVE.length]
                    : TOPIC_BTN_OUTLINE[idx % TOPIC_BTN_OUTLINE.length]"
                  @click="chonDanhMuc(dm.id)"
                >
                  {{ dm.ten_danh_muc }}
                </button>
              </div>
            </div>
          </div>

          <!-- Kết quả tìm kiếm -->
          <div v-if="tuKhoa || danhMucChon || capDoChon" class="row mt-3">
            <div class="col-12">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <small class="text-muted">Đang lọc:</small>
                <span v-if="tuKhoa" class="badge bg-light text-dark border rounded-pill px-3 py-2">
                  <i class="bi bi-search me-1"></i>{{ tuKhoa }}
                  <button type="button" class="btn-close ms-2" style="font-size:10px;" @click="tuKhoa = ''"></button>
                </span>
                <span v-if="danhMucChon" class="badge bg-light text-dark border rounded-pill px-3 py-2">
                  <i class="bi bi-bookmark me-1"></i>{{ tenDanhMucChon }}
                  <button type="button" class="btn-close ms-2" style="font-size:10px;" @click="chonDanhMuc(null)"></button>
                </span>
                <span v-if="capDoChon" class="badge bg-light text-dark border rounded-pill px-3 py-2">
                  <i class="bi bi-bar-chart me-1"></i>{{ tenCapDoChon }}
                  <button type="button" class="btn-close ms-2" style="font-size:10px;" @click="chonCapDo('')"></button>
                </span>
                <button
                  type="button"
                  class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                  @click="xoaBoLoc"
                >
                  Xóa tất cả
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Lộ trình được gán (học viên đăng nhập) -->
        <div
          v-if="hienThiKhoiLoTrinh"
          class="bg-white rounded-5 shadow-sm mb-5 border border-light overflow-hidden"
        >
          <button
            type="button"
            class="btn w-100 d-flex align-items-center justify-content-between gap-3 p-4 border-0 bg-transparent text-start"
            @click="moLoTrinh = !moLoTrinh"
          >
            <div>
              <h3 class="fw-bold mb-1" style="color: #0d3b66;">Lộ Trình Của Bé</h3>
              <p class="text-muted small mb-0">Bài học theo thứ tự giáo viên đã sắp xếp</p>
            </div>
            <i
              class="bi fs-4"
              :class="moLoTrinh ? 'bi-chevron-up' : 'bi-chevron-down'"
              style="color: #0d3b66; transition: transform 0.25s;"
            ></i>
          </button>

          <div v-show="moLoTrinh" class="px-4 pb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
              <div style="min-width: 220px; max-width: 100%;">
                <select
                  v-model.number="loTrinhChonId"
                  class="form-select rounded-pill border-0 bg-light py-2 px-3"
                  :disabled="dangTaiLoTrinh || danhSachLoTrinh.length === 0"
                  @change="loadChiTietLoTrinh"
                >
                  <option
                    v-for="lt in danhSachLoTrinh"
                    :key="lt.id"
                    :value="lt.id"
                  >
                    {{ lt.ten_lo_trinh }}{{ lt.can_mua ? ' (cần thanh toán)' : '' }}
                  </option>
                </select>
              </div>
            </div>

            <div v-if="dangTaiLoTrinh" class="text-muted small py-2">Đang tải lộ trình...</div>

            <template v-else-if="danhSachLoTrinh.length > 0 && loTrinhHienTai">
              <div v-if="loTrinhError" class="alert alert-warning rounded-4 mb-0 py-2 small">{{ loTrinhError }}</div>

              <div v-else-if="loTrinhHienTai.can_mua" class="rounded-4 p-3" style="background: #fff8f5; border: 1px solid #ffe4d9;">
                <p class="mb-2 small text-dark">
                  Lộ trình trả phí — <strong>{{ formatVnd(loTrinhHienTai.gia) }}</strong>. Thanh toán từ trang tiến độ để xem bài theo thứ tự.
                </p>
                <button
                  type="button"
                  class="btn rounded-pill px-4 py-2 fw-bold text-white"
                  style="background: linear-gradient(135deg, #10b981, #059669);"
                  @click="goToTienDo"
                >
                  Đi tới trang Tiến độ
                </button>
              </div>

              <div
                v-else-if="loTrinhHienTai.la_tra_phi && !loTrinhHienTai.tra_phi_da_duyet"
                class="alert alert-warning rounded-4 mb-0 py-2 small"
              >
                Giá lộ trình đang chờ duyệt. Vui lòng quay lại sau.
              </div>

              <div v-else-if="loTrinhHienTai.can_hoc">
                <div v-if="dangTaiChiTietLoTrinh" class="text-muted small py-3">Đang tải danh sách bài...</div>
                <div v-else-if="chiTietLoTrinh.length === 0" class="text-muted small py-2">
                  Chưa có bài học trong lộ trình này.
                </div>
                <ol v-else class="list-group list-group-numbered list-group-flush rounded-4 overflow-hidden border">
                  <li
                    v-for="(row) in chiTietLoTrinh"
                    :key="row.bai_hoc_id"
                    class="list-group-item d-flex flex-wrap align-items-center justify-content-between gap-2 py-3"
                  >
                    <div class="flex-grow-1" style="min-width: 200px;">
                      <span class="fw-semibold" style="color: #0d3b66;">{{ row.tieu_de || 'Bài học' }}</span>
                      <p v-if="row.ghi_chu_gv" class="small text-muted mb-0 mt-1">{{ row.ghi_chu_gv }}</p>
                    </div>
                    <button
                      type="button"
                      class="btn btn-sm rounded-pill px-3 fw-bold text-white shrink-0"
                      style="background: linear-gradient(135deg, #ff6b35, #ff8c42);"
                      @click="vaoChiTietBaiHoc(row.bai_hoc_id)"
                    >
                      Học ngay
                    </button>
                  </li>
                </ol>
              </div>
            </template>
          </div>
        </div>
  
        <!-- Danh sách bài học -->
        <div v-if="lessons.length === 0 && !dangTai" class="text-center py-5">
          <div class="mb-3" style="font-size: 64px;">🔍</div>
          <h5 class="fw-bold" style="color: #0d3b66;">Không tìm thấy bài học</h5>
          <p class="text-muted">Thử thay đổi từ khóa hoặc bộ lọc để xem nhiều bài học hơn.</p>
          <button class="btn btn-primary rounded-pill px-4" @click="xoaBoLoc">Xem tất cả bài học</button>
        </div>

        <div class="row g-4">
          <div
            class="col-xl-3 col-lg-4 col-md-6"
            v-for="lesson in lessons"
            :key="lesson.id"
          >
            <div class="card border-0 rounded-5 shadow-sm overflow-hidden lesson-card h-100">
              <div class="position-relative">
                <img
                  :src="lesson.image"
                  class="card-img-top"
                  style="height: 220px; object-fit: cover;"
                  alt=""
                />
  
                <span
                  class="position-absolute top-0 start-0 badge rounded-pill px-3 py-2 m-3"
                  :style="{
                    backgroundColor: lesson.topicColor,
                    color: '#fff'
                  }"
                >
                  {{ lesson.topic }}
                </span>
              </div>
  
              <div class="card-body p-4">
                <div
                  class="rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3"
                  :style="{
                    width: '70px',
                    height: '70px',
                    backgroundColor: lesson.iconBg
                  }"
                >
                  <span style="font-size: 34px;">
                    {{ lesson.icon }}
                  </span>
                </div>
  
                <h4 class="fw-bold mb-2" style="color: #0d3b66;">
                  {{ lesson.title }}
                </h4>
  
                <p class="text-muted mb-4">
                  {{ lesson.description }}
                </p>
  
                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Tiến Độ</small>
                    <small class="fw-bold" :style="{ color: lesson.topicColor }">
                      {{ lesson.progress }}%
                    </small>
                  </div>
  
                  <div
                    class="progress rounded-pill"
                    style="height: 10px; background-color: #f1f1f1;"
                  >
                    <div
                      class="progress-bar rounded-pill"
                      role="progressbar"
                      :style="{
                        width: lesson.progress + '%',
                        backgroundColor: lesson.topicColor
                      }"
                    ></div>
                  </div>
                </div>
  
                <button
                  type="button"
                  class="btn w-100 rounded-pill py-3 fw-bold text-white"
                  :style="{ backgroundColor: lesson.topicColor }"
                  @click="vaoChiTietBaiHoc(lesson.id)"
                >
                  Học Ngay
                </button>
              </div>
            </div>
          </div>
        </div>
  <div class="text-center mt-5" v-if="coThemBaiHoc || dangTaiThem">
    <p class="text-muted mb-3">
      Bé muốn khám phá thêm nhiều bài học thú vị hơn?
    </p>

    <button
      class="btn rounded-pill px-5 py-3 fw-bold text-white shadow-sm"
      style="background: linear-gradient(135deg, #ff6b35, #ff8c42); transition: all 0.3s ease;"
      :disabled="dangTaiThem"
      @click="xemThem"
      @mouseover="$event.currentTarget.style.transform='translateY(-4px)'"
      @mouseout="$event.currentTarget.style.transform='translateY(0px)'"
    >
      <span v-if="dangTaiThem">
        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
        Đang tải...
      </span>
      <span v-else>
        Xem Thêm Bài Học
        <i class="bi bi-arrow-down-circle ms-2"></i>
      </span>
    </button>
  </div>
        <!-- Banner -->
  <div class="position-relative overflow-hidden rounded-5 shadow-sm bg-white mt-5">
    <div class="row align-items-center">
      <div class="col-lg-6 p-4 p-lg-5">
        <span class="badge bg-warning text-dark rounded-pill px-4 py-2 mb-3 fs-6">
          Kho Bài Học EchoKids
        </span>
  
        <h1
          class="fw-bold mb-4"
          style="
            color: #0d3b66;
            font-size: 58px;
            line-height: 1.2;
            font-family: 'Lobster Two', cursive;
          "
        >
          Chọn Bài Học<br />
          Bé Yêu Thích
        </h1>
  
        <p class="text-secondary fs-5 mb-4" style="max-width: 600px;">
          Hàng trăm bài học thú vị đang chờ bé khám phá với hình ảnh sinh động,
          âm thanh vui nhộn và nhiều chủ đề hấp dẫn mỗi ngày.
        </p>
  
        <div class="d-flex flex-wrap gap-3 mb-4">
          <button class="btn start-btn rounded-pill px-4 py-3 fw-bold text-white">
            Học Ngay
          </button>
  
          <button class="btn btn-light border rounded-pill px-4 py-3 fw-bold shadow-sm">
            Xem Tiến Độ
          </button>
        </div>
  
  
      </div>
  
      <div class="col-lg-6 position-relative text-center py-5">
        <div class="position-relative d-inline-block">
          <!-- vòng nền -->
          <div
            class="position-absolute top-50 start-50 translate-middle rounded-circle"
            style="
              width: 460px;
              height: 460px;
              background: #fff3ef;
              z-index: 1;
            "
          ></div>
  
          <!-- ảnh chính -->
          <div
            class="position-relative rounded-circle overflow-hidden shadow-lg"
            style="
              width: 370px;
              height: 370px;
              border: 10px solid #fff;
              z-index: 2;
            "
          >
            <img
              src="https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=900&q=80"
              class="w-100 h-100"
              style="object-fit: cover;"
              alt=""
            />
          </div>
  
  <!-- card nhỏ 1 -->
  <div
    class="position-absolute bg-white rounded-circle shadow-sm d-flex flex-column align-items-center justify-content-center text-center"
    style="
      top: 25px;
      left: -20px;
      width: 110px;
      height: 110px;
      z-index: 3;
      border: 6px solid #fff1eb;
    "
  >
    <div
      class="rounded-circle d-flex align-items-center justify-content-center mb-2"
      style="
        width: 42px;
        height: 42px;
        background: #fff1eb;
      "
    >
      <span style="font-size: 20px;">🔤</span>
    </div>
    <p
      class="mb-0 fw-bold"
      style="
        font-size: 13px;
        color: #0d3b66;
        line-height: 1.3;
      "
    >
      Bảng Chữ Cái
    </p>
  </div>
  
  <!-- card nhỏ 2 -->
  <div
    class="position-absolute bg-white rounded-circle shadow-sm d-flex flex-column align-items-center justify-content-center text-center"
    style="
      bottom: -10px;
      left: -15px;
      width: 105px;
      height: 105px;
      z-index: 3;
      border: 6px solid #e8faf5;
    "
  >
    <div
      class="rounded-circle d-flex align-items-center justify-content-center mb-2"
      style="
        width: 40px;
        height: 40px;
        background: #e8faf5;
      "
    >
      <span style="font-size: 20px;">🐶</span>
    </div>
    <p
      class="mb-0 fw-bold"
      style="
        font-size: 13px;
        color: #0d3b66;
        line-height: 1.3;
      "
    >
      Động Vật
    </p>
  </div>
  
  <!-- card nhỏ 3 -->
  <div
    class="position-absolute bg-white rounded-circle shadow-sm d-flex flex-column align-items-center justify-content-center text-center"
    style="
      top: 190px;
      right: -35px;
      width: 105px;
      height: 105px;
      z-index: 4;
      border: 6px solid #fff8e6;
    "
  >
    <div
      class="rounded-circle d-flex align-items-center justify-content-center mb-2"
      style="
        width: 40px;
        height: 40px;
        background: #fff8e6;
      "
    >
      <span style="font-size: 20px;">🍎</span>
    </div>
    <p
      class="mb-0 fw-bold"
      style="
        font-size: 13px;
        color: #0d3b66;
        line-height: 1.3;
      "
    >
      Trái Cây
    </p>
  </div>
        </div>
      </div>
    </div>
  </div>
      </div>
      
    </div>
  </template>
  
<script>
import { API_BASE } from '../../../api/http.js';
import axios from 'axios';

const LESSON_STYLE_PRESETS = [
    {
        image: 'https://media.istockphoto.com/id/1473080999/vi/vec-to/nguy%C3%AAn-%C3%A2m-nh%C3%A2n-v%E1%BA%ADt-linh-v%E1%BA%ADt-c%C3%A1c-y%E1%BA%BFu-t%E1%BB%91-gi%C3%A1o-d%E1%BB%A5c.jpg?s=612x612&w=0&k=20&c=SH5Czo-RdNyEAycNNenn-1FnXWCntvmuHO27Uky7GSE=',
        icon: '🔤',
        topicColor: '#ff6b35',
        iconBg: '#fff1eb',
    },
    {
        image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRa9EJpKeIEZiYEFzQKiWxEHtUGhx18CiMkew&s',
        icon: '👨‍👩‍👧‍👦',
        topicColor: '#20c997',
        iconBg: '#e8faf5',
    },
    {
        image: 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?auto=format&fit=crop&w=800&q=80',
        icon: '🍎',
        topicColor: '#f4a100',
        iconBg: '#fff8e6',
    },
    {
        image: 'https://images.unsplash.com/photo-1494256997604-768d1f608cac?auto=format&fit=crop&w=800&q=80',
        icon: '🐱',
        topicColor: '#ff4d6d',
        iconBg: '#ffeaf0',
    },
    {
        image: 'https://buulong.com.vn/wp-content/uploads/2026/02/hinh-anh-cac-loai-hoa-dep.jpg',
        icon: '🌻',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://llv.edu.vn/media/2018/11/BLOG-PHOTO-Back-to-School-stationery-v1-20170115-1.jpg',
        icon: '📐',
        topicColor: '#4d96ff',
        iconBg: '#eaf3ff',
    },
    {
        image: 'https://kendotoy.com/wp-content/uploads/2023/07/lo-to-cac-ph%C6%B0%C6%A1ng-tien-giao-thong-1.jpg',
        icon: '🚗',
        topicColor: '#4d96ff',
        iconBg: '#eaf3ff',
    },
    {
        image: 'https://ila.edu.vn/wp-content/uploads/2024/01/chao-hoi-bang-tieng-anh-1.jpg',
        icon: '👋',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://vnmedia2.monkeyuni.net/upload/web/storage_web/10-04-2023_11:54:31_6-cach-phat-am-chu-p.jpg',
        icon: '🔤',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://thammydoctor.com/wp-content/uploads/2022/06/khuon-mat-trai-xoan-de-thuong.jpg',
        icon: '😀',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://lifemadesweeter.com/veggie-platter/how-to-make-a-veggie-platter-recipe-photo-picture/',
        icon: '🍠',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://epic7travel.com/wp-content/uploads/2019/10/African-Safari-Animals-Elephant-procession-sunset-1.jpg',
        icon: '🐘',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://ngoaithatxanh.vn/wp-content/uploads/2025/06/cham-soc-cay-xanh-quan-12.jpg',
        icon: '🌳',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://images.stockcake.com/public/2/3/6/236caabc-de97-4c14-bbfe-8821e1f0f01a_large/color-palette-array-stockcake.jpg',
        icon: '🎨',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://freedompointefl.com/wp-content/uploads/d06a6ca4-diversecareers-2050x854.jpg',
        icon: '🧑‍⚕️',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://www.kidsup.net/wp-content/uploads/2024/07/tap-cho-tre-dien-dat-nhu-cau-bang-loi.jpg',
        icon: '💬',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://res.hailinhquehuong.com/media/images/Article/2010/39/luyen_21.gif',
        icon: '👅',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://cdn.popsww.com/blog-kids/sites/3/2023/04/bai-hat-tieng-anh-ve-bo-phan-co-the-nguoi-7.jpg',
        icon: '💪',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://giadinh.mediacdn.vn/296230595582509056/2025/4/13/img2451-1744532197494776196164.jpeg',
        icon: '🍛',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://www.noaa.gov/sites/default/files/styles/landscape_width_650/public/legacy/image/2019/Jun/coral%20ecosystems%20reeffish.jpg?itok=YEV0WvB2',
        icon: '🐠',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://sohanews.sohacdn.com/160588918557773824/2022/8/29/photo-1-16617598134781477408834.png',
        icon: '⛅',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=800&q=80',
        icon: '⬜',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://baokhanhhoa.vn/file/e7837c02857c8ca30185a8c39b582c03/052026/tt5_20260514103249.jpg',
        icon: '🏡',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://media.istockphoto.com/id/2194054490/photo/happy-smiling-senior-parents-with-adult-daughter-drinking-tea.jpg?s=612x612&w=0&k=20&c=b14MrOu1bQOX8P2JH_YtrZik2WtjEGA7bQQ1IGpjqxQ=',
        icon: '💬',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
        image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTvFpwGPWq5uPgkB2uDv2GielgDa5auldnu1jlQ7AGd0pw0OgUu',
        icon: '🔠',
        topicColor: '#6f42c1',
        iconBg: '#f3ebff',
    },
    {
      image: 'https://cdn.popsww.com/blog-kids-learn/sites/5/2022/08/tieng-anh-bo-phan-co-the-cho-be-1.jpg',
      icon: '🦵',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://gcs.tripi.vn/public-tripi/tripi-feed/img/473896irP/do-uong-mua-he1.jpg',
      icon: '🥛',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://dietcontrung24h.com.vn/wp-content/uploads/2018/10/nhung-thong-tin-can-biet-ve-cong-trung-1.jpg',
      icon: '🐛',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://hoc24.vn/source/%C4%90%E1%BB%8BaTHCS/C%C3%A1nh%20Di%E1%BB%81u/3-43.jpg',
      icon: '⛰️',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://i.ytimg.com/vi/gEcvnnuTFwg/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLCcOh_wiq0GG1_fKLflNgkdwCfwkg',
      icon: '🔢',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://ctsvietnam.vn/wp-content/uploads/2023/09/1-ky-nang-quan-ly-thoi-gian-nhu-the-nao-la-hieu-qua.jpg',
      icon: '🕐',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://vnmedia2.monkeyuni.net/upload/web/img/02-cach-phat-am-phu-am-tieng-viet.png',
      icon: '🗣️',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://vnmedia2.monkeyuni.net/upload/web/img/02-cach-phat-am-phu-am-tieng-viet.png',
      icon: '🗣️',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://lh3.googleusercontent.com/proxy/mIyzd5j2kHqA8hA3mFwUGDRPk2INlM_LNr57fG20ryhYBvID_nznO8b-SqzndIG_93OrockDPpCL9o8jLgKCJT3B4W3y2yE_H0n_94gY3t4Zsfcn33MWQIVwMpbqMdRh',
      icon: '😁',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://toomva.com/tai-lieu/anhweb/102015/am-thanh-cac-loai-dong-vat.jpg',
      icon: '🐖',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://sna.edu.vn/wp-content/uploads/2025/03/hoat-dong-ngoai-khoa-o-truong-thumbnail.jpg',
      icon: '🏫',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://www.art2all.net/tho/lathuy/dau-thanh-trong-tieng-viet.jpg',
      icon: '❓',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://sakuraschools.edu.vn/wp-content/uploads/2023/04/NguyC3AAn-C3A2m-phE1BBA5-C3A2m-trong-bE1BAA3ng-chE1BBAF-cC3A1i-tiE1BABFng-ViE1BB87t.jpg',
      icon: '🔡',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },
    {
      image: 'https://i.ytimg.com/vi/nls1YMtg6w0/hq720.jpg?sqp=-oaymwE7CK4FEIIDSFryq4qpAy0IARUAAAAAGAElAADIQj0AgKJD8AEB-AH8CYAC0AWKAgwIABABGH8gGigTMA8=&rs=AOn4CLBM5Pi-w2THESRtrUvtxwLw73rt9w',
      icon: '🔡',
      topicColor: '#6f42c1',
      iconBg: '#f3ebff',
    },

];

function getLessonStyleByOrderIndex(orderIndex) {
    if (LESSON_STYLE_PRESETS.length === 0) {
        return {
            image: '',
            icon: '📘',
            topicColor: '#6f42c1',
            iconBg: '#f3ebff',
        };
    }
    const numericOrder = Number(orderIndex);
    const safeOrder = Number.isFinite(numericOrder) ? Math.max(Math.floor(numericOrder), 0) : 0;
    return LESSON_STYLE_PRESETS[safeOrder % LESSON_STYLE_PRESETS.length];
}

const TOPIC_BTN_OUTLINE = [
    'btn-outline-warning',
    'btn-outline-success',
    'btn-outline-danger',
    'btn-outline-info',
    'btn-outline-secondary',
];
const TOPIC_BTN_ACTIVE = [
    'btn-warning',
    'btn-success',
    'btn-danger',
    'btn-info',
    'btn-secondary',
];

export default {
    name: 'LessonPage',
    data() {
        return {
            apiBase: (API_BASE).replace(/\/$/, ''),
            lessons: [],
            chuDeTen: '',
            tuKhoa: '',
            capDoChon: '',
            danhMucChon: null,
            danhSachChuDe: [],
            dangTai: false,
            dangTaiThem: false,
            coThemBaiHoc: false,
            trangHienTai: 1,
            debounceTimer: null,
            abortController: null,
            lessonStyleById: {},
            lessonStyleReadyPromise: null,
            danhSachLoTrinh: [],
            loTrinhChonId: null,
            chiTietLoTrinh: [],
            loTrinhError: '',
            dangTaiLoTrinh: false,
            dangTaiChiTietLoTrinh: false,
            moLoTrinh: false,
            TOPIC_BTN_OUTLINE,
            TOPIC_BTN_ACTIVE,
            danhSachCapDo: [
                { value: '', label: 'Tất Cả', classOutline: 'btn-outline-primary', classActive: 'btn-primary' },
                { value: 'basic', label: 'Cơ Bản', classOutline: 'btn-outline-success', classActive: 'btn-success' },
                { value: 'intermediate', label: 'Trung Cấp', classOutline: 'btn-outline-warning', classActive: 'btn-warning text-white' },
                { value: 'advanced', label: 'Nâng Cao', classOutline: 'btn-outline-danger', classActive: 'btn-danger' },
            ],
        };
    },
    computed: {
        tenDanhMucChon() {
            const dm = this.danhSachChuDe.find((d) => d.id === this.danhMucChon);
            return dm?.ten_danh_muc || '';
        },
        tenCapDoChon() {
            const cd = this.danhSachCapDo.find((c) => c.value === this.capDoChon);
            return cd?.label || '';
        },
        coTokenHocVien() {
            return !!localStorage.getItem('token_nguoi_dung');
        },
        hienThiKhoiLoTrinh() {
            return this.coTokenHocVien && (this.dangTaiLoTrinh || this.danhSachLoTrinh.length > 0);
        },
        loTrinhHienTai() {
            if (!this.loTrinhChonId) return null;
            return this.danhSachLoTrinh.find((l) => Number(l.id) === Number(this.loTrinhChonId)) || null;
        },
    },
    mounted() {
        const idFromRoute = this.$route.query.danh_muc_id;
        if (idFromRoute) {
            this.danhMucChon = Number(idFromRoute);
        }
        this.fetchDanhMuc();
        this.fetchLessons(false);
        this.fetchDanhSachLoTrinh();
    },
    beforeUnmount() {
        clearTimeout(this.debounceTimer);
        if (this.abortController) {
            this.abortController.abort();
        }
    },
    watch: {
        '$route.query.danh_muc_id'(newVal) {
            this.danhMucChon = newVal ? Number(newVal) : null;
            this.fetchLessons(false);
        },
        tuKhoa() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.fetchLessons(false);
            }, 400);
        },
    },
    methods: {
        authHeaders() {
            const token = localStorage.getItem('token_nguoi_dung');
            return token ? { Authorization: `Bearer ${token}` } : {};
        },
        formatVnd(n) {
            if (n == null || n === '') return '0 đ';
            return `${Number(n).toLocaleString('vi-VN')} đ`;
        },
        goToTienDo() {
            this.$router.push('/tien-do');
        },
        async fetchDanhSachLoTrinh() {
            const token = localStorage.getItem('token_nguoi_dung');
            if (!token) {
                this.danhSachLoTrinh = [];
                this.loTrinhChonId = null;
                this.chiTietLoTrinh = [];
                return;
            }
            this.dangTaiLoTrinh = true;
            this.loTrinhError = '';
            try {
                const { data: res } = await axios.get(`${this.apiBase}/api/hoc-vien/lo-trinh-ca-nhan`, {
                    headers: this.authHeaders(),
                });
                if (res.status && Array.isArray(res.data)) {
                    this.danhSachLoTrinh = res.data;
                    const stillValid = this.danhSachLoTrinh.some(
                        (l) => Number(l.id) === Number(this.loTrinhChonId)
                    );
                    if (!stillValid) {
                        this.loTrinhChonId = this.danhSachLoTrinh.length ? this.danhSachLoTrinh[0].id : null;
                    }
                    await this.loadChiTietLoTrinh();
                } else {
                    this.danhSachLoTrinh = [];
                    this.loTrinhChonId = null;
                    this.chiTietLoTrinh = [];
                }
            } catch (err) {
                if (err.response?.status === 401) {
                    this.danhSachLoTrinh = [];
                    this.loTrinhChonId = null;
                    this.chiTietLoTrinh = [];
                } else {
                    this.loTrinhError = 'Không tải được danh sách lộ trình.';
                    this.danhSachLoTrinh = [];
                    this.loTrinhChonId = null;
                    this.chiTietLoTrinh = [];
                }
            } finally {
                this.dangTaiLoTrinh = false;
            }
        },
        async loadChiTietLoTrinh() {
            this.loTrinhError = '';
            const lt = this.loTrinhHienTai;
            if (!lt || !lt.can_hoc) {
                this.chiTietLoTrinh = [];
                this.dangTaiChiTietLoTrinh = false;
                return;
            }
            this.dangTaiChiTietLoTrinh = true;
            this.chiTietLoTrinh = [];
            try {
                const { data: res } = await axios.get(
                    `${this.apiBase}/api/hoc-vien/lo-trinh-ca-nhan/${lt.id}`,
                    { headers: this.authHeaders() }
                );
                if (res.status && res.data?.chi_tiet) {
                    const raw = Array.isArray(res.data.chi_tiet) ? res.data.chi_tiet : [];
                    this.chiTietLoTrinh = [...raw].sort(
                        (a, b) => Number(a.thu_tu_uu_tien || 0) - Number(b.thu_tu_uu_tien || 0)
                    );
                }
            } catch (err) {
                if (err.response?.status === 403 || err.response?.data?.code === 'REQUIRES_PURCHASE') {
                    this.chiTietLoTrinh = [];
                } else if (err.response?.status === 401) {
                    this.chiTietLoTrinh = [];
                } else {
                    this.loTrinhError = 'Không tải được chi tiết lộ trình.';
                }
            } finally {
                this.dangTaiChiTietLoTrinh = false;
            }
        },
        bindLessonStyles(rows, startIndex) {
            rows.forEach((row, i) => {
                this.lessonStyleById[String(row.id)] = getLessonStyleByOrderIndex(startIndex + i);
            });
        },
        async ensureLessonStylesLoaded() {
            if (this.lessonStyleReadyPromise) {
                return this.lessonStyleReadyPromise;
            }

            this.lessonStyleReadyPromise = (async () => {
                let page = 1;
                let orderIndex = 0;
                let hasMore = true;

                while (hasMore) {
                    const { data } = await axios.get(`${this.apiBase}/api/bai-hoc`, {
                        params: { page },
                    });
                    const rows = Array.isArray(data?.data) ? data.data : [];
                    this.bindLessonStyles(rows, orderIndex);
                    orderIndex += rows.length;
                    hasMore = data?.pagination?.con_trang_tiep ?? false;
                    page += 1;
                }
            })();

            return this.lessonStyleReadyPromise;
        },
        getLessonStyleForRow(row, fallbackIndex) {
            const styleKey = String(row.id);
            if (!this.lessonStyleById[styleKey]) {
                this.lessonStyleById[styleKey] = getLessonStyleByOrderIndex(fallbackIndex);
            }
            return this.lessonStyleById[styleKey];
        },
        mapBaiHocToLesson(row, orderIndex) {
            const style = this.getLessonStyleForRow(row, orderIndex);
            const dm = row.danh_muc;
            return {
                id: row.id,
                title: row.tieu_de,
                description: row.mo_ta || '',
                image: style.image,
                topic: dm?.ten_danh_muc || 'Bài học',
                icon: style.icon,
                progress: 0,
                topicColor: style.topicColor,
                iconBg: style.iconBg,
            };
        },
        async fetchLessonProgress() {
            const token = localStorage.getItem('token_nguoi_dung');
            if (!token || this.lessons.length === 0) {
                this.lessons = this.lessons.map((lesson) => ({ ...lesson, progress: 0 }));
                return;
            }

            try {
                const { data: res } = await axios.get(
                    `${this.apiBase}/api/tien-do-bai-hoc/tong-quan`,
                    {
                        headers: this.authHeaders(),
                        params: {
                            bai_hoc_limit: 1000,
                            bai_hoc_offset: 0,
                        },
                    }
                );

                const danhSachTienDo = Array.isArray(res?.data?.bai_hocs?.danh_sach)
                    ? res.data.bai_hocs.danh_sach
                    : [];

                const progressMap = new Map(
                    danhSachTienDo.map((item) => [
                        Number(item.bai_hoc_id),
                        Number(item.tien_do || 0),
                    ])
                );

                this.lessons = this.lessons.map((lesson) => ({
                    ...lesson,
                    progress: progressMap.get(Number(lesson.id)) || 0,
                }));
            } catch (err) {
                if (err.response?.status === 401) {
                    this.lessons = this.lessons.map((lesson) => ({ ...lesson, progress: 0 }));
                    return;
                }
                console.error(err);
            }
        },
        chonDanhMuc(id) {
            this.danhMucChon = id;
            this.fetchLessons(false);
        },
        chonCapDo(value) {
            this.capDoChon = value;
            this.fetchLessons(false);
        },
        xoaBoLoc() {
            this.tuKhoa = '';
            this.capDoChon = '';
            this.danhMucChon = null;
            this.$router.push({ path: '/bai-hoc' });
            this.fetchLessons(false);
        },
        xemTatCaBaiHoc() {
            this.xoaBoLoc();
        },
        xemThem() {
            if (!this.coThemBaiHoc || this.dangTaiThem) return;
            this.fetchLessons(true);
        },
        vaoChiTietBaiHoc(id) {
            this.$router.push({ path: `/chi-tiet-bai-hoc/${id}` });
        },
        fetchDanhMuc() {
            axios
                .get(`${this.apiBase}/api/danh-muc-bai-hoc`)
                .then((res) => {
                    this.danhSachChuDe = Array.isArray(res.data?.data) ? res.data.data : [];
                })
                .catch((err) => {
                    console.error(err);
                    this.danhSachChuDe = [];
                });
        },
        fetchLessons(themVao) {
            // Hủy request đang chạy nếu có
            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();

            if (themVao) {
                this.trangHienTai += 1;
                this.dangTaiThem = true;
            } else {
                this.trangHienTai = 1;
                this.dangTai = true;
            }

            const params = { page: this.trangHienTai };
            if (this.danhMucChon) params.danh_muc_id = this.danhMucChon;
            if (this.tuKhoa.trim()) params.tim_kiem = this.tuKhoa.trim();
            if (this.capDoChon) params.cap_do = this.capDoChon;

            axios
                .get(`${this.apiBase}/api/bai-hoc`, {
                    params,
                    signal: this.abortController.signal,
                })
                .then((res) => {
                    const data = res.data;
                    const rows = Array.isArray(data?.data) ? data.data : [];
                    const startIndex = themVao ? this.lessons.length : 0;
                    const dangLoc = !!(this.danhMucChon || this.capDoChon || this.tuKhoa.trim());

                    if (!dangLoc) {
                        this.bindLessonStyles(rows, startIndex);
                    }

                    return (dangLoc ? this.ensureLessonStylesLoaded() : Promise.resolve()).then(() => ({
                        data,
                        rows,
                        startIndex,
                    }));
                })
                .then(({ data, rows, startIndex }) => {
                    const mapped = rows.map((row, i) => this.mapBaiHocToLesson(row, startIndex + i));

                    if (themVao) {
                        this.lessons = [...this.lessons, ...mapped];
                    } else {
                        this.lessons = mapped;
                    }

                    this.coThemBaiHoc = data?.pagination?.con_trang_tiep ?? false;
                    this.chuDeTen = data?.meta?.danh_muc?.ten_danh_muc || '';
                    this.fetchLessonProgress();
                })
                .catch((err) => {
                    if (err.code === 'ERR_CANCELED') return;
                    console.error(err);
                    if (!themVao) {
                        this.lessons = [];
                        this.chuDeTen = '';
                    }
                    this.coThemBaiHoc = false;
                })
                .finally(() => {
                    this.dangTai = false;
                    this.dangTaiThem = false;
                });
        },
    },
};
</script>
  
  <style scoped>
  .start-btn {
    background: linear-gradient(135deg, #ff6b35, #ff8c42);
    transition: all 0.3s ease;
  }
  
  .start-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(255, 107, 53, 0.25);
  }
  
  .lesson-card {
    transition: all 0.3s ease;
  }
  
  .lesson-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 35px rgba(0, 0, 0, 0.12) !important;
  }
  
  .lesson-card img {
    transition: all 0.4s ease;
  }
  
  .lesson-card:hover img {
    transform: scale(1.05);
  }
  .content-wrapper { width: 92%; max-width: 1650px; margin: 0 auto; } .page-full { width: 100vw; margin-left: calc(-50vw + 50%); }
  </style>