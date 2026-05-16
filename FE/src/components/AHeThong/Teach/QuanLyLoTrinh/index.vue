<template>
  <div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
      <div>
        <h4 class="fw-bold mb-1 text-dark">
          <i class="fa-solid fa-route text-primary me-2"></i>Lộ trình cá nhân
        </h4>
        <p class="text-muted mb-0 small">Tạo lộ trình, sắp xếp bài học, ghi chú GV và cấu hình phí cho học viên bạn phụ trách.</p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-body">
            <label class="form-label fw-bold">Học viên</label>
            <select v-model.number="hocVienId" class="form-select rounded-3 mb-3" @change="onDoiHocVien">
              <option :value="null" disabled>— Chọn học viên —</option>
              <option v-for="s in students" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
            <button type="button" class="btn btn-primary w-100 rounded-3 mb-2" :disabled="!hocVienId || saving" @click="moModalTaoLoTrinh">
              <i class="fa-solid fa-plus me-1"></i> Tạo lộ trình mới
            </button>
            <div v-if="loadingList" class="text-muted small text-center py-2">Đang tải...</div>
            <ul v-else class="list-group list-group-flush mt-2">
              <li
                v-for="lt in loTrinhs"
                :key="lt.id"
                class="list-group-item list-group-item-action rounded-3 mb-1 border"
                :class="{ 'active text-white': lt.id === selectedLoTrinhId }"
                style="cursor: pointer;"
                @click="chonLoTrinh(lt.id)"
              >
                <div class="fw-semibold">{{ lt.ten_lo_trinh }}</div>
                <small :class="lt.id === selectedLoTrinhId ? 'text-white-50' : 'text-muted'">
                  <span v-if="lt.la_tra_phi">Trả phí: {{ formatVnd(lt.tra_phi?.gia) }}</span>
                  <span v-else>Miễn phí</span>
                </small>
                <div v-if="lt.la_tra_phi" class="mt-1">
                  <span
                    class="badge rounded-pill small"
                    :class="lt.da_mua ? (lt.id === selectedLoTrinhId ? 'bg-success' : 'bg-success bg-opacity-75') : (lt.id === selectedLoTrinhId ? 'bg-warning text-dark' : 'bg-secondary')"
                  >
                    {{ lt.da_mua ? 'Học viên đã thanh toán' : 'Chưa thanh toán' }}
                  </span>
                  <span v-if="lt.da_mua && lt.thu_nhap_gv_sau_khau_tru != null" class="d-block mt-1 small" :class="lt.id === selectedLoTrinhId ? 'text-white-50' : 'text-muted'">
                    Thu nhập GV: {{ formatVnd(lt.thu_nhap_gv_sau_khau_tru) }}
                  </span>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-lg-8" v-if="selectedLoTrinhId">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3">Tên lộ trình</h6>
            <div class="input-group mb-3">
              <input v-model="tenLoTrinh" class="form-control rounded-start-3" maxlength="255" />
              <button class="btn btn-outline-primary rounded-end-3" type="button" :disabled="savingTen" @click="luuTen">
                {{ savingTen ? '...' : 'Lưu tên' }}
              </button>
            </div>

            <h6 class="fw-bold mb-2">Giá & mô tả bán</h6>
            <p class="text-muted small mb-2">Đặt 0 để miễn phí. Giá &gt; 0: học viên phải thanh toán từ ví để mở nội dung.</p>

            <div
              v-if="thanhToanThu.hienThi"
              class="rounded-3 border p-3 mb-3 bg-light-subtle small"
              style="background: #f8fafc;"
            >
              <div class="fw-semibold mb-2 text-secondary">Thanh toán từ phía học viên</div>
              <template v-if="thanhToanThu.la_tra_phi">
                <template v-if="thanhToanThu.da_mua">
                  <p class="mb-1"><span class="text-muted">Trạng thái:</span> <span class="text-success fw-semibold">Đã thanh toán</span></p>
                  <p class="mb-1"><span class="text-muted">Học viên đã trả:</span> {{ formatVnd(thanhToanThu.gia_hoc_vien_da_tra) }}</p>
                  <p class="mb-1"><span class="text-muted">Thu nhập GV (sau trừ hoa hồng):</span> <span class="fw-bold text-success">{{ formatVnd(thanhToanThu.thu_nhap_gv_sau_khau_tru) }}</span></p>
                  <p class="mb-1" v-if="thanhToanThu.phi_nen_tang != null"><span class="text-muted">Phí nền tảng (giữ lại):</span> {{ formatVnd(thanhToanThu.phi_nen_tang) }}</p>
                  <p class="mb-0 text-muted" v-if="thanhToanThu.ti_le_hoa_hong_platform != null">
                    % hoa hồng nền tảng đã áp: {{ Number(thanhToanThu.ti_le_hoa_hong_platform).toFixed(2) }}%
                  </p>
                  <p class="mb-0 mt-1 text-muted" v-if="thanhToanThu.ngay_mua">Ngày giao dịch: {{ formatNgayIso(thanhToanThu.ngay_mua) }}</p>
                </template>
                <template v-else>
                  <p class="mb-0"><span class="text-muted">Trạng thái:</span> <span class="text-warning fw-semibold">Học viên chưa thanh toán</span></p>
                  <p class="mb-0 text-muted mt-1">Thu nhập giáo viên chỉ được ghi nhận sau khi học viên thanh toán từ ví.</p>
                </template>
              </template>
              <p v-else class="mb-0 text-muted">Lộ trình miễn phí — không có giao dịch thanh toán.</p>
            </div>
            <div class="row g-2 align-items-end mb-2">
              <div class="col-md-4">
                <label class="form-label small mb-0">Giá (VNĐ)</label>
                <input v-model.number="traPhi.gia" type="number" min="0" step="1000" class="form-control rounded-3" />
              </div>
              <div class="col-md-8">
                <label class="form-label small mb-0">Mô tả</label>
                <input v-model="traPhi.mo_ta_ban" type="text" class="form-control rounded-3" placeholder="Tùy chọn" />
              </div>
            </div>
            <button type="button" class="btn btn-success rounded-3" :disabled="savingTraPhi" @click="luuTraPhi">
              {{ savingTraPhi ? 'Đang lưu...' : 'Lưu cấu hình phí' }}
            </button>
          </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
              <h6 class="fw-bold mb-0">Bài học trong lộ trình</h6>
              <div class="d-flex gap-2 flex-wrap">
                <select v-model.number="baiThemId" class="form-select form-select-sm rounded-3" style="min-width: 200px;">
                  <option :value="null" disabled>+ Thêm bài học</option>
                  <optgroup v-for="(nhom, idx) in nhomBaiHoc" :key="idx" :label="nhom.ten_danh_muc">
                    <option v-for="bh in nhom.bai_hoc" :key="bh.id" :value="bh.id">{{ bh.tieu_de }}</option>
                  </optgroup>
                </select>
                <button type="button" class="btn btn-sm btn-primary rounded-3" :disabled="!baiThemId" @click="themBai">Thêm</button>
              </div>
            </div>
            <p class="text-muted small">Kéo thả dòng để đổi thứ tự, sau đó bấm &quot;Lưu thứ tự&quot;.</p>

            <div v-if="loadingDetail" class="text-center py-4 text-muted">Đang tải chi tiết...</div>
            <div v-else>
              <div class="table-responsive">
                <table class="table align-middle">
                  <thead>
                    <tr class="text-secondary small">
                      <th style="width:36px;"></th>
                      <th>Thứ tự</th>
                      <th>Bài học</th>
                      <th>Ghi chú GV</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(row, idx) in chiTietRows"
                      :key="row.bai_hoc_id"
                      draggable="true"
                      @dragstart="onDragStart(idx)"
                      @dragover.prevent
                      @drop="onDrop(idx)"
                      class="table-light"
                      style="cursor: grab;"
                    >
                      <td class="text-muted"><i class="fa-solid fa-grip-vertical"></i></td>
                      <td>{{ idx + 1 }}</td>
                      <td class="fw-medium">{{ row.tieu_de || ('#' + row.bai_hoc_id) }}</td>
                      <td style="min-width: 200px;">
                        <input v-model="row.ghi_chu_gv" class="form-control form-control-sm rounded-3" placeholder="Dặn dò..." />
                      </td>
                      <td>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3" @click="xoaDong(idx)">Xóa</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-if="chiTietRows.length === 0" class="text-muted py-3 text-center">Chưa có bài học. Hãy thêm từ danh sách.</div>
              <button type="button" class="btn btn-primary rounded-3 mt-2" :disabled="savingChiTiet || chiTietRows.length === 0" @click="luuChiTiet">
                {{ savingChiTiet ? 'Đang lưu...' : 'Lưu thứ tự & ghi chú' }}
              </button>
            </div>
          </div>
        </div>

        <div class="mt-3">
          <button type="button" class="btn btn-outline-danger rounded-3" :disabled="saving || !selectedLoTrinhId" @click="xoaLoTrinh">
            Xóa lộ trình này
          </button>
        </div>
      </div>

      <div class="col-12" v-else>
        <div class="alert alert-light border rounded-4 shadow-sm">Chọn học viên và một lộ trình để chỉnh sửa.</div>
      </div>
    </div>

    <!-- Modal xác nhận tạo lộ trình mới -->
    <div
      v-if="modalTaoLoTrinh"
      class="modal-backdrop-qt d-flex align-items-center justify-content-center p-3"
      tabindex="-1"
      @click.self="dongModalTaoLoTrinh"
    >
      <div class="card border-0 shadow rounded-4 modal-tao-lo-trinh" role="dialog" aria-modal="true" aria-labelledby="modalTaoLoTrinhTi">
        <div class="card-body p-4">
          <h5 id="modalTaoLoTrinhTi" class="fw-bold mb-2">Tạo lộ trình mới?</h5>
          <p class="text-muted small mb-3">
            Bạn sẽ thêm một lộ trình cá nhân cho học viên đã chọn.
            <template v-if="tenHocVienDaChon">({{ tenHocVienDaChon }})</template>
          </p>
          <label class="form-label small fw-semibold mb-1">Tên lộ trình</label>
          <input v-model.trim="tenLoTrinhMoi" type="text" class="form-control rounded-3 mb-3" maxlength="255" placeholder="Lộ trình cá nhân" @keyup.enter="xacNhanTaoLoTrinh" />
          <div class="d-flex gap-2 justify-content-end flex-wrap">
            <button type="button" class="btn btn-outline-secondary rounded-3" :disabled="saving" @click="dongModalTaoLoTrinh">Hủy</button>
            <button type="button" class="btn btn-primary rounded-3" :disabled="saving || !tenLoTrinhMoi" @click="xacNhanTaoLoTrinh">
              {{ saving ? 'Đang tạo...' : 'Xác nhận tạo' }}
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
  name: 'QuanLyLoTrinh',
  data() {
    return {
      apiBase: (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, ''),
      students: [],
      hocVienId: null,
      loTrinhs: [],
      selectedLoTrinhId: null,
      tenLoTrinh: '',
      traPhi: { gia: 0, mo_ta_ban: '' },
      chiTietRows: [],
      nhomBaiHoc: [],
      baiThemId: null,
      loadingList: false,
      loadingDetail: false,
      saving: false,
      savingTen: false,
      savingTraPhi: false,
      savingChiTiet: false,
      dragIndex: null,
      modalTaoLoTrinh: false,
      tenLoTrinhMoi: 'Lộ trình cá nhân',
      thanhToanThu: {
        hienThi: false,
        la_tra_phi: false,
        da_mua: false,
        gia_hoc_vien_da_tra: null,
        thu_nhap_gv_sau_khau_tru: null,
        phi_nen_tang: null,
        ti_le_hoa_hong_platform: null,
        ngay_mua: null,
      },
    };
  },
  computed: {
    tenHocVienDaChon() {
      const hv = this.students.find((s) => s.id === this.hocVienId);
      return hv?.name || '';
    },
  },
  mounted() {
    this.taiHocVien();
    this.taiNhomBaiHoc();
  },
  methods: {
    authHeaders() {
      return { Authorization: 'Bearer ' + (localStorage.getItem('token_teacher') || '') };
    },
    formatVnd(n) {
      if (!n) return '0 đ';
      return Number(n).toLocaleString('vi-VN') + ' đ';
    },
    formatNgayIso(iso) {
      if (!iso) return '';
      try {
        return new Date(iso).toLocaleString('vi-VN', { dateStyle: 'medium', timeStyle: 'short' });
      } catch (e) {
        return iso;
      }
    },
    taiHocVien() {
      axios
        .get(this.apiBase + '/api/teacher/gv-hv/hoc-vien', { headers: this.authHeaders() })
        .then((res) => {
          if (res.data.status) {
            this.students = res.data.data || [];
          }
        })
        .catch(() => {});
    },
    taiNhomBaiHoc() {
      axios
        .get(this.apiBase + '/api/teacher/gv-hv/bai-hoc-goi-y', { headers: this.authHeaders() })
        .then((res) => {
          if (res.data.status) {
            this.nhomBaiHoc = res.data.data || [];
          }
        })
        .catch(() => {
          this.nhomBaiHoc = [];
        });
    },
    onDoiHocVien() {
      this.selectedLoTrinhId = null;
      this.chiTietRows = [];
      this.thanhToanThu = this.trangThaiThanhToanRong();
      this.taiDanhSachLoTrinh();
    },
    taiDanhSachLoTrinh() {
      if (!this.hocVienId) {
        this.loTrinhs = [];
        return Promise.resolve();
      }
      this.loadingList = true;
      return axios
        .get(this.apiBase + '/api/teacher/lo-trinh', {
          headers: this.authHeaders(),
          params: { hoc_vien_id: this.hocVienId },
        })
        .then((res) => {
          if (res.data.status) {
            this.loTrinhs = res.data.data || [];
            if (this.loTrinhs.length && !this.selectedLoTrinhId) {
              this.chonLoTrinh(this.loTrinhs[0].id);
            }
          }
        })
        .finally(() => {
          this.loadingList = false;
        });
    },
    chonLoTrinh(id) {
      this.selectedLoTrinhId = id;
      this.taiChiTiet();
    },
    taiChiTiet() {
      if (!this.selectedLoTrinhId) return;
      this.loadingDetail = true;
      this.thanhToanThu = this.trangThaiThanhToanRong();
      axios
        .get(this.apiBase + '/api/teacher/lo-trinh/' + this.selectedLoTrinhId, { headers: this.authHeaders() })
        .then((res) => {
          if (!res.data.status || !res.data.data) return;
          const d = res.data.data;
          this.tenLoTrinh = d.ten_lo_trinh || '';
          const tp = d.tra_phi;
          this.traPhi = {
            gia: tp ? tp.gia : 0,
            mo_ta_ban: tp ? tp.mo_ta_ban || '' : '',
          };
          this.chiTietRows = (d.chi_tiet || []).map((r) => ({
            bai_hoc_id: r.bai_hoc_id,
            tieu_de: r.tieu_de,
            ghi_chu_gv: r.ghi_chu_gv || '',
          }));
          const laTraPhi = Boolean(d.la_tra_phi);
          this.thanhToanThu = {
            hienThi: true,
            la_tra_phi: laTraPhi,
            da_mua: Boolean(d.da_mua),
            gia_hoc_vien_da_tra: d.gia_hoc_vien_da_tra ?? null,
            thu_nhap_gv_sau_khau_tru: d.thu_nhap_gv_sau_khau_tru ?? null,
            phi_nen_tang: d.phi_nen_tang ?? null,
            ti_le_hoa_hong_platform: d.ti_le_hoa_hong_platform ?? null,
            ngay_mua: d.ngay_mua ?? null,
          };
        })
        .finally(() => {
          this.loadingDetail = false;
        });
    },
    trangThaiThanhToanRong() {
      return {
        hienThi: false,
        la_tra_phi: false,
        da_mua: false,
        gia_hoc_vien_da_tra: null,
        thu_nhap_gv_sau_khau_tru: null,
        phi_nen_tang: null,
        ti_le_hoa_hong_platform: null,
        ngay_mua: null,
      };
    },
    moModalTaoLoTrinh() {
      if (!this.hocVienId) return;
      this.tenLoTrinhMoi = 'Lộ trình cá nhân';
      this.modalTaoLoTrinh = true;
    },
    dongModalTaoLoTrinh() {
      if (this.saving) return;
      this.modalTaoLoTrinh = false;
    },
    xacNhanTaoLoTrinh() {
      const ten = (this.tenLoTrinhMoi || '').trim();
      if (!ten || !this.hocVienId) return;
      this.saving = true;
      axios
        .post(
          this.apiBase + '/api/teacher/lo-trinh',
          { hoc_vien_id: this.hocVienId, ten_lo_trinh: ten },
          { headers: this.authHeaders() }
        )
        .then((res) => {
          if (res.data.status) {
            this.$toast.success('Đã tạo lộ trình.');
            this.modalTaoLoTrinh = false;
            const id = res.data.data?.id;
            this.taiDanhSachLoTrinh().then(() => {
              if (id) this.chonLoTrinh(id);
            });
          } else {
            this.$toast.error(res.data.message || 'Lỗi');
          }
        })
        .catch((e) => {
          this.$toast.error(e.response?.data?.message || 'Không tạo được.');
        })
        .finally(() => {
          this.saving = false;
        });
    },
    luuTen() {
      if (!this.selectedLoTrinhId) return;
      this.savingTen = true;
      axios
        .put(
          this.apiBase + '/api/teacher/lo-trinh/' + this.selectedLoTrinhId,
          { ten_lo_trinh: this.tenLoTrinh },
          { headers: this.authHeaders() }
        )
        .then((res) => {
          if (res.data.status) {
            this.$toast.success('Đã lưu.');
            this.taiDanhSachLoTrinh();
          } else this.$toast.error(res.data.message);
        })
        .finally(() => {
          this.savingTen = false;
        });
    },
    luuTraPhi() {
      if (!this.selectedLoTrinhId) return;
      this.savingTraPhi = true;
      axios
        .put(
          this.apiBase + '/api/teacher/lo-trinh/' + this.selectedLoTrinhId + '/tra-phi',
          { gia: this.traPhi.gia || 0, mo_ta_ban: this.traPhi.mo_ta_ban || null },
          { headers: this.authHeaders() }
        )
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message || 'Đã lưu.');
            this.taiDanhSachLoTrinh();
            return this.selectedLoTrinhId && this.chonLoTrinh(this.selectedLoTrinhId);
          }
          this.$toast.error(res.data.message);
        })
        .finally(() => {
          this.savingTraPhi = false;
        });
    },
    luuChiTiet() {
      if (!this.selectedLoTrinhId) return;
      const items = this.chiTietRows.map((r, i) => ({
        bai_hoc_id: r.bai_hoc_id,
        thu_tu_uu_tien: i + 1,
        ghi_chu_gv: r.ghi_chu_gv || null,
      }));
      this.savingChiTiet = true;
      axios
        .put(
          this.apiBase + '/api/teacher/lo-trinh/' + this.selectedLoTrinhId + '/chi-tiet',
          { items },
          { headers: this.authHeaders() }
        )
        .then((res) => {
          if (res.data.status) this.$toast.success(res.data.message || 'Đã lưu.');
          else this.$toast.error(res.data.message);
        })
        .catch((e) => {
          this.$toast.error(e.response?.data?.message || 'Lỗi lưu.');
        })
        .finally(() => {
          this.savingChiTiet = false;
        });
    },
    themBai() {
      if (!this.baiThemId) return;
      if (this.chiTietRows.some((r) => r.bai_hoc_id === this.baiThemId)) {
        this.$toast.error('Bài đã có trong lộ trình.');
        return;
      }
      let tieu_de = '';
      for (const nhom of this.nhomBaiHoc) {
        const f = (nhom.bai_hoc || []).find((b) => b.id === this.baiThemId);
        if (f) {
          tieu_de = f.tieu_de;
          break;
        }
      }
      this.chiTietRows.push({
        bai_hoc_id: this.baiThemId,
        tieu_de,
        ghi_chu_gv: '',
      });
      this.baiThemId = null;
    },
    xoaDong(idx) {
      this.chiTietRows.splice(idx, 1);
    },
    onDragStart(idx) {
      this.dragIndex = idx;
    },
    onDrop(idx) {
      if (this.dragIndex === null || this.dragIndex === idx) return;
      const moved = this.chiTietRows.splice(this.dragIndex, 1)[0];
      this.chiTietRows.splice(idx, 0, moved);
      this.dragIndex = null;
    },
    xoaLoTrinh() {
      if (!this.selectedLoTrinhId) return;
      if (!window.confirm('Xóa lộ trình này? Hành động không hoàn tác.')) return;
      this.saving = true;
      axios
        .delete(this.apiBase + '/api/teacher/lo-trinh/' + this.selectedLoTrinhId, { headers: this.authHeaders() })
        .then((res) => {
          if (res.data.status) {
            this.$toast.success('Đã xóa.');
            this.selectedLoTrinhId = null;
            this.chiTietRows = [];
            this.thanhToanThu = this.trangThaiThanhToanRong();
            this.taiDanhSachLoTrinh();
          } else this.$toast.error(res.data.message);
        })
        .finally(() => {
          this.saving = false;
        });
    },
  },
};
</script>

<style scoped>
.table-light:hover {
  background-color: #eef6ff !important;
}

.modal-backdrop-qt {
  position: fixed;
  inset: 0;
  z-index: 1050;
  background: rgba(15, 23, 42, 0.45);
}

.modal-tao-lo-trinh {
  width: 100%;
  max-width: 420px;
}
</style>
