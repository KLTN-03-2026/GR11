import { createRouter, createWebHistory } from "vue-router";
import checkClient from "./checkClient";
import checkAdmin from "./checkAdmin";
import checkTeacher from "./checkTeacher";

const routes = [
  //---------------------------------------------CLIENT--------------------------------------------------------------
  {
    path: "/",
    component: () => import("../components/Client/TrangChu/index.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/trang-chu",
    component: () => import("../components/Client/TrangChu/index.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/dang-nhap",
    component: () => import("../components/Client/DangNhap/index.vue"),
    meta: { layout: "blank" },
  },
  {
    path: "/dang-ky",
    component: () => import("../components/Client/DangKy/index.vue"),
    meta: { layout: "blank" },
  },
  {
    path: "/quen-mat-khau",
    component: () => import("../components/Client/QuenMatKhau/index.vue"),
    meta: { layout: "blank" },
  },
  {
    path: "/profile",
    component: () => import("../components/Client/Profile/index.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/chat-box",
    component: () => import("../components/Client/ChatBox/index.vue"),
    meta: { layout: "client" },
    beforeEnter: checkClient,
  },
  {
    path: "/on-tap-loi",
    component: () => import("../components/Client/OnTapLoiSai/index.vue"),
    meta: { layout: "blank" },
    beforeEnter: checkClient,
  },

  //---------------------------------------------KHACH HANG--------------------------------------------------------------
  {
    path: "/chu-de",
    component: () => import("../components/KhachHang/ChuDe/index.vue"),
    meta: { layout: "client" },
    beforeEnter: checkClient,
  },
  {
    path: "/bai-hoc",
    component: () => import("../components/KhachHang/BaiHoc/index.vue"),
    meta: { layout: "client" },
    beforeEnter: checkClient,
  },
  {
    path: "/chi-tiet-bai-hoc/:id",
    component: () => import("../components/KhachHang/ChiTietBaiHoc/index.vue"),
    meta: { layout: "client" },
    beforeEnter: checkClient,
  },
  {
    path: "/bai-kiem-tra",
    component: () => import("../components/KhachHang/BaiKiemTra/index.vue"),
    meta: { layout: "client" },
    beforeEnter: checkClient,
  },
  {
    path: "/luyen-tap",
    component: () => import("../components/KhachHang/LuyenTap/index.vue"),
    meta: { layout: "client" },
    beforeEnter: checkClient,
  },
  {
    path: "/xep-hang",
    component: () => import("../components/KhachHang/XepHang/index.vue"),
    meta: { layout: "client" },
    beforeEnter: checkClient,
  },
  {
    path: "/chuoi-ngay-hoc",
    component: () => import("../components/KhachHang/ChuoiNgayHoc/index.vue"),
    meta: { layout: "client" },
    beforeEnter: checkClient,
  },
  {
    path: "/hoan-thanh-kiem-tra",
    component: () => import("../components/KhachHang/HoanThanhKT/index.vue"),
    meta: { layout: "client" },
    beforeEnter: checkClient,
  },
  {
    path: "/tien-do",
    component: () => import("../components/KhachHang/TienDo/index.vue"),
    meta: { layout: "client" },
    beforeEnter: checkClient,
  },

  //---------------------------------------------ADMIN--------------------------------------------------------------
  {
    path: "/admin/dashboard",
    component: () => import("../components/AHeThong/Admin/Dashboard/index.vue"),
    meta: { layout: "admin" },
    beforeEnter: checkAdmin,
  },
  {
    path: "/admin/quan-ly-tai-khoan",
    component: () =>
      import("../components/AHeThong/Admin/QuanLyTaiKhoan/index.vue"),
    meta: { layout: "admin" },
    beforeEnter: checkAdmin,
  },
  {
    path: "/admin/kiem-duyet",
    component: () => import("../components/AHeThong/Admin/KiemDuyet/index.vue"),
    meta: { layout: "admin" },
    beforeEnter: checkAdmin,
  },
  {
    path: "/admin/thong-ke",
    redirect: "/admin/dashboard?tab=reports",
    meta: { layout: "admin" },
    beforeEnter: checkAdmin,
  },
  {
    path: "/admin/phan-quyen",
    component: () => import("../components/AHeThong/Admin/PhanQuyen/index.vue"),
    meta: { layout: "admin" },
    beforeEnter: checkAdmin,
  },
  {
    path: "/admin/quan-ly-cau-hinh",
    component: () =>
      import("../components/AHeThong/Admin/QuanLyCauHinhHeThong/index.vue"),
    meta: { layout: "admin" },
    beforeEnter: checkAdmin,
  },
  {
    path: "/thong-tin-ca-nhan",
    component: () => import("../components/Client/Profile/index.vue"),
    meta: { layout: "admin" },
    beforeEnter: checkAdmin,
  },

  //---------------------------------------------TEACHER--------------------------------------------------------------
  {
    path: "/teacher/dashboard",
    component: () => import("../components/AHeThong/Teach/Dashboard/index.vue"),
    meta: { layout: "teach" },
    beforeEnter: checkTeacher,
  },
  {
    path: "/teacher/quan-ly-hoc-sinh",
    component: () =>
      import("../components/AHeThong/Teach/QuanLyHocSinh/index.vue"),
    meta: { layout: "teach" },
    beforeEnter: checkTeacher,
  },
  {
    path: "/teacher/quan-ly-bai-hoc",
    component: () =>
      import("../components/AHeThong/Teach/QuanLyBaiHoc/index.vue"),
    meta: { layout: "teach" },
    beforeEnter: checkTeacher,
  },
  {
    path: "/teacher/bao-cao-thong-ke",
    component: () =>
      import("../components/AHeThong/Teach/BaoCaoThongKe/index.vue"),
    meta: { layout: "teach" },
    beforeEnter: checkTeacher,
  },
  {
    path: "/teacher/tu-vung",
    component: () => import("../components/AHeThong/Teach/TuVung/index.vue"),
    meta: { layout: "teach" },
    beforeEnter: checkTeacher,
  },
  {
    path: "/teacher/chat-box",
    component: () => import("../components/Client/ChatBox/index.vue"),
    props: { mode: "teacher", floating: false },
    meta: { layout: "teach" },
    beforeEnter: checkTeacher,
  },
  {
    path: "/teacher/thong-tin-ca-nhan",
    component: () => import("../components/Client/Profile/index.vue"),
    meta: { layout: "teach" },
    beforeEnter: checkTeacher,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
