import axios from "axios";
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({ position: "top-right" });

export default function (to, from, next) {
  var token = localStorage.getItem("token_admin");

  if (!token) {
    toaster.error("Bạn chưa đăng nhập. Vui lòng đăng nhập!");
    return next("/dang-nhap");
  }

  axios
    .get("http://127.0.0.1:8000/api/check-token", {
      headers: { Authorization: "Bearer " + token },
    })
    .then((response) => {
      if (response.data.status && response.data.vai_tro_id === 1) {
        const d = response.data;
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
        toaster.error("Bạn không có quyền truy cập khu vực này!");
        next("/dang-nhap");
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
      localStorage.removeItem("token_admin");
      next("/dang-nhap");
    });
}
