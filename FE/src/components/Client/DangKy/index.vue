<template>
  <div
    class="register-page d-flex align-items-center justify-content-center py-5 min-vh-100 position-relative overflow-hidden"
    style="background: linear-gradient(to bottom, #fff8f4, #fff1eb)"
  >
    <div
      class="position-absolute rounded-circle"
      style="
        width: 260px;
        height: 260px;
        background: rgba(255, 107, 53, 0.08);
        top: -80px;
        left: -80px;
        animation: float 8s ease-in-out infinite;
      "
    ></div>
    <div
      class="position-absolute rounded-circle"
      style="
        width: 160px;
        height: 160px;
        background: rgba(255, 193, 7, 0.12);
        right: 80px;
        top: 100px;
        animation: float 6s ease-in-out infinite;
      "
    ></div>
    <div
      class="position-absolute rounded-circle"
      style="
        width: 220px;
        height: 220px;
        background: rgba(32, 201, 151, 0.08);
        bottom: -80px;
        right: -60px;
        animation: float 10s ease-in-out infinite;
      "
    ></div>

    <div class="container position-relative" style="z-index: 2">
      <div class="row justify-content-center align-items-center">
        <!-- Left panel -->
        <div
          v-show="!((buoc === 2 || buoc === 3) && loaiTK === 'giao_vien')"
          class="col-lg-6 mb-4 mb-lg-0 pe-lg-5 text-center text-lg-start transition-all"
        >
          <span
            class="badge rounded-pill px-4 py-2 mb-3 shadow-sm border"
            style="
              background: #fff0e8;
              color: #ff6b35;
              font-size: 1rem;
              border-color: #ffd8c8 !important;
            "
            >✨ Tạo Tài Khoản Cùng EchoKids</span
          >
          <h1
            class="display-4 fw-bold mb-3"
            style="
              color: #0d3b66;
              font-family: &quot;Lobster Two&quot;, cursive;
            "
          >
            Bắt Đầu Hành Trình Học Tập Cùng EchoKids
          </h1>
          <p class="text-secondary fs-5 mb-4">
            Tạo tài khoản để luyện phát âm tiếng Việt, nhận huy hiệu và khám phá
            những bài học vui nhộn mỗi ngày.
          </p>
          <div class="row g-3 mb-4">
            <div v-for="stat in stats" :key="stat.label" class="col-4">
              <div
                class="bg-white bg-opacity-75 rounded-4 p-3 shadow-sm border-2 border text-center"
                style="transition: transform 0.3s"
                onmouseover="this.style.transform = 'translateY(-5px)'"
                onmouseout="this.style.transform = 'translateY(0)'"
                :style="{ borderColor: stat.color + '33' }"
              >
                <h3 class="fw-bold mb-1" :style="{ color: stat.color }">
                  {{ stat.value }}
                </h3>
                <small class="text-muted fw-bold small text-uppercase">{{
                  stat.label
                }}</small>
              </div>
            </div>
          </div>
          <div
            class="d-flex gap-3 flex-wrap justify-content-center justify-content-lg-start"
          >
            <div
              v-for="feat in ['🎤 Ghi Âm', '🤖 AI Chấm Điểm', '🏆 Huy Hiệu']"
              :key="feat"
              class="bg-white px-3 py-2 rounded-pill shadow-sm fw-bold small"
              style="color: #0d3b66"
            >
              {{ feat }}
            </div>
          </div>
        </div>

        <!-- Right panel -->
        <div
          :class="
            (buoc === 2 || buoc === 3) && loaiTK === 'giao_vien'
              ? 'col-lg-10'
              : 'col-lg-6 col-xl-5'
          "
          class="transition-all"
        >
          <div
            class="card border-0 shadow-lg p-3 p-md-4 position-relative overflow-hidden"
            style="
              border-radius: 36px;
              background: rgba(255, 255, 255, 0.94);
              backdrop-filter: blur(12px);
            "
          >
            <div
              class="position-absolute rounded-circle"
              style="
                width: 100px;
                height: 100px;
                background: rgba(255, 107, 53, 0.05);
                top: -30px;
                right: -30px;
              "
            ></div>

            <div class="text-center mb-3">
              <div
                v-if="!((buoc === 2 || buoc === 3) && loaiTK === 'giao_vien')"
                class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle shadow"
                style="
                  width: 70px;
                  height: 70px;
                  background: linear-gradient(135deg, #ffb18f, #ffd6c7);
                  color: white;
                  font-size: 28px;
                "
              >
                <i class="fa fa-user-plus"></i>
              </div>
              <div v-else class="icon-circle-xl mx-auto mb-3">
                <i
                  class="fa-solid fa-chalkboard-user fa-2x"
                  style="color: #667eea"
                ></i>
              </div>

              <h2
                v-if="!((buoc === 2 || buoc === 3) && loaiTK === 'giao_vien')"
                class="fw-bold mb-1"
                style="color: #0d3b66"
              >
                Đăng Ký
              </h2>
              <h3 v-else class="fw-bold mb-2 text-dark">Đăng ký Giáo viên</h3>

              <p
                class="text-muted small mb-0"
                v-if="!((buoc === 2 || buoc === 3) && loaiTK === 'giao_vien')"
              >
                Tạo tài khoản để bắt đầu học cùng EchoKids
              </p>
              <p class="text-muted mb-0" v-else>
                Điền đầy đủ thông tin bên dưới để nộp hồ sơ. Admin sẽ xét duyệt
                và thông báo qua email.
              </p>
            </div>

            <!-- BƯỚC 1: Chọn loại tài khoản -->
            <div v-if="buoc === 1">
              <p class="fw-bold text-center mb-3" style="color: #0d3b66">
                Bạn muốn đăng ký loại tài khoản nào?
              </p>
              <div class="row g-3">
                <div class="col-6">
                  <div
                    class="chon-loai h-100 d-flex flex-column align-items-center justify-content-center text-center p-4 rounded-4 border-2 border"
                    style="
                      cursor: pointer;
                      transition: all 0.3s;
                      border-color: #ffe5d9 !important;
                      background: #fff8f4;
                    "
                    @click="chonLoai('hoc_sinh')"
                    @mouseover="hoverType = 'hoc_sinh'"
                    @mouseout="hoverType = ''"
                    :class="{ selected: hoverType === 'hoc_sinh' }"
                  >
                    <div class="fs-1 mb-2">🎓</div>
                    <div class="fw-bold" style="color: #ff6b35">Học Sinh</div>
                    <small class="text-muted"
                      >Tài khoản học tập thông thường</small
                    >
                  </div>
                </div>
                <div class="col-6">
                  <div
                    class="chon-loai h-100 d-flex flex-column align-items-center justify-content-center text-center p-4 rounded-4 border-2 border"
                    style="
                      cursor: pointer;
                      transition: all 0.3s;
                      border-color: #e0e7ff !important;
                      background: #f8fafc;
                    "
                    @click="chonLoai('giao_vien')"
                    @mouseover="hoverType = 'giao_vien'"
                    @mouseout="hoverType = ''"
                    :class="{ selected: hoverType === 'giao_vien' }"
                  >
                    <div class="fs-1 mb-2">👨‍🏫</div>
                    <div class="fw-bold" style="color: #667eea">Giáo Viên</div>
                    <small class="text-muted">Nộp hồ sơ đăng ký dạy học</small>
                  </div>
                </div>
              </div>
              <div class="text-center mt-4">
                <span class="text-muted">Đã có tài khoản?</span>
                <router-link
                  to="/dang-nhap"
                  class="ms-1 fw-bold text-decoration-none"
                  style="color: #ff6b35"
                  >Đăng nhập ngay</router-link
                >
              </div>
            </div>

            <!-- BƯỚC 2: Form đăng ký -->
            <div v-if="buoc === 2">
              <button
                type="button"
                class="btn btn-sm btn-light rounded-pill mb-3 px-3"
                @click="buoc = 1"
              >
                <i class="fa fa-arrow-left me-1"></i> Quay lại
              </button>

              <form @submit.prevent="xuLyDangKy">
                <template v-if="loaiTK === 'hoc_sinh'">
                  <div class="text-center mb-3">
                    <span
                      class="badge rounded-pill px-3 py-2"
                      style="background: #fff0e8; color: #ff6b35"
                      >🎓 Tài Khoản Học Sinh</span
                    >
                  </div>
                  <!-- Thông tin cơ bản Học sinh -->
                  <div class="row g-2">
                    <div class="col-md-6">
                      <label class="form-label fw-bold text-secondary ms-2"
                        >Họ Và Tên</label
                      >
                      <div
                        class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm"
                        style="border-color: #ffe5d9 !important"
                      >
                        <span class="input-group-text bg-transparent border-0"
                          ><i class="fa fa-user text-warning"></i
                        ></span>
                        <input
                          v-model.trim="form.ho_ten"
                          type="text"
                          class="form-control hs-input bg-transparent border-0 shadow-none"
                          placeholder="Nhập họ tên"
                          autocomplete="name"
                        />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold text-secondary ms-2"
                        >Ngày Sinh</label
                      >
                      <div
                        class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm"
                        style="border-color: #ffe5d9 !important"
                      >
                        <span class="input-group-text bg-transparent border-0"
                          ><i class="fa fa-calendar text-warning"></i
                        ></span>
                        <input
                          v-model="form.ngay_sinh"
                          type="date"
                          class="form-control hs-input bg-transparent border-0 shadow-none text-muted"
                        />
                      </div>
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-bold text-secondary ms-2"
                        >Email</label
                      >
                      <div
                        class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm"
                        style="border-color: #ffe5d9 !important"
                      >
                        <span class="input-group-text bg-transparent border-0"
                          ><i class="fa fa-envelope text-warning"></i
                        ></span>
                        <input
                          v-model.trim="form.email"
                          type="email"
                          class="form-control hs-input bg-transparent border-0 shadow-none"
                          placeholder="example@email.com"
                          autocomplete="email"
                        />
                      </div>
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-bold text-secondary ms-2"
                        >Số điện thoại
                        <span class="text-muted fw-normal"
                          >(tùy chọn)</span
                        ></label
                      >
                      <div
                        class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm"
                        style="border-color: #ffe5d9 !important"
                      >
                        <span class="input-group-text bg-transparent border-0"
                          ><i class="fa fa-phone text-warning"></i
                        ></span>
                        <input
                          v-model.trim="form.sdt"
                          type="tel"
                          maxlength="10"
                          class="form-control hs-input bg-transparent border-0 shadow-none"
                          placeholder="0xxxxxxxxx"
                          autocomplete="tel"
                        />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold text-secondary ms-2"
                        >Mật Khẩu</label
                      >
                      <div
                        class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm"
                        style="border-color: #ffe5d9 !important"
                      >
                        <span class="input-group-text bg-transparent border-0"
                          ><i class="fa fa-lock text-warning"></i
                        ></span>
                        <input
                          v-model="form.password"
                          :type="showPassword ? 'text' : 'password'"
                          class="form-control hs-input bg-transparent border-0 shadow-none"
                          placeholder="Nhập mật khẩu"
                          autocomplete="new-password"
                        />
                        <span
                          class="input-group-text bg-transparent border-0"
                          style="cursor: pointer"
                          @click="showPassword = !showPassword"
                          ><i
                            :class="
                              showPassword ? 'fa fa-eye' : 'fa fa-eye-slash'
                            "
                            class="text-secondary"
                          ></i
                        ></span>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold text-secondary ms-2"
                        >Nhập Lại Mật Khẩu</label
                      >
                      <div
                        class="input-group p-1 bg-light border border-2 rounded-4 shadow-sm"
                        style="border-color: #ffe5d9 !important"
                      >
                        <span class="input-group-text bg-transparent border-0"
                          ><i class="fa fa-key text-warning"></i
                        ></span>
                        <input
                          v-model="form.password_confirmation"
                          :type="showPasswordConfirm ? 'text' : 'password'"
                          class="form-control hs-input bg-transparent border-0 shadow-none"
                          placeholder="Nhập lại mật khẩu"
                          autocomplete="new-password"
                        />
                        <span
                          class="input-group-text bg-transparent border-0"
                          style="cursor: pointer"
                          @click="showPasswordConfirm = !showPasswordConfirm"
                          ><i
                            :class="
                              showPasswordConfirm
                                ? 'fa fa-eye'
                                : 'fa fa-eye-slash'
                            "
                            class="text-secondary"
                          ></i
                        ></span>
                      </div>
                    </div>
                    <div class="col-12">
                      <small class="text-muted ms-1 d-block"
                        >Mật khẩu: chữ thường, chữ in hoa và ít nhất một chữ số
                        (tối thiểu 6 ký tự).</small
                      >
                    </div>
                  </div>

                  <div class="form-check mt-3 mb-3 ms-1">
                    <input
                      v-model="dongYDieuKhoan"
                      class="form-check-input shadow-none"
                      type="checkbox"
                      id="terms"
                    />
                    <label
                      class="form-check-label text-muted"
                      for="terms"
                      style="cursor: pointer"
                    >
                      Tôi đồng ý với
                      <a
                        href="#"
                        class="text-decoration-none fw-bold"
                        style="color: #ff6b35"
                        >điều khoản</a
                      >
                      của EchoKids
                    </label>
                  </div>

                  <button
                    type="submit"
                    class="btn w-100 rounded-pill py-3 fw-bold border-0 text-white shadow mb-3"
                    style="
                      background: linear-gradient(135deg, #ff6b35, #ff914d);
                    "
                    :disabled="dangGui"
                  >
                    <span v-if="dangGui"
                      ><i class="fa fa-spinner fa-spin me-2"></i>Đang xử
                      lý...</span
                    >
                    <span v-else>Tạo Tài Khoản</span>
                  </button>
                </template>

                <!-- ================== FORM GIÁO VIÊN ================== -->
                <template v-else>
                  <h5
                    class="fw-bold mb-3 border-bottom pb-2"
                    style="color: #667eea"
                  >
                    1. Thông tin tài khoản
                  </h5>
                  <div class="row g-4 mb-4">
                    <div class="col-md-6">
                      <label class="gv-label"
                        >Họ và tên <span class="text-danger">*</span></label
                      >
                      <input
                        type="text"
                        class="form-control gv-input"
                        v-model.trim="form.ho_ten"
                        placeholder="Nhập họ và tên"
                      />
                    </div>
                    <div class="col-md-6">
                      <label class="gv-label"
                        >Ngày sinh <span class="text-danger">*</span></label
                      >
                      <input
                        type="date"
                        class="form-control gv-input"
                        v-model="form.ngay_sinh"
                      />
                    </div>
                    <div class="col-md-6">
                      <label class="gv-label"
                        >Email <span class="text-danger">*</span></label
                      >
                      <input
                        type="email"
                        class="form-control gv-input"
                        v-model.trim="form.email"
                        placeholder="example@email.com"
                      />
                    </div>
                    <div class="col-md-6">
                      <label class="gv-label"
                        >Số điện thoại
                        <span class="text-muted fw-normal"
                          >(tùy chọn)</span
                        ></label
                      >
                      <input
                        type="tel"
                        maxlength="10"
                        class="form-control gv-input"
                        v-model.trim="form.sdt"
                        placeholder="Ví dụ: 0901234567"
                      />
                    </div>
                    <div class="col-md-6">
                      <label class="gv-label"
                        >Mật khẩu <span class="text-danger">*</span></label
                      >
                      <div class="input-group">
                        <input
                          :type="showPassword ? 'text' : 'password'"
                          class="form-control gv-input"
                          v-model="form.password"
                          placeholder="Nhập mật khẩu"
                          style="border-right: none"
                        />
                        <span
                          class="input-group-text bg-white"
                          style="
                            cursor: pointer;
                            border-radius: 0 12px 12px 0;
                            border-color: #e2e8f0;
                          "
                          @click="showPassword = !showPassword"
                        >
                          <i
                            :class="
                              showPassword ? 'fa fa-eye' : 'fa fa-eye-slash'
                            "
                            class="text-secondary"
                          ></i>
                        </span>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label class="gv-label"
                        >Nhập lại mật khẩu
                        <span class="text-danger">*</span></label
                      >
                      <div class="input-group">
                        <input
                          :type="showPasswordConfirm ? 'text' : 'password'"
                          class="form-control gv-input"
                          v-model="form.password_confirmation"
                          placeholder="Nhập lại mật khẩu"
                          style="border-right: none"
                        />
                        <span
                          class="input-group-text bg-white"
                          style="
                            cursor: pointer;
                            border-radius: 0 12px 12px 0;
                            border-color: #e2e8f0;
                          "
                          @click="showPasswordConfirm = !showPasswordConfirm"
                        >
                          <i
                            :class="
                              showPasswordConfirm
                                ? 'fa fa-eye'
                                : 'fa fa-eye-slash'
                            "
                            class="text-secondary"
                          ></i>
                        </span>
                      </div>
                    </div>
                    <div class="col-12 mt-1">
                      <small class="text-muted"
                        >Mật khẩu: chữ thường, chữ in hoa và ít nhất một chữ số
                        (tối thiểu 6 ký tự).</small
                      >
                    </div>
                  </div>

                  <h5
                    class="fw-bold mb-3 mt-5 border-bottom pb-2"
                    style="color: #667eea"
                  >
                    2. Thông tin cá nhân
                  </h5>
                  <div class="row g-4 mb-4">
                    <div class="col-md-6">
                      <label class="gv-label"
                        >Nghề nghiệp <span class="text-danger">*</span></label
                      >
                      <select
                        class="form-control gv-select gv-input"
                        v-model="gvForm.chuyen_mon"
                      >
                        <option value="" disabled>
                          -- Chọn nghề nghiệp --
                        </option>
                        <option value="Giáo viên">Giáo viên</option>
                        <option value="Chuyên gia">Chuyên gia</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="gv-label">Ảnh đại diện (Tùy chọn)</label>
                      <div
                        class="upload-area-sm"
                        @click="$refs.anhInput.click()"
                      >
                        <input
                          ref="anhInput"
                          type="file"
                          class="d-none"
                          accept="image/*"
                          @change="(e) => handleFile(e, 'anh_dai_dien')"
                        />
                        <div v-if="!gvFiles.anh_dai_dien">
                          <i
                            class="fa-solid fa-image mb-1"
                            style="color: #667eea"
                          ></i>
                          <p class="mb-0 small text-muted">Chọn ảnh đại diện</p>
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
                            >{{ gvFiles.anh_dai_dien.name }}
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
                    <div class="col-12">
                      <label class="gv-label">Giới thiệu bản thân</label>
                      <textarea
                        class="form-control gv-input"
                        v-model="gvForm.mo_ta"
                        rows="2"
                        placeholder="Mô tả kinh nghiệm giảng dạy, thành tích..."
                      ></textarea>
                    </div>
                  </div>

                  <h5
                    class="fw-bold mb-3 mt-5 border-bottom pb-2"
                    style="color: #667eea"
                  >
                    3. Định danh cá nhân (CCCD/CMND)
                  </h5>
                  <div class="row g-4 mb-4">
                    <div class="col-md-6">
                      <label class="gv-label"
                        >CCCD Mặt trước
                        <span class="text-danger">*</span></label
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
                        <div v-if="!gvFiles.cccd_mat_truoc">
                          <i
                            class="fa-solid fa-id-card mb-1"
                            style="color: #667eea"
                          ></i>
                          <p class="mb-0 small text-muted">Tải lên mặt trước</p>
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
                            >{{ gvFiles.cccd_mat_truoc.name }}
                          </span>
                          <button
                            type="button"
                            class="btn-close-sm flex-shrink-0"
                            @click.stop="removeFile('cccd_mat_truoc')"
                          >
                            <i class="fa-solid fa-xmark"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label class="gv-label"
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
                        <div v-if="!gvFiles.cccd_mat_sau">
                          <i
                            class="fa-solid fa-id-card mb-1"
                            style="color: #667eea"
                          ></i>
                          <p class="mb-0 small text-muted">Tải lên mặt sau</p>
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
                            >{{ gvFiles.cccd_mat_sau.name }}
                          </span>
                          <button
                            type="button"
                            class="btn-close-sm flex-shrink-0"
                            @click.stop="removeFile('cccd_mat_sau')"
                          >
                            <i class="fa-solid fa-xmark"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <h5
                    class="fw-bold mb-3 mt-5 border-bottom pb-2"
                    style="color: #667eea"
                  >
                    4. Hồ sơ chuyên môn
                  </h5>
                  <div class="row g-4 mb-4">
                    <div class="col-12">
                      <label class="gv-label"
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
                        <div v-if="!gvFiles.bang_cap" class="text-center">
                          <i
                            class="fa-solid fa-certificate fa-2x mb-2"
                            style="color: #667eea"
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
                            {{ gvFiles.bang_cap.name }}
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

                  <div class="form-check mt-5 pt-2 mb-3 ms-1">
                    <input
                      v-model="dongYDieuKhoan"
                      class="form-check-input shadow-none"
                      type="checkbox"
                      id="terms"
                    />
                    <label
                      class="form-check-label text-muted"
                      for="terms"
                      style="cursor: pointer"
                    >
                      Tôi đồng ý với
                      <a
                        href="#"
                        class="text-decoration-none fw-bold"
                        style="color: #667eea"
                        >điều khoản</a
                      >
                      của EchoKids
                    </label>
                  </div>

                  <div class="text-end pt-3 border-top mt-2">
                    <button
                      type="submit"
                      class="btn px-5 py-2 fw-bold text-white rounded-3 shadow-sm btn-submit-gv"
                      :disabled="dangGui"
                    >
                      <span v-if="dangGui"
                        ><i class="fa fa-spinner fa-spin me-2"></i>Đang xử
                        lý...</span
                      >
                      <span v-else
                        ><i class="fa-solid fa-paper-plane me-2"></i>Nộp hồ
                        sơ</span
                      >
                    </button>
                  </div>
                </template>

                <div v-if="loaiTK === 'hoc_sinh'" class="text-center mt-3">
                  <span class="text-muted">Đã có tài khoản?</span>
                  <router-link
                    to="/dang-nhap"
                    class="ms-1 fw-bold text-decoration-none"
                    style="color: #ff6b35"
                    >Đăng nhập ngay</router-link
                  >
                </div>
                <div v-if="loaiTK === 'giao_vien'" class="text-center mt-4">
                  <span class="text-muted">Đã có tài khoản?</span>
                  <router-link
                    to="/dang-nhap"
                    class="ms-1 fw-bold text-decoration-none"
                    style="color: #667eea"
                    >Đăng nhập ngay</router-link
                  >
                </div>
              </form>
            </div>

            <!-- BƯỚC 3: Thành công (dành riêng cho giáo viên) -->
            <div v-if="buoc === 3" class="text-center mt-3">
              <div class="alert mt-4 text-start" style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; color: #92400e;">
                <div class="d-flex align-items-center flex-wrap gap-2">
                  <div class="d-flex align-items-center">
                    <i class="fa-solid fa-hourglass-half me-2"></i>
                    <strong class="mb-0">Hồ sơ đang chờ duyệt</strong>
                  </div>
                  <span class="small opacity-75 d-none d-sm-inline">|</span>
                  <span class="small opacity-75">
                    Nộp ngày: {{ new Date().toLocaleString('vi-VN', { dateStyle: 'short', timeStyle: 'short' }) }}. Vui lòng chờ Nhân Viên EchoKids xét duyệt.
                  </span>
                </div>
              </div>
              
              <div class="mt-5 mb-3">
                <router-link to="/dang-nhap" class="btn btn-light px-5 py-2 shadow-sm border" style="color: #475569; border-radius: 12px;">
                  <i class="fa-solid fa-arrow-left me-2"></i>Quay lại đăng nhập
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { publicHttp } from '../../../api/http.js';

const REGEX_MAT_KHAU_MANH = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/;

export default {
  data() {
    return {
      buoc: 1,
      loaiTK: "",
      hoverType: "",
      dangGui: false,
      dongYDieuKhoan: false,
      showPassword: false,
      showPasswordConfirm: false,
      stats: [
        { value: "10K+", label: "Bé học", color: "#ff6b35" },
        { value: "500+", label: "Bài học", color: "#20c997" },
        { value: "98%", label: "Hài lòng", color: "#0d6efd" },
      ],
      form: {
        ho_ten: "",
        email: "",
        password: "",
        password_confirmation: "",
        sdt: "",
        ngay_sinh: "",
      },
      gvForm: {
        chuyen_mon: "",
        mo_ta: "",
      },
      gvFiles: {
        anh_dai_dien: null,
        cccd_mat_truoc: null,
        cccd_mat_sau: null,
        bang_cap: null,
      },
    };
  },
  methods: {
    chonLoai(loai) {
      this.loaiTK = loai;
      this.buoc = 2;
    },
    handleFile(e, fieldName) {
      const file = e.target.files[0];
      if (!file) return;
      if (file.size > 5 * 1024 * 1024) {
        this.$toast.error("File không được vượt quá 5MB.");
        return;
      }
      this.gvFiles[fieldName] = file;
    },
    removeFile(fieldName) {
      this.gvFiles[fieldName] = null;
      const inputMap = {
        anh_dai_dien: "anhInput",
        cccd_mat_truoc: "cccdTruocInput",
        cccd_mat_sau: "cccdSauInput",
        bang_cap: "bangCapInput",
      };
      const refName = inputMap[fieldName];
      if (this.$refs[refName]) this.$refs[refName].value = "";
    },
    validForm() {
      if (!this.dongYDieuKhoan) {
        this.$toast.error("Vui lòng đồng ý điều khoản sử dụng.");
        return false;
      }
      if (this.form.password !== this.form.password_confirmation) {
        this.$toast.error("Mật khẩu nhập lại không khớp.");
        return false;
      }
      if (!REGEX_MAT_KHAU_MANH.test(this.form.password)) {
        this.$toast.error(
          "Mật khẩu phải có ít nhất 6 ký tự, gồm chữ thường, chữ in hoa và ít nhất một chữ số.",
        );
        return false;
      }
      if (this.loaiTK === "giao_vien") {
        if (!this.gvForm.chuyen_mon) {
          this.$toast.error("Vui lòng chọn nghề nghiệp.");
          return false;
        }
        if (!this.gvFiles.cccd_mat_truoc) {
          this.$toast.error("Vui lòng tải lên CCCD mặt trước.");
          return false;
        }
        if (!this.gvFiles.cccd_mat_sau) {
          this.$toast.error("Vui lòng tải lên CCCD mặt sau.");
          return false;
        }
        if (!this.gvFiles.bang_cap) {
          this.$toast.error("Vui lòng tải lên bằng cấp / chứng chỉ.");
          return false;
        }
      }
      return true;
    },
    async xuLyDangKy() {
      if (!this.validForm()) return;

      const sdtSo = this.form.sdt.replace(/\D/g, "").slice(0, 10);
      const payload = {
        ho_ten: this.form.ho_ten,
        email: this.form.email,
        password: this.form.password,
        password_confirmation: this.form.password_confirmation,
      };
      if (this.form.ngay_sinh) payload.ngay_sinh = this.form.ngay_sinh;
      if (sdtSo) payload.sdt = sdtSo;

      this.dangGui = true;
      try {
        // Bước 1: Đăng ký tài khoản
        const res = await publicHttp.post('/dang-ky', payload);
        if (!res.data.status) {
          this.$toast.error(res.data.message || "Đăng ký thất bại.");
          return;
        }

        // Nếu là học sinh → redirect đăng nhập
        if (this.loaiTK === "hoc_sinh") {
          this.$toast.success(res.data.message);
          this.$router.push("/dang-nhap");
          return;
        }

        // Nếu là giáo viên → dùng token để nộp hồ sơ
        const token = res.data.data?.token;
        if (!token) {
          this.$toast.success(
            "Đăng ký thành công! Vui lòng đăng nhập rồi nộp hồ sơ giáo viên.",
          );
          this.$router.push("/dang-nhap");
          return;
        }

        const fd = new FormData();
        fd.append("ho_ten", this.form.ho_ten);
        fd.append("so_dien_thoai", sdtSo || "");
        fd.append("chuyen_mon", this.gvForm.chuyen_mon);
        fd.append("mo_ta", this.gvForm.mo_ta || "");
        for (const [key, file] of Object.entries(this.gvFiles)) {
          if (file) fd.append(key, file);
        }

        const resGv = await publicHttp.post('/homepage/ho-so-giao-vien', fd, {
          headers: {
            Authorization: "Bearer " + token,
            "Content-Type": "multipart/form-data",
          },
        });

        if (resGv.data.status) {
          this.$toast.success(
            "Đăng ký thành công! Hồ sơ giáo viên đã được gửi, vui lòng chờ Admin xét duyệt.",
          );
          this.buoc = 3;
        } else {
          this.$toast.warning(
            "Tài khoản đã tạo, nhưng nộp hồ sơ giáo viên thất bại: " +
              (resGv.data.message || ""),
          );
          this.$router.push("/dang-nhap");
        }
      } catch (err) {
        const errors = err.response?.data?.errors;
        if (errors) {
          Object.values(errors).forEach((msg) =>
            this.$toast.error(Array.isArray(msg) ? msg[0] : msg),
          );
        } else {
          this.$toast.error(
            err.response?.data?.message ||
              "Đăng ký thất bại. Vui lòng thử lại.",
          );
        }
      } finally {
        this.dangGui = false;
      }
    },
  },
};
</script>

<style scoped>
@keyframes float {
  0%,
  100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}
.register-page {
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}
.transition-all {
  transition: all 0.4s ease-in-out;
}

/* Styles cho Học sinh */
.hs-input.form-control {
  border-radius: 12px;
}
.input-group:focus-within {
  border-color: #ffb18f !important;
  background-color: #fff !important;
}
input[type="date"]::-webkit-calendar-picker-indicator {
  cursor: pointer;
  filter: invert(48%) sepia(89%) saturate(1831%) hue-rotate(345deg)
    brightness(101%) contrast(101%);
}

.chon-loai {
  transition: all 0.25s;
}
.chon-loai:hover,
.chon-loai.selected {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

/* Styles cho form Giáo viên (Mượn từ trang profile) */
.icon-circle-xl {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #ede9fe, #ddd6fe);
  display: flex;
  align-items: center;
  justify-content: center;
}
.gv-label {
  font-weight: 600;
  color: #334155;
  margin-bottom: 8px;
  font-size: 0.95rem;
}
.gv-input {
  border-radius: 12px;
  padding: 12px 18px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  color: #334155;
  transition: all 0.3s;
}
.gv-input:focus,
.input-group:focus-within .gv-input {
  background: #fff;
  border-color: #667eea;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
}
select.gv-select {
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 16px center;
  padding-right: 40px;
  cursor: pointer;
  height: auto;
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
.btn-submit-gv {
  background: linear-gradient(135deg, #667eea, #764ba2);
  border: none;
}
.btn-submit-gv:hover {
  background: linear-gradient(135deg, #5a6ed0, #6a4190);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(118, 75, 162, 0.4);
}
</style>
