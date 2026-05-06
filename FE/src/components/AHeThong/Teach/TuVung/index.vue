<template>
    <div class="container-fluid" style="background-color: #f8fafc; min-height: 100vh;">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <button
                    class="btn btn-sm text-secondary fw-medium border-0 bg-transparent px-0 mb-2 d-flex align-items-center"
                    @click="quayLai" style="transition: all 0.2s;" onmouseover="this.style.color='#667eea'"
                    onmouseout="this.style.color=''">
                    <i class="fa-solid fa-arrow-left me-2"></i>Quay lại danh sách bài học
                </button>
                <h4 class="fw-bold mb-1 text-dark">Danh Sách Từ Vựng Luyện Âm</h4>
                <p class="text-muted mb-0" style="font-size: 0.95rem;">
                    <span class="badge rounded-pill px-2 py-1 me-1"
                        style="background-color: #e0e7ff; color: #4f46e5;">ID: {{ bai_hoc_id || '—' }}</span>
                    <span v-if="tieu_de_bai_hoc" class="fw-medium text-secondary">{{ tieu_de_bai_hoc }}</span>
                </p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary shadow-sm rounded-pill px-4 py-2 fw-medium"
                    data-bs-toggle="modal" data-bs-target="#huongDanExcelModal">
                    <i class="fa-regular fa-circle-question me-2"></i>Hướng dẫn Excel
                </button>
                <input ref="excelTuVungInput" type="file" class="d-none"
                    accept=".xlsx,.xls,.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv"
                    @change="nhapTuVungTuExcel" />
                <button type="button" class="btn btn-outline-primary shadow-sm rounded-pill px-4 py-2 fw-medium"
                    :disabled="!bai_hoc_id || importingExcel" @click="moChonFileExcelTuVung">
                    <span v-if="importingExcel" class="spinner-border spinner-border-sm me-2"></span>
                    <i v-else class="fa-regular fa-file-excel me-2"></i>{{ importingExcel ? 'Đang nhập...' : 'Nhập từ Excel' }}
                </button>
                <button class="btn text-white shadow-sm rounded-pill px-4 py-2 fw-medium border-0"
                    data-bs-toggle="modal" data-bs-target="#tuVungModal" @click="themTuVung" :disabled="!bai_hoc_id"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fa-solid fa-plus me-2"></i>Thêm từ mới
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background-color: #ffffff;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table tu-vung-table align-middle mb-0 border-0">
                        <thead style="background-color: #f1f5f9;">
                            <tr>
                                <th class="ps-4 py-3 text-secondary fw-bold text-uppercase border-0 text-center"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px; width: 5%;">TT</th>
                                <th class="py-3 text-secondary fw-bold text-uppercase border-0 text-center"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px; width: 10%;">Hình ảnh</th>
                                <th class="py-3 text-secondary fw-bold text-uppercase border-0"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px; width: 25%;">Từ chuẩn</th>
                                <th class="py-3 text-secondary fw-bold text-uppercase border-0"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px; width: 20%;">Phiên âm</th>
                                <th class="py-3 text-secondary fw-bold text-uppercase border-0 text-center"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px; width: 15%;">Cấp độ</th>
                                <th class="pe-4 py-3 text-secondary fw-bold text-uppercase text-end border-0 text-center"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px; width: 15%;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-if="loading">
                                <td colspan="6" class="text-center py-5 text-muted border-0">
                                    <div class="spinner-border text-primary mb-2" role="status"
                                        style="width: 2rem; height: 2rem;"></div>
                                    <div class="fw-medium">Đang tải từ vựng...</div>
                                </td>
                            </tr>

                            <tr v-else-if="!bai_hoc_id">
                                <td colspan="6" class="text-center py-5 text-muted border-0 fw-medium">
                                    <i class="fa-solid fa-triangle-exclamation fs-3 mb-2 opacity-50"></i><br>
                                    Thiếu tham số bài học. Vui lòng mở từ màn hình Quản lý bài học.
                                </td>
                            </tr>

                            <tr v-else-if="danh_sach_tu_vung.length === 0">
                                <td colspan="6" class="p-4 border-0">
                                    <div class="text-center py-5 rounded-4"
                                        style="border: 2px dashed #e2e8f0; background-color: #f8fafc;">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm mb-3"
                                            style="width: 70px; height: 70px;">
                                            <i class="fa-solid fa-language fs-2" style="color: #667eea;"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Chưa có từ vựng nào</h6>
                                        <p class="text-muted small mb-0">Hãy bấm "Thêm từ mới" để tạo nội dung luyện âm
                                            cho bé.</p>
                                    </div>
                                </td>
                            </tr>

                            <tr v-for="tu in danh_sach_tu_vung" :key="tu.id" style="transition: background-color 0.2s;"
                                onmouseover="this.style.backgroundColor='#f8fafc'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="text-secondary fw-semibold text-center"
                                    style="border-bottom: 1px solid #f1f5f9;">{{ tu.thu_tu }}</td>

                                <td class="text-center" style="border-bottom: 1px solid #f1f5f9;">
                                    <div class="rounded-4 bg-white d-inline-flex align-items-center justify-content-center shadow-sm overflow-hidden"
                                        style="width: 50px; height: 50px; border: 1px solid #e2e8f0;">
                                        <img v-if="tu.hinh_anh_url" :src="tu.hinh_anh_url" alt="img"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                        <i v-else class="fa-regular fa-image text-secondary opacity-50 fs-5"></i>
                                    </div>
                                </td>

                                <td style="border-bottom: 1px solid #f1f5f9;">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <button type="button"
                                            class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center me-3 border-0"
                                            :style="tu.am_thanh_mau_url ? 'background-color: #e0e7ff; color: #4f46e5; width: 36px; height: 36px;' : 'background-color: #f1f5f9; color: #94a3b8; width: 36px; height: 36px; cursor: not-allowed;'"
                                            :title="tu.am_thanh_mau_url ? 'Nghe âm thanh mẫu' : 'Chưa có âm thanh'"
                                            :disabled="!tu.am_thanh_mau_url" @click.prevent="phatAmMau(tu)">
                                            <i class="fa-solid fa-volume-high"></i>
                                        </button>
                                        <span class="fw-bold text-dark fs-6">{{ tu.tu_chuan }}</span>
                                    </div>
                                </td>

                                <td style="border-bottom: 1px solid #f1f5f9;" class="text-center">
                                    <span class="text-secondary d-inline-block text-truncate fw-medium"
                                        style="max-width: 250px;" :title="tu.phien_am">
                                        {{ tu.phien_am || '—' }}
                                    </span>
                                </td>

                                <td class="text-center" style="border-bottom: 1px solid #f1f5f9;">
                                    <span class="badge rounded-pill px-3 py-2 fw-medium border"
                                        :style="tu.cap_do === 'de' ? 'background-color: #dcfce7; color: #166534; border-color: #bbf7d0 !important;' :
                                            (tu.cap_do === 'trung_binh' ? 'background-color: #fef9c3; color: #854d0e; border-color: #fef08a !important;' :
                                                'background-color: #fee2e2; color: #991b1b; border-color: #fecaca !important;')">
                                        {{ formatCapDo(tu.cap_do) }}
                                    </span>
                                </td>

                                <td class="pe-4 text-center" style="border-bottom: 1px solid #f1f5f9;">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button"
                                            class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center border-0 bg-transparent text-primary"
                                            style="width: 36px; height: 36px; transition: all 0.2s;"
                                            onmouseover="this.style.backgroundColor='#e0e7ff'"
                                            onmouseout="this.style.backgroundColor='transparent'" data-bs-toggle="modal"
                                            data-bs-target="#tuVungModal" @click="suaTuVung(tu)" title="Chỉnh sửa">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                        <button type="button"
                                            class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center border-0 bg-transparent text-danger"
                                            style="width: 36px; height: 36px; transition: all 0.2s;"
                                            onmouseover="this.style.backgroundColor='#fee2e2'"
                                            onmouseout="this.style.backgroundColor='transparent'" data-bs-toggle="modal"
                                            data-bs-target="#xoaTuVungModal" @click="xoaTuVung(tu)" title="Xóa">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="tuVungModal" tabindex="-1" aria-labelledby="tuVungModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                    <div class="modal-header bg-light border-bottom-0 p-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                :class="isEdit ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success'"
                                style="width: 45px; height: 45px;">
                                <i class="fa-solid fs-5" :class="isEdit ? 'fa-pen-to-square' : 'fa-plus'"></i>
                            </div>
                            <h5 class="modal-title fw-bold text-dark" id="tuVungModalLabel">
                                {{ isEdit ? 'Cập Nhật Từ Vựng' : 'Thêm Từ Vựng Mới' }}
                            </h5>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-dark mb-2">Từ chuẩn <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3 py-2 bg-light border-0 shadow-none"
                                    v-model="tuVungForm.tu_chuan" placeholder="VD: Con Lợn, Mặt Trời..." required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark mb-2">Thứ tự hiển thị</label>
                                <input type="number" class="form-control rounded-3 py-2 bg-light border-0 shadow-none"
                                    v-model.number="tuVungForm.thu_tu" min="1" placeholder="VD: 1, 2, 3...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark mb-2">Phiên âm / Hướng dẫn</label>
                                <input type="text" class="form-control rounded-3 py-2 bg-light border-0 shadow-none"
                                    v-model="tuVungForm.phien_am" placeholder="VD: Âm L uốn cong lưỡi...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark mb-2">Cấp độ</label>
                                <select class="form-select rounded-3 py-2 bg-light border-0 shadow-none"
                                    v-model="tuVungForm.cap_do" style="cursor: pointer;">
                                    <option value="de">Dễ</option>
                                    <option value="trung_binh">Trung bình</option>
                                    <option value="kho">Khó</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-dark mb-2">Link Hình ảnh minh họa
                                    (URL)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="fa-regular fa-image text-muted"></i></span>
                                    <input type="text" class="form-control py-2 bg-light border-0 shadow-none"
                                        v-model="tuVungForm.hinh_anh_url"
                                        placeholder="https://... hoặc đường dẫn file ảnh">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-dark mb-2">Link Âm thanh mẫu (URL)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="fa-solid fa-microphone-lines text-muted"></i></span>
                                    <input type="text" class="form-control py-2 bg-light border-0 shadow-none"
                                        v-model="tuVungForm.am_thanh_mau_url"
                                        placeholder="https://... hoặc đường dẫn file MP3/WAV">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-light fw-medium px-4 py-2 rounded-3 border"
                            data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn text-white fw-medium px-4 py-2 rounded-3 border-0 shadow-sm"
                            :disabled="saving" @click="luuTuVung"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-else class="fa-regular fa-floppy-disk me-2"></i> Lưu thông tin
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="xoaTuVungModal" tabindex="-1" aria-labelledby="xoaTuVungModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-md-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle rounded-circle"
                    style="width: 80px; height: 80px;">
                    <i class="fa-solid fa-triangle-exclamation text-danger" style="font-size: 2.5rem;"></i>
                </div>
                <h5 class="fw-bold text-dark">Xác nhận xóa?</h5>
                <p class="text-muted" style="font-size: 0.95rem;">
                    Bạn có chắc chắn muốn xóa từ 
                    <strong class="text-danger fs-5">{{ tuVungXoa?.tu_chuan }}</strong> 
                    khỏi bài học này không?
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light fw-medium px-4 border"
                        data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger fw-medium px-4 shadow-sm" :disabled="deleting"
                        @click="xacNhanXoa">
                        <span v-if="deleting" class="spinner-border spinner-border-sm me-2"></span>
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

        <div class="modal fade" id="huongDanExcelModal" tabindex="-1" aria-labelledby="huongDanExcelModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                    <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="huongDanExcelModalLabel">
                                Hướng dẫn trình bày file Excel
                            </h5>
                            <p class="text-muted small mb-0 mt-1">Dòng đầu tiên là tiêu đề cột — cột <strong>Từ chuẩn</strong>
                                bắt buộc; các cột khác có thể để trống.</p>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body px-4 pb-4 pt-2">
                        <div class="rounded-3 bg-light p-2 text-center border" style="border-color: #e2e8f0 !important;">
                            <img :src="huongDanExcelAnhUrl" alt="Hướng dẫn nhập từ vựng hàng loạt từ Excel"
                                class="img-fluid rounded-2" style="max-height: min(85vh, 900px); width: auto;" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="excelTrungModal" tabindex="-1" aria-labelledby="excelTrungModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            
            <!-- Header -->
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="excelTrungModalLabel">
                    <i class="fa-solid fa-triangle-exclamation text-warning me-2 fs-5"></i> 
                    Từ đã tồn tại trong bài học
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 py-3">
                <p class="text-muted small mb-3">
                    Các từ dưới đây đã tồn tại nên sẽ bị bỏ qua.
                </p>

                <!-- Trạng thái trống -->
                <div v-if="tu_vung_trung_excel.length === 0" class="text-muted small fst-italic text-center py-3 bg-light rounded-3">
                    Không có dữ liệu từ trùng.
                </div>

                <!-- Khung chứa danh sách (Tự động cuộn nếu > 10 từ) -->
                <div v-else class="duplicate-word-list border rounded-3" :class="{ 'is-scrollable': tu_vung_trung_excel.length > 10 }">
                    <ul class="list-group list-group-flush m-0">
                        <li v-for="(item, idx) in tu_vung_trung_excel" :key="`${item.line}-${item.tu_chuan}-${idx}`"
                            class="list-group-item px-3 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="fw-semibold text-dark">{{ item.tu_chuan }}</span>
                            <span class="badge bg-light text-secondary border px-3 py-2 fw-medium" style="border-radius: 6px;">
                                Dòng {{ item.line }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-top-0 px-4 pb-4 pt-1 justify-content-center">
                <button type="button" class="btn btn-warning px-5 py-2 fw-bold text-light rounded-3 shadow-sm w-100" data-bs-dismiss="modal">
                    Đã hiểu
                </button>
            </div>
            
        </div>
    </div>
</div>

    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'TeachTuVung',
    data() {
        return {
            apiBase: (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, ''),
            bai_hoc_id: null,
            tieu_de_bai_hoc: '',
            danh_sach_tu_vung: [],
            loading: false,
            saving: false,
            deleting: false,
            importingExcel: false,
            tu_vung_trung_excel: [],
            isEdit: false,
            tuVungForm: {
                id: null,
                bai_hoc_id: null,
                tu_chuan: '',
                phien_am: '',
                cap_do: 'de',
                hinh_anh_url: '',
                am_thanh_mau_url: '',
                thu_tu: 1,
            },
            tuVungXoa: null,
        };
    },
    watch: {
        '$route.query.bai_hoc_id': {
            handler() {
                this.dongBoBaiHocTuQuery();
            },
            immediate: false,
        },
    },
    created() {
        this.dongBoBaiHocTuQuery();
    },
    computed: {
        huongDanExcelAnhUrl() {
            const base = import.meta.env.BASE_URL || '/';
            const prefix = base.endsWith('/') ? base.slice(0, -1) : base;
            return `${prefix}/teach/huong-dan-nhap-tu-vung-excel.png`;
        },
    },
    methods: {
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
        moModalTheoId(modalId) {
            const el = document.getElementById(modalId);
            if (!el || typeof window.bootstrap === 'undefined') return;
            const inst = window.bootstrap.Modal.getInstance(el) || new window.bootstrap.Modal(el);
            inst.show();
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
        dongBoBaiHocTuQuery() {
            const raw = this.$route.query.bai_hoc_id;
            const id = raw != null && String(raw).trim() !== '' ? parseInt(String(raw), 10) : NaN;
            if (!Number.isFinite(id) || id < 1) {
                this.bai_hoc_id = null;
                this.tieu_de_bai_hoc = '';
                this.danh_sach_tu_vung = [];
                return;
            }
            this.bai_hoc_id = id;
            this.layDuLieuTuVung();
        },
        formatCapDo(capDo) {
            const map = {
                de: 'Dễ',
                trung_binh: 'Trung bình',
                kho: 'Khó',
            };
            return map[capDo] || 'Không xác định';
        },
        layDuLieuTuVung() {
            if (!this.bai_hoc_id) return;
            this.loading = true;
            axios
                .get(this.apiBase + '/api/teacher/bai-hoc/' + this.bai_hoc_id + '/tu-vung', {
                    headers: this.authHeaders(),
                })
                .then((res) => {
                    if (res.data.status) {
                        this.tieu_de_bai_hoc = res.data.bai_hoc?.tieu_de || '';
                        this.danh_sach_tu_vung = res.data.data || [];
                    } else {
                        this.$toast.error(res.data.message || 'Không tải được từ vựng.');
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
                    if (err.response && err.response.status === 404) {
                        this.$toast.error('Bài học không tồn tại.');
                        this.quayLai();
                        return;
                    }
                    this.toastLoiAxios(err);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        quayLai() {
            this.$router.push('/teacher/quan-ly-bai-hoc');
        },
        moChonFileExcelTuVung() {
            if (!this.bai_hoc_id || this.importingExcel) return;
            const el = this.$refs.excelTuVungInput;
            if (el) el.click();
        },
        nhapTuVungTuExcel(event) {
            const input = event.target;
            const file = input.files && input.files[0];
            if (!file || !this.bai_hoc_id) {
                if (input) input.value = '';
                return;
            }
            this.importingExcel = true;
            const fd = new FormData();
            fd.append('file', file);
            axios
                .post(this.apiBase + '/api/teacher/bai-hoc/' + this.bai_hoc_id + '/tu-vung/import-excel', fd, {
                    headers: this.authHeaders(),
                })
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message || 'Nhập Excel thành công.');
                        this.tu_vung_trung_excel = res.data?.data?.skipped_existing || [];
                        if (this.tu_vung_trung_excel.length > 0) {
                            this.moModalTheoId('excelTrungModal');
                        }
                        this.layDuLieuTuVung();
                    } else {
                        this.$toast.error(res.data.message || 'Không nhập được từ file.');
                    }
                })
                .catch((err) => {
                    const d = err.response && err.response.data;
                    if (d && Array.isArray(d.errors) && d.errors.length) {
                        d.errors.slice(0, 8).forEach((msg) => this.$toast.error(msg));
                        if (d.errors.length > 8) {
                            this.$toast.error('… và ' + (d.errors.length - 8) + ' lỗi khác.');
                        }
                        return;
                    }
                    this.toastLoiAxios(err);
                })
                .finally(() => {
                    this.importingExcel = false;
                    if (input) input.value = '';
                });
        },
        phatAmMau(tu) {
            const url = tu.am_thanh_mau_url;
            if (!url) {
                this.$toast.warning('Chưa có âm thanh mẫu.');
                return;
            }
            const audio = new Audio(url);
            audio.play().catch(() => {
                this.$toast.error('Không phát được âm thanh (kiểm tra link hoặc CORS).');
            });
        },
        themTuVung() {
            if (!this.bai_hoc_id) {
                this.$toast.error('Chưa xác định bài học.');
                return;
            }
            this.isEdit = false;
            const nextThuTu =
                this.danh_sach_tu_vung.length > 0
                    ? Math.max(...this.danh_sach_tu_vung.map((t) => t.thu_tu || 0)) + 1
                    : 1;
            this.tuVungForm = {
                id: null,
                bai_hoc_id: this.bai_hoc_id,
                tu_chuan: '',
                phien_am: '',
                cap_do: 'de',
                hinh_anh_url: '',
                am_thanh_mau_url: '',
                thu_tu: nextThuTu,
            };
        },
        suaTuVung(tu) {
            this.isEdit = true;
            this.tuVungForm = {
                id: tu.id,
                bai_hoc_id: tu.bai_hoc_id,
                tu_chuan: tu.tu_chuan || '',
                phien_am: tu.phien_am || '',
                cap_do: tu.cap_do || 'de',
                hinh_anh_url: tu.hinh_anh_url || '',
                am_thanh_mau_url: tu.am_thanh_mau_url || '',
                thu_tu: tu.thu_tu != null ? tu.thu_tu : 1,
            };
        },
        chuanHoaPayloadTuVung() {
            const trimOrNull = (s) => {
                const t = (s || '').trim();
                return t === '' ? null : t;
            };
            return {
                tu_chuan: (this.tuVungForm.tu_chuan || '').trim(),
                phien_am: trimOrNull(this.tuVungForm.phien_am),
                cap_do: this.tuVungForm.cap_do,
                hinh_anh_url: trimOrNull(this.tuVungForm.hinh_anh_url),
                am_thanh_mau_url: trimOrNull(this.tuVungForm.am_thanh_mau_url),
                thu_tu: this.tuVungForm.thu_tu != null && this.tuVungForm.thu_tu !== '' ? Number(this.tuVungForm.thu_tu) : null,
            };
        },
        luuTuVung() {
            if (!this.bai_hoc_id) {
                this.$toast.error('Chưa xác định bài học.');
                return;
            }
            const payload = this.chuanHoaPayloadTuVung();
            if (!payload.tu_chuan) {
                this.$toast.error('Vui lòng nhập từ chuẩn.');
                return;
            }
            this.saving = true;
            const req = this.isEdit
                ? axios.put(this.apiBase + '/api/teacher/tu-vung/' + this.tuVungForm.id, payload, {
                    headers: this.authHeaders(),
                })
                : axios.post(this.apiBase + '/api/teacher/bai-hoc/' + this.bai_hoc_id + '/tu-vung', payload, {
                    headers: this.authHeaders(),
                });
            req
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message || 'Lưu thành công.');
                        this.dongModalTheoId('tuVungModal');
                        this.layDuLieuTuVung();
                    } else {
                        this.$toast.error(res.data.message || 'Không lưu được.');
                    }
                })
                .catch((err) => {
                    this.toastLoiAxios(err);
                })
                .finally(() => {
                    this.saving = false;
                });
        },
        xoaTuVung(tu) {
            this.tuVungXoa = tu;
        },
        xacNhanXoa() {
            if (!this.tuVungXoa) return;
            this.deleting = true;
            axios
                .delete(this.apiBase + '/api/teacher/tu-vung/' + this.tuVungXoa.id, {
                    headers: this.authHeaders(),
                })
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message || 'Đã xóa.');
                        this.dongModalTheoId('xoaTuVungModal');
                        this.layDuLieuTuVung();
                    } else {
                        this.$toast.error(res.data.message || 'Không xóa được.');
                    }
                })
                .catch((err) => {
                    this.toastLoiAxios(err);
                })
                .finally(() => {
                    this.deleting = false;
                    this.tuVungXoa = null;
                });
        },
    },
};
</script>
<style scoped>
.tu-vung-table {
    border: 1px solid #e2e8f0;
    border-collapse: separate;
    border-spacing: 0;
}

.tu-vung-table thead th,
.tu-vung-table tbody td {
    border-right: 1px solid #e2e8f0 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    vertical-align: middle !important;
    text-align: center !important;
}

.tu-vung-table thead th:last-child,
.tu-vung-table tbody td:last-child {
    border-right: 0 !important;
}

/* Class kích hoạt thanh cuộn dọc khi vượt quá số lượng */
.duplicate-word-list.is-scrollable {
    max-height: 450px;
    overflow-y: auto;
}

/* Loại bỏ viền bottom của item cuối cùng để tránh lỗi UI */
.duplicate-word-list .list-group-item:last-child {
    border-bottom: none !important;
}

/* --- Tuỳ chỉnh thanh cuộn (Scrollbar) cho đẹp và hiện đại --- */
.duplicate-word-list::-webkit-scrollbar {
    width: 6px; /* Thanh cuộn mỏng nhẹ */
}

.duplicate-word-list::-webkit-scrollbar-track {
    background: #F9FAFB; /* Nền thanh cuộn trùng màu nền xám siêu nhạt */
    border-radius: 0 8px 8px 0;
}

.duplicate-word-list::-webkit-scrollbar-thumb {
    background: #D1D5DB; /* Màu tay cầm thanh cuộn (xám nhạt) */
    border-radius: 4px;
}

.duplicate-word-list::-webkit-scrollbar-thumb:hover {
    background: #9CA3AF; /* Đậm hơn khi hover */
}
</style>