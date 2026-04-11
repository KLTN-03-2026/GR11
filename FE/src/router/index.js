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
  },

  //---------------------------------------------KHACH HANG--------------------------------------------------------------
  {
    path: "/chu-de",
    component: () => import("../components/KhachHang/ChuDe/index.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/bai-hoc",
    component: () => import("../components/KhachHang/BaiHoc/index.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/chi-tiet-bai-hoc",
    component: () => import("../components/KhachHang/ChiTietBaiHoc/index.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/bai-kiem-tra",
    component: () => import("../components/KhachHang/BaiKiemTra/index.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/luyen-tap",
    component: () => import("../components/KhachHang/LuyenTap/index.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/xep-hang",
    component: () => import("../components/KhachHang/XepHang/index.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/chuoi-ngay-hoc",
    component: () => import("../components/KhachHang/ChuoiNgayHoc/index.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/hoan-thanh-kiem-tra",
    component: () => import("../components/KhachHang/HoanThanhKT/index.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/tien-do",
    component: () => import("../components/KhachHang/TienDo/index.vue"),
    meta: { layout: "client" },
  },

  //---------------------------------------------ADMIN--------------------------------------------------------------
  {
    path: "/admin/dashboard",
    component: () => import("../components/AHeThong/Admin/Dashboard/index.vue"),
    meta: { layout: "admin" },
  },
  {
    path: "/admin/quan-ly-tai-khoan",
    component: () =>
      import("../components/AHeThong/Admin/QuanLyTaiKhoan/index.vue"),
    meta: { layout: "admin" },
  },
  {
    path: "/admin/kiem-duyet",
    component: () => import("../components/AHeThong/Admin/KiemDuyet/index.vue"),
    meta: { layout: "admin" },
  },
  {
    path: "/admin/thong-ke",
    component: () => import("../components/AHeThong/Admin/ThongKe/index.vue"),
    meta: { layout: "admin" },
  },
  {
    path: "/admin/phan-quyen",
    component: () => import("../components/AHeThong/Admin/PhanQuyen/index.vue"),
    meta: { layout: "admin" },
  },
  {
    path: "/admin/quan-ly-cau-hinh",
    component: () =>
      import("../components/AHeThong/Admin/QuanLyCauHinhHeThong/index.vue"),
    meta: { layout: "admin" },
  },
  {
    path: "/thong-tin-ca-nhan",
    component: () => import("../components/Client/Profile/index.vue"),
    meta: { layout: "admin" },
  },

  //---------------------------------------------TEACHER--------------------------------------------------------------
  {
    path: "/teacher/dashboard",
    component: () => import("../components/AHeThong/Teach/Dashboard/index.vue"),
    meta: { layout: "teach" },
  },
  {
    path: "/teacher/quan-ly-hoc-sinh",
    component: () => import("../components/AHeThong/Teach/QuanLyHocSinh/index.vue"),
    meta: { layout: "teach" },
  },
  {
    path: "/teacher/quan-ly-bai-hoc",
    component: () => import("../components/AHeThong/Teach/QuanLyBaiHoc/index.vue"),
    meta: { layout: "teach" },
  },
  {
    path: "/teacher/bao-cao-thong-ke",
    component: () => import("../components/AHeThong/Teach/BaoCaoThongKe/index.vue"),
    meta: { layout: "teach" },
  },
  {
    path: "/teacher/tu-vung",
    component: () => import("../components/AHeThong/Teach/TuVung/index.vue"),
    meta: { layout: "teach" },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
