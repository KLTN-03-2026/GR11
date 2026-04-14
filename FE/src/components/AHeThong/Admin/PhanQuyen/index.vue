<template>
    <div class="container-fluid" style="background-color: #f8f9fa; min-height: 100vh;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="text-start">
                <h4 class="fw-bold mb-1" style="color: #2b3445;">Phân Quyền</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                    Thêm/bớt vai trò tùy ý và quản lý quyền truy cập chéo
                </p>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-success rounded-3 px-4 shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#addRoleModal">
                    <i class="fa-solid fa-plus me-1"></i> Thêm vai trò mới
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 mb-4" style="background: #fff;">
            <div class="card-body ">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8 text-start">
                        <label class="form-label mb-1">Tìm kiếm chức năng</label>
                        <div class="input-group"
                            style="border: 1px solid #ced4da; border-radius: 0.375rem; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;height: 36px;">
                            <span class="input-group-text bg-white border-end-0 d-flex align-items-center"
                                style="border: none; height: 36px;">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 shadow-none ps-0"
                                placeholder="Nhập tên chức năng hoặc mô tả..." v-model="tu_khoa"
                                style="border: none; height: 36px;">
                        </div>
                    </div>

                    <div class="col-md-4 text-start">
                        <label class="form-label mb-1">Tập trung vào Vai trò</label>
                        <select class="form-select shadow-none" v-model="id_vai_tro_chon" style="cursor: pointer;">
                            <option value="">Hiển thị tất cả vai trò</option>
                            <option v-for="vt in danh_sach_vai_tro" :key="vt.id" :value="vt.id">
                                {{ vt.ten }}
                            </option>
                        </select>
                    </div>

                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden" style="background: #fff;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-bordered"
                    style="border-collapse: collapse; border: 1px solid #dee2e6;">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="align-middle border-bottom-0 text-uppercase" style="border: 1px solid #dee2e6;">
                                <h5><b>Chức năng hệ thống</b></h5>
                            </th>

                            <th v-for="vt in danh_sach_vai_tro" :key="vt.id" v-show="hienThiCot(vt.id)"
                                class="text-center align-middle py-2 border-bottom-0"
                                style="min-width: 130px; border: 1px solid #dee2e6;">
                                <div class="text-center">
                                    <i class="fa-solid mb-2 fs-5 me-1"
                                        :class="[vt.bieu_tuong || 'fa-user-tag', vt.mau_sac || 'text-secondary']"></i>
                                    <span class="text-uppercase text-center"
                                        style="font-size: 0.8rem; letter-spacing: 0.5px;">{{ vt.ten }}</span>
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="q in danh_sach_quyen_da_loc" :key="q.id">
                            <td class="text-start" style="border: 1px solid #dee2e6; vertical-align: middle;">
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ q.ten }}</div>
                                <small class="text-muted">{{ q.mo_ta }}</small>
                            </td>

                            <td v-for="vt in danh_sach_vai_tro" :key="vt.id" v-show="hienThiCot(vt.id)"
                                class="text-center" style="border: 1px solid #dee2e6; vertical-align: middle;">

                                <div class="d-flex align-items-center justify-content-center" style="height: 100%;">
                                    <input type="checkbox" class="form-check-input"
                                        style="width: 1.25rem; height: 1.25rem; cursor: pointer;"
                                        v-model="q.truy_cap_vai_tro[vt.id]">
                                </div>
                            </td>
                        </tr>

                        <tr v-if="danh_sach_quyen_da_loc.length === 0">
                            <td :colspan="tong_so_cot" class="text-center py-5" style="border: 1px solid #dee2e6;">
                                <div class="mb-3" style="font-size: 3rem; color: #dee2e6;"><i
                                        class="fa-solid fa-shield-cat"></i></div>
                                <div class="fw-bold text-dark mb-1">Không tìm thấy chức năng phù hợp</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button class="btn btn-success rounded-3 px-4 shadow-sm" @click="luuPhanQuyen">
                <i class="fa-solid fa-floppy-disk me-1"></i> Lưu toàn bộ thay đổi
            </button>
        </div>

        <!-- Modal Thêm vai trò -->
        <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark">Thêm vai trò</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            @click="ten_vai_tro_moi = ''"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Tên vai trò mới <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-none"
                                placeholder="VD: Kế toán, Trợ giảng, Quản lý..." v-model="ten_vai_tro_moi"
                                @keyup.enter="themVaiTroMoi">
                        </div>
                        <div class="alert alert-info small border-0 bg-info-subtle mb-0">
                            <i class="fa-solid fa-circle-info me-1"></i> Hệ thống sẽ tự động tạo thêm một cột mới trong
                            Ma trận phân quyền.
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal"
                            @click="ten_vai_tro_moi = ''">Hủy</button>
                        <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal"
                            @click="themVaiTroMoi">
                            <i class="fa-solid fa-plus me-1"></i> Tạo vai trò
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
            id_vai_tro_chon: "",
            tu_khoa: "",
            ten_vai_tro_moi: "",
            danh_sach_vai_tro: [],
            danh_sach_quyen: [],
            isLoading: false,
            isSaving: false,
            isAddingRole: false,
        };
    },
    mounted() {
        this.loadPhanQuyenData();
    },

    computed: {
        danh_sach_quyen_da_loc() {
            if (!this.tu_khoa) return this.danh_sach_quyen;
            const tk = this.tu_khoa.toLowerCase();
            return this.danh_sach_quyen.filter(q =>
                q.ten.toLowerCase().includes(tk) || q.mo_ta.toLowerCase().includes(tk)
            );
        },

        tong_so_cot() {
            let vt_hien_thi = this.id_vai_tro_chon ? 1 : this.danh_sach_vai_tro.length;
            return 1 + vt_hien_thi;
        }
    },

    methods: {
        authHeaders() {
            return {
                Authorization: "Bearer " + (localStorage.getItem("token_admin") || localStorage.getItem("key_admin") || "")
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

        loadPhanQuyenData() {
            this.isLoading = true;
            axios
                .get("http://127.0.0.1:8000/api/admin/phan-quyen/data", {
                    headers: this.authHeaders()
                })
                .then((res) => {
                    if (!res.data.status) {
                        this.$toast.error(res.data.message || "Không thể tải dữ liệu phân quyền");
                        return;
                    }

                    const payload = res.data.data || {};
                    const vaiTros = payload.vai_tros || [];
                    const quyens = payload.quyens || [];
                    const mapping = payload.mapping || {};

                    this.danh_sach_vai_tro = vaiTros.map((vt) => {
                        const ui = this.mapVaiTroUi(vt.ten_vai_tro);
                        return {
                            id: vt.id,
                            ten: vt.ten_vai_tro,
                            bieu_tuong: ui.bieu_tuong,
                            mau_sac: ui.mau_sac,
                        };
                    });

                    this.danh_sach_quyen = quyens.map((q) => {
                        const truyCap = {};
                        this.danh_sach_vai_tro.forEach((vt) => {
                            truyCap[vt.id] = (mapping[vt.id] || []).includes(q.id);
                        });
                        return {
                            id: q.id,
                            ten: q.ten_quyen,
                            mo_ta: q.ma_quyen || "Không có mô tả",
                            truy_cap_vai_tro: truyCap,
                        };
                    });
                })
                .catch((err) => {
                    this.xuLyLoiAxios(err, "Có lỗi xảy ra khi tải dữ liệu phân quyền");
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        mapVaiTroUi(tenVaiTro) {
            const name = (tenVaiTro || "").toLowerCase();
            if (name.includes("admin")) {
                return { bieu_tuong: "fa-shield-halved", mau_sac: "text-danger" };
            }
            if (name.includes("giáo viên") || name.includes("giao vien")) {
                return { bieu_tuong: "fa-chalkboard-user", mau_sac: "text-primary" };
            }
            if (name.includes("học viên") || name.includes("hoc vien")) {
                return { bieu_tuong: "fa-child-reaching", mau_sac: "text-warning" };
            }
            return { bieu_tuong: "fa-user-tag", mau_sac: "text-secondary" };
        },

        hienThiCot(idVaiTro) {
            if (!this.id_vai_tro_chon) return true;
            return Number(this.id_vai_tro_chon) === Number(idVaiTro);
        },

        themVaiTroMoi() {
            if (!this.ten_vai_tro_moi.trim()) {
                this.$toast.error("Vui lòng nhập tên vai trò!");
                return;
            }
            this.isAddingRole = true;
            axios
                .post("http://127.0.0.1:8000/api/admin/vai-tro/create", {
                    ten_vai_tro: this.ten_vai_tro_moi.trim(),
                    mo_ta: null,
                }, {
                    headers: this.authHeaders()
                })
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message || "Đã thêm vai trò thành công");
                        this.ten_vai_tro_moi = "";
                        this.loadPhanQuyenData();
                    } else {
                        this.$toast.error(res.data.message || "Không thể thêm vai trò");
                    }
                })
                .catch((err) => {
                    this.xuLyLoiAxios(err, "Có lỗi xảy ra khi thêm vai trò");
                })
                .finally(() => {
                    this.isAddingRole = false;
                });
        },

        luuPhanQuyen() {
            this.isSaving = true;
            const tasks = this.danh_sach_vai_tro.map((vt) => {
                const quyenIds = this.danh_sach_quyen
                    .filter((q) => !!q.truy_cap_vai_tro[vt.id])
                    .map((q) => q.id);

                return axios.post("http://127.0.0.1:8000/api/admin/phan-quyen/dong-bo", {
                    vai_tro_id: vt.id,
                    quyen_ids: quyenIds,
                }, {
                    headers: this.authHeaders()
                });
            });

            Promise.all(tasks)
                .then(() => {
                    this.$toast.success("Đã lưu toàn bộ phân quyền thành công");
                    this.loadPhanQuyenData();
                })
                .catch((err) => {
                    this.xuLyLoiAxios(err, "Có lỗi xảy ra khi lưu phân quyền");
                })
                .finally(() => {
                    this.isSaving = false;
                });
        },
    }
};
</script>