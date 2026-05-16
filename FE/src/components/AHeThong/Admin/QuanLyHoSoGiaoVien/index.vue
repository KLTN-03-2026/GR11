<template>
  <div class="row">
    <div class="col-12">
      <div class="card border-0 shadow-sm main-card">
        <div class="card-body p-4">
          <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
              <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-user-tie me-2 text-primary"></i>Quản lý Hồ
                sơ Giáo viên
              </h4>
              <p class="text-muted mb-0 small">
                Xét duyệt hồ sơ đăng ký giáo viên từ học viên
              </p>
            </div>
            <div class="d-flex gap-2 align-items-center">
              <span class="badge-count bg-warning"
                >{{ choDuyetCount }} chờ duyệt</span
              >
            </div>
          </div>

          <ul class="nav nav-tabs-custom mb-4">
            <li class="nav-item">
              <a
                class="nav-link-custom"
                :class="{ active: tab === 'cho_duyet' }"
                @click="tab = 'cho_duyet'"
              >
                <i class="fa-solid fa-hourglass-half me-1"></i>Chờ duyệt
                <span
                  v-if="choDuyetCount > 0"
                  class="badge bg-warning text-dark ms-1"
                  >{{ choDuyetCount }}</span
                >
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link-custom"
                :class="{ active: tab === 'da_xu_ly' }"
                @click="tab = 'da_xu_ly'"
              >
                <i class="fa-solid fa-clipboard-check me-1"></i>Đã xử lý
              </a>
            </li>
          </ul>

          <div v-if="loading" class="text-center py-5">
            <span class="spinner-border text-primary"></span>
            <p class="text-muted mt-2">Đang tải...</p>
          </div>

          <div v-else-if="currentList.length === 0" class="empty-state">
            <i class="fa-solid fa-folder-open fa-3x text-muted mb-3"></i>
            <p class="text-muted">Không có hồ sơ nào</p>
          </div>

          <div v-else class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-head">
                <tr>
                  <th>#</th>
                  <th>Họ và tên</th>
                  <th>Liên hệ</th>
                  <th>Chuyên môn</th>
                  <th>Hồ sơ chi tiết</th>
                  <th>Ngày nộp</th>
                  <th v-if="tab === 'da_xu_ly'">Trạng thái</th>
                  <th v-if="tab === 'da_xu_ly'">Lý do từ chối</th>
                  <th v-if="tab === 'cho_duyet'" class="text-center">
                    Hành động
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(hs, idx) in currentList" :key="hs.id">
                  <td class="text-muted small">{{ idx + 1 }}</td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <img
                        :src="hs.anh_dai_dien_url || '/Admin/images/user/1.jpg'"
                        class="avatar-sm"
                        alt="Avatar"
                      />
                      <span class="fw-semibold text-dark">{{ hs.ho_ten }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="text-muted small">
                      <i class="fa-solid fa-envelope me-1"></i>{{ hs.email }}
                    </div>
                    <div class="text-muted small mt-1">
                      <i class="fa-solid fa-phone me-1"></i
                      >{{ hs.so_dien_thoai || "—" }}
                    </div>
                  </td>
                  <td>
                    <span class="badge-chuyen-mon">{{ hs.chuyen_mon }}</span>
                  </td>
                  <td>
                    <button
                      class="btn btn-sm btn-outline-primary fw-bold"
                      @click="openDetails(hs)"
                    >
                      <i class="fa-solid fa-eye me-1"></i>Xem hồ sơ
                    </button>
                  </td>
                  <td class="text-muted small">{{ hs.created_at }}</td>
                  <td v-if="tab === 'da_xu_ly'">
                    <span
                      :class="
                        hs.trang_thai === 1
                          ? 'badge-approved'
                          : 'badge-rejected'
                      "
                    >
                      {{
                        hs.trang_thai_label ||
                        (hs.trang_thai === 1 ? "Đã duyệt" : "Từ chối")
                      }}
                    </span>
                  </td>
                  <td
                    v-if="tab === 'da_xu_ly'"
                    class="text-muted small"
                    style="max-width: 200px"
                  >
                    {{ hs.ghi_chu_admin || "—" }}
                  </td>
                  <td v-if="tab === 'cho_duyet'" class="text-center">
                    <div class="d-flex gap-2 justify-content-center">
                      <button
                        class="btn btn-success btn-sm"
                        @click="openApprove(hs)"
                        :disabled="processing[hs.id]"
                        title="Duyệt"
                      >
                        <i class="fa-solid fa-check"></i>
                      </button>
                      <button
                        class="btn btn-danger btn-sm"
                        @click="openReject(hs)"
                        :disabled="processing[hs.id]"
                        title="Từ chối"
                      >
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="showDetailsModal"
      class="modal-overlay"
      @click.self="showDetailsModal = false"
    >
      <div class="modal-card modal-lg">
        <div class="modal-header-custom info">
          <i class="fa-solid fa-id-card fa-lg me-2"></i>Chi tiết hồ sơ giáo viên
          <button
            class="btn-close-modal ms-auto"
            @click="showDetailsModal = false"
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
                Thông tin cơ bản
              </h6>
              <div class="d-flex align-items-start gap-3 mb-3">
                <img
                  :src="
                    selectedHoSo?.anh_dai_dien_url || '/Admin/images/user/1.jpg'
                  "
                  class="avatar-lg"
                  alt="Avatar"
                />
                <div>
                  <h5 class="fw-bold mb-1">{{ selectedHoSo?.ho_ten }}</h5>
                  <p class="text-muted mb-1">
                    <i class="fa-solid fa-envelope me-2"></i
                    >{{ selectedHoSo?.email }}
                  </p>
                  <p class="text-muted mb-1">
                    <i class="fa-solid fa-phone me-2"></i
                    >{{ selectedHoSo?.so_dien_thoai || "Chưa cung cấp" }}
                  </p>
                  <span class="badge-chuyen-mon mt-1 d-inline-block">{{
                    selectedHoSo?.chuyen_mon
                  }}</span>
                </div>
              </div>
              <div v-if="selectedHoSo?.mo_ta" class="bg-light p-3 rounded mt-2">
                <strong>Giới thiệu bản thân:</strong>
                <p class="mb-0 mt-1 text-muted small">
                  {{ selectedHoSo?.mo_ta }}
                </p>
              </div>
            </div>

            <div class="col-md-12 mt-4">
              <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">
                Định danh (KYC)
              </h6>
              <div class="row g-3">
                <div class="col-sm-6">
                  <div class="document-card">
                    <div class="doc-title">CCCD/CMND (Mặt trước)</div>
                    <div
                      v-if="selectedHoSo?.cccd_mat_truoc_url"
                      class="mt-2 text-center"
                    >
                      <a
                        :href="selectedHoSo.cccd_mat_truoc_url"
                        target="_blank"
                        title="Click để xem ảnh gốc"
                      >
                        <img
                          :src="selectedHoSo.cccd_mat_truoc_url"
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
                      v-if="selectedHoSo?.cccd_mat_sau_url"
                      class="mt-2 text-center"
                    >
                      <a
                        :href="selectedHoSo.cccd_mat_sau_url"
                        target="_blank"
                        title="Click để xem ảnh gốc"
                      >
                        <img
                          :src="selectedHoSo.cccd_mat_sau_url"
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
              <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">
                Năng lực chuyên môn
              </h6>
              <div class="row g-3">
                <div class="col-sm-12">
                  <div class="document-card py-4">
                    <div class="doc-title mb-3">
                      Bằng cấp (ĐH/CĐ) hoặc Chứng chỉ bổ trợ
                    </div>
                    <div v-if="selectedHoSo?.bang_cap_url">
                      <template v-if="isPdf(selectedHoSo.bang_cap_url)">
                        <iframe
                          :src="selectedHoSo.bang_cap_url"
                          class="w-100 rounded border"
                          style="height: 350px"
                        ></iframe>
                      </template>
                      <template v-else>
                        <a
                          :href="selectedHoSo.bang_cap_url"
                          target="_blank"
                          title="Click để xem ảnh gốc"
                        >
                          <img
                            :src="selectedHoSo.bang_cap_url"
                            class="img-fluid rounded border kyc-image"
                            style="max-height: 350px"
                            alt="Bằng cấp"
                          />
                        </a>
                      </template>
                      <div class="mt-3">
                        <a
                          :href="selectedHoSo.bang_cap_url"
                          target="_blank"
                          class="btn btn-light"
                        >
                          <i
                            class="fa-solid fa-arrow-up-right-from-square text-primary me-2"
                          ></i>
                          Xem toàn màn hình
                        </a>
                      </div>
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
        <div
          v-if="tab === 'cho_duyet'"
          class="modal-footer-custom bg-light d-flex justify-content-between"
        >
          <button
            class="btn btn-outline-secondary"
            @click="showDetailsModal = false"
          >
            Đóng
          </button>
          <div>
            <button
              class="btn btn-danger me-2"
              @click="openReject(selectedHoSo)"
            >
              Từ chối
            </button>
            <button class="btn btn-success" @click="openApprove(selectedHoSo)">
              Duyệt hồ sơ
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="showApproveModal"
      class="modal-overlay"
      style="z-index: 2005"
      @click.self="showApproveModal = false"
    >
      <div class="modal-card">
        <div class="modal-header-custom approve">
          <i class="fa-solid fa-circle-check fa-lg me-2"></i>Xác nhận duyệt hồ
          sơ
        </div>
        <div class="modal-body-custom">
          <p>
            Bạn có chắc muốn <strong>duyệt</strong> hồ sơ của
            <strong>{{ selectedHoSo?.ho_ten }}</strong
            >?
          </p>
          <p class="text-muted small mb-0">
            Tài khoản sẽ được nâng cấp lên Giáo viên và gửi email thông báo.
          </p>
        </div>
        <div class="modal-footer-custom">
          <button
            class="btn btn-light me-2"
            @click="showApproveModal = false"
            :disabled="isActing"
          >
            Hủy
          </button>
          <button
            class="btn btn-success px-4"
            @click="doApprove"
            :disabled="isActing"
          >
            <span
              v-if="isActing"
              class="spinner-border spinner-border-sm me-1"
            ></span>
            Duyệt hồ sơ
          </button>
        </div>
      </div>
    </div>

    <div
      v-if="showRejectModal"
      class="modal-overlay"
      style="z-index: 2005"
      @click.self="showRejectModal = false"
    >
      <div class="modal-card">
        <div class="modal-header-custom reject">
          <i class="fa-solid fa-circle-xmark fa-lg me-2"></i>Từ chối hồ sơ
        </div>
        <div class="modal-body-custom">
          <p>
            Nhập lý do từ chối hồ sơ của
            <strong>{{ selectedHoSo?.ho_ten }}</strong
            >:
          </p>
          <textarea
            class="form-control"
            v-model="ghiChu"
            rows="4"
            placeholder="Nhập lý do từ chối..."
            required
          ></textarea>
        </div>
        <div class="modal-footer-custom">
          <button
            class="btn btn-light me-2"
            @click="showRejectModal = false"
            :disabled="isActing"
          >
            Hủy
          </button>
          <button
            class="btn btn-danger px-4"
            @click="doReject"
            :disabled="isActing || !ghiChu.trim()"
          >
            <span
              v-if="isActing"
              class="spinner-border spinner-border-sm me-1"
            ></span>
            Xác nhận từ chối
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { http } from '../../../../api/http.js';

export default {
  name: "QuanLyHoSoGiaoVien",
  data() {
    return {
      tab: "cho_duyet",
      loading: false,
      allHoSo: [],
      processing: {},
      showDetailsModal: false,
      showApproveModal: false,
      showRejectModal: false,
      selectedHoSo: null,
      ghiChu: "",
      isActing: false,
    };
  },
  computed: {
    choDuyetList() {
      return this.allHoSo.filter((hs) => hs.trang_thai === 0);
    },
    daXuLyList() {
      return this.allHoSo.filter((hs) => hs.trang_thai !== 0);
    },
    choDuyetCount() {
      return this.choDuyetList.length;
    },
    currentList() {
      return this.tab === "cho_duyet" ? this.choDuyetList : this.daXuLyList;
    },
  },
  mounted() {
    this.loadData();
  },
  methods: {
    token() {
      return localStorage.getItem("token_admin") || "";
    },
    headers() {
      return { Authorization: "Bearer " + this.token() };
    },
    isPdf(url) {
      if (!url) return false;
      return url.toLowerCase().includes(".pdf");
    },
    async loadData() {
      this.loading = true;
      try {
        const res = await http.get('/admin/ho-so-giao-vien', {
          headers: this.headers(),
        });
        this.allHoSo = res.data.data || [];
      } catch {
        this.$toast.error("Không thể tải dữ liệu.");
      } finally {
        this.loading = false;
      }
    },
    openDetails(hs) {
      this.selectedHoSo = hs;
      this.showDetailsModal = true;
    },
    openApprove(hs) {
      this.selectedHoSo = hs;
      this.showApproveModal = true;
      this.showDetailsModal = false;
    },
    openReject(hs) {
      this.selectedHoSo = hs;
      this.ghiChu = "";
      this.showRejectModal = true;
      this.showDetailsModal = false;
    },
    async doApprove() {
      this.isActing = true;
      try {
        const res = await http.patch(
          `/admin/ho-so-giao-vien/${this.selectedHoSo.id}/approve`,
          {},
          { headers: this.headers() },
        );
        if (res.data.status) {
          this.$toast.success(res.data.message);
          this.showApproveModal = false;
          await this.loadData();
        } else {
          this.$toast.error(res.data.message);
        }
      } catch (e) {
        this.$toast.error(e.response?.data?.message || "Có lỗi xảy ra.");
      } finally {
        this.isActing = false;
      }
    },
    async doReject() {
      if (!this.ghiChu.trim()) {
        this.$toast.error("Vui lòng nhập lý do từ chối.");
        return;
      }
      this.isActing = true;
      try {
        const res = await http.patch(
          `/admin/ho-so-giao-vien/${this.selectedHoSo.id}/reject`,
          { ghi_chu: this.ghiChu },
          { headers: this.headers() },
        );
        if (res.data.status) {
          this.$toast.success(res.data.message);
          this.showRejectModal = false;
          await this.loadData();
        } else {
          this.$toast.error(res.data.message);
        }
      } catch (e) {
        this.$toast.error(e.response?.data?.message || "Có lỗi xảy ra.");
      } finally {
        this.isActing = false;
      }
    },
  },
};
</script>

<style scoped>
.main-card {
  border-radius: 20px;
}
.text-primary {
  color: #667eea !important;
}

.badge-count {
  padding: 6px 14px;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 700;
  color: #92400e;
}

/* Tabs */
.nav-tabs-custom {
  list-style: none;
  padding: 6px;
  margin: 0;
  background: #f8fafc;
  border-radius: 14px;
  display: flex;
  gap: 8px;
}
.nav-item {
  flex: 1;
}
.nav-link-custom {
  display: block;
  text-align: center;
  padding: 10px 20px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  color: #64748b;
  transition: all 0.3s;
  user-select: none;
}
.nav-link-custom:hover {
  background: #e2e8f0;
  color: #334155;
}
.nav-link-custom.active {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: #fff;
  box-shadow: 0 4px 12px rgba(118, 75, 162, 0.3);
}

/* Table & Avatars */
.table-head th {
  background: #f8fafc;
  font-weight: 700;
  font-size: 0.85rem;
  color: #64748b;
  border-bottom: 2px solid #e2e8f0;
  padding: 12px 16px;
}
.table tbody tr {
  transition: background 0.2s;
}
.table tbody tr:hover {
  background: #f8faff;
}
.table td {
  padding: 14px 16px;
  vertical-align: middle;
}

.avatar-sm {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  border: 1px solid #e2e8f0;
}
.avatar-lg {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #667eea;
  box-shadow: 0 4px 10px rgba(102, 126, 234, 0.2);
}

.badge-chuyen-mon {
  background: #ede9fe;
  color: #5b21b6;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 600;
}
.badge-approved {
  background: #dcfce7;
  color: #166534;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 600;
}
.badge-rejected {
  background: #fee2e2;
  color: #991b1b;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 600;
}

.empty-state {
  text-align: center;
  padding: 60px 0;
}

/* Documents Card */
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

/* CSS cho ảnh hiển thị trực tiếp */
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

/* Modal */
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
.modal-header-custom.approve {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: #fff;
}
.modal-header-custom.reject {
  background: linear-gradient(135deg, #f97316, #ef4444);
  color: #fff;
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

.form-control {
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  padding: 10px 14px;
}
.form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}
.btn {
  border-radius: 10px;
  font-weight: 600;
  padding: 8px 16px;
}
.btn-success {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  border: none;
  color: #fff;
}
.btn-danger {
  background: linear-gradient(135deg, #f97316, #ef4444);
  border: none;
  color: #fff;
}
.btn-light {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
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
