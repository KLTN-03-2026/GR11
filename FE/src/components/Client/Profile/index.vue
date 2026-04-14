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
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="profile">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <div class="card inner-card border-0 shadow-sm h-100">
                                        <div class="card-body text-center p-4">
                                            <div class="avatar-wrapper mb-4">
                                                <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center">
                                                    <img v-if="avatarDisplayUrl" :src="avatarDisplayUrl" class="avatar-image" alt="avatar">
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
                                                >
                                                <button
                                                    type="button"
                                                    class="btn btn-light btn-sm"
                                                    @click="openAvatarPicker"
                                                    :disabled="isUpdatingAvatar"
                                                >
                                                    <span v-if="isUpdatingAvatar" class="spinner-border spinner-border-sm me-2"></span>
                                                    <i class="fa-solid fa-camera me-2" v-else></i>Đổi ảnh đại diện
                                                </button>
                                                <small class="text-muted d-block mt-2">Hỗ trợ JPG, PNG, WEBP (tối đa 2MB)</small>
                                            </div>
                                            <h3 class="mb-2 fw-bold text-dark">{{ thong_tin.ho_va_ten || 'Chưa có tên' }}</h3>
                                            <h6 class="mb-2 text-muted">{{ thong_tin.email }}</h6>
                                            <p class="mb-4">
                                                <span class="badge custom-badge">{{ thong_tin.chuc_vu?.ten_chuc_vu || 'Chưa có chức vụ' }}</span>
                                            </p>
                                            
                                            <hr class="text-muted opacity-25 mb-4">

                                            <div class="row text-start px-2">
                                                <div class="col-12 mb-3">
                                                    <div class="info-item d-flex align-items-center">
                                                        <div class="icon-box me-3">
                                                            <i class="fa-solid fa-phone text-primary"></i>
                                                        </div>
                                                        <span class="fw-medium text-secondary">{{ thong_tin.so_dien_thoai || 'Chưa có số điện thoại' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="card inner-card border-0 shadow-sm h-100">
                                        <div class="card-body p-4 p-md-5">
                                            <h4 class="card-title mb-2 fw-bold text-dark">
                                                <i class="fa-solid fa-user-pen me-2 text-primary"></i>Cập nhật thông tin
                                            </h4>
                                            <form @submit.prevent="updateProfile">
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" v-model="profileData.ho_va_ten"
                                                            placeholder="Nhập họ và tên" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Email</label>
                                                        <input :value="thong_tin.email" type="email"
                                                            class="form-control bg-light" disabled>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Số điện thoại</label>
                                                        <input type="tel" class="form-control" v-model="profileData.so_dien_thoai"
                                                            placeholder="Nhập số điện thoại">
                                                    </div>
                                                </div>
                                                <div class="text-end mt-5 pt-3 border-top">
                                                    <button type="button" @click="resetProfileForm" class="btn btn-light me-3 px-4">Huỷ</button>
                                                    <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="isUpdatingProfile">
                                                        <span v-if="isUpdatingProfile" class="spinner-border spinner-border-sm me-2"></span>
                                                        <i class="fa-solid fa-save me-2" v-else></i>Lưu thay đổi
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
                                        <div class="card-body ">
                                            <div class="text-center">
                                                <div class="icon-circle-large mx-auto">
                                                    <i class="fa-solid fa-shield-halved fa-2x text-primary"></i>
                                                </div>
                                                <h4 class="card-title fw-bold text-dark">Đổi mật khẩu bảo mật</h4>
                                                <p class="text-muted">Vui lòng nhập mật khẩu hiện tại và mật khẩu mới để thay đổi.</p>
                                            </div>
                                            
                                            <form @submit.prevent="changePassword">
                                                <div class="row g-4 px-md-4">
                                                    <div class="col-md-12">
                                                        <label class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                                        <div class="password-input-wrapper">
                                                            <input 
                                                                :type="showOldPassword ? 'text' : 'password'" 
                                                                class="form-control form-control-lg" 
                                                                v-model="passwordData.old_password"
                                                                placeholder="Nhập mật khẩu hiện tại" 
                                                                required>
                                                            <button 
                                                                type="button" 
                                                                class="password-toggle-btn"
                                                                @click="showOldPassword = !showOldPassword">
                                                                <i :class="showOldPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                                                        <div class="password-input-wrapper">
                                                            <input 
                                                                :type="showNewPassword ? 'text' : 'password'" 
                                                                class="form-control form-control-lg" 
                                                                v-model="passwordData.password"
                                                                placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)" 
                                                                required>
                                                            <button 
                                                                type="button" 
                                                                class="password-toggle-btn"
                                                                @click="showNewPassword = !showNewPassword">
                                                                <i :class="showNewPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                                        <div class="password-input-wrapper">
                                                            <input 
                                                                :type="showConfirmPassword ? 'text' : 'password'" 
                                                                class="form-control form-control-lg" 
                                                                v-model="passwordData.re_password"
                                                                placeholder="Nhập lại mật khẩu mới" 
                                                                required>
                                                            <button 
                                                                type="button" 
                                                                class="password-toggle-btn"
                                                                @click="showConfirmPassword = !showConfirmPassword">
                                                                <i :class="showConfirmPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-center mt-5 pt-4 border-top">
                                                    <button type="button" @click="resetPasswordForm" class="btn btn-light me-3 px-4">Huỷ thao tác</button>
                                                    <button type="submit" class="btn btn-primary px-5 shadow-sm" :disabled="isChangingPassword">
                                                        <span v-if="isChangingPassword" class="spinner-border spinner-border-sm me-2"></span>
                                                        <i class="fa-solid fa-key me-2" v-else></i>Cập nhật mật khẩu
                                                    </button>
                                                </div>
                                            </form>
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
</template>

<script>
import axios from 'axios';

export default {
    name: 'NhanVienProfile',
    computed: {
        avatarDisplayUrl() {
            return this.duongDanAnh(this.thong_tin.anh_dai_dien || '');
        }
    },
    data() {
        return {
            thong_tin: {},
            profileData: {
                ho_va_ten: '',
                so_dien_thoai: ''
            },
            passwordData: {
                old_password: '',
                password: '',
                re_password: ''
            },
            showOldPassword: false,
            showNewPassword: false,
            showConfirmPassword: false,
            isUpdatingProfile: false,
            isChangingPassword: false,
            isUpdatingAvatar: false
        }
    },
    mounted() {
        this.loadProfile();
    },
    methods: {
        duongDanAnh(raw) {
            if (!raw) return '';
            if (String(raw).startsWith('http://') || String(raw).startsWith('https://')) {
                return raw;
            }
            const base = (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, '');
            return `${base}/storage/${String(raw).replace(/^\//, '')}`;
        },
        getAuthToken() {
            return localStorage.getItem("token_admin")
                || localStorage.getItem("token_teacher")
                || localStorage.getItem("token_nguoi_dung")
                || "";
        },
        getRedirectLoginPath() {
            if (localStorage.getItem("token_admin")) return "/dang-nhap";
            if (localStorage.getItem("token_teacher")) return "/dang-nhap";
            return "/dang-nhap";
        },
        loadProfile() {
            axios
                .get("http://127.0.0.1:8000/api/profile", {
                    headers: {
                        Authorization: "Bearer " + this.getAuthToken()
                    }
                })
                .then((res) => {
                    if (res.data.status) {
                        this.thong_tin = res.data.thong_tin;
                        if (res.data.thong_tin?.anh_dai_dien) {
                            localStorage.setItem("anh_dai_dien", res.data.thong_tin.anh_dai_dien);
                        } else {
                            localStorage.removeItem("anh_dai_dien");
                        }
                        // Populate profile form
                        this.profileData = {
                            ho_va_ten: res.data.thong_tin.ho_va_ten || '',
                            so_dien_thoai: res.data.thong_tin.so_dien_thoai || ''
                        };
                    } else {
                        this.$toast.error(res.data.message || 'Không thể tải thông tin profile');
                    }
                })
                .catch((err) => {
                    if (err.response && err.response.status === 401) {
                        this.$toast.error("Vui lòng đăng nhập lại!");
                        localStorage.removeItem("token_admin");
                        localStorage.removeItem("token_teacher");
                        localStorage.removeItem("token_nguoi_dung");
                        this.$router.push(this.getRedirectLoginPath());
                    } else {
                        this.$toast.error(err.response?.data?.message || 'Đã xảy ra lỗi khi tải thông tin');
                    }
                });
        },
        updateProfile() {
            this.isUpdatingProfile = true;
            axios
                .post("http://127.0.0.1:8000/api/profile/update", this.profileData, {
                    headers: {
                        Authorization: "Bearer " + this.getAuthToken()
                    }
                })
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        // Update thong_tin với dữ liệu mới
                        if (res.data.thong_tin) {
                            this.thong_tin = res.data.thong_tin;
                            // Update localStorage nếu có ho_va_ten
                            if (res.data.thong_tin.ho_va_ten) {
                                localStorage.setItem("ho_ten", res.data.thong_tin.ho_va_ten);
                            }
                            if (res.data.thong_tin.anh_dai_dien) {
                                localStorage.setItem("anh_dai_dien", res.data.thong_tin.anh_dai_dien);
                            } else {
                                localStorage.removeItem("anh_dai_dien");
                            }
                        }
                        // Reload profile để đảm bảo đồng bộ
                        this.loadProfile();
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch((err) => {
                    if (err.response && err.response.data) {
                        if (err.response.data.errors) {
                            const errors = Object.values(err.response.data.errors);
                            errors.forEach(errorList => {
                                if (Array.isArray(errorList)) {
                                    errorList.forEach(error => this.$toast.error(error));
                                }
                            });
                        } else {
                            this.$toast.error(err.response.data.message || 'Có lỗi xảy ra khi cập nhật thông tin');
                        }
                    } else {
                        this.$toast.error('Có lỗi xảy ra khi cập nhật thông tin');
                    }
                })
                .finally(() => {
                    this.isUpdatingProfile = false;
                });
        },
        resetProfileForm() {
            this.profileData = {
                ho_va_ten: this.thong_tin.ho_va_ten || '',
                so_dien_thoai: this.thong_tin.so_dien_thoai || ''
            };
        },
        openAvatarPicker() {
            if (this.$refs.avatarInput) {
                this.$refs.avatarInput.value = '';
                this.$refs.avatarInput.click();
            }
        },
        handleAvatarChange(event) {
            const file = event.target.files?.[0];
            if (!file) {
                return;
            }

            if (!file.type || !file.type.startsWith('image/')) {
                this.$toast.error('Vui lòng chọn file ảnh hợp lệ.');
                return;
            }

            const maxSize = 2 * 1024 * 1024;
            if (file.size > maxSize) {
                this.$toast.error('Ảnh đại diện không được vượt quá 2MB.');
                return;
            }

            this.uploadAvatar(file);
        },
        uploadAvatar(file) {
            this.isUpdatingAvatar = true;
            const formData = new FormData();
            formData.append('anh_dai_dien', file);

            axios
                .post("http://127.0.0.1:8000/api/profile/update-avatar", formData, {
                    headers: {
                        Authorization: "Bearer " + this.getAuthToken(),
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message || 'Cập nhật ảnh đại diện thành công.');
                        if (res.data.thong_tin) {
                            this.thong_tin = res.data.thong_tin;
                            if (res.data.thong_tin.anh_dai_dien) {
                                localStorage.setItem("anh_dai_dien", res.data.thong_tin.anh_dai_dien);
                            } else {
                                localStorage.removeItem("anh_dai_dien");
                            }
                        }
                    } else {
                        this.$toast.error(res.data.message || 'Không thể cập nhật ảnh đại diện.');
                    }
                })
                .catch((err) => {
                    if (err.response && err.response.data) {
                        if (err.response.data.errors) {
                            const errors = Object.values(err.response.data.errors);
                            errors.forEach(errorList => {
                                if (Array.isArray(errorList)) {
                                    errorList.forEach(error => this.$toast.error(error));
                                }
                            });
                        } else {
                            this.$toast.error(err.response.data.message || 'Có lỗi xảy ra khi cập nhật ảnh đại diện');
                        }
                    } else {
                        this.$toast.error('Có lỗi xảy ra khi cập nhật ảnh đại diện');
                    }
                })
                .finally(() => {
                    this.isUpdatingAvatar = false;
                    if (this.$refs.avatarInput) {
                        this.$refs.avatarInput.value = '';
                    }
                });
        },
        changePassword() {
            this.isChangingPassword = true;
            axios
                .post("http://127.0.0.1:8000/api/profile/change-password", this.passwordData, {
                    headers: {
                        Authorization: "Bearer " + this.getAuthToken()
                    }
                })
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
                            errors.forEach(errorList => {
                                if (Array.isArray(errorList)) {
                                    errorList.forEach(error => this.$toast.error(error));
                                }
                            });
                        } else {
                            this.$toast.error(err.response.data.message || 'Có lỗi xảy ra khi đổi mật khẩu');
                        }
                    } else {
                        this.$toast.error('Có lỗi xảy ra khi đổi mật khẩu');
                    }
                })
                .finally(() => {
                    this.isChangingPassword = false;
                });
        },
        resetPasswordForm() {
            this.passwordData = {
                old_password: '',
                password: '',
                re_password: ''
            };
            this.showOldPassword = false;
            this.showNewPassword = false;
            this.showConfirmPassword = false;
        }
    }
}
</script>

<style scoped>
/* Base styling & Typography */
.text-primary { color: #667eea !important; }
.bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }

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
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.inner-card:hover {
    box-shadow: 0 15px 35px rgba(0,0,0,0.06) !important;
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
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6ed0 0%, #6a4190 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(118, 75, 162, 0.4) !important;
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
</style>