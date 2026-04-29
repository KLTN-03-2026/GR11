import axios from "axios";
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({ position: "top-right" });

export default function (to, from, next) {
  var token = localStorage.getItem("token_nguoi_dung");

  if (!token) {
    toaster.error("Vui lòng đăng nhập để sử dụng chức năng này.");
    return next("/dang-nhap");
  }

  axios
    .get("http://127.0.0.1:8000/api/check-token", {
      headers: { Authorization: "Bearer " + token },
    })
    .then((response) => {
      if (response.data.status) {
        const d = response.data;
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
        toaster.error("Phiên đăng nhập hết hạn!");
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
        toaster.error("Vui lòng đăng nhập lại!");
      }
      localStorage.removeItem("token_nguoi_dung");
      next("/dang-nhap");
    });
}
