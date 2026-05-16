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
            
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" href="#nap-tien" @click.once="loadSoDu">
                <i class="fa-solid fa-wallet me-2"></i>Ví & Giao dịch
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

            <!-- TAB: Nạp tiền / Rút tiền / Lịch sử -->
            <div class="tab-pane fade" id="nap-tien">
              <!-- Số dư nổi bật -->
              <div class="wallet-hero mb-4">
                <div class="wallet-hero-left">
                  <div class="wallet-icon-wrap">
                    <i class="fa-solid fa-wallet fa-lg"></i>
                  </div>
                  <div>
                    <div class="wallet-label">Số dư hiện tại</div>
                    <div class="wallet-amount">{{ formatMoney(soDu) }}</div>
                  </div>
                </div>
                <button class="btn btn-light btn-sm" @click="loadSoDu" :disabled="loadingSoDu">
                  <span v-if="loadingSoDu" class="spinner-border spinner-border-sm"></span>
                  <i v-else class="fa-solid fa-rotate-right"></i>
                </button>
              </div>

              <!-- Sub-tabs -->
              <ul class="nav nav-pills wallet-tabs mb-4">
                <li class="nav-item">
                  <a
                    class="nav-link active"
                    data-bs-toggle="pill"
                    href="#tab-nap"
                    @click.once="loadDepositDonList"
                  >
                    <i class="fa-solid fa-circle-plus me-2"></i>Nạp tiền
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-bs-toggle="pill" href="#tab-rut">
                    <i class="fa-solid fa-circle-minus me-2"></i>Rút tiền
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-bs-toggle="pill" href="#tab-lich-su" @click.once="loadLichSu">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử
                  </a>
                </li>
                <li v-if="canAccessPremiumTab" class="nav-item">
                  <a
                    class="nav-link"
                    data-bs-toggle="pill"
                    href="#tab-premium"
                    @click.once="loadGoiPremium"
                  >
                    <i class="fa-solid fa-crown me-2 text-warning"></i>Gói Premium
                    <i
                      v-if="premiumGoiDangDung"
                      class="fa-solid fa-circle-check text-success ms-1"
                      title="Gói đang hoạt động"
                    ></i>
                  </a>
                </li>
              </ul>

              <div class="tab-content">
                <!-- Nạp tiền -->
                <div class="tab-pane fade show active" id="tab-nap">
                  <div class="card inner-card border-0 shadow-sm">
                    <div class="card-body p-4">
                      <h5 class="fw-bold mb-4"><i class="fa-solid fa-circle-plus me-2 text-success"></i>Nạp tiền vào ví</h5>
                      <form @submit.prevent="onNapTienSubmit">
                        <div class="row g-3">
                          <div class="col-12">
                            <label class="form-label">Số tiền nạp (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model.number="napData.so_tien" placeholder="Nhập số tiền" min="10000" step="1000" required />
                            <div class="quick-amounts mt-2">
                              <button type="button" class="quick-btn" v-for="amt in quickAmounts" :key="amt" @click="napData.so_tien = amt">{{ formatMoney(amt) }}</button>
                            </div>
                          </div>
                          <div class="col-12">
                            <label class="form-label">Ghi chú</label>
                            <input type="text" class="form-control" v-model="napData.ghi_chu" placeholder="Ghi chú (tuỳ chọn)" />
                          </div>
                        </div>
                        <div class="text-end mt-4 pt-3 border-top">
                          <button type="button" class="btn btn-light me-2 px-4" @click="napData = { so_tien: '', ghi_chu: '' }">Huỷ</button>
                          <button type="submit" class="btn btn-success-custom px-5" :disabled="napTienFlowLoading">
                            <span v-if="napTienFlowLoading" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-else class="fa-solid fa-circle-plus me-2"></i>Nạp tiền
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>

                  <div class="card inner-card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <h6 class="fw-bold mb-0">
                          <i class="fa-solid fa-receipt me-2 text-primary"></i>Đơn nạp & chứng từ CK
                        </h6>
                        <button
                          type="button"
                          class="btn btn-light btn-sm"
                          :disabled="loadingDepositDon"
                          @click="loadDepositDonList"
                        >
                          <span v-if="loadingDepositDon" class="spinner-border spinner-border-sm"></span>
                          <i v-else class="fa-solid fa-rotate-right"></i>
                        </button>
                      </div>
                      <p class="small text-muted mb-3">
                        Đính kèm ảnh biên lai chuyển khoản (tối đa 5 ảnh/lần, tối đa 12 ảnh/đơn) cho đơn
                        <strong>đang chờ</strong> hoặc <strong>đã từ chối</strong> để admin đối soát / xử lý hoàn tiền ngoài app.
                      </p>
                      <div v-if="loadingDepositDon" class="text-center py-3">
                        <span class="spinner-border spinner-border-sm text-primary"></span>
                      </div>
                      <div v-else-if="!depositDonList.length" class="text-muted small">Chưa có đơn nạp.</div>
                      <div v-else class="deposit-don-list">
                        <div
                          v-for="don in depositDonList"
                          :key="don.id"
                          class="deposit-don-item border rounded-3 p-3 mb-3"
                        >
                          <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                            <div>
                              <code class="small">{{ don.ma_don }}</code>
                              <span class="ms-2 fw-semibold">{{ formatMoney(don.so_tien) }}</span>
                            </div>
                            <span class="badge rounded-pill" :class="depositStatusBadgeClass(don.trang_thai)">{{
                              depositStatusLabel(don.trang_thai)
                            }}</span>
                          </div>
                          <p v-if="don.ly_do_tu_choi" class="small text-danger mb-2">
                            <strong>Lý do từ chối:</strong> {{ don.ly_do_tu_choi }}
                          </p>
                          <div v-if="don.chung_tu_ck_urls && don.chung_tu_ck_urls.length" class="d-flex flex-wrap gap-2 mb-2">
                            <a
                              v-for="(u, i) in don.chung_tu_ck_urls"
                              :key="i"
                              :href="depositProofUrl(u)"
                              target="_blank"
                              rel="noopener noreferrer"
                            >
                              <img :src="depositProofUrl(u)" alt="" class="deposit-proof-thumb" />
                            </a>
                          </div>
                          <div
                            v-if="don.trang_thai === 'cho_thanh_toan' || don.trang_thai === 'that_bai'"
                            class="mt-2"
                          >
                            <input
                              type="file"
                              class="form-control form-control-sm"
                              accept="image/jpeg,image/png,image/webp,image/jpg"
                              multiple
                              :disabled="uploadingChungTuDonId === don.id"
                              @change="onDepositChungTuFiles($event, don)"
                            />
                            <span v-if="uploadingChungTuDonId === don.id" class="small text-muted ms-2">Đang tải…</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Rút tiền -->
                <div class="tab-pane fade" id="tab-rut">
                  <div class="card inner-card border-0 shadow-sm">
                    <div class="card-body p-4">
                      <h5 class="fw-bold mb-4"><i class="fa-solid fa-circle-minus me-2 text-danger"></i>Rút tiền về tài khoản</h5>
                      <form @submit.prevent="rutTien">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <label class="form-label">Số tiền rút (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model.number="rutData.so_tien" :placeholder="'Tối đa ' + formatMoney(soDu)" min="50000" step="1000" :max="soDu" required />
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Ngân hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="rutData.ten_ngan_hang" placeholder="Tên ngân hàng" required />
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Số tài khoản <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="rutData.so_tai_khoan" placeholder="Số tài khoản ngân hàng" required />
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Tên chủ tài khoản <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="rutData.chu_tai_khoan" placeholder="Tên chủ tài khoản" required />
                          </div>
                          <div class="col-12">
                            <label class="form-label">Ghi chú</label>
                            <input type="text" class="form-control" v-model="rutData.ghi_chu" placeholder="Ghi chú (tuỳ chọn)" />
                          </div>
                        </div>
                        <div class="rut-warning mt-3">
                          <i class="fa-solid fa-triangle-exclamation me-2"></i>
                          Yêu cầu rút tiền sẽ được xử lý trong vòng 1-3 ngày làm việc.
                        </div>
                        <div class="text-end mt-4 pt-3 border-top">
                          <button type="button" class="btn btn-light me-2 px-4" @click="resetRutForm">Huỷ</button>
                          <button type="submit" class="btn btn-danger-custom px-5" :disabled="isRutTien">
                            <span v-if="isRutTien" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-else class="fa-solid fa-circle-minus me-2"></i>Gửi yêu cầu rút
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Gói Premium -->
                <div v-if="canAccessPremiumTab" class="tab-pane fade" id="tab-premium">
                  <div class="card inner-card border-0 shadow-sm">
                    <div class="card-body p-4">
                      <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                        <h5 class="fw-bold mb-0">
                          <i class="fa-solid fa-crown me-2 text-warning"></i>Gói Premium
                        </h5>
                        <button
                          type="button"
                          class="btn btn-light btn-sm"
                          :disabled="loadingPremium"
                          @click="loadGoiPremium"
                        >
                          <span v-if="loadingPremium" class="spinner-border spinner-border-sm"></span>
                          <i v-else class="fa-solid fa-rotate-right"></i>
                        </button>
                      </div>

                      <div v-if="loadingPremium" class="text-center py-4">
                        <span class="spinner-border text-primary"></span>
                      </div>

                      <div v-else-if="premiumError" class="alert alert-warning mb-0">
                        {{ premiumError }}
                      </div>

                      <template v-else-if="premiumGoi">
                        <div
                          v-if="premiumGoiDangDung"
                          class="alert alert-success d-flex align-items-center gap-2 mb-4"
                          role="status"
                        >
                          <i class="fa-solid fa-circle-check fa-lg"></i>
                          <div>
                            <strong>Gói đang hoạt động</strong>
                            <div class="small mb-0">
                              Hết hạn: {{ formatPremiumDate(premiumGoiDangDung.ngay_het_han) }}
                            </div>
                          </div>
                        </div>

                        <div class="premium-package-card p-4 rounded-3 border position-relative">
                          <div
                            v-if="premiumGoiDangDung"
                            class="premium-active-badge"
                            title="Gói premium đang hiệu lực"
                          >
                            <i class="fa-solid fa-certificate"></i>
                          </div>
                          <h6 class="fw-bold text-dark mb-2">{{ premiumGoi.ten_goi }}</h6>
                          <p class="text-muted small mb-3">{{ premiumGoi.mo_ta || "—" }}</p>
                          <div class="d-flex flex-wrap align-items-baseline gap-2 mb-3">
                            <span class="fs-4 fw-bold text-primary">{{ formatMoney(premiumGoi.gia) }}</span>
                            <span class="text-muted small">/ {{ premiumGoi.thoi_han_ngay }} ngày</span>
                          </div>
                          <ul v-if="premiumTinhNangList.length" class="small text-secondary ps-3 mb-4">
                            <li v-for="(line, idx) in premiumTinhNangList" :key="idx">{{ line }}</li>
                          </ul>

                          <button
                            v-if="premiumCoTheMua"
                            type="button"
                            class="btn btn-warning text-dark fw-semibold px-4"
                            :disabled="buyingPremium"
                            @click="openPremiumConfirmModal"
                          >
                            <i class="fa-solid fa-bag-shopping me-2"></i>
                            Mua bằng số dư ví
                          </button>
                          <p v-else-if="premiumGoiDangDung" class="text-muted small mb-0">
                            Khi hết hạn bạn có thể mua lại tại đây.
                          </p>
                          <p v-else class="text-muted small mb-0">
                            Hiện không mở bán gói cho tài khoản của bạn.
                          </p>
                        </div>
                      </template>

                      <p v-else class="text-muted mb-0">Không có gói premium cho tài khoản của bạn.</p>
                    </div>
                  </div>
                </div>

                <!-- Lịch sử giao dịch -->
                <div class="tab-pane fade" id="tab-lich-su">
                  <div class="card inner-card border-0 shadow-sm">
                    <div class="card-body p-4">
                      <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Lịch sử giao dịch</h5>
                        <button class="btn btn-light btn-sm" @click="loadLichSu" :disabled="loadingLichSu">
                          <span v-if="loadingLichSu" class="spinner-border spinner-border-sm"></span>
                          <i v-else class="fa-solid fa-rotate-right"></i> Làm mới
                        </button>
                      </div>

                      <!-- Filter -->
                      <div class="row g-2 mb-3">
                        <div class="col-md-4">
                          <select class="form-select" v-model="filterLoai" @change="loadLichSu">
                            <option value="">Tất cả loại</option>
                            <option value="nap">Nạp tiền</option>
                            <option value="rut">Rút tiền</option>
                            <option value="mua_goi">Mua gói premium</option>
                            <option value="thanh_toan">Thanh toán</option>
                            <option value="hoan_tien">Hoàn tiền</option>
                          </select>
                        </div>
                      </div>

                      <div v-if="loadingLichSu" class="text-center py-5">
                        <span class="spinner-border text-primary"></span>
                        <p class="text-muted mt-2 small">Đang tải...</p>
                      </div>

                      <div v-else-if="lichSuGD.length === 0" class="empty-state">
                        <i class="fa-solid fa-receipt fa-3x mb-3 text-muted"></i>
                        <p class="text-muted mb-0">Chưa có giao dịch nào</p>
                      </div>

                      <div v-else>
                        <div class="gd-item" v-for="gd in lichSuGD" :key="gd.id">
                          <div class="gd-icon" :class="gdIconClass(gd.loai)">
                            <i :class="gdIcon(gd.loai)"></i>
                          </div>
                          <div class="gd-info">
                            <div class="gd-mo-ta">{{ gd.mo_ta || loaiLabel(gd.loai) }}</div>
                            <div class="gd-time">{{ gd.created_at }}</div>
                          </div>
                          <div class="gd-right">
                            <div
                              class="gd-so-tien"
                              :class="gdAmountIsCredit(gd.loai) ? 'text-success' : 'text-danger'"
                            >
                              {{ gdAmountIsCredit(gd.loai) ? "+" : "-"
                              }}{{ formatMoney(gd.so_tien) }}
                            </div>
                            <span class="gd-status" :class="statusClass(gd.trang_thai)">{{ statusLabel(gd.trang_thai) }}</span>
                          </div>
                        </div>

                        <!-- Phân trang -->
                        <div class="d-flex justify-content-center mt-4" v-if="lichSuMeta.last_page > 1">
                          <button class="btn btn-light btn-sm me-2" :disabled="lichSuMeta.current_page === 1" @click="changePage(lichSuMeta.current_page - 1)">
                            <i class="fa-solid fa-chevron-left"></i>
                          </button>
                          <span class="align-self-center text-muted small">Trang {{ lichSuMeta.current_page }} / {{ lichSuMeta.last_page }}</span>
                          <button class="btn btn-light btn-sm ms-2" :disabled="lichSuMeta.current_page === lichSuMeta.last_page" @click="changePage(lichSuMeta.current_page + 1)">
                            <i class="fa-solid fa-chevron-right"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
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

    <!-- Modal: mã QR thanh toán nạp tiền -->
    <div
      v-if="showNapQrModal"
      class="modal-overlay"
      @click.self="closeNapQrModal"
    >
      <div class="modal-card nap-qr-modal-card" role="dialog" aria-modal="true" aria-labelledby="napQrModalTitle">
        <div class="nap-qr-modal-header">
          <div class="nap-qr-header-main">
            <div class="nap-qr-header-icon" aria-hidden="true">
              <i class="fa-solid fa-qrcode"></i>
            </div>
            <div>
              <div id="napQrModalTitle" class="nap-qr-header-title">Thanh toán nạp tiền</div>
              <div class="nap-qr-header-sub">Quét mã và chuyển khoản đúng số tiền</div>
            </div>
          </div>
          <button
            class="btn-close-modal ms-auto"
            type="button"
            aria-label="Đóng"
            @click="closeNapQrModal"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        <div class="modal-body-custom nap-qr-modal-body">
          <div class="nap-qr-amount-card">
            <div class="nap-qr-amount-label">Số tiền cần chuyển</div>
            <div class="nap-qr-amount-value">{{ formatMoney(napQrAmount) }}</div>
          </div>

          <div v-if="napMaDon" class="nap-qr-order-panel">
            <div class="nap-qr-order-meta">
              <span class="badge rounded-pill" :class="napTrangThaiBadgeClass">{{
                statusLabel(napTrangThai)
              }}</span>
              <span class="nap-qr-order-hint">Dùng mã này trong nội dung CK</span>
            </div>
            <div class="nap-qr-order-code-row">
              <code class="nap-qr-order-code">{{ napMaDon }}</code>
              <button
                type="button"
                class="btn btn-sm nap-qr-copy-btn"
                :disabled="!napMaDon"
                @click="copyNapMaDon"
              >
                <i class="fa-regular fa-copy me-1"></i>Sao chép
              </button>
            </div>
          </div>

          <div
            class="nap-qr-mode-chip"
            :class="napQrMode === 'vietqr' ? 'nap-qr-mode-live' : 'nap-qr-mode-demo'"
          >
            <i
              class="fa-solid me-2"
              :class="napQrMode === 'vietqr' ? 'fa-circle-check' : 'fa-triangle-exclamation'"
            ></i>
            <span v-if="napQrMode === 'vietqr'">VietQR đang bật — quét bằng app ngân hàng</span>
            <span v-else>Chế độ mô phỏng — cấu hình <code class="nap-qr-code-inline">VIETQR_*</code> trên API để dùng VietQR thật</span>
          </div>

          <div v-if="napQrMode === 'vietqr'" class="nap-qr-info-card nap-qr-info-live">
            <div class="nap-qr-info-icon"><i class="fa-solid fa-building-columns"></i></div>
            <div class="nap-qr-info-text">
              Quét mã VietQR để chuyển khoản. Ghi đúng <strong>mã đơn</strong> trong nội dung để admin đối soát.
              <template v-if="napQrBankHint || vietQrDisplayName">
                <span class="d-block mt-1 text-muted small">{{ napQrBankHint || vietQrDisplayName }}</span>
              </template>
            </div>
          </div>
          <div v-else class="nap-qr-info-card nap-qr-info-demo">
            <div class="nap-qr-info-icon"><i class="fa-solid fa-image"></i></div>
            <div class="nap-qr-info-text">
              Hiện hiển thị QR minh họa. Khi máy chủ cấu hình VietQR, mã tại đây sẽ là mã thanh toán thật.
            </div>
          </div>

          <ul class="nap-qr-steps" aria-label="Các bước thanh toán">
            <li>
              <span class="nap-qr-step-num">1</span>
              <span>Mở app ngân hàng và chọn quét QR</span>
            </li>
            <li>
              <span class="nap-qr-step-num">2</span>
              <span>Kiểm tra số tiền, dán <strong>mã đơn</strong> vào nội dung chuyển khoản</span>
            </li>
            <li>
              <span class="nap-qr-step-num">3</span>
              <span>Xác nhận — trạng thái đơn cập nhật khi được duyệt</span>
            </li>
          </ul>

          <div class="nap-qr-frame-outer">
            <div class="nap-qr-frame-glow" aria-hidden="true"></div>
            <div class="nap-qr-frame">
              <img
                v-if="napQrImageUrl"
                :src="napQrImageUrl"
                alt="Mã QR thanh toán"
                class="nap-qr-img"
              />
              <div v-else class="nap-qr-placeholder">
                <i class="fa-solid fa-qrcode nap-qr-placeholder-icon"></i>
                <span>Chưa có hình QR</span>
              </div>
            </div>
          </div>

          <p class="nap-qr-method-foot">
            <i class="fa-solid fa-credit-card me-1 opacity-75"></i>
            Phương thức: <strong>{{ napQrMethodLabel }}</strong>
          </p>
        </div>
        <div class="modal-footer-custom nap-qr-footer d-flex flex-wrap gap-2 justify-content-end align-items-center">
          <button type="button" class="btn btn-light px-3" @click="closeNapQrModal">
            Đóng
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: xác nhận mua gói Premium -->
    <div
      v-if="showPremiumConfirmModal"
      class="modal-overlay"
      @click.self="!buyingPremium && closePremiumConfirmModal()"
    >
      <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="premiumConfirmTitle">
        <div class="modal-header-custom info">
          <span id="premiumConfirmTitle">
            <i class="fa-solid fa-crown me-2 text-warning"></i>Xác nhận mua gói
          </span>
          <button
            type="button"
            class="btn-close-modal ms-auto"
            aria-label="Đóng"
            :disabled="buyingPremium"
            @click="closePremiumConfirmModal"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        <div class="modal-body-custom">
          <p class="mb-2" v-if="premiumGoi">
            <strong>{{ premiumGoi.ten_goi }}</strong>
            — {{ formatMoney(premiumGoi.gia) }} / {{ premiumGoi.thoi_han_ngay }} ngày
          </p>
          <p class="text-muted small mb-0">
            Số tiền sẽ được trừ từ ví. Bạn có chắc muốn tiếp tục?
          </p>
        </div>
        <div class="modal-footer-custom bg-light d-flex flex-wrap gap-2 justify-content-end">
          <button
            type="button"
            class="btn btn-light px-3"
            :disabled="buyingPremium"
            @click="closePremiumConfirmModal"
          >
            Hủy
          </button>
          <button
            type="button"
            class="btn btn-warning text-dark fw-semibold px-4"
            :disabled="buyingPremium"
            @click="confirmMuaGoiPremium"
          >
            <span v-if="buyingPremium" class="spinner-border spinner-border-sm me-2"></span>
            Xác nhận mua
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { API_BASE } from '../../../api/http.js';
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
    canAccessPremiumTab() {
      return this.isHocVien || this.isGiaoVien;
    },
    premiumGoi() {
      return this.premiumPayload && this.premiumPayload.goi
        ? this.premiumPayload.goi
        : null;
    },
    premiumGoiDangDung() {
      return this.premiumPayload && this.premiumPayload.goi_dang_dung
        ? this.premiumPayload.goi_dang_dung
        : null;
    },
    premiumCoTheMua() {
      return !!(this.premiumPayload && this.premiumPayload.co_the_mua);
    },
    premiumTinhNangList() {
      const raw = this.premiumGoi && this.premiumGoi.tinh_nang;
      if (!raw || typeof raw !== "object") return [];

      const boolLabels = {
        uu_tien_cham_diem: "Ưu tiên xếp hàng chấm điểm phát âm",
        bao_cao_tuan: "Báo cáo tiến độ học tập theo tuần",
        bao_cao_lop: "Báo cáo và thống kê lớp học",
      };

      const lines = [];
      for (const [key, val] of Object.entries(raw)) {
        if (typeof val === "boolean") {
          if (val !== true) continue;
          const label = boolLabels[key];
          if (label) lines.push(label);
        } else if (key === "gioi_han_hoc_vien" && typeof val === "number") {
          lines.push(`Quản lý tối đa ${val} học viên`);
        } else if (val != null && val !== "") {
          lines.push(String(val));
        }
      }
      return lines;
    },
    /** Tên chủ TK hiển thị khi dùng VietQR (env VITE_VIETQR_ACCOUNT_NAME) */
    vietQrDisplayName() {
      return String(import.meta.env.VITE_VIETQR_ACCOUNT_NAME || "").trim();
    },
    napTrangThaiBadgeClass() {
      const m = {
        cho_thanh_toan: "text-bg-warning text-dark",
        cho_quet_ma: "text-bg-warning text-dark",
        cho_xac_nhan: "text-bg-info",
        thanh_cong: "text-bg-success",
        that_bai: "text-bg-danger",
        cho_xu_ly: "text-bg-secondary",
      };
      return m[this.napTrangThai] || "text-bg-secondary";
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

      // Ví & Giao dịch
      soDu: 0,
      loadingSoDu: false,
      napData: { so_tien: "", ghi_chu: "" },
      rutData: { so_tien: "", ten_ngan_hang: "", so_tai_khoan: "", chu_tai_khoan: "", ghi_chu: "" },
      isRutTien: false,
      lichSuGD: [],
      lichSuMeta: { current_page: 1, last_page: 1 },
      loadingLichSu: false,
      filterLoai: "",
      currentPage: 1,
      quickAmounts: [50000, 100000, 200000, 500000],

      /** Mã QR nạp tiền: mở sau khi bấm «Nạp tiền» */
      showNapQrModal: false,
      napQrImageUrl: "",
      napQrMode: "demo",
      napQrAmount: 0,
      napQrMethodLabel: "",
      napQrBankHint: "",
      napMaDon: "",
      napTrangThai: "",
      napTienFlowLoading: false,
      napPollTimerId: null,

      depositDonList: [],
      loadingDepositDon: false,
      uploadingChungTuDonId: null,

      premiumPayload: null,
      loadingPremium: false,
      buyingPremium: false,
      premiumError: "",
      showPremiumConfirmModal: false,
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
        API_BASE
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
        .get(`${API_BASE}/api/profile`, {
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
        .post(`${API_BASE}/api/profile/update`, this.profileData, {
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
        .post(`${API_BASE}/api/profile/update-avatar`, formData, {
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
          `${API_BASE}/api/profile/change-password`,
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
          `${API_BASE}/api/homepage/ho-so-giao-vien/my-status`,
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
          `${API_BASE}/api/homepage/ho-so-giao-vien/my-status`,
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
    // ===== VÍ & GIAO DỊCH =====
    formatMoney(val) {
      return Number(val || 0).toLocaleString("vi-VN") + " ₫";
    },
    async loadSoDu() {
      this.loadingSoDu = true;
      try {
        const res = await axios.get(this.beApi("/api/vi/so-du"), {
          headers: { Authorization: "Bearer " + this.getAuthToken() },
        });
        if (res.data.status) this.soDu = res.data.so_du || 0;
      } catch {
        // Bỏ qua lỗi
      } finally {
        this.loadingSoDu = false;
      }
    },
    beApi(path) {
      const base = (
        API_BASE
      ).replace(/\/$/, "");
      const p = path.startsWith("/") ? path : `/${path}`;
      return `${base}${p}`;
    },
    depositProofUrl(u) {
      const s = String(u || "").trim();
      if (!s) return "";
      if (s.startsWith("http://") || s.startsWith("https://")) return s;
      const base = (
        API_BASE
      ).replace(/\/$/, "");
      if (s.startsWith("/")) return `${base}${s}`;
      return `${base}/${s.replace(/^\//, "")}`;
    },
    depositStatusLabel(tt) {
      const m = {
        cho_thanh_toan: "Chờ duyệt",
        thanh_cong: "Thành công",
        that_bai: "Từ chối",
        cho_quet_ma: "Chờ quét mã",
        cho_xac_nhan: "Chờ xác nhận",
        cho_xu_ly: "Chờ xử lý",
      };
      return m[tt] || tt || "—";
    },
    depositStatusBadgeClass(tt) {
      const m = {
        cho_thanh_toan: "text-bg-warning text-dark",
        thanh_cong: "text-bg-success",
        that_bai: "text-bg-danger",
        cho_quet_ma: "text-bg-secondary",
        cho_xac_nhan: "text-bg-secondary",
        cho_xu_ly: "text-bg-secondary",
      };
      return m[tt] || "text-bg-secondary";
    },
    async loadDepositDonList() {
      this.loadingDepositDon = true;
      try {
        const res = await axios.get(this.beApi("/api/deposit/history"), {
          params: { per_page: 30, page: 1 },
          headers: { Authorization: "Bearer " + this.getAuthToken() },
        });
        if (res.data.status) {
          this.depositDonList = res.data.data || [];
        } else {
          this.depositDonList = [];
        }
      } catch {
        this.depositDonList = [];
      } finally {
        this.loadingDepositDon = false;
      }
    },
    async onDepositChungTuFiles(event, don) {
      const input = event.target;
      const files = input.files;
      if (!files || !files.length) return;
      this.uploadingChungTuDonId = don.id;
      try {
        const fd = new FormData();
        for (let i = 0; i < files.length; i += 1) {
          fd.append("files[]", files[i]);
        }
        const res = await axios.post(
          this.beApi(`/api/deposit/don/${don.id}/chung-tu`),
          fd,
          {
            headers: {
              Authorization: "Bearer " + this.getAuthToken(),
            },
          },
        );
        if (res.data.status) {
          this.$toast.success(res.data.message || "Đã tải chứng từ.");
          await this.loadDepositDonList();
        } else {
          this.$toast.error(res.data.message || "Không tải được chứng từ.");
        }
      } catch (e) {
        this.$toast.error(
          e.response?.data?.message || "Không tải được chứng từ.",
        );
      } finally {
        this.uploadingChungTuDonId = null;
        input.value = "";
      }
    },
    formatPremiumDate(iso) {
      if (!iso) return "—";
      const d = new Date(iso);
      if (Number.isNaN(d.getTime())) return String(iso);
      return d.toLocaleString("vi-VN");
    },
    gdAmountIsCredit(loai) {
      return loai === "nap" || loai === "hoan_tien";
    },
    async loadGoiPremium() {
      this.loadingPremium = true;
      this.premiumError = "";
      try {
        const res = await axios.get(this.beApi("/api/goi-premium/goi-hien-tai"), {
          headers: { Authorization: "Bearer " + this.getAuthToken() },
        });
        if (res.data.status) {
          this.premiumPayload = {
            goi: res.data.goi || null,
            goi_dang_dung: res.data.goi_dang_dung || null,
            co_the_mua: !!res.data.co_the_mua,
          };
        } else {
          this.premiumPayload = null;
          this.premiumError = res.data.message || "Không tải được gói premium.";
        }
      } catch (e) {
        this.premiumPayload = null;
        this.premiumError =
          e.response?.data?.message || "Không tải được gói premium.";
      } finally {
        this.loadingPremium = false;
      }
    },
    openPremiumConfirmModal() {
      if (!this.premiumCoTheMua || !this.premiumGoi) return;
      this.showPremiumConfirmModal = true;
    },
    closePremiumConfirmModal() {
      if (this.buyingPremium) return;
      this.showPremiumConfirmModal = false;
    },
    async confirmMuaGoiPremium() {
      this.buyingPremium = true;
      try {
        const res = await axios.post(
          this.beApi("/api/goi-premium/mua"),
          {},
          { headers: { Authorization: "Bearer " + this.getAuthToken() } },
        );
        if (res.data.status) {
          this.$toast.success(res.data.message || "Mua gói thành công.");
          if (typeof res.data.so_du_sau === "number") {
            this.soDu = res.data.so_du_sau;
          }
          this.showPremiumConfirmModal = false;
          await this.loadGoiPremium();
          this.loadLichSu();
          window.dispatchEvent(new CustomEvent("premium-status-changed"));
        } else {
          this.$toast.error(res.data.message || "Không thể mua gói.");
        }
      } catch (e) {
        this.$toast.error(
          e.response?.data?.message || "Không thể mua gói.",
        );
      } finally {
        this.buyingPremium = false;
      }
    },
    clearNapDonTimers() {
      if (this.napPollTimerId != null) {
        clearInterval(this.napPollTimerId);
        this.napPollTimerId = null;
      }
    },
    closeNapQrModal() {
      this.clearNapDonTimers();
      this.showNapQrModal = false;
    },
    async copyNapMaDon() {
      if (!this.napMaDon) return;
      try {
        await navigator.clipboard.writeText(String(this.napMaDon));
        this.$toast.success("Đã sao chép mã đơn.");
      } catch {
        this.$toast.error("Không thể sao chép tự động — hãy chọn và copy thủ công.");
      }
    },
    startNapDonFlow() {
      this.clearNapDonTimers();
      if (!this.napMaDon) return;
      this.napPollTimerId = setInterval(() => {
        this.refreshNapTrangThai();
      }, 4000);
    },
    async refreshNapTrangThai() {
      if (!this.napMaDon || !this.showNapQrModal) return;
      try {
        const res = await axios.get(
          this.beApi(
            `/api/vi/nap-tien/${encodeURIComponent(this.napMaDon)}/trang-thai`,
          ),
          { headers: { Authorization: "Bearer " + this.getAuthToken() } },
        );
        if (res.data.status && res.data.trang_thai) {
          this.napTrangThai = res.data.trang_thai;
          if (this.napTrangThai === "thanh_cong") {
            this.finishNapFlowSuccess();
          }
          if (this.napTrangThai === "that_bai") {
            this.clearNapDonTimers();
            this.$toast.error("Đơn nạp đã bị từ chối.");
            this.closeNapQrModal();
            this.loadLichSu();
          }
        }
      } catch {
        /* bỏ qua lỗi poll */
      }
    },
    finishNapFlowSuccess() {
      this.clearNapDonTimers();
      this.showNapQrModal = false;
      this.napMaDon = "";
      this.napTrangThai = "";
      this.napQrImageUrl = "";
      this.napQrBankHint = "";
      this.napData = { so_tien: "", ghi_chu: "" };
      this.$toast.success("Nạp tiền thành công!");
      this.loadSoDu();
      this.loadLichSu();
      this.loadDepositDonList();
    },
    async onNapTienSubmit() {
      if (!this.napData.so_tien || this.napData.so_tien < 10000) {
        this.$toast.error("Số tiền nạp tối thiểu là 10.000 ₫");
        return;
      }
      const amount = Math.floor(Number(this.napData.so_tien));

      this.napQrAmount = amount;
      this.napQrMethodLabel = "Chuyển khoản (VietQR)";

      this.napTienFlowLoading = true;
      try {
        const res = await axios.post(
          this.beApi("/api/deposit/create"),
          {
            so_tien: amount,
            phuong_thuc: null,
            ghi_chu: this.napData.ghi_chu || null,
          },
          { headers: { Authorization: "Bearer " + this.getAuthToken() } },
        );
        if (!res.data.status || !res.data.ma_don) {
          this.$toast.error(res.data.message || "Không tạo được đơn nạp.");
          return;
        }
        this.napMaDon = res.data.ma_don;
        this.napTrangThai = res.data.trang_thai || "cho_thanh_toan";
        this.napQrImageUrl = res.data.qr_url || "";
        this.napQrMode = res.data.vietqr_configured ? "vietqr" : "demo";
        this.napQrBankHint = res.data.qr_account_name || "";

        this.showNapQrModal = true;
        this.startNapDonFlow();
        this.loadDepositDonList();
      } catch (err) {
        this.$toast.error(
          err.response?.data?.message || "Lỗi khi tạo đơn nạp tiền.",
        );
      } finally {
        this.napTienFlowLoading = false;
      }
    },
    async rutTien() {
      if (!this.rutData.so_tien || this.rutData.so_tien < 50000) {
        this.$toast.error("Số tiền rút tối thiểu là 50.000 ₫");
        return;
      }
      if (this.rutData.so_tien > this.soDu) {
        this.$toast.error("Số tiền rút vượt quá số dư trong ví.");
        return;
      }
      this.isRutTien = true;
      try {
        const res = await axios.post(
          this.beApi("/api/vi/rut-tien"),
          this.rutData,
          { headers: { Authorization: "Bearer " + this.getAuthToken() } }
        );
        if (res.data.status) {
          this.$toast.success(res.data.message || "Yêu cầu rút tiền đã được ghi nhận!");
          this.resetRutForm();
          this.loadSoDu();
        } else {
          this.$toast.error(res.data.message || "Không thể thực hiện rút tiền.");
        }
      } catch (err) {
        this.$toast.error(err.response?.data?.message || "Lỗi khi rút tiền.");
      } finally {
        this.isRutTien = false;
      }
    },
    resetRutForm() {
      this.rutData = { so_tien: "", ten_ngan_hang: "", so_tai_khoan: "", chu_tai_khoan: "", ghi_chu: "" };
    },
    async loadLichSu() {
      this.loadingLichSu = true;
      try {
        const res = await axios.get(this.beApi("/api/vi/lich-su"), {
          params: { loai: this.filterLoai || undefined, page: this.currentPage },
          headers: { Authorization: "Bearer " + this.getAuthToken() },
        });
        if (res.data.status) {
          this.lichSuGD = res.data.data || [];
          this.lichSuMeta = {
            current_page: res.data.current_page || 1,
            last_page: res.data.last_page || 1,
          };
        }
      } catch {
        this.lichSuGD = [];
      } finally {
        this.loadingLichSu = false;
      }
    },
    changePage(page) {
      this.currentPage = page;
      this.loadLichSu();
    },
    gdIcon(loai) {
      const map = {
        nap: "fa-solid fa-circle-plus",
        rut: "fa-solid fa-circle-minus",
        mua_goi: "fa-solid fa-crown",
        thanh_toan: "fa-solid fa-cart-shopping",
        hoan_tien: "fa-solid fa-rotate-left",
      };
      return map[loai] || "fa-solid fa-circle-dot";
    },
    gdIconClass(loai) {
      const map = {
        nap: "gd-icon-nap",
        rut: "gd-icon-rut",
        mua_goi: "gd-icon-mua-goi",
        thanh_toan: "gd-icon-thanh-toan",
        hoan_tien: "gd-icon-hoan",
      };
      return map[loai] || "";
    },
    loaiLabel(loai) {
      const map = {
        nap: "Nạp tiền",
        rut: "Rút tiền",
        mua_goi: "Mua gói premium",
        thanh_toan: "Thanh toán",
        hoan_tien: "Hoàn tiền",
      };
      return map[loai] || loai;
    },
    statusLabel(tt) {
      const map = {
        cho_thanh_toan: "Chờ thanh toán",
        cho_quet_ma: "Chờ quét mã / CK",
        cho_xac_nhan: "Chờ xác nhận",
        cho_xu_ly: "Chờ xử lý",
        thanh_cong: "Thành công",
        that_bai: "Thất bại",
        da_huy: "Đã huỷ",
      };
      return map[tt] || tt;
    },
    statusClass(tt) {
      const map = {
        cho_thanh_toan: "status-pending",
        cho_quet_ma: "status-pending",
        cho_xac_nhan: "status-pending",
        cho_xu_ly: "status-pending",
        thanh_cong: "status-success",
        that_bai: "status-fail",
        da_huy: "status-cancel",
      };
      return map[tt] || "";
    },
  },
  watch: {
    avatarDisplayUrl() {
      this.avatarLoadError = false;
    },
  },
  beforeUnmount() {
    this.clearNapDonTimers();
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

/* Không áp dụng padding custom cho select - để trình duyệt render đúng */
select.form-control {
  padding: 0.375rem 0.75rem;
  appearance: auto;
  -webkit-appearance: auto;
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

/* ===== WALLET & GIAO DỊCH ===== */
.wallet-hero {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  padding: 24px 28px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #fff;
}
.wallet-hero-left {
  display: flex;
  align-items: center;
  gap: 16px;
}
.wallet-icon-wrap {
  width: 54px;
  height: 54px;
  border-radius: 14px;
  background: rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
}
.wallet-label {
  font-size: 0.85rem;
  opacity: 0.85;
  font-weight: 500;
  margin-bottom: 4px;
}
.wallet-amount {
  font-size: 1.8rem;
  font-weight: 700;
  letter-spacing: -0.5px;
}

/* Sub-tabs ví */
.wallet-tabs {
  background: #f8fafc;
  border-radius: 12px;
  padding: 6px;
  gap: 6px;
}
.wallet-tabs .nav-link {
  color: #64748b;
  border-radius: 8px;
  font-weight: 600;
  padding: 10px 20px;
  border: none;
  transition: all 0.25s ease;
}
.wallet-tabs .nav-link:hover:not(.active) {
  background: #e2e8f0;
  color: #334155;
}
.wallet-tabs .nav-link.active {
  background: #fff;
  color: #667eea;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* Quick amount buttons */
.quick-amounts {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.quick-btn {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 5px 12px;
  font-size: 0.8rem;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s;
}
.quick-btn:hover {
  background: #667eea;
  color: #fff;
  border-color: #667eea;
}

/* Nút nạp/rút */
.btn-success-custom {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  border: none;
  color: #fff;
  border-radius: 10px;
  font-weight: 600;
  padding: 10px 20px;
  transition: all 0.3s;
}
.btn-success-custom:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(34,197,94,0.35);
  color: #fff;
}
.btn-danger-custom {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  border: none;
  color: #fff;
  border-radius: 10px;
  font-weight: 600;
  padding: 10px 20px;
  transition: all 0.3s;
}
.btn-danger-custom:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(239,68,68,0.35);
  color: #fff;
}

/* Cảnh báo rút tiền */
.rut-warning {
  background: #fefce8;
  border: 1px solid #fde047;
  border-radius: 10px;
  padding: 12px 16px;
  font-size: 0.875rem;
  color: #854d0e;
}

/* Giao dịch items */
.gd-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 0;
  border-bottom: 1px solid #f1f5f9;
}
.gd-item:last-child { border-bottom: none; }
.gd-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  flex-shrink: 0;
}
.gd-icon-nap    { background: #dcfce7; color: #16a34a; }
.gd-icon-rut    { background: #fee2e2; color: #dc2626; }
.gd-icon-thanh-toan { background: #ede9fe; color: #7c3aed; }
.gd-icon-hoan   { background: #dbeafe; color: #2563eb; }
.gd-icon-mua-goi { background: #fef3c7; color: #b45309; }

.premium-package-card {
  background: linear-gradient(145deg, #fffef8 0%, #fff 100%);
}
.premium-active-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  color: #198754;
  font-size: 1.35rem;
}

.deposit-don-item {
  background: #fafbff;
  border-color: #e2e8f0 !important;
}
.deposit-proof-thumb {
  width: 56px;
  height: 56px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.gd-info { flex: 1; min-width: 0; }
.gd-mo-ta {
  font-weight: 600;
  color: #1e293b;
  font-size: 0.9rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.gd-time {
  font-size: 0.78rem;
  color: #94a3b8;
  margin-top: 2px;
}
.gd-right { text-align: right; flex-shrink: 0; }
.gd-so-tien {
  font-weight: 700;
  font-size: 0.95rem;
}
.gd-status {
  font-size: 0.72rem;
  font-weight: 600;
  border-radius: 20px;
  padding: 2px 10px;
  margin-top: 4px;
  display: inline-block;
}
.status-pending  { background: #fef9c3; color: #854d0e; }
.status-success  { background: #dcfce7; color: #166534; }
.status-fail     { background: #fee2e2; color: #991b1b; }
.status-cancel   { background: #f1f5f9; color: #475569; }

/* Modal nạp tiền — VietQR */
.nap-qr-modal-card {
  max-width: 440px;
  width: min(440px, 94vw);
  border: 1px solid rgba(148, 163, 184, 0.35);
}
.nap-qr-modal-header {
  display: flex;
  align-items: flex-start;
  padding: 20px 22px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  gap: 12px;
}
.nap-qr-header-main {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}
.nap-qr-header-icon {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.22);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}
.nap-qr-header-title {
  font-weight: 800;
  font-size: 1.12rem;
  letter-spacing: -0.02em;
  line-height: 1.25;
}
.nap-qr-header-sub {
  font-size: 0.8rem;
  opacity: 0.92;
  margin-top: 4px;
  font-weight: 500;
}
.nap-qr-modal-header .btn-close-modal {
  color: rgba(255, 255, 255, 0.85);
}
.nap-qr-modal-header .btn-close-modal:hover {
  color: #fff;
}
.nap-qr-modal-body {
  padding: 22px 22px 20px;
  background: linear-gradient(180deg, #f8fafc 0%, #fff 120px);
}
.nap-qr-amount-card {
  text-align: center;
  padding: 16px 18px;
  border-radius: 14px;
  background: #fff;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 20px rgba(102, 126, 234, 0.12);
  margin-bottom: 16px;
}
.nap-qr-amount-label {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #64748b;
  margin-bottom: 6px;
}
.nap-qr-amount-value {
  font-size: 1.65rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.03em;
  line-height: 1.2;
}
.nap-qr-order-panel {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 12px 14px;
  margin-bottom: 14px;
}
.nap-qr-order-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px 12px;
  margin-bottom: 10px;
}
.nap-qr-order-hint {
  font-size: 0.78rem;
  color: #64748b;
}
.nap-qr-order-code-row {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
.nap-qr-order-code {
  flex: 1;
  min-width: 0;
  font-size: 0.95rem;
  font-weight: 700;
  color: #1e293b;
  background: #f1f5f9;
  border-radius: 8px;
  padding: 8px 12px;
  border: 1px dashed #cbd5e1;
  word-break: break-all;
}
.nap-qr-copy-btn {
  border-radius: 8px;
  font-weight: 600;
  border: 1px solid #cbd5e1;
  color: #475569;
  flex-shrink: 0;
}
.nap-qr-copy-btn:hover:not(:disabled) {
  background: #f8fafc;
  border-color: #94a3b8;
  color: #1e293b;
}
.nap-qr-mode-chip {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 0.8rem;
  font-weight: 600;
  padding: 10px 12px;
  border-radius: 10px;
  margin-bottom: 12px;
  line-height: 1.45;
}
.nap-qr-mode-live {
  background: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}
.nap-qr-mode-demo {
  background: #fffbeb;
  color: #92400e;
  border: 1px solid #fde68a;
}
.nap-qr-code-inline {
  font-size: 0.72rem;
  padding: 1px 6px;
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.06);
}
.nap-qr-info-card {
  display: flex;
  gap: 12px;
  padding: 12px 14px;
  border-radius: 12px;
  margin-bottom: 14px;
  text-align: left;
  font-size: 0.85rem;
  line-height: 1.5;
}
.nap-qr-info-live {
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #1e3a5f;
}
.nap-qr-info-demo {
  background: #faf5ff;
  border: 1px solid #e9d5ff;
  color: #5b21b6;
}
.nap-qr-info-icon {
  flex-shrink: 0;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
}
.nap-qr-info-live .nap-qr-info-icon {
  background: #dbeafe;
  color: #1d4ed8;
}
.nap-qr-info-demo .nap-qr-info-icon {
  background: #ede9fe;
  color: #6d28d9;
}
.nap-qr-info-text {
  min-width: 0;
}
.nap-qr-steps {
  list-style: none;
  padding: 0;
  margin: 0 0 18px;
  text-align: left;
}
.nap-qr-steps li {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 0.82rem;
  color: #475569;
  margin-bottom: 8px;
  line-height: 1.45;
}
.nap-qr-steps li:last-child {
  margin-bottom: 0;
}
.nap-qr-step-num {
  flex-shrink: 0;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: #fff;
  font-size: 0.72rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 1px;
}
.nap-qr-frame-outer {
  position: relative;
  display: flex;
  justify-content: center;
  margin-bottom: 14px;
}
.nap-qr-frame-glow {
  position: absolute;
  inset: -8px;
  border-radius: 20px;
  background: radial-gradient(
    ellipse at center,
    rgba(102, 126, 234, 0.25) 0%,
    transparent 70%
  );
  pointer-events: none;
}
.nap-qr-frame {
  position: relative;
  background: #fff;
  padding: 14px;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 12px 40px rgba(15, 23, 42, 0.12);
}
.nap-qr-img {
  display: block;
  max-width: 260px;
  width: 100%;
  height: auto;
  border-radius: 8px;
}
.nap-qr-placeholder {
  width: 220px;
  max-width: 100%;
  aspect-ratio: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #94a3b8;
  font-size: 0.8rem;
  font-weight: 600;
  background: #f8fafc;
  border-radius: 8px;
  border: 2px dashed #e2e8f0;
}
.nap-qr-placeholder-icon {
  font-size: 2.5rem;
  opacity: 0.45;
}
.nap-qr-method-foot {
  text-align: center;
  font-size: 0.8rem;
  color: #64748b;
  margin: 0;
}
.nap-qr-footer {
  background: #f8fafc !important;
  border-top-color: #e2e8f0 !important;
}

/* Empty state */
.empty-state {
  text-align: center;
  padding: 48px 20px;
}
</style>
