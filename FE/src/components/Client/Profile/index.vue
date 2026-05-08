<template>
  <div class="row">
    <div class="col-12">
      <div class="card border-0 shadow-sm main-wrapper-card">
        <div class="card-body p-md-5">
          <ul class="nav nav-tabs nav-fill mb-4">
            <li class="nav-item">
              <a class="nav-link active" data-bs-toggle="tab" href="#profile">
                <i class="fa-solid fa-user me-2"></i>Thông tin cá nhân
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" href="#password">
                <i class="fa-solid fa-lock me-2"></i>Đổi mật khẩu
              </a>
            </li>
            <li v-if="isHocVien" class="nav-item">
              <a
                class="nav-link"
                data-bs-toggle="tab"
                href="#dangky-giaovien"
                @click.once="loadHoSoStatus"
              >
                <i class="fa-solid fa-chalkboard-user me-2"></i>Đăng ký Giáo
                viên
                <span
                  v-if="hoSoStatus && hoSoStatus.trang_thai === 0"
                  class="badge bg-warning text-dark ms-1"
                  style="font-size: 0.7rem"
                  >Chờ</span
                >
                <span
                  v-if="hoSoStatus && hoSoStatus.trang_thai === 1"
                  class="badge bg-success ms-1"
                  style="font-size: 0.7rem"
                  >✓</span
                >
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade show active" id="profile">
              <div class="row g-4">
                <div class="col-lg-4">
                  <div class="card inner-card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                      <div class="avatar-wrapper mb-4">
                        <div
                          class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center"
                        >
                          <img
                            v-if="avatarDisplayUrl && !avatarLoadError"
                            :src="avatarDisplayUrl"
                            class="avatar-image"
                            alt=""
                            @error="onAvatarLoadError"
                          />
                          <i v-else class="fa-solid fa-user fa-3x"></i>
                        </div>
                      </div>
                      <div class="mb-3">
                        <input
                          ref="avatarInput"
                          type="file"
                          class="d-none"
                          accept="image/png,image/jpeg,image/jpg,image/webp"
                          @change="handleAvatarChange"
                        />
                        <button
                          type="button"
                          class="btn btn-light btn-sm"
                          @click="openAvatarPicker"
                          :disabled="isUpdatingAvatar"
                        >
                          <span
                            v-if="isUpdatingAvatar"
                            class="spinner-border spinner-border-sm me-2"
                          ></span>
                          <i class="fa-solid fa-camera me-2" v-else></i>Đổi ảnh
                          đại diện
                        </button>
                      </div>
                      <h3 class="mb-2 fw-bold text-dark">
                        {{ thong_tin.ho_va_ten || "Chưa có tên" }}
                      </h3>
                      <h6 class="mb-2 text-muted">{{ thong_tin.email }}</h6>
                      <p class="mb-4">
                        <span class="badge custom-badge">{{
                          thong_tin.chuc_vu?.ten_chuc_vu || "Chưa có chức vụ"
                        }}</span>
                      </p>

                      <hr class="text-muted opacity-25 mb-4" />

                      <div class="row text-start px-2">
                        <div class="col-12 mb-3">
                          <div
                            class="info-item d-flex align-items-center justify-content-between w-100"
                          >
                            <div class="d-flex align-items-center">
                              <div class="icon-box me-3">
                                <i class="fa-solid fa-phone text-primary"></i>
                              </div>
                              <span class="fw-medium text-secondary">{{
                                thong_tin.so_dien_thoai ||
                                "Chưa có số điện thoại"
                              }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-8">
                  <div class="card inner-card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-md-5">
                      <h4 class="card-title mb-4 fw-bold text-dark">
                        <i class="fa-solid fa-user-pen me-2 text-primary"></i
                        >Cập nhật thông tin
                      </h4>
                      <form @submit.prevent="updateProfile">
                        <div class="row g-4">
                          <div class="col-md-6">
                            <label class="form-label"
                              >Họ và tên
                              <span class="text-danger">*</span></label
                            >
                            <input
                              type="text"
                              class="form-control"
                              v-model="profileData.ho_va_ten"
                              placeholder="Nhập họ và tên"
                              required
                            />
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input
                              :value="thong_tin.email"
                              type="email"
                              class="form-control bg-light"
                              disabled
                            />
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input
                              type="tel"
                              class="form-control"
                              v-model="profileData.so_dien_thoai"
                              placeholder="Nhập số điện thoại"
                            />
                          </div>

                          <div class="col-md-6" v-if="isGiaoVien">
                            <label
                              class="form-label d-none d-md-block"
                              style="visibility: hidden"
                              >Hồ Sơ Gốc</label
                            >
                            <div>
                              <button
                                type="button"
                                @click="openTeacherProfile"
                                class="btn btn-outline-danger rounded-pill px-4 py-2"
                                style="
                                  border-color: #ff7a45;
                                  color: #ff7a45;
                                  font-weight: 600;
                                "
                                :disabled="isFetchingHoSo"
                                title="Xem lại hồ sơ năng lực đã nộp"
                              >
                                <span
                                  v-if="isFetchingHoSo"
                                  class="spinner-border spinner-border-sm me-2"
                                ></span>
                                <i v-else class="fa-solid fa-id-card me-2"></i>
                                Hồ Sơ Gốc
                              </button>
                            </div>
                          </div>
                        </div>

                        <div class="text-end mt-5 pt-3 border-top">
                          <button
                            type="button"
                            @click="resetProfileForm"
                            class="btn btn-light me-3 px-4"
                          >
                            Huỷ
                          </button>
                          <button
                            type="submit"
                            class="btn btn-primary px-4 shadow-sm"
                            :disabled="isUpdatingProfile"
                          >
                            <span
                              v-if="isUpdatingProfile"
                              class="spinner-border spinner-border-sm me-2"
                            ></span>
                            <i class="fa-solid fa-save me-2" v-else></i>Lưu thay
                            đổi
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="password">
              <div class="row justify-content-center">
                <div class="col-lg-8">
                  <div class="card inner-card border-0 shadow-sm">
                    <div class="card-body">
                      <div class="text-center">
                        <div class="icon-circle-large mx-auto">
                          <i
                            class="fa-solid fa-shield-halved fa-2x text-primary"
                          ></i>
                        </div>
                        <h4 class="card-title fw-bold text-dark">
                          Đổi mật khẩu bảo mật
                        </h4>
                        <p class="text-muted">
                          Vui lòng nhập mật khẩu hiện tại và mật khẩu mới để
                          thay đổi.
                        </p>
                      </div>

                      <form @submit.prevent="changePassword">
                        <div class="row g-4 px-md-4">
                          <div class="col-md-12">
                            <label class="form-label"
                              >Mật khẩu hiện tại
                              <span class="text-danger">*</span></label
                            >
                            <div class="password-input-wrapper">
                              <input
                                :type="showOldPassword ? 'text' : 'password'"
                                class="form-control form-control-lg"
                                v-model="passwordData.old_password"
                                placeholder="Nhập mật khẩu hiện tại"
                                required
                              />
                              <button
                                type="button"
                                class="password-toggle-btn"
                                @click="showOldPassword = !showOldPassword"
                              >
                                <i
                                  :class="
                                    showOldPassword
                                      ? 'fa-solid fa-eye-slash'
                                      : 'fa-solid fa-eye'
                                  "
                                ></i>
                              </button>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <label class="form-label"
                              >Mật khẩu mới
                              <span class="text-danger">*</span></label
                            >
                            <div class="password-input-wrapper">
                              <input
                                :type="showNewPassword ? 'text' : 'password'"
                                class="form-control form-control-lg"
                                v-model="passwordData.password"
                                placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)"
                                required
                              />
                              <button
                                type="button"
                                class="password-toggle-btn"
                                @click="showNewPassword = !showNewPassword"
                              >
                                <i
                                  :class="
                                    showNewPassword
                                      ? 'fa-solid fa-eye-slash'
                                      : 'fa-solid fa-eye'
                                  "
                                ></i>
                              </button>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <label class="form-label"
                              >Xác nhận mật khẩu mới
                              <span class="text-danger">*</span></label
                            >
                            <div class="password-input-wrapper">
                              <input
                                :type="
                                  showConfirmPassword ? 'text' : 'password'
                                "
                                class="form-control form-control-lg"
                                v-model="passwordData.re_password"
                                placeholder="Nhập lại mật khẩu mới"
                                required
                              />
                              <button
                                type="button"
                                class="password-toggle-btn"
                                @click="
                                  showConfirmPassword = !showConfirmPassword
                                "
                              >
                                <i
                                  :class="
                                    showConfirmPassword
                                      ? 'fa-solid fa-eye-slash'
                                      : 'fa-solid fa-eye'
                                  "
                                ></i>
                              </button>
                            </div>
                          </div>
                        </div>
                        <div class="text-center mt-5 pt-4 border-top">
                          <button
                            type="button"
                            @click="resetPasswordForm"
                            class="btn btn-light me-3 px-4"
                          >
                            Huỷ thao tác
                          </button>
                          <button
                            type="submit"
                            class="btn btn-primary px-5 shadow-sm"
                            :disabled="isChangingPassword"
                          >
                            <span
                              v-if="isChangingPassword"
                              class="spinner-border spinner-border-sm me-2"
                            ></span>
                            <i class="fa-solid fa-key me-2" v-else></i>Cập nhật
                            mật khẩu
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="dangky-giaovien">
              <div class="row justify-content-center">
                <div class="col-lg-9">
                  <div v-if="loadingHoSo" class="text-center py-5">
                    <span class="spinner-border text-primary"></span>
                    <p class="text-muted mt-2 small">Đang tải...</p>
                  </div>

                  <template v-else>
                    <div
                      v-if="!hoSoStatus"
                      class="card inner-card border-0 shadow-sm"
                    >
                      <div class="card-body p-4 p-md-5 text-center">
                        <div class="icon-circle-xl mx-auto mb-3">
                          <i
                            class="fa-solid fa-chalkboard-user fa-2x text-primary"
                          ></i>
                        </div>
                        <h4 class="fw-bold text-dark">
                          Đăng ký trở thành Giáo viên
                        </h4>
                        <p class="text-muted">
                          Gửi hồ sơ để Admin xét duyệt. Bạn sẽ nhận email thông
                          báo kết quả.
                        </p>
                        <button
                          @click="$router.push('/dang-ky-giao-vien')"
                          class="btn btn-primary px-5 mt-2"
                        >
                          <i class="fa-solid fa-paper-plane me-2"></i>Nộp hồ sơ
                          đăng ký
                        </button>
                      </div>
                    </div>

                    <div v-else class="card inner-card border-0 shadow-sm">
                      <div class="card-body p-4 p-md-5">
                        <div
                          class="hs-alert hs-waiting mb-4"
                          v-if="hoSoStatus.trang_thai === 0"
                        >
                          <div class="d-flex align-items-center">
                            <i
                              class="fa-solid fa-hourglass-half fa-lg me-3"
                            ></i>
                            <div>
                              <strong>Đang chờ Admin xét duyệt</strong>
                              <p class="mb-0 small mt-1">
                                Ngày nộp: {{ hoSoStatus.created_at }} · Vui lòng
                                chờ thông báo qua email.
                              </p>
                            </div>
                          </div>
                        </div>
                        <div
                          class="hs-alert hs-approved mb-4"
                          v-else-if="hoSoStatus.trang_thai === 1"
                        >
                          <div class="d-flex align-items-center">
                            <i class="fa-solid fa-circle-check fa-lg me-3"></i>
                            <div>
                              <strong>Hồ sơ đã được duyệt!</strong>
                              <p class="mb-0 small mt-1">
                                Tài khoản đã được nâng cấp lên Giáo viên. Vui
                                lòng đăng nhập lại để cập nhật quyền.
                              </p>
                            </div>
                          </div>
                        </div>
                        <div
                          class="hs-alert hs-rejected mb-4"
                          v-else-if="hoSoStatus.trang_thai === 2"
                        >
                          <div class="d-flex align-items-center">
                            <i class="fa-solid fa-circle-xmark fa-lg me-3"></i>
                            <div>
                              <strong>Hồ sơ bị từ chối</strong>
                              <p class="mb-0 small mt-1">
                                Lý do: {{ hoSoStatus.ghi_chu_admin }}
                              </p>
                            </div>
                          </div>
                        </div>

                        <h5 class="fw-bold text-dark mb-3">
                          <i
                            class="fa-solid fa-file-lines me-2 text-primary"
                          ></i
                          >Thông tin hồ sơ đã nộp
                        </h5>
                        <hr class="mb-4" />

                        <div class="row g-3 mb-4">
                          <div class="col-md-6">
                            <div class="hs-info-item">
                              <span class="hs-info-label"
                                ><i
                                  class="fa-solid fa-user me-2 text-primary"
                                ></i
                                >Họ và tên</span
                              >
                              <span class="hs-info-value">{{
                                hoSoStatus.ho_ten || "—"
                              }}</span>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="hs-info-item">
                              <span class="hs-info-label"
                                ><i
                                  class="fa-solid fa-envelope me-2 text-primary"
                                ></i
                                >Email</span
                              >
                              <span class="hs-info-value">{{
                                hoSoStatus.email || "—"
                              }}</span>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="hs-info-item">
                              <span class="hs-info-label"
                                ><i
                                  class="fa-solid fa-phone me-2 text-primary"
                                ></i
                                >Số điện thoại</span
                              >
                              <span class="hs-info-value">{{
                                hoSoStatus.so_dien_thoai || "—"
                              }}</span>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="hs-info-item">
                              <span class="hs-info-label"
                                ><i
                                  class="fa-solid fa-briefcase me-2 text-primary"
                                ></i
                                >Vai trò đăng ký</span
                              >
                              <span class="hs-info-value">
                                <span class="badge-vai-tro">{{
                                  hoSoStatus.chuyen_mon || "—"
                                }}</span>
                              </span>
                            </div>
                          </div>
                          <div class="col-12" v-if="hoSoStatus.mo_ta">
                            <div class="hs-info-item">
                              <span class="hs-info-label"
                                ><i
                                  class="fa-solid fa-align-left me-2 text-primary"
                                ></i
                                >Giới thiệu bản thân</span
                              >
                              <p
                                class="hs-info-value mb-0"
                                style="white-space: pre-wrap"
                              >
                                {{ hoSoStatus.mo_ta }}
                              </p>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="hs-info-item">
                              <span class="hs-info-label"
                                ><i
                                  class="fa-solid fa-calendar me-2 text-primary"
                                ></i
                                >Ngày nộp</span
                              >
                              <span class="hs-info-value">{{
                                hoSoStatus.created_at || "—"
                              }}</span>
                            </div>
                          </div>
                        </div>

                        <div
                          class="text-end border-top pt-4 mt-2"
                          v-if="hoSoStatus.trang_thai === 2"
                        >
                          <button
                            @click="$router.push('/dang-ky-giao-vien')"
                            class="btn btn-primary px-5"
                          >
                            <i class="fa-solid fa-rotate-right me-2"></i>Nộp lại
                            hồ sơ
                          </button>
                        </div>
                      </div>
                    </div>
                  </template>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="showTeacherProfileModal"
      class="modal-overlay"
      @click.self="showTeacherProfileModal = false"
    >
      <div class="modal-card modal-lg">
        <div class="modal-header-custom info">
          <i class="fa-solid fa-folder-open fa-lg me-2"></i>Hồ sơ năng lực của
          bạn
          <button
            class="btn-close-modal ms-auto"
            @click="showTeacherProfileModal = false"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        <div
          class="modal-body-custom custom-scrollbar"
          style="max-height: 75vh; overflow-y: auto"
        >
          <div class="row g-4">
            <div class="col-md-12">
              <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">
                Định danh (CCCD)
              </h6>
              <div class="row g-3">
                <div class="col-sm-6">
                  <div class="document-card">
                    <div class="doc-title">CCCD/CMND (Mặt trước)</div>
                    <div
                      v-if="hoSoStatus?.cccd_mat_truoc_url"
                      class="mt-2 text-center"
                    >
                      <a
                        :href="hoSoStatus.cccd_mat_truoc_url"
                        target="_blank"
                        title="Click để xem ảnh gốc"
                      >
                        <img
                          :src="hoSoStatus.cccd_mat_truoc_url"
                          class="img-fluid rounded border kyc-image"
                          alt="CCCD Mặt trước"
                        />
                      </a>
                    </div>
                    <span
                      v-else
                      class="text-muted small mt-2 d-block text-center"
                      >Chưa cập nhật</span
                    >
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="document-card">
                    <div class="doc-title">CCCD/CMND (Mặt sau)</div>
                    <div
                      v-if="hoSoStatus?.cccd_mat_sau_url"
                      class="mt-2 text-center"
                    >
                      <a
                        :href="hoSoStatus.cccd_mat_sau_url"
                        target="_blank"
                        title="Click để xem ảnh gốc"
                      >
                        <img
                          :src="hoSoStatus.cccd_mat_sau_url"
                          class="img-fluid rounded border kyc-image"
                          alt="CCCD Mặt sau"
                        />
                      </a>
                    </div>
                    <span
                      v-else
                      class="text-muted small mt-2 d-block text-center"
                      >Chưa cập nhật</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12 mt-4">
              <div class="row g-3">
                <div class="col-sm-12">
                  <div class="document-card py-4">
                    <div class="doc-title mb-3">
                      Bằng cấp (ĐH/CĐ) hoặc Chứng chỉ bổ trợ
                    </div>
                    <div v-if="hoSoStatus?.bang_cap_url">
                      <template v-if="isPdf(hoSoStatus.bang_cap_url)">
                        <iframe
                          :src="hoSoStatus.bang_cap_url"
                          class="w-100 rounded border"
                          style="height: 350px"
                        ></iframe>
                      </template>
                      <template v-else>
                        <a
                          :href="hoSoStatus.bang_cap_url"
                          target="_blank"
                          title="Click để xem ảnh gốc"
                        >
                          <img
                            :src="hoSoStatus.bang_cap_url"
                            class="img-fluid rounded border kyc-image"
                            style="max-height: 350px"
                            alt="Bằng cấp"
                          />
                        </a>
                      </template>
                    </div>
                    <span
                      v-else
                      class="text-muted small mt-2 d-block text-center"
                      >Chưa cập nhật</span
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer-custom bg-light">
          <button
            class="btn btn-outline-secondary px-4"
            @click="showTeacherProfileModal = false"
          >
            Đóng
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "NhanVienProfile",
  computed: {
    avatarDisplayUrl() {
      if (this.localAvatarPreviewUrl) {
        return this.localAvatarPreviewUrl;
      }
      if (this.thong_tin.anh_dai_dien_url) {
        return this.thong_tin.anh_dai_dien_url;
      }
      const savedLocalAvatar = localStorage.getItem("anh_dai_dien_local") || "";
      if (savedLocalAvatar) {
        return savedLocalAvatar;
      }
      return this.duongDanAnh(this.thong_tin.anh_dai_dien || "");
    },
    isHocVien() {
      const vaiTro = this.thong_tin.vai_tro_id;
      return !vaiTro || Number(vaiTro) === 3;
    },
    isGiaoVien() {
      const vaiTro = this.thong_tin.vai_tro_id;
      return Number(vaiTro) === 2; // Giả sử ID 2 là Giáo viên trong DB của bạn
    },
  },
  data() {
    return {
      thong_tin: {},
      profileData: {
        ho_va_ten: "",
        so_dien_thoai: "",
      },
      passwordData: {
        old_password: "",
        password: "",
        re_password: "",
      },
      showOldPassword: false,
      showNewPassword: false,
      showConfirmPassword: false,
      isUpdatingProfile: false,
      isChangingPassword: false,
      isUpdatingAvatar: false,
      localAvatarPreviewUrl: "",
      avatarLoadError: false,
      pendingAvatarDataUrl: "",

      // Xử lý Hồ sơ Giáo viên
      hoSoStatus: null,
      loadingHoSo: false,
      hoSoLoaded: false,
      showTeacherProfileModal: false,
      isFetchingHoSo: false,
    };
  },
  mounted() {
    this.loadProfile();
  },
  methods: {
    duongDanAnh(raw) {
      if (!raw) return "";
      const source = String(raw).trim().replace(/\\/g, "/");
      if (!source) return "";
      if (
        source.startsWith("http://") ||
        source.startsWith("https://") ||
        source.startsWith("blob:")
      ) {
        return source;
      }
      const base = (
        import.meta.env.VITE_API_URL || "http://127.0.0.1:8000"
      ).replace(/\/$/, "");
      if (source.startsWith("/storage/")) {
        return `${base}${source}`;
      }
      if (source.startsWith("storage/")) {
        return `${base}/${source}`;
      }
      return `${base}/storage/${source.replace(/^\//, "")}`;
    },
    getAuthToken() {
      const layout = this.$route?.meta?.layout || "";
      const path = this.$route?.path || "";
      if (layout === "admin" || path.startsWith("/admin")) {
        return localStorage.getItem("token_admin") || "";
      }
      if (layout === "teach" || path.startsWith("/teacher")) {
        return localStorage.getItem("token_teacher") || "";
      }
      return (
        localStorage.getItem("token_nguoi_dung") ||
        localStorage.getItem("token_khach_hang") ||
        localStorage.getItem("token_admin") ||
        localStorage.getItem("token_teacher") ||
        ""
      );
    },
    getRedirectLoginPath() {
      const layout = this.$route?.meta?.layout || "";
      if (layout === "admin" || layout === "teach") return "/dang-nhap";
      return "/dang-nhap";
    },
    clearAuthTokenByContext() {
      const layout = this.$route?.meta?.layout || "";
      if (layout === "admin") {
        localStorage.removeItem("token_admin");
        return;
      }
      if (layout === "teach") {
        localStorage.removeItem("token_teacher");
        return;
      }
      localStorage.removeItem("token_nguoi_dung");
      localStorage.removeItem("token_khach_hang");
    },
    capNhatProfileLocal(thongTin) {
      if (!thongTin || typeof thongTin !== "object") {
        return;
      }

      const hoTen = thongTin.ho_va_ten || "";
      if (hoTen) {
        localStorage.setItem("ho_ten", hoTen);
      }

      if (thongTin.anh_dai_dien) {
        localStorage.setItem("anh_dai_dien", thongTin.anh_dai_dien);
      } else {
        localStorage.removeItem("anh_dai_dien");
      }
      if (thongTin.anh_dai_dien_url) {
        localStorage.setItem("anh_dai_dien_url", thongTin.anh_dai_dien_url);
      } else {
        localStorage.removeItem("anh_dai_dien_url");
      }

      window.dispatchEvent(
        new CustomEvent("profile-updated", {
          detail: {
            ho_ten: hoTen,
            anh_dai_dien: thongTin.anh_dai_dien || null,
            anh_dai_dien_url: thongTin.anh_dai_dien_url || null,
            anh_dai_dien_local:
              localStorage.getItem("anh_dai_dien_local") || null,
          },
        }),
      );
    },
    fileToDataUrl(file) {
      return new Promise((resolve) => {
        try {
          const reader = new FileReader();
          reader.onload = () =>
            resolve(typeof reader.result === "string" ? reader.result : "");
          reader.onerror = () => resolve("");
          reader.readAsDataURL(file);
        } catch (_) {
          resolve("");
        }
      });
    },
    loadProfile() {
      axios
        .get("http://127.0.0.1:8000/api/profile", {
          headers: {
            Authorization: "Bearer " + this.getAuthToken(),
          },
        })
        .then((res) => {
          if (res.data.status) {
            this.thong_tin = res.data.thong_tin;
            
            if (Number(this.thong_tin.vai_tro_id) === 2 && this.$route.path === '/profile') {
                window.location.href = '/teacher/dashboard';
                return;
            }

            this.capNhatProfileLocal(res.data.thong_tin);
            // Populate profile form
            this.profileData = {
              ho_va_ten: res.data.thong_tin.ho_va_ten || "",
              so_dien_thoai: res.data.thong_tin.so_dien_thoai || "",
            };
          } else {
            this.$toast.error(
              res.data.message || "Không thể tải thông tin profile",
            );
          }
        })
        .catch((err) => {
          if (err.response && err.response.status === 401) {
            this.$toast.error("Vui lòng đăng nhập lại!");
            this.clearAuthTokenByContext();
            this.$router.push(this.getRedirectLoginPath());
          } else {
            this.$toast.error(
              err.response?.data?.message || "Đã xảy ra lỗi khi tải thông tin",
            );
          }
        });
    },
    updateProfile() {
      this.isUpdatingProfile = true;
      axios
        .post("http://127.0.0.1:8000/api/profile/update", this.profileData, {
          headers: {
            Authorization: "Bearer " + this.getAuthToken(),
          },
        })
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            if (res.data.thong_tin) {
              this.thong_tin = res.data.thong_tin;
              this.capNhatProfileLocal(res.data.thong_tin);
            }
            this.loadProfile();
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          if (err.response && err.response.data) {
            if (err.response.data.errors) {
              const errors = Object.values(err.response.data.errors);
              errors.forEach((errorList) => {
                if (Array.isArray(errorList)) {
                  errorList.forEach((error) => this.$toast.error(error));
                }
              });
            } else {
              this.$toast.error(
                err.response.data.message ||
                  "Có lỗi xảy ra khi cập nhật thông tin",
              );
            }
          } else {
            this.$toast.error("Có lỗi xảy ra khi cập nhật thông tin");
          }
        })
        .finally(() => {
          this.isUpdatingProfile = false;
        });
    },
    resetProfileForm() {
      this.profileData = {
        ho_va_ten: this.thong_tin.ho_va_ten || "",
        so_dien_thoai: this.thong_tin.so_dien_thoai || "",
      };
    },
    openAvatarPicker() {
      if (this.$refs.avatarInput) {
        this.$refs.avatarInput.value = "";
        this.$refs.avatarInput.click();
      }
    },
    async handleAvatarChange(event) {
      const file = event.target.files?.[0];
      if (!file) {
        return;
      }

      if (!file.type || !file.type.startsWith("image/")) {
        this.$toast.error("Vui lòng chọn file ảnh hợp lệ.");
        return;
      }

      const maxSize = 2 * 1024 * 1024;
      if (file.size > maxSize) {
        this.$toast.error("Ảnh đại diện không được vượt quá 2MB.");
        return;
      }

      this.setAvatarPreview(file);
      this.pendingAvatarDataUrl = await this.fileToDataUrl(file);
      this.uploadAvatar(file);
    },
    setAvatarPreview(file) {
      if (this.localAvatarPreviewUrl) {
        URL.revokeObjectURL(this.localAvatarPreviewUrl);
      }
      this.localAvatarPreviewUrl = URL.createObjectURL(file);
      this.avatarLoadError = false;
    },
    clearAvatarPreview() {
      if (this.localAvatarPreviewUrl) {
        URL.revokeObjectURL(this.localAvatarPreviewUrl);
      }
      this.localAvatarPreviewUrl = "";
    },
    onAvatarLoadError() {
      this.avatarLoadError = true;
    },
    uploadAvatar(file) {
      this.isUpdatingAvatar = true;
      const formData = new FormData();
      formData.append("anh_dai_dien", file);

      axios
        .post("http://127.0.0.1:8000/api/profile/update-avatar", formData, {
          headers: {
            Authorization: "Bearer " + this.getAuthToken(),
            "Content-Type": "multipart/form-data",
          },
        })
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(
              res.data.message || "Cập nhật ảnh đại diện thành công.",
            );
            if (this.pendingAvatarDataUrl) {
              localStorage.setItem(
                "anh_dai_dien_local",
                this.pendingAvatarDataUrl,
              );
            }
            if (res.data.thong_tin) {
              this.thong_tin = res.data.thong_tin;
              this.capNhatProfileLocal(res.data.thong_tin);
            }
            this.clearAvatarPreview();
          } else {
            this.$toast.error(
              res.data.message || "Không thể cập nhật ảnh đại diện.",
            );
            this.clearAvatarPreview();
          }
        })
        .catch((err) => {
          if (err.response && err.response.data) {
            if (err.response.data.errors) {
              const errors = Object.values(err.response.data.errors);
              errors.forEach((errorList) => {
                if (Array.isArray(errorList)) {
                  errorList.forEach((error) => this.$toast.error(error));
                }
              });
            } else {
              this.$toast.error(
                err.response.data.message ||
                  "Có lỗi xảy ra khi cập nhật ảnh đại diện",
              );
            }
          } else {
            this.$toast.error("Có lỗi xảy ra khi cập nhật ảnh đại diện");
          }
          this.clearAvatarPreview();
        })
        .finally(() => {
          this.isUpdatingAvatar = false;
          this.pendingAvatarDataUrl = "";
          if (this.$refs.avatarInput) {
            this.$refs.avatarInput.value = "";
          }
        });
    },
    changePassword() {
      this.isChangingPassword = true;
      axios
        .post(
          "http://127.0.0.1:8000/api/profile/change-password",
          this.passwordData,
          {
            headers: {
              Authorization: "Bearer " + this.getAuthToken(),
            },
          },
        )
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            this.resetPasswordForm();
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          if (err.response && err.response.data) {
            if (err.response.data.errors) {
              const errors = Object.values(err.response.data.errors);
              errors.forEach((errorList) => {
                if (Array.isArray(errorList)) {
                  errorList.forEach((error) => this.$toast.error(error));
                }
              });
            } else {
              this.$toast.error(
                err.response.data.message || "Có lỗi xảy ra khi đổi mật khẩu",
              );
            }
          } else {
            this.$toast.error("Có lỗi xảy ra khi đổi mật khẩu");
          }
        })
        .finally(() => {
          this.isChangingPassword = false;
        });
    },
    async loadHoSoStatus() {
      if (this.hoSoLoaded) return;
      this.loadingHoSo = true;
      try {
        const axios = (await import("axios")).default;
        const res = await axios.get(
          "http://127.0.0.1:8000/api/ho-so-giao-vien/my-status",
          {
            headers: { Authorization: "Bearer " + this.getAuthToken() },
          },
        );
        this.hoSoStatus = res.data.data || null;
        this.hoSoLoaded = true;
      } catch {
        this.hoSoStatus = null;
      } finally {
        this.loadingHoSo = false;
      }
    },
    isPdf(url) {
      if (!url) return false;
      return url.toLowerCase().includes(".pdf");
    },
    async openTeacherProfile() {
      this.isFetchingHoSo = true;
      try {
        const res = await axios.get(
          "http://127.0.0.1:8000/api/ho-so-giao-vien/my-status",
          {
            headers: { Authorization: "Bearer " + this.getAuthToken() },
          },
        );
        this.hoSoStatus = res.data.data || null;
        this.hoSoLoaded = true;

        if (this.hoSoStatus) {
          this.showTeacherProfileModal = true;
        } else {
          this.$toast.warning(
            "Hệ thống không tìm thấy hồ sơ đăng ký cũ của bạn.",
          );
        }
      } catch {
        this.$toast.error("Lỗi khi tải hồ sơ đăng ký.");
      } finally {
        this.isFetchingHoSo = false;
      }
    },
    resetPasswordForm() {
      this.passwordData = {
        old_password: "",
        password: "",
        re_password: "",
      };
      this.showOldPassword = false;
      this.showNewPassword = false;
      this.showConfirmPassword = false;
    },
  },
  watch: {
    avatarDisplayUrl() {
      this.avatarLoadError = false;
    },
  },
  beforeUnmount() {
    this.clearAvatarPreview();
  },
};
</script>

<style scoped>
/* Base styling & Typography */
.text-primary {
  color: #667eea !important;
}
.bg-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

/* Cards & Wrappers */
.main-wrapper-card {
  background-color: #ffffff;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03) !important;
}

.inner-card {
  border-radius: 16px;
  background: #ffffff;
  border: 1px solid #f1f5f9 !important;
  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease;
}

.inner-card:hover {
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06) !important;
  transform: translateY(-2px);
}

/* Avatar styling */
.avatar-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
}

.avatar-circle {
  width: 130px;
  height: 130px;
  border-radius: 50%;
  box-shadow: 0 8px 25px rgba(118, 75, 162, 0.25);
  transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  overflow: hidden;
}

.avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-circle:hover {
  transform: scale(1.08) rotate(3deg);
}

.icon-circle-large {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: #f8f9ff;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Badges */
.custom-badge {
  background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
  padding: 8px 16px;
  border-radius: 50rem;
  font-weight: 500;
  letter-spacing: 0.5px;
}

/* Info Box */
.icon-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background-color: #f8f9ff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}

/* Navigation Tabs */
.nav-tabs {
  border-bottom: none;
  gap: 15px;
  background: #f8fafc;
  padding: 8px;
  border-radius: 14px;
}

.nav-tabs .nav-link {
  color: #64748b;
  border: none;
  border-radius: 10px;
  padding: 12px 24px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover:not(.active) {
  color: #667eea;
  background-color: #e2e8f0;
}

.nav-tabs .nav-link.active {
  color: #ffffff;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  box-shadow: 0 4px 15px rgba(118, 75, 162, 0.3);
}

/* Form Elements */
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
  background-color: #f8fafc;
  color: #334155;
  transition: all 0.3s ease;
}

.form-control-lg {
  padding: 14px 20px;
}

.custom-textarea {
  resize: none;
}

.form-control:focus {
  background-color: #ffffff;
  border-color: #667eea;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
}

.form-control:disabled {
  background-color: #f1f5f9 !important;
  cursor: not-allowed;
  opacity: 0.8;
}

/* Buttons */
.btn {
  border-radius: 10px;
  font-weight: 600;
  letter-spacing: 0.3px;
  transition: all 0.3s ease;
  padding-top: 10px;
  padding-bottom: 10px;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  color: #fff;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #5a6ed0 0%, #6a4190 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(118, 75, 162, 0.4) !important;
  color: #fff;
}

.btn-light {
  background-color: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
}

.btn-light:hover {
  background-color: #e2e8f0;
  color: #1e293b;
  transform: translateY(-2px);
}

/* Password Inputs */
.password-input-wrapper {
  position: relative;
}

.password-input-wrapper .form-control {
  padding-right: 50px;
}

.password-toggle-btn {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 8px;
  border-radius: 50%;
  z-index: 10;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.password-toggle-btn:hover {
  color: #667eea;
  background-color: #f1f5f9;
}

.password-toggle-btn:focus {
  outline: none;
}

.password-toggle-btn i {
  font-size: 1.1rem;
}

/* Teacher Registration Tab */
.icon-circle-xl {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #ede9fe, #ddd6fe);
  display: flex;
  align-items: center;
  justify-content: center;
}
.hs-alert {
  border-radius: 14px;
  padding: 16px 20px;
  border-left: 4px solid;
}
.hs-waiting {
  background: #fefce8;
  border-color: #eab308;
  color: #854d0e;
}
.hs-approved {
  background: #f0fdf4;
  border-color: #22c55e;
  color: #166534;
}
.hs-rejected {
  background: #fff7ed;
  border-color: #f97316;
  color: #9a3412;
}

/* Hồ sơ info grid */
.hs-info-item {
  background: #f8fafc;
  border-radius: 12px;
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  border: 1px solid #f1f5f9;
  height: 100%;
}
.hs-info-label {
  font-size: 0.82rem;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}
.hs-info-value {
  font-size: 0.95rem;
  font-weight: 500;
  color: #1e293b;
  word-break: break-word;
}
.badge-vai-tro {
  background: linear-gradient(135deg, #ede9fe, #ddd6fe);
  color: #5b21b6;
  padding: 4px 14px;
  border-radius: 50px;
  font-size: 0.85rem;
  font-weight: 600;
}
.hs-cv-link {
  color: #667eea;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.2s;
}
.hs-cv-link:hover {
  color: #764ba2;
  text-decoration: underline;
}

/* Document Cards inside Modal */
.document-card {
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 16px;
  background: #fff;
  text-align: center;
  height: 100%;
}
.doc-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: #475569;
  margin-bottom: 8px;
}
.kyc-image {
  max-height: 180px;
  width: auto;
  object-fit: contain;
  cursor: zoom-in;
  transition: transform 0.2s;
  background: #f8fafc;
  padding: 4px;
}
.kyc-image:hover {
  transform: scale(1.02);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Modal Core Styles */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(2px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}
.modal-card {
  width: min(480px, 92vw);
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}
.modal-card.modal-lg {
  width: min(720px, 95vw);
}
.modal-header-custom {
  padding: 18px 24px;
  font-weight: 700;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
}
.modal-header-custom.info {
  background: linear-gradient(135deg, #f8fafc, #e2e8f0);
  color: #1e293b;
  border-bottom: 1px solid #cbd5e1;
}
.modal-body-custom {
  padding: 24px;
}
.modal-footer-custom {
  padding: 16px 24px;
  border-top: 1px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
}
.btn-close-modal {
  background: transparent;
  border: none;
  font-size: 1.2rem;
  color: #64748b;
  cursor: pointer;
  transition: color 0.2s;
}
.btn-close-modal:hover {
  color: #ef4444;
}

/* Scrollbar cho Modal Body */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
