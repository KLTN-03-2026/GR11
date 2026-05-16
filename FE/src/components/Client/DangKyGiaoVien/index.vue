<template>
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card border-0 shadow-sm dk-card">
        <div class="card-body">
          <div class="text-center">
            <div class="icon-circle-xl mx-auto mb-3">
              <i class="fa-solid fa-chalkboard-user fa-2x text-primary"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">Đăng ký Giáo viên</h3>
            <p class="text-muted">
              Điền đầy đủ thông tin bên dưới để nộp hồ sơ. Admin sẽ xét duyệt và
              thông báo qua email.
            </p>
          </div>

          <div v-if="loadingStatus" class="text-center py-4">
            <span class="spinner-border text-primary"></span>
          </div>

          <template v-else>
            <div
              v-if="hoSo && hoSo.trang_thai === 0"
              class="alert-box alert-waiting mb-4"
            >
              <i class="fa-solid fa-hourglass-half me-2"></i>
              <strong>Hồ sơ đang chờ duyệt</strong>
              <p class="mb-0 mt-1 small">
                Nộp ngày: {{ hoSo.created_at }}. Vui lòng chờ
                <b>Nhân Viên EchoKids</b>
                xét duyệt.
              </p>
            </div>

            <div
              v-else-if="hoSo && hoSo.trang_thai === 1"
              class="alert-box alert-approved mb-4"
            >
              <i class="fa-solid fa-circle-check me-2"></i>
              <strong>Hồ sơ đã được duyệt!</strong>
              <p class="mb-0 mt-1 small">
                Tài khoản của bạn đã được nâng cấp lên Giáo viên.
              </p>
            </div>

            <div
              v-else-if="hoSo && hoSo.trang_thai === 2"
              class="alert-box alert-rejected mb-4"
            >
              <i class="fa-solid fa-circle-xmark me-2"></i>
              <strong>Hồ sơ bị từ chối</strong>
              <p class="mb-0 mt-1 small">Lý do: {{ hoSo.ghi_chu_admin }}</p>
              <p class="mb-0 mt-1 small text-secondary">
                Bạn có thể chỉnh sửa và nộp lại hồ sơ bên dưới.
              </p>
            </div>

            <form
              v-if="!hoSo || hoSo.trang_thai === 2"
              @submit.prevent="submitHoSo"
            >
              <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                1. Thông tin cá nhân
              </h5>
              <div class="row g-4 mb-4">
                <div class="col-md-6">
                  <label class="form-label"
                    >Họ và tên <span class="text-danger">*</span></label
                  >
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.ho_ten"
                    placeholder="Nhập họ và tên"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input
                    type="email"
                    class="form-control bg-light"
                    :value="email"
                    disabled
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Số điện thoại</label>
                  <input
                    type="tel"
                    class="form-control"
                    :class="{ 'is-invalid': phoneError }"
                    v-model="form.so_dien_thoai"
                    placeholder="Ví dụ: 0901234567"
                    @input="validatePhone"
                  />
                  <div v-if="phoneError" class="invalid-feedback">
                    {{ phoneError }}
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label"
                    >Nghề nghiệp <span class="text-danger">*</span></label
                  >
                  <select
                    class="form-control"
                    v-model="form.chuyen_mon"
                    style="height: auto"
                  >
                    <option value="" disabled>-- Chọn nghề nghiệp --</option>
                    <option value="Giáo viên">Giáo viên</option>
                    <option value="Chuyên gia">Chuyên gia</option>
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label">Giới thiệu bản thân</label>
                  <textarea
                    class="form-control"
                    v-model="form.mo_ta"
                    rows="2"
                    placeholder="Mô tả kinh nghiệm giảng dạy, thành tích..."
                  ></textarea>
                </div>
                <div class="col-6">
                  <label class="form-label">Ảnh đại diện (Tùy chọn)</label>
                  <div class="upload-area-sm" @click="$refs.anhInput.click()">
                    <input
                      ref="anhInput"
                      type="file"
                      class="d-none"
                      accept="image/*"
                      @change="(e) => handleFile(e, 'anh_dai_dien')"
                    />
                    <div v-if="!files.anh_dai_dien">
                      <i class="fa-solid fa-image text-primary mb-1"></i>
                      <p class="mb-0 small">Chọn ảnh đại diện</p>
                    </div>
                    <div
                      v-else
                      class="d-flex align-items-center justify-content-between px-2 w-100 gap-2"
                    >
                      <span
                        class="text-truncate small fw-bold text-success text-start"
                        style="flex: 1; min-width: 0"
                      >
                        <i class="fa-solid fa-check me-1"></i
                        >{{ files.anh_dai_dien.name }}
                      </span>
                      <button
                        type="button"
                        class="btn-close-sm flex-shrink-0"
                        @click.stop="removeFile('anh_dai_dien')"
                      >
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <h5 class="fw-bold text-primary mb-3 mt-5 border-bottom pb-2">
                2. Định danh cá nhân (CCCD/CMND)
              </h5>
              <div class="row g-4 mb-4">
                <div class="col-md-6">
                  <label class="form-label"
                    >CCCD Mặt trước <span class="text-danger">*</span></label
                  >
                  <div
                    class="upload-area-sm"
                    @click="$refs.cccdTruocInput.click()"
                  >
                    <input
                      ref="cccdTruocInput"
                      type="file"
                      class="d-none"
                      accept="image/*"
                      @change="(e) => handleFile(e, 'cccd_mat_truoc')"
                    />
                    <div v-if="!files.cccd_mat_truoc">
                      <i class="fa-solid fa-id-card text-primary mb-1"></i>
                      <p class="mb-0 small">Tải lên mặt trước</p>
                    </div>
                    <div
                      v-else
                      class="d-flex align-items-center justify-content-between px-2"
                    >
                      <span class="text-truncate small fw-bold text-success"
                        ><i class="fa-solid fa-check me-1"></i
                        >{{ files.cccd_mat_truoc.name }}</span
                      >
                      <button
                        type="button"
                        class="btn-close-sm"
                        @click.stop="removeFile('cccd_mat_truoc')"
                      >
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label"
                    >CCCD Mặt sau <span class="text-danger">*</span></label
                  >
                  <div
                    class="upload-area-sm"
                    @click="$refs.cccdSauInput.click()"
                  >
                    <input
                      ref="cccdSauInput"
                      type="file"
                      class="d-none"
                      accept="image/*"
                      @change="(e) => handleFile(e, 'cccd_mat_sau')"
                    />
                    <div v-if="!files.cccd_mat_sau">
                      <i class="fa-solid fa-id-card text-primary mb-1"></i>
                      <p class="mb-0 small">Tải lên mặt sau</p>
                    </div>
                    <div
                      v-else
                      class="d-flex align-items-center justify-content-between px-2"
                    >
                      <span class="text-truncate small fw-bold text-success"
                        ><i class="fa-solid fa-check me-1"></i
                        >{{ files.cccd_mat_sau.name }}</span
                      >
                      <button
                        type="button"
                        class="btn-close-sm"
                        @click.stop="removeFile('cccd_mat_sau')"
                      >
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <h5 class="fw-bold text-primary mb-3 mt-5 border-bottom pb-2">
                3. Hồ sơ chuyên môn
              </h5>
              <div class="row g-4 mb-4">
                <div class="col-12">
                  <label class="form-label"
                    >Bằng cấp (ĐH/CĐ) hoặc Chứng chỉ bổ trợ
                    <span class="text-danger">*</span></label
                  >
                  <div
                    class="upload-area-sm p-4"
                    @click="$refs.bangCapInput.click()"
                  >
                    <input
                      ref="bangCapInput"
                      type="file"
                      class="d-none"
                      accept=".jpg,.jpeg,.png,.pdf"
                      @change="(e) => handleFile(e, 'bang_cap')"
                    />
                    <div v-if="!files.bang_cap" class="text-center">
                      <i
                        class="fa-solid fa-certificate fa-2x text-primary mb-2"
                      ></i>
                      <p class="mb-0 text-muted">
                        Click để tải lên tài liệu chứng minh năng lực
                      </p>
                    </div>
                    <div
                      v-else
                      class="d-flex align-items-center justify-content-center gap-3 px-3 w-100"
                    >
                      <i
                        class="fa-solid fa-file-circle-check text-success fa-xl flex-shrink-0"
                      ></i>
                      <span
                        class="text-truncate fw-bold text-dark text-start"
                        style="flex: 1; min-width: 0"
                      >
                        {{ files.bang_cap.name }}
                      </span>
                      <button
                        type="button"
                        class="btn btn-sm btn-light flex-shrink-0"
                        @click.stop="removeFile('bang_cap')"
                      >
                        <i class="fa-solid fa-xmark"></i> Xóa
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="text-end mt-5 pt-3 border-top">
                <button
                  type="button"
                  @click="$router.back()"
                  class="btn btn-light me-3 px-4"
                >
                  Quay lại
                </button>
                <button
                  type="submit"
                  class="btn btn-primary px-5"
                  :disabled="isSubmitting"
                >
                  <span
                    v-if="isSubmitting"
                    class="spinner-border spinner-border-sm me-2"
                  ></span>
                  <i class="fa-solid fa-paper-plane me-2" v-else></i>Nộp hồ sơ
                </button>
              </div>
            </form>

            <div v-else class="text-center mt-4">
              <button
                type="button"
                @click="$router.back()"
                class="btn btn-light px-5"
              >
                <i class="fa-solid fa-arrow-left me-2"></i>Quay lại Profile
              </button>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

const API = "http://127.0.0.1:8000/api";

export default {
  name: "DangKyGiaoVien",
  data() {
    return {
      hoSo: null,
      loadingStatus: true,
      isSubmitting: false,
      email: localStorage.getItem("email") || "",
      phoneError: "",
      form: {
        ho_ten: localStorage.getItem("ho_ten") || "",
        so_dien_thoai: "",
        chuyen_mon: "",
        mo_ta: "",
      },
      files: {
        anh_dai_dien: null,
        cccd_mat_truoc: null,
        cccd_mat_sau: null,
        bang_cap: null,
      },
      statusInterval: null,
    };
  },
  mounted() {
    this.loadStatus();
    this.statusInterval = setInterval(() => {
      if (this.hoSo && this.hoSo.trang_thai === 0) {
        this.pollStatus();
      }
    }, 3000);
  },
  beforeUnmount() {
    if (this.statusInterval) clearInterval(this.statusInterval);
  },
  methods: {
    async pollStatus() {
      try {
        const res = await axios.get(`${API}/homepage/ho-so-giao-vien/my-status`, {
          headers: this.headers(),
        });
        const newStatus = res.data.data;
        if (
          newStatus &&
          newStatus.trang_thai === 1 &&
          this.hoSo.trang_thai === 0
        ) {
          this.hoSo = newStatus;
          this.$toast.success("Hồ sơ đã được duyệt! Đang chuyển hướng...");
          if (this.statusInterval) clearInterval(this.statusInterval);
          setTimeout(() => {
            window.location.href = "/teacher/dashboard";
          }, 1500);
        } else if (
          newStatus &&
          newStatus.trang_thai === 2 &&
          this.hoSo.trang_thai === 0
        ) {
          this.hoSo = newStatus;
          this.$toast.error("Hồ sơ của bạn đã bị từ chối!");
        }
      } catch (e) {}
    },
    token() {
      return (
        localStorage.getItem("token_nguoi_dung") ||
        localStorage.getItem("token_khach_hang") ||
        ""
      );
    },
    headers() {
      return { Authorization: "Bearer " + this.token() };
    },
    validatePhone() {
      const sdt = (this.form.so_dien_thoai || "").trim();
      if (!sdt) {
        this.phoneError = "";
        return true;
      }
      const vnPhone =
        /^(0[3|5|7|8|9])([0-9]{8})$|^(\+84[3|5|7|8|9])([0-9]{8})$/;
      if (!vnPhone.test(sdt.replace(/\s/g, ""))) {
        this.phoneError = "Số điện thoại không hợp lệ (VD: 0901234567)";
        return false;
      }
      this.phoneError = "";
      return true;
    },
    async loadStatus() {
      this.loadingStatus = true;
      try {
        const res = await axios.get(`${API}/homepage/ho-so-giao-vien/my-status`, {
          headers: this.headers(),
        });
        this.hoSo = res.data.data || null;
        if (this.hoSo && this.hoSo.trang_thai === 2) {
          this.form.ho_ten = this.hoSo.ho_ten;
          this.form.so_dien_thoai = this.hoSo.so_dien_thoai;
          this.form.chuyen_mon = this.hoSo.chuyen_mon;
          this.form.mo_ta = this.hoSo.mo_ta;
        }
      } catch {
        this.hoSo = null;
      } finally {
        this.loadingStatus = false;
      }
    },
    handleFile(e, fieldName) {
      const file = e.target.files[0];
      if (!file) return;
      if (file.size > 5 * 1024 * 1024) {
        this.$toast.error("File không được vượt quá 5MB.");
        return;
      }
      this.files[fieldName] = file;
    },
    removeFile(fieldName) {
      this.files[fieldName] = null;
      const inputMap = {
        anh_dai_dien: "anhInput",
        cccd_mat_truoc: "cccdTruocInput",
        cccd_mat_sau: "cccdSauInput",
        bang_cap: "bangCapInput",
      };
      const refName = inputMap[fieldName];
      if (this.$refs[refName]) this.$refs[refName].value = "";
    },
    async submitHoSo() {
      if (!this.validatePhone()) return;

      this.isSubmitting = true;
      try {
        const fd = new FormData();
        fd.append("ho_ten", this.form.ho_ten);
        fd.append("so_dien_thoai", this.form.so_dien_thoai || "");
        fd.append("chuyen_mon", this.form.chuyen_mon);
        fd.append("mo_ta", this.form.mo_ta || "");

        for (const [key, file] of Object.entries(this.files)) {
          if (file) {
            fd.append(key, file);
          }
        }

        const res = await axios.post(`${API}/homepage/ho-so-giao-vien`, fd, {
          headers: { ...this.headers(), "Content-Type": "multipart/form-data" },
        });

        if (res.data.status) {
          // Đã cập nhật thành this.$toast.success(res.data.message) ở đây
          this.$toast.success(res.data.message);
          await this.loadStatus();
        } else {
          this.$toast.error(res.data.message);
        }
      } catch (err) {
        const errors = err.response?.data?.errors;
        if (errors) {
          Object.values(errors)
            .flat()
            .forEach((msg) => this.$toast.error(msg));
        } else {
          this.$toast.error(
            err.response?.data?.message || "Có lỗi xảy ra. Vui lòng thử lại.",
          );
        }
      } finally {
        this.isSubmitting = false;
      }
    },
  },
};
</script>

<style scoped>
.dk-card {
  border-radius: 20px;
  background: #ffffff;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06) !important;
}
.icon-circle-xl {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #ede9fe, #ddd6fe);
  display: flex;
  align-items: center;
  justify-content: center;
}
.text-primary {
  color: #667eea !important;
}
.form-label {
  font-weight: 600;
  color: #334155;
  margin-bottom: 8px;
  font-size: 0.95rem;
}
.form-control {
  border-radius: 12px;
  padding: 12px 18px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  color: #334155;
  transition: all 0.3s;
}
.form-control:focus {
  background: #fff;
  border-color: #667eea;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
}
.form-control:disabled {
  background: #f1f5f9 !important;
  cursor: not-allowed;
}
.btn {
  border-radius: 10px;
  font-weight: 600;
  padding: 10px 20px;
  transition: all 0.3s;
}
.btn-primary {
  background: linear-gradient(135deg, #667eea, #764ba2);
  border: none;
  color: #fff;
}
.btn-primary:hover {
  background: linear-gradient(135deg, #5a6ed0, #6a4190);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(118, 75, 162, 0.4);
  color: #fff;
}
.btn-light {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
}
.btn-light:hover {
  background: #e2e8f0;
  transform: translateY(-2px);
}

.upload-area-sm {
  border: 1px dashed #cbd5e1;
  border-radius: 10px;
  padding: 12px;
  text-align: center;
  cursor: pointer;
  background: #f8fafc;
  transition: all 0.3s;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 60px;
}
.upload-area-sm:hover {
  border-color: #667eea;
  background: #f0faff;
}
.upload-area-sm.p-4 {
  padding: 24px !important;
  min-height: 120px;
}
.btn-close-sm {
  background: none;
  border: none;
  color: #ef4444;
  font-size: 1.1rem;
  padding: 0;
}

select.form-control {
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 16px center;
  padding-right: 40px;
  cursor: pointer;
}

.is-invalid {
  border-color: #ef4444 !important;
}
.invalid-feedback {
  color: #ef4444;
  font-size: 0.82rem;
  margin-top: 4px;
}

.alert-box {
  border-radius: 14px;
  padding: 16px 20px;
  border-left: 4px solid;
}
.alert-waiting {
  background: #fefce8;
  border-color: #eab308;
  color: #854d0e;
}
.alert-approved {
  background: #f0fdf4;
  border-color: #22c55e;
  color: #166534;
}
.alert-rejected {
  background: #fff7ed;
  border-color: #f97316;
  color: #9a3412;
}
</style>
