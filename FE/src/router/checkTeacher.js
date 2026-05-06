import axios from "axios";
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({ position: "top-right" });
const TEACHER_AUTH_CACHE_KEY = "teacher_auth_checked_at";
const TEACHER_AUTH_CACHE_TTL_MS = 60 * 1000;

export default function (to, from, next) {
  const token = localStorage.getItem("token_teacher");

  if (!token) {
    toaster.error("Vui lòng đăng nhập với tài khoản Giáo viên!");
    return next("/dang-nhap");
  }

  const cachedAt = Number(sessionStorage.getItem(TEACHER_AUTH_CACHE_KEY) || 0);
  const now = Date.now();
  if (cachedAt > 0 && now - cachedAt < TEACHER_AUTH_CACHE_TTL_MS) {
    return next();
  }

  axios
    .get("http://127.0.0.1:8000/api/check-token", {
      headers: { Authorization: "Bearer " + token },
      timeout: 5000,
    })
    .then((response) => {
      if (response.data.status && response.data.vai_tro_id === 2) {
        const d = response.data;
        sessionStorage.setItem(TEACHER_AUTH_CACHE_KEY, String(Date.now()));
        if (d.id != null && d.id !== "") {
          localStorage.setItem("nguoi_dung_id", String(d.id));
        } else {
          localStorage.removeItem("nguoi_dung_id");
        }
        localStorage.setItem("ho_ten", d.ho_ten ?? "");
        localStorage.setItem("email", d.email ?? "");
        localStorage.setItem("check_token", String(d.status));
        if (d.ten_vai_tro != null && d.ten_vai_tro !== "") {
          localStorage.setItem("ten_vai_tro", String(d.ten_vai_tro));
        } else {
          localStorage.removeItem("ten_vai_tro");
        }
        if (d.anh_dai_dien) {
          localStorage.setItem("anh_dai_dien", String(d.anh_dai_dien));
        } else {
          localStorage.removeItem("anh_dai_dien");
        }
        next();
      } else {
        toaster.error("Khu vực này chỉ dành cho Giáo viên!");
        next("/");
      }
    })
    .catch((error) => {
      if (error?.response?.status === 403) {
        toaster.error(
          error?.response?.data?.message ||
            "Tài khoản của bạn đã vi phạm chính sách bảo mật của chúng tôi"
        );
      } else {
        toaster.error("Phiên đăng nhập hết hạn hoặc lỗi xác thực!");
      }
      sessionStorage.removeItem(TEACHER_AUTH_CACHE_KEY);
      localStorage.removeItem("token_teacher");
      next("/dang-nhap");
    });
}
