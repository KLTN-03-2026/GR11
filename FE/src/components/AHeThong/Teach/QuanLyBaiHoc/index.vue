<template>
    <div class="container-fluid " style="background-color: #f8f9fa; min-height: 100vh;">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-primary" style="color: #2b3445;"> <i
                        class="fa-solid fa-chalkboard-user"></i> Quản Lý Bài Học</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                    Tổ chức danh mục và nội dung luyện phát âm
                </p>
            </div>
        </div>

        <div class="row g-4">

            <div class="col-lg-4 col-md-5">
                <div class="card border-0 shadow-sm rounded-3 h-100" style="background: #fff;">
                    <div
                        class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">
                            <i class="fa-solid fa-folder-tree text-primary me-2"></i> Danh mục
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <input v-model.trim="bo_loc_danh_muc.tim_kiem" type="text"
                                class="form-control form-control-sm" placeholder="Tìm danh mục..."
                                @input="locDanhMuc" />
                            <div class="form-check mt-2 mb-0">
                                <input id="chiCoDanhMucCoBai" class="form-check-input" type="checkbox"
                                    v-model="bo_loc_danh_muc.chi_co_bai" @change="locDanhMuc">
                                <label class="form-check-label small text-muted" for="chiCoDanhMucCoBai">
                                    Chỉ hiển thị danh mục có bài học
                                </label>
                            </div>
                        </div>
                        <div v-if="loadingDanhMuc" class="text-center py-4 text-muted">
                            <span class="spinner-border spinner-border-sm me-2"></span>Đang tải danh mục...
                        </div>
                        <div v-else-if="danh_muc_hien_thi.length === 0" class="text-center py-4 text-muted">
                            Không có danh mục phù hợp bộ lọc.
                        </div>
                        <div v-else class="list-group list-group-flush gap-2">
                            <button v-for="dm in danh_muc_hien_thi" :key="dm.id"
                                class="list-group-item list-group-item-action border-0 rounded-3 d-flex justify-content-between align-items-center p-3 transition-all"
                                :class="{
                                    'bg-primary text-white shadow-sm': danh_muc_dang_chon === dm.id,
                                    'bg-light text-dark': danh_muc_dang_chon !== dm.id
                                }" @click="chonDanhMuc(dm.id)">
                                <div>
                                    <i :class="dm.icon + ' me-3 fs-5'"
                                        :style="danh_muc_dang_chon === dm.id ? 'color: #fff;' : 'color: #6c757d;'"></i>
                                    <span class="fw-medium">{{ dm.ten }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge rounded-pill"
                                        :class="danh_muc_dang_chon === dm.id ? 'bg-white text-primary' : 'bg-secondary'">
                                        {{ demSoBaiHoc(dm) }}
                                    </span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-md-7">
                <div class="card border-0 shadow-sm rounded-3 h-100" style="background: #fff;">
                    <div
                        class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">
                            <i class="fa-solid fa-book-open text-success me-2"></i>
                            Danh Mục
                            <span v-if="tenDanhMucDangChon" class="text-muted fw-normal" style="font-size: 0.9rem;">
                                - {{ tenDanhMucDangChon }}
                            </span>
                        </h6>
                        <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#baiHocModal" @click="themBaiHoc">
                            <i class="fa-solid fa-plus me-1"></i> Bài học mới
                        </button>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        <div v-if="loadingBaiHoc" class="text-center py-5 my-4 text-muted">
                            <div class="spinner-border text-primary mb-3" role="status"
                                style="width: 3rem; height: 3rem;"></div>
                            <h6 class="fw-semibold">Đang tải dữ liệu bài học...</h6>
                        </div>

                        <div v-else-if="!danh_muc_dang_chon && danh_muc.length > 0" class="text-center py-5 my-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3"
                                style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-arrow-pointer fs-2 text-secondary opacity-50"></i>
                            </div>
                            <h6 class="text-muted fw-medium">Vui lòng chọn một danh mục bên trái để xem.</h6>
                        </div>

                        <div v-else-if="bai_hoc_hien_thi.length === 0" class="text-center py-5 my-4 border rounded-4"
                            style="border-style: dashed !important; background-color: #f8fafc; border-color: #cbd5e1 !important;">
                            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm mb-3"
                                style="width: 80px; height: 80px;">
                                <i class="fa-regular fa-folder-open fs-2" style="color: #667eea;"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Chưa có bài học nào</h5>
                            <p class="text-muted mb-4 small">Danh mục này hiện đang trống. Hãy tạo nội dung đầu tiên
                                nhé!</p>
                            <button class="btn text-white px-4 py-2 shadow-sm rounded-pill fw-medium border-0"
                                data-bs-toggle="modal" data-bs-target="#baiHocModal" @click="themBaiHoc"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fa-solid fa-plus me-2"></i>Tạo bài học đầu tiên
                            </button>
                        </div>

                        <div class="row g-4" v-else>
                            <div class="col-xl-6 col-lg-6" v-for="bai in bai_hoc_hien_thi" :key="bai.id">

                                <div class="card h-100 border-0 shadow-sm rounded-4"
                                    style="cursor: pointer; transition: all 0.3s ease; border: 1px solid transparent !important;"
                                    onmouseover="this.style.transform='translateY(-5px)'; this.classList.replace('shadow-sm', 'shadow'); this.style.borderColor='#e2e8f0';"
                                    onmouseout="this.style.transform='translateY(0)'; this.classList.replace('shadow', 'shadow-sm'); this.style.borderColor='transparent';"
                                    @click="quanLyTuVung(bai)">

                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start gap-3">

                                            <div class="rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                                                :style="`background-color: ${bai.mau_nen || '#f0fdf4'}; width: 64px; height: 64px;`">
                                                <i :class="[bai.icon || 'fa-solid fa-book', 'fs-3']"
                                                    :style="`color: ${bai.mau_icon || '#16a34a'};`"></i>
                                            </div>

                                            <div class="flex-grow-1" style="min-width: 0;">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <h6 class="fw-bold text-truncate mb-0 pe-2 fs-6 text-dark"
                                                        :title="bai.tieu_de" style="line-height: 1.4;">
                                                        {{ bai.tieu_de }}
                                                    </h6>

                                                    <div class="dropdown flex-shrink-0">
                                                        <button
                                                            class="btn text-muted p-0 border-0 bg-transparent d-flex align-items-center justify-content-center"
                                                            type="button" data-bs-toggle="dropdown" @click.stop
                                                            style="width: 30px; height: 30px; border-radius: 50%;"
                                                            onmouseover="this.style.backgroundColor='#f1f5f9'"
                                                            onmouseout="this.style.backgroundColor='transparent'">
                                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                                        </button>
                                                        <ul
                                                            class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-1 py-2">
                                                            <li><a class="dropdown-item py-2 fw-medium" href="#"
                                                                    data-bs-toggle="modal" data-bs-target="#baiHocModal"
                                                                    @click.stop.prevent="suaBaiHoc(bai)"><i
                                                                        class="fa-regular fa-pen-to-square me-2 text-center"
                                                                        style="color: #667eea; width: 20px;"></i> Sửa
                                                                    bài học</a></li>
                                                            <li><a class="dropdown-item py-2 fw-medium" href="#"
                                                                    @click.stop.prevent="quanLyTuVung(bai)"><i
                                                                        class="fa-solid fa-list-ul me-2 text-success text-center"
                                                                        style="width: 20px;"></i> Quản lý từ vựng</a>
                                                            </li>
                                                            <li>
                                                                <hr class="dropdown-divider opacity-25 my-1">
                                                            </li>
                                                            <li><a class="dropdown-item py-2 fw-medium text-danger"
                                                                    href="#" data-bs-toggle="modal"
                                                                    data-bs-target="#xoaModal"
                                                                    @click.stop.prevent="xoaDoiTuong(bai, 'bai_hoc')"><i
                                                                        class="fa-regular fa-trash-can me-2 text-center"
                                                                        style="width: 20px;"></i> Xóa bài học</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <p class="text-secondary small mb-3 text-truncate" :title="bai.mo_ta"
                                                    style="opacity: 0.85;">
                                                    {{ bai.mo_ta || 'Chưa có mô tả cho bài học này' }}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                                    <span
                                                        class="badge rounded-pill bg-light text-secondary me-1 border px-2 py-1 fw-medium"
                                                        style="font-size: 0.8rem;">
                                                        <i class="fa-solid fa-layer-group me-1 opacity-75"></i> Cấp độ:
                                                        {{ formatCapDo(bai.cap_do) }}
                                                    </span>
                                                    <span
                                                        class="badge rounded-pill px-2 py-1 fw-medium d-flex align-items-center gap-1 border"
                                                        :style="bai.trang_thai == 0 ? 'font-size: 0.8rem; background-color: #dcfce7; color: #166534; border-color: #bbf7d0 !important;' : (bai.trang_thai == 2 ? 'font-size: 0.8rem; background-color: #fee2e2; color: #991b1b; border-color: #fecaca !important;' : 'font-size: 0.8rem; background-color: #f1f5f9; color: #475569; border-color: #e2e8f0 !important;')">
                                                        <i class="fa-solid"
                                                            :class="bai.trang_thai == 0 ? 'fa-circle-check' : (bai.trang_thai == 2 ? 'fa-circle-xmark' : 'fa-clock')"></i>
                                                        {{ bai.trang_thai == 0 ? 'Đã duyệt' : (bai.trang_thai == 2 ? 'Từ chối' : 'Đợi duyệt') }}
                                                    </span>
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

        <div class="modal fade" id="baiHocModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title fw-bold">
                            <i class="fa-solid fa-book-open text-primary me-2"></i>
                            {{ isEditBaiHoc ? 'Sửa Bài Học' : 'Thêm Bài Học Mới' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">

                        <div class="mb-3">
                            <label class="form-label fw-medium">Thuộc danh mục <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" v-model="formBaiHoc.danh_muc_id">
                                <option v-for="dm in danh_muc" :key="dm.id" :value="dm.id">
                                    {{ dm.ten }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Tiêu đề bài học <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="formBaiHoc.tieu_de"
                                placeholder="VD: Phân biệt Âm L và N" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Mô tả nội dung</label>
                            <textarea class="form-control" v-model="formBaiHoc.mo_ta" rows="2"
                                placeholder="Ghi chú ngắn về bài học..."></textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-medium">Cấp độ</label>
                                <select class="form-select" v-model="formBaiHoc.cap_do">
                                    <option value="de">Dễ (Mầm non)</option>
                                    <option value="trung_binh">Trung bình (Lớp 1-2)</option>
                                    <option value="kho">Khó (Nâng cao)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn btn-primary" :disabled="savingBaiHoc" @click="luuBaiHoc">
                            <span v-if="savingBaiHoc" class="spinner-border spinner-border-sm me-2"></span>Xác Nhận
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="danhMucModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title fw-bold">
                            <i class="fa-solid fa-folder-tree text-primary me-2"></i>
                            {{ isEditDanhMuc ? 'Sửa Danh Mục' : 'Thêm Danh Mục Mới' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="formDanhMuc.ten"
                                placeholder="VD: Luyện âm cơ bản" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Icon minh họa (Class FontAwesome)</label>
                            <input type="text" class="form-control" v-model="formDanhMuc.icon"
                                placeholder="VD: fa-solid fa-lips">
                            <div class="mt-2 text-muted small">
                                <i :class="formDanhMuc.icon || 'fa-solid fa-folder'"></i> Xem trước Icon
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn btn-primary" :disabled="savingDanhMuc" @click="luuDanhMuc">
                            <span v-if="savingDanhMuc" class="spinner-border spinner-border-sm me-2"></span>Lưu dữ liệu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="xoaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body text-center p-4">
                        <div class="text-danger mb-3">
                            <i class="fa-regular fa-circle-xmark" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Xác nhận xóa?</h5>
                        <p class="text-muted mb-4">
                            Bạn có chắc chắn muốn xóa
                            <strong class="text-danger">{{ doiTuongXoa?.ten || doiTuongXoa?.tieu_de }}</strong>
                            không? Dữ liệu này sẽ không thể khôi phục.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy bỏ</button>
                            <button type="button" class="btn btn-danger" :disabled="deleting" @click="xacNhanXoa">
                                <span v-if="deleting" class="spinner-border spinner-border-sm me-2"></span>Vâng, Xóa
                                ngay
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { API_BASE } from '../../../../api/http.js';
import axios from 'axios';

export default {
    name: 'TeachQuanLyBaiHoc',
    data() {
        return {
            apiBase: (API_BASE).replace(/\/$/, ''),
            danh_muc_dang_chon: null,
            danh_muc: [],
            bai_hoc: [],
            loadingDanhMuc: false,
            loadingBaiHoc: false,
            savingDanhMuc: false,
            savingBaiHoc: false,
            deleting: false,
            isEditBaiHoc: false,
            formBaiHoc: {
                id: null,
                danh_muc_id: null,
                tieu_de: '',
                mo_ta: '',
                cap_do: 'de',
            },
            isEditDanhMuc: false,
            formDanhMuc: { id: null, ten: '', icon: 'fa-solid fa-folder' },
            doiTuongXoa: null,
            loaiXoa: '',
            bo_loc_danh_muc: {
                tim_kiem: '',
                chi_co_bai: false,
            },
        };
    },
    computed: {
        danh_muc_hien_thi() {
            let list = [...this.danh_muc];
            const kw = (this.bo_loc_danh_muc.tim_kiem || '').toLowerCase();
            if (kw) {
                list = list.filter((dm) => (dm.ten || '').toLowerCase().includes(kw));
            }
            if (this.bo_loc_danh_muc.chi_co_bai) {
                list = list.filter((dm) => this.demSoBaiHoc(dm) > 0);
            }
            return list;
        },
        bai_hoc_hien_thi() {
            if (!this.danh_muc_dang_chon) return [];
            return this.bai_hoc.filter((bai) => bai.danh_muc_id === this.danh_muc_dang_chon);
        },
        tenDanhMucDangChon() {
            const dm = this.danh_muc.find((d) => d.id === this.danh_muc_dang_chon);
            return dm ? dm.ten : '';
        },
    },
    mounted() {
        this.taiDanhMuc();
        window.addEventListener('bai-hoc-duoc-duyet', this.handleBaiHocDuyet);
    },
    beforeUnmount() {
        window.removeEventListener('bai-hoc-duoc-duyet', this.handleBaiHocDuyet);
    },
    methods: {
        handleBaiHocDuyet(e) {
            const data = e.detail;
            const baiHocIndex = this.bai_hoc.findIndex(b => b.id === data.bai_hoc_id);
            if (baiHocIndex !== -1) {
                this.bai_hoc[baiHocIndex].trang_thai = data.trang_thai;
            }
        },
        getAuthToken() {
            return localStorage.getItem('token_teacher') || '';
        },
        authHeaders() {
            return { Authorization: 'Bearer ' + this.getAuthToken() };
        },
        dongModalTheoId(modalId) {
            const el = document.getElementById(modalId);
            if (!el || typeof window.bootstrap === 'undefined') return;
            const inst = window.bootstrap.Modal.getInstance(el) || new window.bootstrap.Modal(el);
            inst.hide();
        },
        toastLoiAxios(err) {
            if (err.response && err.response.data) {
                if (err.response.data.errors) {
                    Object.values(err.response.data.errors).forEach((errorList) => {
                        if (Array.isArray(errorList)) {
                            errorList.forEach((msg) => this.$toast.error(msg));
                        }
                    });
                    return;
                }
                this.$toast.error(err.response.data.message || 'Đã xảy ra lỗi.');
                return;
            }
            this.$toast.error('Không thể kết nối máy chủ.');
        },
        giaoDienBaiHocTheoCapDo(capDo) {
            const map = {
                de: { icon: 'fa-solid fa-seedling', mau_nen: '#e7f0ff', mau_icon: '#0d6efd' },
                trung_binh: { icon: 'fa-solid fa-layer-group', mau_nen: '#e6f8f0', mau_icon: '#198754' },
                kho: { icon: 'fa-solid fa-mountain', mau_nen: '#fff4e5', mau_icon: '#ffc107' },
            };
            return map[capDo] || map.de;
        },
        mapBaiHocTuApi(item) {
            return {
                ...item,
                ...this.giaoDienBaiHocTheoCapDo(item.cap_do),
            };
        },
        taiDanhMuc(preferredDanhMucId = null) {
            this.loadingDanhMuc = true;
            const params = {
                tim_kiem: this.bo_loc_danh_muc.tim_kiem || undefined,
                chi_co_bai: this.bo_loc_danh_muc.chi_co_bai ? 1 : undefined,
            };
            axios
                .get(this.apiBase + '/api/teacher/danh-muc-bai-hoc', { headers: this.authHeaders(), params })
                .then((res) => {
                    if (res.data.status) {
                        this.danh_muc = res.data.data || [];
                        if (this.danh_muc.length === 0) {
                            this.danh_muc_dang_chon = null;
                            this.bai_hoc = [];
                            return;
                        }
                        if (
                            preferredDanhMucId != null &&
                            this.danh_muc.some((d) => d.id === preferredDanhMucId)
                        ) {
                            this.danh_muc_dang_chon = preferredDanhMucId;
                        } else {
                            const vanTonTai = this.danh_muc.some((d) => d.id === this.danh_muc_dang_chon);
                            if (!this.danh_muc_dang_chon || !vanTonTai) {
                                this.danh_muc_dang_chon = this.danh_muc[0].id;
                            }
                        }
                        this.taiBaiHoc(this.danh_muc_dang_chon);
                    } else {
                        this.$toast.error(res.data.message || 'Không tải được danh mục.');
                    }
                })
                .catch((err) => {
                    if (err.response && err.response.status === 401) {
                        this.$toast.error('Vui lòng đăng nhập lại.');
                        this.$router.push('/dang-nhap');
                        return;
                    }
                    if (err.response && err.response.status === 403) {
                        this.$toast.error('Bạn không có quyền truy cập.');
                        return;
                    }
                    this.toastLoiAxios(err);
                })
                .finally(() => {
                    this.loadingDanhMuc = false;
                });
        },
        taiBaiHoc(danhMucId) {
            if (!danhMucId) {
                this.bai_hoc = [];
                return;
            }
            this.loadingBaiHoc = true;
            axios
                .get(this.apiBase + '/api/teacher/danh-muc-bai-hoc/' + danhMucId + '/bai-hoc', {
                    headers: this.authHeaders(),
                })
                .then((res) => {
                    if (res.data.status) {
                        this.bai_hoc = (res.data.data || []).map((row) => this.mapBaiHocTuApi(row));
                    } else {
                        this.$toast.error(res.data.message || 'Không tải được bài học.');
                    }
                })
                .catch((err) => {
                    if (err.response && err.response.status === 401) {
                        this.$toast.error('Vui lòng đăng nhập lại.');
                        this.$router.push('/dang-nhap');
                        return;
                    }
                    this.toastLoiAxios(err);
                })
                .finally(() => {
                    this.loadingBaiHoc = false;
                });
        },
        chonDanhMuc(id) {
            this.danh_muc_dang_chon = id;
            this.taiBaiHoc(id);
        },
        locDanhMuc() {
            const danhMucDangChonConHien = this.danh_muc_hien_thi.some((d) => d.id === this.danh_muc_dang_chon);
            if (!danhMucDangChonConHien) {
                this.danh_muc_dang_chon = this.danh_muc_hien_thi[0]?.id || null;
                this.taiBaiHoc(this.danh_muc_dang_chon);
            }
            this.taiDanhMuc(this.danh_muc_dang_chon);
        },
        demSoBaiHoc(dm) {
            if (dm && typeof dm.so_bai === 'number') return dm.so_bai;
            return this.bai_hoc.filter((bai) => bai.danh_muc_id === dm.id).length;
        },
        formatCapDo(capDo) {
            const map = { de: 'Dễ', trung_binh: 'TB', kho: 'Khó' };
            return map[capDo] || 'Dễ';
        },
        suaDanhMuc(dm) {
            this.isEditDanhMuc = true;
            this.formDanhMuc = { id: dm.id, ten: dm.ten || dm.ten_danh_muc || '', icon: dm.icon || 'fa-solid fa-folder' };
        },
        luuDanhMuc() {
            const ten = (this.formDanhMuc.ten || '').trim();
            if (!ten) {
                this.$toast.error('Vui lòng nhập tên danh mục.');
                return;
            }
            this.savingDanhMuc = true;
            const payload = {
                ten_danh_muc: ten,
                icon: (this.formDanhMuc.icon || '').trim() || 'fa-solid fa-folder',
            };
            const req = this.isEditDanhMuc
                ? axios.put(this.apiBase + '/api/teacher/danh-muc-bai-hoc/' + this.formDanhMuc.id, payload, {
                    headers: this.authHeaders(),
                })
                : axios.post(this.apiBase + '/api/teacher/danh-muc-bai-hoc', payload, { headers: this.authHeaders() });
            req
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message || 'Lưu danh mục thành công.');
                        this.dongModalTheoId('danhMucModal');
                        const moiId = !this.isEditDanhMuc && res.data.data && res.data.data.id ? res.data.data.id : null;
                        this.taiDanhMuc(moiId);
                    } else {
                        this.$toast.error(res.data.message || 'Không lưu được danh mục.');
                    }
                })
                .catch((err) => {
                    this.toastLoiAxios(err);
                })
                .finally(() => {
                    this.savingDanhMuc = false;
                });
        },
        themBaiHoc() {
            this.isEditBaiHoc = false;
            this.formBaiHoc = {
                id: null,
                danh_muc_id: this.danh_muc_dang_chon || (this.danh_muc[0] && this.danh_muc[0].id) || null,
                tieu_de: '',
                mo_ta: '',
                cap_do: 'de',
            };
        },
        suaBaiHoc(bai) {
            this.isEditBaiHoc = true;
            this.formBaiHoc = {
                id: bai.id,
                danh_muc_id: bai.danh_muc_id,
                tieu_de: bai.tieu_de || '',
                mo_ta: bai.mo_ta || '',
                cap_do: bai.cap_do || 'de',
            };
        },
        luuBaiHoc() {
            const tieuDe = (this.formBaiHoc.tieu_de || '').trim();
            if (!tieuDe) {
                this.$toast.error('Vui lòng nhập tiêu đề bài học.');
                return;
            }
            if (!this.formBaiHoc.danh_muc_id) {
                this.$toast.error('Vui lòng chọn danh mục.');
                return;
            }
            const payload = {
                danh_muc_id: this.formBaiHoc.danh_muc_id,
                tieu_de: tieuDe,
                mo_ta: (this.formBaiHoc.mo_ta || '').trim() || null,
                cap_do: this.formBaiHoc.cap_do,
                trang_thai: 0,
            };
            this.savingBaiHoc = true;
            const req = this.isEditBaiHoc
                ? axios.put(this.apiBase + '/api/teacher/bai-hoc/' + this.formBaiHoc.id, payload, {
                    headers: this.authHeaders(),
                })
                : axios.post(this.apiBase + '/api/teacher/bai-hoc', payload, { headers: this.authHeaders() });
            req
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message || 'Lưu bài học thành công.');
                        this.dongModalTheoId('baiHocModal');
                        const sid = this.formBaiHoc.danh_muc_id;
                        this.danh_muc_dang_chon = sid;
                        this.taiDanhMuc(sid);
                    } else {
                        this.$toast.error(res.data.message || 'Không lưu được bài học.');
                    }
                })
                .catch((err) => {
                    this.toastLoiAxios(err);
                })
                .finally(() => {
                    this.savingBaiHoc = false;
                });
        },
        quanLyTuVung(bai) {
            this.$router.push({ path: '/teacher/tu-vung', query: { bai_hoc_id: String(bai.id) } });
        },
        xoaDoiTuong(doiTuong, loai) {
            this.doiTuongXoa = doiTuong;
            this.loaiXoa = loai;
        },
        xacNhanXoa() {
            if (!this.doiTuongXoa || !this.loaiXoa) return;
            this.deleting = true;
            if (this.loaiXoa === 'bai_hoc') {
                axios
                    .delete(this.apiBase + '/api/teacher/bai-hoc/' + this.doiTuongXoa.id, {
                        headers: this.authHeaders(),
                    })
                    .then((res) => {
                        if (res.data.status) {
                            this.$toast.success(res.data.message || 'Đã xóa bài học.');
                            this.dongModalTheoId('xoaModal');
                            this.taiDanhMuc();
                        } else {
                            this.$toast.error(res.data.message || 'Không xóa được.');
                        }
                    })
                    .catch((err) => {
                        this.toastLoiAxios(err);
                    })
                    .finally(() => {
                        this.deleting = false;
                        this.doiTuongXoa = null;
                        this.loaiXoa = '';
                    });
                return;
            }
            if (this.loaiXoa === 'danh_muc') {
                const idDaXoa = this.doiTuongXoa.id;
                axios
                    .delete(this.apiBase + '/api/teacher/danh-muc-bai-hoc/' + idDaXoa, {
                        headers: this.authHeaders(),
                    })
                    .then((res) => {
                        if (res.data.status) {
                            this.$toast.success(res.data.message || 'Đã xóa danh mục.');
                            this.dongModalTheoId('xoaModal');
                            if (this.danh_muc_dang_chon === idDaXoa) {
                                this.danh_muc_dang_chon = null;
                            }
                            this.taiDanhMuc();
                        } else {
                            this.$toast.error(res.data.message || 'Không xóa được.');
                        }
                    })
                    .catch((err) => {
                        this.toastLoiAxios(err);
                    })
                    .finally(() => {
                        this.deleting = false;
                        this.doiTuongXoa = null;
                        this.loaiXoa = '';
                    });
            }
        },
    },
};
</script>

<style scoped>
.transition-all {
    transition: all 0.2s ease-in-out;
}

.hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .1) !important;
}

.list-group {
    max-height: 600px;
    overflow-y: auto;
}

.list-group::-webkit-scrollbar {
    width: 5px;
}

.list-group::-webkit-scrollbar-thumb {
    background-color: #dee2e6;
    border-radius: 10px;
}
</style>