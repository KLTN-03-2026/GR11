<template>
  <div class="row">
    <div class="col-12">
      <div class="card border-0 shadow-sm main-wrapper-card">
        <div class="card-body p-4">
          <!-- Hero — cùng ngôn ngữ visual tab Ví trong Profile -->
          <div class="deposit-hero mb-4">
            <div class="deposit-hero-left">
              <div class="deposit-icon-wrap">
                <i class="fa-solid fa-wallet fa-lg"></i>
              </div>
              <div>
                <div class="deposit-hero-label">Quản lý nạp tiền</div>
                <div class="deposit-hero-title">Đơn nạp ví</div>
                <p class="deposit-hero-desc mb-0">
                  Đối soát CK theo <strong>mã đơn</strong>, sau đó xác nhận hoặc từ chối.
                </p>
              </div>
            </div>
            <div class="deposit-hero-meta text-end">
              <div class="deposit-meta-label">Đơn trên trang này</div>
              <div class="deposit-meta-value">{{ items.length }}</div>
            </div>
          </div>

          <ul class="nav nav-pills deposit-tabs mb-4">
            <li class="nav-item">
              <a
                href="#"
                class="nav-link"
                :class="{ active: !showAll }"
                @click.prevent="setShowAll(false)"
              >
                <i class="fa-solid fa-hourglass-half me-2"></i>Chờ thanh toán
              </a>
            </li>
            <li class="nav-item">
              <a
                href="#"
                class="nav-link"
                :class="{ active: showAll }"
                @click.prevent="setShowAll(true)"
              >
                <i class="fa-solid fa-list me-2"></i>Tất cả trạng thái
              </a>
            </li>
          </ul>

          <div class="card inner-card border-0 shadow-sm">
            <div class="card-body p-0 p-md-1">
              <div v-if="loading" class="text-center py-5">
                <span class="spinner-border text-primary"></span>
                <p class="text-muted mt-3 small mb-0">Đang tải danh sách...</p>
              </div>

              <div v-else-if="items.length === 0" class="empty-state">
                <i class="fa-solid fa-receipt fa-3x mb-3 text-muted opacity-75"></i>
                <p class="text-muted mb-0 fw-semibold">Chưa có đơn nạp</p>
                <p class="small text-secondary mt-2 mb-0">
                  {{ showAll ? "Thử tải lại hoặc kiểm tra bộ lọc." : "Không có đơn đang chờ thanh toán." }}
                </p>
              </div>

              <div v-else class="table-responsive px-2 pb-2">
                <table class="table table-hover align-middle deposit-table mb-0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Mã đơn</th>
                      <th>Người dùng</th>
                      <th>Số tiền</th>
                      <th>Trạng thái</th>
                      <th>Thời gian</th>
                      <th class="text-end">Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="d in items" :key="d.id">
                      <td class="text-muted small">{{ d.id }}</td>
                      <td>
                        <code class="ma-don-code">{{ d.ma_don }}</code>
                      </td>
                      <td>
                        <div class="user-cell-name">{{ d.nguoi_dung?.ho_ten || "—" }}</div>
                        <div class="user-cell-mail">
                          <i class="fa-solid fa-envelope me-1 opacity-75"></i>{{ d.nguoi_dung?.email || "—" }}
                        </div>
                      </td>
                      <td class="deposit-amount">{{ formatMoney(d.so_tien) }}</td>
                      <td>
                        <span class="gd-status" :class="statusClass(d.trang_thai)">{{ statusLabel(d.trang_thai) }}</span>
                      </td>
                      <td class="text-muted small">{{ formatTime(d.created_at) }}</td>
                      <td class="text-end text-nowrap">
                        <template v-if="d.trang_thai === 'cho_thanh_toan'">
                          <button
                            type="button"
                            class="btn btn-success-custom btn-sm me-2"
                            :disabled="actingId === d.id"
                            @click="openConfirm(d)"
                          >
                            <i class="fa-solid fa-check me-1"></i>Xác nhận
                          </button>
                          <button
                            type="button"
                            class="btn btn-outline-decline btn-sm"
                            :disabled="actingId === d.id"
                            @click="openReject(d)"
                          >
                            <i class="fa-solid fa-xmark me-1"></i>Từ chối
                          </button>
                        </template>
                        <span v-else class="text-muted small">—</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div
                v-if="!loading && items.length > 0 && meta.last_page > 1"
                class="d-flex justify-content-center align-items-center gap-2 py-3 border-top border-light"
              >
                <button
                  type="button"
                  class="btn btn-light btn-sm rounded-pill px-3"
                  :disabled="meta.current_page <= 1 || loading"
                  @click="loadDeposits(meta.current_page - 1)"
                >
                  <i class="fa-solid fa-chevron-left"></i>
                </button>
                <span class="text-muted small">Trang {{ meta.current_page }} / {{ meta.last_page }}</span>
                <button
                  type="button"
                  class="btn btn-light btn-sm rounded-pill px-3"
                  :disabled="meta.current_page >= meta.last_page || loading"
                  @click="loadDeposits(meta.current_page + 1)"
                >
                  <i class="fa-solid fa-chevron-right"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal xác nhận — pattern Profile (overlay + header info + nút custom) -->
    <div v-if="confirmRow" class="modal-overlay" @click.self="confirmRow = null">
      <div class="modal-card">
        <div class="modal-header-custom info">
          <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
          <span>Xác nhận nạp tiền</span>
          <button type="button" class="btn-close-modal ms-auto" aria-label="Đóng" @click="confirmRow = null">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        <div class="modal-body-custom">
          <p class="mb-2 text-dark">
            Xác nhận đã nhận đủ tiền cho đơn <strong class="text-primary">{{ confirmRow.ma_don }}</strong>?
          </p>
          <p class="text-muted small mb-0">
            Số tiền <span class="fw-bold text-dark">{{ formatMoney(confirmRow.so_tien) }}</span> sẽ được cộng vào ví người dùng.
          </p>
        </div>
        <div class="modal-footer-custom bg-light">
          <button type="button" class="btn btn-light px-4" @click="confirmRow = null">Huỷ</button>
          <button type="button" class="btn btn-success-custom px-4" :disabled="actingId != null" @click="doConfirm">
            <span v-if="actingId" class="spinner-border spinner-border-sm me-1"></span>
            Xác nhận
          </button>
        </div>
      </div>
    </div>

    <div v-if="rejectRow" class="modal-overlay" @click.self="rejectRow = null">
      <div class="modal-card">
        <div class="modal-header-custom info">
          <i class="fa-solid fa-circle-xmark fa-lg me-2 text-danger"></i>
          <span>Từ chối đơn nạp</span>
          <button type="button" class="btn-close-modal ms-auto" aria-label="Đóng" @click="rejectRow = null">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        <div class="modal-body-custom">
          <p class="small text-muted mb-3">Đơn <strong class="text-dark">{{ rejectRow.ma_don }}</strong></p>
          <label class="form-label small fw-semibold text-secondary">Lý do (tuỳ chọn)</label>
          <textarea
            v-model="rejectLyDo"
            class="form-control deposit-textarea"
            rows="3"
            maxlength="500"
            placeholder="Ví dụ: Không khớp nội dung chuyển khoản..."
          ></textarea>
        </div>
        <div class="modal-footer-custom bg-light">
          <button type="button" class="btn btn-light px-4" @click="rejectRow = null">Huỷ</button>
          <button type="button" class="btn btn-danger-custom px-4" :disabled="actingId != null" @click="doReject">
            <span v-if="actingId" class="spinner-border spinner-border-sm me-1"></span>
            Từ chối
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "QuanLyDonNapTien",
  data() {
    return {
      apiBase: (import.meta.env.VITE_API_URL || "http://127.0.0.1:8000").replace(/\/$/, ""),
      loading: false,
      showAll: false,
      items: [],
      meta: { current_page: 1, last_page: 1, per_page: 20, total: 0 },
      confirmRow: null,
      rejectRow: null,
      rejectLyDo: "",
      actingId: null,
    };
  },
  mounted() {
    this.loadDeposits(1);
  },
  methods: {
    setShowAll(val) {
      if (this.showAll === val) return;
      this.showAll = val;
      this.loadDeposits(1);
    },
    authHeaders() {
      return { Authorization: "Bearer " + (localStorage.getItem("token_admin") || "") };
    },
    formatMoney(n) {
      return Number(n || 0).toLocaleString("vi-VN") + " ₫";
    },
    formatTime(iso) {
      if (!iso) return "—";
      try {
        const d = new Date(iso);
        return d.toLocaleString("vi-VN");
      } catch {
        return iso;
      }
    },
    statusLabel(tt) {
      const m = {
        cho_thanh_toan: "Chờ thanh toán",
        thanh_cong: "Thành công",
        that_bai: "Từ chối",
        cho_quet_ma: "Chờ quét mã",
        cho_xac_nhan: "Chờ xác nhận",
        cho_xu_ly: "Chờ xử lý",
      };
      return m[tt] || tt || "—";
    },
    statusClass(tt) {
      const map = {
        cho_thanh_toan: "status-pending",
        cho_quet_ma: "status-pending",
        cho_xac_nhan: "status-pending",
        cho_xu_ly: "status-pending",
        thanh_cong: "status-success",
        that_bai: "status-fail",
      };
      return map[tt] || "status-cancel";
    },
    async loadDeposits(page) {
      this.loading = true;
      try {
        const res = await axios.get(`${this.apiBase}/api/admin/deposits`, {
          params: {
            page: page || 1,
            per_page: this.meta.per_page,
            all: this.showAll ? 1 : 0,
          },
          headers: this.authHeaders(),
        });
        if (res.data.status) {
          this.items = res.data.data || [];
          this.meta = {
            current_page: res.data.current_page || 1,
            last_page: res.data.last_page || 1,
            per_page: res.data.per_page || 20,
            total: res.data.total || 0,
          };
        } else {
          this.$toast?.error?.(res.data.message || "Không tải được danh sách.");
        }
      } catch (e) {
        this.$toast?.error?.(e.response?.data?.message || "Lỗi kết nối máy chủ.");
      } finally {
        this.loading = false;
      }
    },
    openConfirm(row) {
      this.confirmRow = row;
    },
    openReject(row) {
      this.rejectRow = row;
      this.rejectLyDo = "";
    },
    async doConfirm() {
      if (!this.confirmRow) return;
      const id = this.confirmRow.id;
      this.actingId = id;
      try {
        const res = await axios.post(`${this.apiBase}/api/admin/deposits/${id}/confirm`, {}, { headers: this.authHeaders() });
        if (res.data.status) {
          this.$toast?.success?.(res.data.message || "Đã xác nhận.");
          this.confirmRow = null;
          await this.loadDeposits(this.meta.current_page);
        } else {
          this.$toast?.error?.(res.data.message || "Thất bại.");
        }
      } catch (e) {
        this.$toast?.error?.(e.response?.data?.message || "Không xác nhận được.");
      } finally {
        this.actingId = null;
      }
    },
    async doReject() {
      if (!this.rejectRow) return;
      const id = this.rejectRow.id;
      this.actingId = id;
      try {
        const res = await axios.post(
          `${this.apiBase}/api/admin/deposits/${id}/reject`,
          { ly_do: this.rejectLyDo || null },
          { headers: this.authHeaders() },
        );
        if (res.data.status) {
          this.$toast?.success?.(res.data.message || "Đã từ chối.");
          this.rejectRow = null;
          await this.loadDeposits(this.meta.current_page);
        } else {
          this.$toast?.error?.(res.data.message || "Thất bại.");
        }
      } catch (e) {
        this.$toast?.error?.(e.response?.data?.message || "Không từ chối được.");
      } finally {
        this.actingId = null;
      }
    },
  },
};
</script>

<style scoped>
/* Khớp Profile: main-wrapper-card, inner-card, gradient ví, tab pill, modal, gd-status, nút custom */
.text-primary {
  color: #667eea !important;
}

.main-wrapper-card {
  background-color: #ffffff;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03) !important;
}

.inner-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #f1f5f9;
  transition: box-shadow 0.25s ease;
}
.inner-card:hover {
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.08);
}

.deposit-hero {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  padding: 22px 26px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  color: #fff;
}
.deposit-hero-left {
  display: flex;
  align-items: center;
  gap: 16px;
  min-width: 0;
}
.deposit-icon-wrap {
  width: 54px;
  height: 54px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.deposit-hero-label {
  font-size: 0.82rem;
  opacity: 0.88;
  font-weight: 500;
  margin-bottom: 2px;
}
.deposit-hero-title {
  font-size: 1.35rem;
  font-weight: 700;
  letter-spacing: -0.3px;
  margin-bottom: 6px;
}
.deposit-hero-desc {
  font-size: 0.88rem;
  opacity: 0.92;
  max-width: 520px;
  line-height: 1.45;
}
.deposit-hero-meta {
  flex-shrink: 0;
}
.deposit-meta-label {
  font-size: 0.78rem;
  opacity: 0.85;
}
.deposit-meta-value {
  font-size: 1.5rem;
  font-weight: 700;
}

.deposit-tabs {
  background: #f8fafc;
  border-radius: 12px;
  padding: 6px;
  gap: 6px;
  display: flex;
  flex-wrap: wrap;
}
.deposit-tabs .nav-link {
  color: #64748b;
  border-radius: 8px;
  font-weight: 600;
  padding: 10px 18px;
  border: none;
  transition: all 0.25s ease;
}
.deposit-tabs .nav-link:hover:not(.active) {
  background: #e2e8f0;
  color: #334155;
}
.deposit-tabs .nav-link.active {
  background: #fff;
  color: #667eea;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.deposit-table thead th {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  font-weight: 700;
  color: #64748b;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
  padding: 12px 14px;
}
.deposit-table tbody td {
  padding: 14px;
  vertical-align: middle;
  border-color: #f1f5f9;
}
.deposit-table tbody tr:hover {
  background: #fafbff;
}

.ma-don-code {
  background: #f1f5f9;
  color: #475569;
  padding: 4px 10px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 600;
}
.user-cell-name {
  font-weight: 600;
  color: #1e293b;
  font-size: 0.9rem;
}
.user-cell-mail {
  font-size: 0.78rem;
  color: #94a3b8;
  margin-top: 2px;
}
.deposit-amount {
  font-weight: 700;
  font-size: 0.95rem;
  color: #667eea;
}

.gd-status {
  font-size: 0.72rem;
  font-weight: 600;
  border-radius: 20px;
  padding: 4px 12px;
  display: inline-block;
}
.status-pending {
  background: #fef9c3;
  color: #854d0e;
}
.status-success {
  background: #dcfce7;
  color: #166534;
}
.status-fail {
  background: #fee2e2;
  color: #991b1b;
}
.status-cancel {
  background: #f1f5f9;
  color: #475569;
}

.btn-success-custom {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  border: none;
  color: #fff;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.3s;
}
.btn-success-custom:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(34, 197, 94, 0.35);
  color: #fff;
}
.btn-danger-custom {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  border: none;
  color: #fff;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.3s;
}
.btn-danger-custom:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(239, 68, 68, 0.35);
  color: #fff;
}
.btn-outline-decline {
  border-radius: 10px;
  font-weight: 600;
  border: 1px solid #fecaca;
  color: #b91c1c;
  background: #fff;
}
.btn-outline-decline:hover:not(:disabled) {
  background: #fef2f2;
  border-color: #f87171;
  color: #991b1b;
}

.btn-light {
  background-color: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
  font-weight: 600;
}
.btn-light:hover:not(:disabled) {
  background-color: #e2e8f0;
  color: #1e293b;
}

.deposit-textarea {
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  background-color: #f8fafc;
  color: #334155;
  resize: none;
}
.deposit-textarea:focus {
  background-color: #fff;
  border-color: #667eea;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
}

.empty-state {
  text-align: center;
  padding: 48px 20px;
  border: 1px dashed #e2e8f0;
  border-radius: 16px;
  margin: 8px;
  background: #fafbff;
}

/* Modal — đồng bộ Profile */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(2px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 16px;
}
.modal-card {
  width: min(440px, 92vw);
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}
.modal-header-custom {
  padding: 18px 24px;
  font-weight: 700;
  font-size: 1.05rem;
  display: flex;
  align-items: center;
  gap: 8px;
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
  gap: 10px;
  flex-wrap: wrap;
}
.btn-close-modal {
  background: transparent;
  border: none;
  font-size: 1.2rem;
  color: #64748b;
  cursor: pointer;
  padding: 4px 8px;
  line-height: 1;
  transition: color 0.2s;
}
.btn-close-modal:hover {
  color: #ef4444;
}
</style>
