<template>
    <div class="container-fluid" style="background-color: #f8f9fa; min-height: 100vh;">

        <div class="d-flex justify-content-between align-items-center mb-4 text-start">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Kiểm duyệt Nội dung & Bài học</h4>
                <p class="text-muted mb-0">Quản lý cấu trúc chủ đề, bài học và phê duyệt học liệu</p>
            </div>
        </div>

        <ul class="nav nav-tabs custom-tabs mb-4 border-bottom-0" id="adminContentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-4 py-3 border-0" id="categories-tab" data-bs-toggle="tab"
                    data-bs-target="#categories-pane" type="button" role="tab" aria-controls="categories-pane"
                    aria-selected="true">
                    <i class="fa-solid fa-folder-tree me-2"></i> Quản lý Danh mục
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold px-4 py-3 border-0" id="approvals-tab" data-bs-toggle="tab"
                    data-bs-target="#approvals-pane" type="button" role="tab" aria-controls="approvals-pane"
                    aria-selected="false">
                    <i class="fa-solid fa-clipboard-check me-2"></i> Kiểm duyệt Bài học
                    <span v-if="dem_cho_duyet > 0" class="badge bg-danger rounded-pill ms-2">{{ dem_cho_duyet }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="adminContentTabsContent">

            <div class="tab-pane fade show active" id="categories-pane" role="tabpanel"
                aria-labelledby="categories-tab">
                <div class="card border-0 shadow-sm rounded-3">
                    <div
                        class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-start">Danh sách Danh mục bài học</h6>
                        <button class="btn btn-primary btn-sm rounded-3 px-3 shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#categoryModal" @click="chuanBiThemDanhMuc">
                            <i class="fa-solid fa-plus me-1"></i> Thêm Danh mục
                        </button>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-start">
                                <thead class="table-light">
                                    <tr>
                                        <!-- Thêm align-middle và text-center để căn giữa dọc và ngang -->
                                        <th class="text-center align-middle py-3">Thứ tự</th>
                                        <th class="ps-4 py-3">Tên danh mục</th>
                                        <th class="py-3">Slug danh mục</th>
                                        <th class="py-3">Trạng thái</th>
                                        <th class="pe-4 text-center py-3">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="dm in danh_sach_danh_muc" :key="dm.id">
                                        <!-- Căn giữa cột thứ tự dọc và ngang -->
                                        <td class="text-center align-middle">
                                            <span class="fw-bold">{{ dm.thu_tu || 0 }}</span>
                                        </td>
                                        
                                        <td class="ps-4 align-middle">
                                            <div class="fw-bold text-dark" style="font-size: 1rem;">
                                                {{ dm.ten_danh_muc }}
                                            </div>
                                            <!-- Đã xóa phần mô tả danh mục (dm.mo_ta) theo yêu cầu -->
                                        </td>
                                        
                                        <td class="align-middle">
                                            <span class="badge bg-light text-dark border">{{ dm.slug_danh_muc || '-' }}</span>
                                        </td>
                                        
                                        <!-- Button trạng thái có thể thay đổi khi click -->
                                        <td class="align-middle">
                                            <span style="font-size: 15px; cursor: pointer;" v-if="dm.trang_thai === 1"
                                                class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2"
                                                @click="daoTrangThaiDanhMuc(dm)" title="Nhấn để chuyển sang Tạm ẩn">Hiển
                                                thị</span>
                                            <span style="font-size: 15px; cursor: pointer;" v-else
                                                class="badge bg-danger-subtle text-warning border border-danger-subtle px-3 py-2"
                                                @click="daoTrangThaiDanhMuc(dm)" title="Nhấn để chuyển sang Hiển thị">Tạm
                                                ẩn</span>
                                        </td>
                                        
                                        <!-- Các hành động nằm song song 1 hàng -->
                                        <td class="pe-4 text-center align-middle">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <!-- Icon con mắt xem chi tiết -->
                                                <button class="btn btn-sm btn-light border text-info"
                                                    data-bs-toggle="modal" data-bs-target="#lessonsListModal"
                                                    @click="xemBaiHocTheoDanhMuc(dm)" title="Xem chi tiết danh mục">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                                
                                                <button class="btn btn-sm btn-light border text-primary"
                                                    data-bs-toggle="modal" data-bs-target="#categoryModal"
                                                    @click="chuanBiSuaDanhMuc(dm)" title="Sửa danh mục">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                                
                                                <button class="btn btn-sm btn-light border text-danger"
                                                    data-bs-toggle="modal" data-bs-target="#openDeleteModal"
                                                    @click="danh_muc_can_xoa = dm" title="Xóa danh mục">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="danh_sach_danh_muc.length === 0">
                                        <td colspan="5" class="text-center py-5 text-muted">Chưa có danh mục nào trong
                                            hệ thống.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="approvals-pane" role="tabpanel" aria-labelledby="approvals-tab">
                <div class="card border-0 shadow-sm mb-4 rounded-3 text-start">
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control shadow-none" placeholder="Tìm tên bài học..."
                                    v-model="bo_loc.tu_khoa">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select shadow-none" v-model="bo_loc.trang_thai">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="Chờ duyệt">⏳ Chờ duyệt (Cần xử lý)</option>
                                    <option value="Đã duyệt">✅ Đã duyệt (Public)</option>
                                    <option value="Từ chối">❌ Bị từ chối</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select shadow-none" v-model="bo_loc.id_danh_muc">
                                    <option value="">Tất cả danh mục</option>
                                    <option v-for="dm in danh_sach_danh_muc" :key="dm.id" :value="dm.id">{{
                                        dm.ten_danh_muc
                                    }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="table-responsive text-start">
                        <table class="table table-hover align-middle mb-0 review-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center align-middle py-3" style="width: 70px;">Thứ tự</th>
                                    <th class="text-center align-middle py-3">Bài học / Cấp độ</th>
                                    <th class="text-center align-middle py-3">Giáo viên biên soạn</th>
                                    <th class="text-center align-middle py-3">Danh mục</th>
                                    <th class="text-center align-middle py-3">Nội dung</th>
                                    <th class="text-center align-middle py-3">Trạng thái</th>
                                    <th class="text-center align-middle py-3">Hành động QA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(bh, index) in bai_hoc_da_loc" :key="bh.id">
                                    <td class="text-center align-middle fw-bold">{{ index + 1 }}</td>
                                    <td class="text-center align-middle">
                                        <div class="fw-bold text-dark mb-1">{{ bh.tieu_de }}</div>
                                        <span class="badge bg-light border text-dark fw-normal">{{ bh.cap_do
                                            }}</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="fw-semibold text-primary"><i
                                                class="fa-solid fa-chalkboard-user me-1"></i>{{ bh.nguoi_tao_ten }}
                                        </div>
                                        <small class="text-muted">{{ bh.ngay_tao }}</small>
                                    </td>
                                    <td class="text-center align-middle">{{ layTenDanhMuc(bh.danh_muc_id) }}</td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-sm btn-outline-info rounded-pill px-3"
                                            data-bs-toggle="modal" data-bs-target="#previewModal"
                                            @click="chuanBiXemTruocNoiDung(bh)">
                                            <i class="fa-solid fa-eye me-1"></i> {{ bh.so_luong_tu }} từ
                                        </button>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge rounded-pill px-3 py-2 fw-normal"
                                            :class="layBadgeTrangThai(bh.trang_thai)">{{ bh.trang_thai }}</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div v-if="bh.trang_thai === 'Chờ duyệt'"
                                            class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-success btn-sm shadow-sm" data-bs-toggle="modal"
                                                data-bs-target="#approvalConfirmModal"
                                                @click="chuanBiDuyet(bh, 'Đã duyệt')" title="Duyệt bài">
                                                <i class="fa-solid fa-check me-1"></i> Duyệt
                                            </button>
                                            <button class="btn btn-danger btn-sm shadow-sm" data-bs-toggle="modal"
                                                data-bs-target="#approvalConfirmModal"
                                                @click="chuanBiDuyet(bh, 'Từ chối')" title="Từ chối bài">
                                                <i class="fa-solid fa-xmark me-1"></i> Từ chối
                                            </button>
                                        </div>
                                        <div v-else>
                                            <button class="btn btn-light border btn-sm text-muted"
                                                @click="capNhatTrangThaiTruocTiep(bh, 'Chờ duyệt')"
                                                title="Đưa về trạng thái chờ duyệt">
                                                <i class="fa-solid fa-rotate-left me-1"></i> Hoàn tác
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="bai_hoc_da_loc.length === 0">
                                    <td colspan="7" class="text-center py-5 text-muted">Không tìm thấy bài học nào phù
                                        hợp.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Thêm hoặc Cập nhật Danh mục (Chủ đề) -->
        <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow text-start">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold text-white">
                            <i class="fa-solid text-white fa-folder-plus me-2"></i>{{ mau_danh_muc.id ? 'Sửa Danh mục' :
                                'Thêm Danh mục mới' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form @submit.prevent="saveCategory">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control shadow-none"
                                        v-model="mau_danh_muc.ten_danh_muc" @input="capNhatSlugTuTen"
                                        required placeholder="VD: Luyện thanh điệu">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Slug danh mục</label>
                                    <input type="text" class="form-control shadow-none"
                                        v-model="mau_danh_muc.slug_danh_muc" placeholder="vd: luyen-thanh-dieu">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Thứ tự hiển thị</label>
                                    <input type="number" class="form-control shadow-none" v-model="mau_danh_muc.thu_tu"
                                        min="1">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-select shadow-none" v-model="mau_danh_muc.trang_thai">
                                        <option value="1">Hiển thị</option>
                                        <option value="0">Tạm ẩn</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Mô tả ngắn</label>
                                    <textarea class="form-control shadow-none" v-model="mau_danh_muc.mo_ta" rows="2"
                                        placeholder="Nhập mô tả mục đích của danh mục..."></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-light px-4 border shadow-none"
                                    data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="isSavingDanhMuc">
                                    <span v-if="isSavingDanhMuc" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    <i v-else class="fa-solid fa-floppy-disk me-1"></i> Lưu thông tin
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Danh sách bài học thuộc danh mục được chọn (Drill-down) -->
        <div class="modal fade" id="lessonsListModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow text-start">
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Danh sách Bài học</h5>
                            <small class="text-muted">Thuộc danh mục: <strong class="text-primary">{{
                                danh_muc_duoc_chon?.ten_danh_muc }}</strong></small>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0 mb-5" style="max-height: 60vh; overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Tiêu đề bài học</th>
                                        <th>Cấp độ</th>
                                        <th class="text-center">Từ vựng</th>
                                        <th class="pe-4 text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="bh in bai_hoc_trong_danh_muc" :key="bh.id">
                                        <td class="ps-4 fw-semibold text-dark">{{ bh.tieu_de }}</td>
                                        <td><span class="badge bg-light border text-dark fw-normal">{{ bh.cap_do
                                        }}</span></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary rounded-pill">{{ bh.so_luong_tu }} từ</span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <button class="btn btn-sm btn-outline-primary shadow-sm rounded-pill px-3"
                                                @click="xuLyXemTuVung(bh)">
                                                Chi tiết <i class="fa-solid fa-arrow-right ms-1"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="bai_hoc_trong_danh_muc.length === 0">
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-folder-open fs-1 text-secondary mb-3 d-block"></i>
                                            Danh mục này chưa có bài học nào.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Xem trước từ vựng, hình ảnh và âm thanh (Chỉ xem) -->
        <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow text-start">
                    <div class="modal-header bg-danger text-white">
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0">{{ bai_hoc_xem_truoc?.tieu_de }}</h5>
                            <small class="text-dark-50">Chi tiết từ vựng</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white shadow-none"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0" style="max-height: 60vh; overflow-y: auto;">
                        <div class="list-group list-group-flush">
                            <div v-for="tv in tu_vung_xem_truoc" :key="tv.id" class="list-group-item p-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <img :src="tv.hinh_anh_url || 'https://placehold.co/60x60?text=No+Img'"
                                        class="rounded-3 me-3 border"
                                        style="width: 60px; height: 60px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <h5 class="fw-bold text-dark mb-1">{{ tv.tu_chuan }}</h5>
                                        <small class="text-muted"><i class="fa-solid fa-language me-1"></i> Phiên âm:
                                            /{{ tv.phien_am }}/ • Cấp độ: {{ tv.cap_do }}</small>
                                    </div>
                                    <button class="btn btn-outline-primary rounded-circle" title="Phát âm thanh mẫu">
                                        <i class="fa-solid fa-volume-high"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Xác nhận phê duyệt hoặc từ chối bài học (QA Approval) -->
        <div class="modal fade" id="approvalConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3">
                            <i v-if="du_lieu_duyet.trang_thai_moi === 'Đã duyệt'"
                                class="fa-solid fa-circle-check text-success" style="font-size: 4rem;"></i>
                            <i v-else class="fa-solid fa-circle-xmark text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Xác nhận thao tác?</h5>
                        <p class="small text-muted mb-4">Bạn đang chuẩn bị <b>{{ du_lieu_duyet.trang_thai_moi }}</b> bài
                            học:<br> <strong class="text-dark">"{{ du_lieu_duyet.bai_hoc?.tieu_de }}"</strong></p>

                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-light border px-4 shadow-none" data-bs-dismiss="modal">Hủy</button>
                            <button class="btn text-white px-4 shadow-sm"
                                :class="du_lieu_duyet.trang_thai_moi === 'Đã duyệt' ? 'btn-success' : 'btn-danger'"
                                @click="xacNhanDuyet" data-bs-dismiss="modal">
                                Xác nhận
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Xác nhận xóa vĩnh viễn danh mục -->
        <div class="modal fade" id="openDeleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow text-start">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger"><i
                                class="fa-solid fa-triangle-exclamation me-2"></i>Cảnh báo xóa</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="bg-warning-subtle modal-body py-4 px-4">
                        <p class="mb-0">Hành động này sẽ xóa vĩnh viễn danh mục <b class="text-dark">"{{
                            danh_muc_can_xoa?.ten_danh_muc }}"</b>.</p>
                        <p class="text-danger small mt-2 mb-0 fw-bold">Lưu ý: Bạn không thể hoàn tác thao tác này!</p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button class="btn btn-light border px-4 shadow-none" data-bs-dismiss="modal">Hủy</button>
                        <button class="btn btn-danger px-4 shadow-sm" @click="xacNhanXoaDanhMuc"
                            data-bs-dismiss="modal">
                            <i class="fa-solid fa-trash me-1"></i> Xóa vĩnh viễn
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            bo_loc: { tu_khoa: '', id_danh_muc: '', trang_thai: 'Chờ duyệt' },
            danh_sach_danh_muc: [],
            tat_ca_bai_hoc: [],

            mau_danh_muc: { id: null, ten_danh_muc: '', slug_danh_muc: '', mo_ta: '', thu_tu: 1, trang_thai: 1 },
            danh_muc_can_xoa: null,
            danh_muc_duoc_chon: null,
            bai_hoc_trong_danh_muc: [],
            bai_hoc_xem_truoc: null,
            tu_vung_xem_truoc: [],
            du_lieu_duyet: { bai_hoc: null, trang_thai_moi: '' },
            isLoadingDanhMuc: false,
            isLoadingBaiHoc: false,
            isSavingDanhMuc: false,
            isUpdatingTrangThai: false,
        }
    },
    mounted() {
        this.loadInitialData();
        window.addEventListener('bai-hoc-cho-duyet-moi', this.fetchTatCaBaiHoc);
    },
    beforeUnmount() {
        window.removeEventListener('bai-hoc-cho-duyet-moi', this.fetchTatCaBaiHoc);
    },

    computed: {
        dem_cho_duyet() {
            return this.tat_ca_bai_hoc.filter(l => l.trang_thai === 'Chờ duyệt').length;
        },
        bai_hoc_da_loc() {
            return this.tat_ca_bai_hoc.filter(bh => {
                const khopTuKhoa = bh.tieu_de.toLowerCase().includes(this.bo_loc.tu_khoa.toLowerCase());
                const khopDanhMuc = !this.bo_loc.id_danh_muc || bh.danh_muc_id === Number(this.bo_loc.id_danh_muc);
                const khopTrangThai = !this.bo_loc.trang_thai || bh.trang_thai === this.bo_loc.trang_thai;
                return khopTuKhoa && khopDanhMuc && khopTrangThai;
            });
        }
    },

    methods: {
        authHeaders() {
            return {
                Authorization: "Bearer " + (localStorage.getItem("token_admin") || "")
            };
        },

        xuLyLoiAxios(err, defaultMessage) {
            if (err.response && err.response.data) {
                if (err.response.data.errors) {
                    const errors = Object.values(err.response.data.errors);
                    errors.forEach(errorList => {
                        if (Array.isArray(errorList)) {
                            errorList.forEach(error => this.$toast.error(error));
                        }
                    });
                } else {
                    this.$toast.error(err.response.data.message || defaultMessage);
                }
            } else {
                this.$toast.error(defaultMessage);
            }
        },

        loadInitialData() {
            this.fetchDanhMuc();
            this.fetchTatCaBaiHoc();
        },

        fetchDanhMuc() {
            this.isLoadingDanhMuc = true;
            axios
                .get("http://127.0.0.1:8000/api/admin/danh-muc-bai-hoc", {
                    headers: this.authHeaders()
                })
                .then((res) => {
                    if (res.data.status) {
                        this.danh_sach_danh_muc = res.data.data || [];
                    } else {
                        this.$toast.error(res.data.message || "Không thể tải danh mục");
                    }
                })
                .catch((err) => {
                    this.xuLyLoiAxios(err, "Có lỗi xảy ra khi tải danh mục");
                })
                .finally(() => {
                    this.isLoadingDanhMuc = false;
                });
        },

        fetchTatCaBaiHoc() {
            this.isLoadingBaiHoc = true;
            axios
                .get("http://127.0.0.1:8000/api/admin/kiem-duyet-bai-hoc", {
                    headers: this.authHeaders()
                })
                .then((res) => {
                    if (res.data.status) {
                        this.tat_ca_bai_hoc = res.data.data || [];
                    } else {
                        this.$toast.error(res.data.message || "Không thể tải bài học");
                    }
                })
                .catch((err) => {
                    this.xuLyLoiAxios(err, "Có lỗi xảy ra khi tải bài học");
                })
                .finally(() => {
                    this.isLoadingBaiHoc = false;
                });
        },

        chuanBiThemDanhMuc() {
            this.mau_danh_muc = { id: null, ten_danh_muc: '', slug_danh_muc: '', mo_ta: '', thu_tu: this.danh_sach_danh_muc.length + 1, trang_thai: 1 };
        },

        chuanBiSuaDanhMuc(dm) {
            this.mau_danh_muc = { ...dm, slug_danh_muc: dm.slug_danh_muc || this.taoSlug(dm.ten_danh_muc) };
        },

        taoSlug(text) {
            return (text || "")
                .toString()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .replace(/đ/g, "d")
                .replace(/Đ/g, "D")
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, "")
                .replace(/\s+/g, "-")
                .replace(/-+/g, "-");
        },

        capNhatSlugTuTen() {
            return;
        },

        saveCategory() {
            this.luuDanhMuc();
        },

        luuDanhMuc() {
            if (this.isSavingDanhMuc) return;
            this.isSavingDanhMuc = true;

            const payload = {
                ten_danh_muc: this.mau_danh_muc.ten_danh_muc,
                slug_danh_muc: this.taoSlug(this.mau_danh_muc.slug_danh_muc || this.mau_danh_muc.ten_danh_muc),
                mo_ta: this.mau_danh_muc.mo_ta,
                thu_tu: Number(this.mau_danh_muc.thu_tu || 1),
                trang_thai: Number(this.mau_danh_muc.trang_thai),
            };

            const isUpdate = !!this.mau_danh_muc.id;
            const method = isUpdate ? "put" : "post";
            const url = isUpdate
                ? `http://127.0.0.1:8000/api/admin/danh-muc-bai-hoc/${this.mau_danh_muc.id}`
                : "http://127.0.0.1:8000/api/admin/danh-muc-bai-hoc";

            axios({
                method,
                url,
                data: payload,
                headers: this.authHeaders(),
            })
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message || "Lưu danh mục thành công");
                        this.fetchDanhMuc();
                        const modalEl = document.getElementById('categoryModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    } else {
                        this.$toast.error(res.data.message || "Không thể lưu danh mục");
                    }
                })
                .catch((err) => {
                    this.xuLyLoiAxios(err, "Có lỗi xảy ra khi lưu danh mục");
                })
                .finally(() => {
                    this.isSavingDanhMuc = false;
                });
        },

        xacNhanXoaDanhMuc() {
            if (!this.danh_muc_can_xoa) return;

            axios
                .delete(`http://127.0.0.1:8000/api/admin/danh-muc-bai-hoc/${this.danh_muc_can_xoa.id}`, {
                    headers: this.authHeaders()
                })
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message || "Xóa danh mục thành công");
                        this.fetchDanhMuc();
                        this.fetchTatCaBaiHoc();
                    } else {
                        this.$toast.error(res.data.message || "Không thể xóa danh mục");
                    }
                    this.danh_muc_can_xoa = null;
                })
                .catch((err) => {
                    this.xuLyLoiAxios(err, "Có lỗi xảy ra khi xóa danh mục");
                });
        },

        daoTrangThaiDanhMuc(dm) {
            this.isUpdatingTrangThai = true;
            axios
                .patch(`http://127.0.0.1:8000/api/admin/danh-muc-bai-hoc/${dm.id}/trang-thai`, {}, {
                    headers: this.authHeaders()
                })
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message || "Đổi trạng thái thành công");
                        this.fetchDanhMuc();
                    } else {
                        this.$toast.error(res.data.message || "Không thể đổi trạng thái");
                    }
                })
                .catch((err) => {
                    this.xuLyLoiAxios(err, "Có lỗi xảy ra khi đổi trạng thái danh mục");
                })
                .finally(() => {
                    this.isUpdatingTrangThai = false;
                });
        },

        xemBaiHocTheoDanhMuc(dm) {
            this.danh_muc_duoc_chon = dm;
            axios
                .get(`http://127.0.0.1:8000/api/admin/danh-muc-bai-hoc/${dm.id}/bai-hoc`, {
                    headers: this.authHeaders()
                })
                .then((res) => {
                    if (res.data.status) {
                        this.bai_hoc_trong_danh_muc = res.data.data || [];
                    } else {
                        this.$toast.error(res.data.message || "Không thể tải bài học theo danh mục");
                    }
                })
                .catch((err) => {
                    this.xuLyLoiAxios(err, "Có lỗi xảy ra khi tải danh sách bài học");
                });
        },

        xuLyXemTuVung(bh) {
            const listModalEl = document.getElementById('lessonsListModal');
            if (listModalEl) bootstrap.Modal.getInstance(listModalEl).hide();

            this.chuanBiXemTruocNoiDung(bh);
            setTimeout(() => {
                const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
                previewModal.show();
            }, 300);
        },

        chuanBiXemTruocNoiDung(bh) {
            this.bai_hoc_xem_truoc = bh;
            axios
                .get(`http://127.0.0.1:8000/api/admin/kiem-duyet-bai-hoc/${bh.id}/tu-vung`, {
                    headers: this.authHeaders()
                })
                .then((res) => {
                    if (res.data.status) {
                        this.tu_vung_xem_truoc = res.data.data || [];
                    } else {
                        this.$toast.error(res.data.message || "Không thể tải từ vựng");
                    }
                })
                .catch((err) => {
                    this.xuLyLoiAxios(err, "Có lỗi xảy ra khi tải từ vựng");
                });
        },

        chuanBiDuyet(bh, trangThai) {
            this.du_lieu_duyet.bai_hoc = bh;
            this.du_lieu_duyet.trang_thai_moi = trangThai;
        },

        xacNhanDuyet() {
            if (this.du_lieu_duyet.bai_hoc) {
                this.capNhatTrangThaiTruocTiep(this.du_lieu_duyet.bai_hoc, this.du_lieu_duyet.trang_thai_moi);
            }
        },

        capNhatTrangThaiTruocTiep(bh, trangThaiMoi) {
            let trangThaiSo = trangThaiMoi;
            if (trangThaiMoi === 'Đã duyệt') trangThaiSo = 1;
            else if (trangThaiMoi === 'Từ chối') trangThaiSo = 2;
            else if (trangThaiMoi === 'Chờ duyệt') trangThaiSo = 0;

            this.isUpdatingTrangThai = true;
            axios
                .patch(`http://127.0.0.1:8000/api/admin/kiem-duyet-bai-hoc/${bh.id}/trang-thai`, {
                    trang_thai: trangThaiSo
                }, {
                    headers: this.authHeaders()
                })
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message || "Cập nhật trạng thái thành công");
                        this.fetchTatCaBaiHoc();
                        if (this.danh_muc_duoc_chon?.id) {
                            this.xemBaiHocTheoDanhMuc(this.danh_muc_duoc_chon);
                        }
                    } else {
                        this.$toast.error(res.data.message || "Không thể cập nhật trạng thái");
                    }
                })
                .catch((err) => {
                    this.xuLyLoiAxios(err, "Có lỗi xảy ra khi cập nhật trạng thái bài học");
                })
                .finally(() => {
                    this.isUpdatingTrangThai = false;
                });
        },

        layTenDanhMuc(id) {
            return this.danh_sach_danh_muc.find(c => c.id === id)?.ten_danh_muc || 'Không xác định';
        },

        layBadgeTrangThai(status) {
            if (status === 'Đã duyệt') return 'bg-success text-white';
            if (status === 'Từ chối') return 'bg-danger text-white';
            return 'bg-warning text-dark';
        }
    }
}
</script>

<style scoped>
.admin-dashboard {
  background-color: #f6f8fc;
  min-height: 100vh;
  padding: 1.5rem 1rem 2rem;
  color: #0f172a;
}

.dashboard-header {
  padding-bottom: 0.5rem;
  border-bottom: 1px solid rgba(148, 163, 184, 0.18);
}

.dashboard-title {
  color: #111827;
  font-size: 1.6rem;
}

.dashboard-subtitle {
  color: #475569;
  font-size: 0.95rem;
}

.dashboard-card {
  background: #ffffff;
  border: 1px solid rgba(148, 163, 184, 0.16);
  box-shadow: 0 14px 40px rgba(15, 23, 42, 0.06);
}

.dashboard-card .card-body {
  padding: 1.4rem;
}

.input-rounded {
  border-radius: 14px !important;
}

.form-control,
.form-select {
  border-color: #e5e7eb;
  min-height: 46px;
  border-radius: 14px;
}

.table thead th {
  border-bottom: 1px solid #e2e8f0;
  text-transform: uppercase;
  font-size: 0.8rem;
  color: #475569;
}

.table tbody tr:hover {
  background-color: #f8fafc;
}

.review-table {
  border: 1px solid #dbe3ee;
  border-collapse: separate;
  border-spacing: 0;
}

.review-table thead th,
.review-table tbody td {
  border-right: 1px solid #e2e8f0;
  border-bottom: 1px solid #e2e8f0;
  vertical-align: middle !important;
  text-align: center !important;
}

.review-table thead th:last-child,
.review-table tbody td:last-child {
  border-right: 0;
}

.custom-tabs .nav-link {
    color: #0b0a0a !important;
    border-bottom: 3px solid transparent !important;
    background: transparent;
    transition: all 0.3s ease;
}

.custom-tabs .nav-link:hover {
    color: #d63f1d !important;
    border-bottom: 3px solid #dee2e6 !important;
}

.custom-tabs .nav-link.active {
    color: #cb1d1d !important;
    border-bottom: 3px solid #cb3822 !important;
}
</style>