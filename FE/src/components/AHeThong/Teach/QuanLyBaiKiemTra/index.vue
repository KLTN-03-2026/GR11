<template>
  <div class="container-fluid" style="background-color: #f8f9fa; min-height: 100vh">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-1 text-primary" style="color: #2b3445">
          <i class="fa-solid fa-clipboard-question me-2"></i>Bài kiểm tra
        </h4>
        <p class="text-muted mb-0" style="font-size: 0.9rem">
          Tạo đề gắn với bài học đã duyệt, soạn câu hỏi sau khi tạo khung.
        </p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 h-100" style="background: #fff">
          <div
            class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2"
          >
            <h6 class="fw-bold mb-0">
              <i class="fa-solid fa-list-check text-success me-2"></i>
              Danh sách đề
            </h6>
            <button
              type="button"
              class="btn btn-primary btn-sm shadow-sm"
              data-bs-toggle="modal"
              data-bs-target="#modalTaoBaiKiemTra"
              @click="moModalTao"
            >
              <i class="fa-solid fa-plus me-1"></i>Tạo bài kiểm tra
            </button>
          </div>

          <div class="card-body p-4 p-md-5">
            <div v-if="loading" class="text-center py-5 my-4 text-muted">
              <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem"></div>
              <h6 class="fw-semibold">Đang tải danh sách bài kiểm tra...</h6>
            </div>

            <div
              v-else-if="!danh_sach.length"
              class="text-center py-5 my-4 border rounded-4"
              style="border-style: dashed !important; background-color: #f8fafc; border-color: #cbd5e1 !important"
            >
              <div
                class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm mb-3"
                style="width: 80px; height: 80px"
              >
                <i class="fa-solid fa-clipboard-question fs-2" style="color: #667eea"></i>
              </div>
              <h5 class="fw-bold text-dark mb-2">Chưa có bài kiểm tra nào</h5>
              <p class="text-muted mb-4 small">Tạo đề mới gắn với bài học đã duyệt, sau đó soạn câu hỏi trong màn sửa.</p>
              <button
                type="button"
                class="btn text-white px-4 py-2 shadow-sm rounded-pill fw-medium border-0"
                data-bs-toggle="modal"
                data-bs-target="#modalTaoBaiKiemTra"
                @click="moModalTao"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)"
              >
                <i class="fa-solid fa-plus me-2"></i>Tạo bài kiểm tra đầu tiên
              </button>
            </div>

            <div v-else class="row g-4">
              <div v-for="q in danh_sach" :key="q.id" class="col-xl-6 col-lg-6">
                <div
                  class="card h-100 border-0 shadow-sm rounded-4 transition-all hover-card-quiz"
                  style="cursor: pointer; border: 1px solid transparent !important"
                  role="button"
                  tabindex="0"
                  @click="suaQuiz(q)"
                  @keydown.enter.prevent="suaQuiz(q)"
                >
                  <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                      <div
                        class="rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                        style="background-color: #eef2ff; width: 64px; height: 64px"
                      >
                        <i class="fa-solid fa-clipboard-list fs-3" style="color: #667eea"></i>
                      </div>
                      <div class="flex-grow-1" style="min-width: 0">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                          <h6 class="fw-bold text-truncate mb-0 pe-2 fs-6 text-dark" style="line-height: 1.4" :title="q.tieu_de || '—'">
                            {{ q.tieu_de || "—" }}
                          </h6>
                          <div class="dropdown flex-shrink-0" @click.stop>
                            <button
                              type="button"
                              class="btn text-muted p-0 border-0 bg-transparent d-flex align-items-center justify-content-center"
                              data-bs-toggle="dropdown"
                              style="width: 30px; height: 30px; border-radius: 50%"
                              @mouseover="(e) => (e.currentTarget.style.backgroundColor = '#f1f5f9')"
                              @mouseout="(e) => (e.currentTarget.style.backgroundColor = 'transparent')"
                            >
                              <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-1 py-2">
                              <li>
                                <a class="dropdown-item py-2 fw-medium" href="#" @click.prevent="suaQuiz(q)">
                                  <i class="fa-regular fa-pen-to-square me-2 text-center" style="color: #667eea; width: 20px"></i>
                                  Sửa đề
                                </a>
                              </li>
                              <li>
                                <a class="dropdown-item py-2 fw-medium" href="#" @click.prevent="moModalKetQua(q)">
                                  <i class="fa-solid fa-chart-line me-2 text-center" style="color: #0d6efd; width: 20px"></i>
                                  Xem kết quả học viên
                                </a>
                              </li>
                              <li><hr class="dropdown-divider opacity-25 my-1" /></li>
                              <li>
                                <a
                                  class="dropdown-item py-2 fw-medium text-danger"
                                  :class="{
                                    disabled: (q.phien_kiem_tras_count ?? 0) > 0 || xoa_dang_chay === q.id,
                                  }"
                                  href="#"
                                  @click.prevent="(q.phien_kiem_tras_count ?? 0) === 0 && xoa_dang_chay !== q.id && xoaBaiKiemTra(q)"
                                >
                                  <span v-if="xoa_dang_chay === q.id" class="spinner-border spinner-border-sm me-2"></span>
                                  <i v-else class="fa-regular fa-trash-can me-2 text-center" style="width: 20px"></i>
                                  Xóa đề
                                </a>
                              </li>
                            </ul>
                          </div>
                        </div>
                        <p class="text-secondary small mb-3 text-truncate" style="opacity: 0.85" :title="q.bai_hoc?.tieu_de || ''">
                          <i class="fa-solid fa-book-open me-1 opacity-75"></i>
                          {{ q.bai_hoc?.tieu_de || "Chưa gắn tiêu đề bài học" }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-auto">
                          <span
                            class="badge rounded-pill px-2 py-1 fw-medium d-flex align-items-center gap-1 border"
                            :style="
                              q.trang_thai === 1
                                ? 'font-size: 0.8rem; background-color: #dcfce7; color: #166534; border-color: #bbf7d0 !important;'
                                : 'font-size: 0.8rem; background-color: #f1f5f9; color: #475569; border-color: #e2e8f0 !important;'
                            "
                          >
                            <i class="fa-solid" :class="q.trang_thai === 1 ? 'fa-circle-check' : 'fa-file-pen'"></i>
                            {{ q.trang_thai === 1 ? "Xuất bản" : "Nháp" }}
                          </span>
                          <span
                            v-if="(q.phien_kiem_tras_count ?? 0) > 0"
                            class="badge rounded-pill bg-light text-secondary border px-2 py-1 fw-medium"
                            style="font-size: 0.75rem"
                          >
                            <i class="fa-solid fa-user-check me-1 opacity-75"></i>
                            {{ q.phien_kiem_tras_count }} lượt làm
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

    <!-- Modal tạo -->
    <div id="modalTaoBaiKiemTra" class="modal fade" tabindex="-1" aria-labelledby="modalTaoBaiKiemTraLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-3">
          <div class="modal-header bg-light border-bottom-0">
            <h5 id="modalTaoBaiKiemTraLabel" class="modal-title fw-bold">
              <i class="fa-solid fa-clipboard-list text-primary me-2"></i>
              Tạo bài kiểm tra (không câu hỏi)
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-medium">Tìm bài học đã duyệt</label>
              <input
                v-model.trim="tim_bai_hoc"
                type="text"
                class="form-control form-control-sm"
                placeholder="Theo tiêu đề…"
                @input="debounceTaiBaiHoc"
              />
            </div>
            <div v-if="loading_bai_hoc" class="text-center py-4 text-muted">
              <span class="spinner-border spinner-border-sm me-2"></span>Đang tải danh sách bài học...
            </div>
            <div v-else class="list-group list-group-flush gap-2 mb-3 quiz-bh-picker">
              <button
                v-for="bh in bai_hoc_trang"
                :key="bh.id"
                type="button"
                class="list-group-item list-group-item-action border-0 rounded-3 d-flex justify-content-between align-items-start p-3 transition-all text-start"
                :class="{
                  'bg-primary text-white shadow-sm': bai_hoc_chon?.id === bh.id,
                  'bg-light text-dark': bai_hoc_chon?.id !== bh.id,
                }"
                @click="bai_hoc_chon = bh"
              >
                <div>
                  <i
                    class="fa-solid fa-book me-2"
                    :style="bai_hoc_chon?.id === bh.id ? 'color: #fff' : 'color: #6c757d'"
                  ></i>
                  <span class="fw-medium">{{ bh.tieu_de }}</span>
                  <div class="small mt-1" :class="bai_hoc_chon?.id === bh.id ? 'text-white opacity-75' : 'text-muted'">
                    {{ bh.danh_muc?.ten_danh_muc || "—" }} · GV: {{ bh.nguoi_tao?.ho_ten || "—" }}
                  </div>
                </div>
              </button>
              <div v-if="!bai_hoc_trang.length" class="list-group-item border-0 text-muted small rounded-3">
                Không có bài học phù hợp.
              </div>
            </div>
            <div v-if="meta_bai_hoc.last_page > 1" class="d-flex justify-content-between align-items-center mb-4">
              <button type="button" class="btn btn-sm btn-outline-secondary" :disabled="trang_bai_hoc <= 1" @click="trang_bai_hoc--; taiBaiHocHoatDong()">
                Trước
              </button>
              <span class="small text-muted">Trang {{ trang_bai_hoc }} / {{ meta_bai_hoc.last_page }}</span>
              <button
                type="button"
                class="btn btn-sm btn-outline-secondary"
                :disabled="trang_bai_hoc >= meta_bai_hoc.last_page"
                @click="trang_bai_hoc++; taiBaiHocHoatDong()"
              >
                Sau
              </button>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-medium">Tiêu đề <span class="text-muted fw-normal small">(tuỳ chọn)</span></label>
                <input v-model.trim="form_tao.tieu_de" type="text" class="form-control" maxlength="200" />
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Thời gian làm bài (giây)</label>
                <input v-model.number="form_tao.thoi_gian_gioi_han_giay" type="number" min="30" max="86400" class="form-control" />
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Điểm tối thiểu đạt</label>
                <input v-model.number="form_tao.diem_toi_thieu" type="number" min="0" max="1000" class="form-control" />
              </div>
              <div class="col-md-6">
                <label class="form-label fw-medium">Trạng thái đề</label>
                <select v-model.number="form_tao.trang_thai" class="form-select">
                  <option :value="0">Nháp</option>
                  <option :value="1">Xuất bản</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label fw-medium">Hướng dẫn <span class="text-muted fw-normal small">(tuỳ chọn)</span></label>
                <textarea v-model.trim="form_tao.mo_ta_huong_dan" class="form-control" rows="3"></textarea>
              </div>
            </div>
            <p v-if="loi_tao" class="text-danger small mt-3 mb-0">{{ loi_tao }}</p>
          </div>
          <div class="modal-footer bg-light border-top-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Huỷ</button>
            <button type="button" class="btn btn-primary shadow-sm" :disabled="tao_dang_chay || !bai_hoc_chon" @click="taoBaiKiemTra">
              <span v-if="tao_dang_chay" class="spinner-border spinner-border-sm me-1"></span>
              Tạo
            </button>
          </div>
        </div>
      </div>
    </div>

    <div id="modalKetQuaBaiKiemTra" class="modal fade" tabindex="-1" aria-labelledby="modalKetQuaBaiKiemTraLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-3">
          <div class="modal-header bg-light border-bottom-0">
            <h5 id="modalKetQuaBaiKiemTraLabel" class="modal-title fw-bold">
              <i class="fa-solid fa-square-poll-vertical text-primary me-2"></i>
              Kết quả học viên
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body p-4">
            <p class="text-muted mb-3">
              Đề: <strong>{{ quizDangXemKetQua?.tieu_de || "—" }}</strong>
            </p>
            <div v-if="loadingKetQua" class="text-center py-4 text-muted">
              <span class="spinner-border spinner-border-sm me-2"></span>Đang tải kết quả...
            </div>
            <div v-else-if="loiKetQua" class="alert alert-warning mb-0">{{ loiKetQua }}</div>
            <div v-else-if="!danhSachKetQua.length" class="text-muted small">Chưa có học viên nào nộp bài kiểm tra này.</div>
            <div v-else class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>Học viên</th>
                    <th style="width: 150px">Điểm</th>
                    <th style="width: 180px">Kết quả</th>
                    <th style="width: 220px">Thời gian nộp</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="r in danhSachKetQua" :key="r.phien_kiem_tra_id">
                    <td>
                      <div class="fw-semibold">{{ r.hoc_vien?.ho_ten || "—" }}</div>
                      <div class="small text-muted">{{ r.hoc_vien?.email || "" }}</div>
                    </td>
                    <td class="fw-semibold">{{ r.tong_diem ?? 0 }}</td>
                    <td>
                      <span
                        class="badge rounded-pill px-3 py-2"
                        :class="r.dat ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'"
                      >
                        {{ r.dat ? "Đạt" : "Chưa đạt" }}
                      </span>
                    </td>
                    <td>{{ dinhDangThoiGian(r.thoi_gian_ket_thuc) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer bg-light border-top-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { API_BASE } from '../../../../api/http.js';
import axios from "axios";

let _debounce = null;

export default {
  name: "QuanLyBaiKiemTra",
  data() {
    return {
      apiBase: (API_BASE).replace(/\/$/, ""),
      loading: false,
      danh_sach: [],
      loading_bai_hoc: false,
      bai_hoc_trang: [],
      meta_bai_hoc: { current_page: 1, last_page: 1, per_page: 15, total: 0 },
      trang_bai_hoc: 1,
      tim_bai_hoc: "",
      bai_hoc_chon: null,
      form_tao: {
        tieu_de: "",
        mo_ta_huong_dan: "",
        thoi_gian_gioi_han_giay: 600,
        diem_toi_thieu: 0,
        trang_thai: 0,
      },
      loi_tao: "",
      tao_dang_chay: false,
      xoa_dang_chay: null,
      quizDangXemKetQua: null,
      loadingKetQua: false,
      loiKetQua: "",
      danhSachKetQua: [],
    };
  },
  mounted() {
    this.taiDanhSach();
  },
  methods: {
    authHeaders() {
      const t = localStorage.getItem("token_teacher");
      return { Authorization: "Bearer " + (t || "") };
    },
    dinhDangThoiGian(iso) {
      if (!iso) return "—";
      const d = new Date(iso);
      if (Number.isNaN(d.getTime())) return "—";
      return d.toLocaleString("vi-VN");
    },
    moModalKetQua(quiz) {
      this.quizDangXemKetQua = quiz || null;
      this.loiKetQua = "";
      this.danhSachKetQua = [];
      this.loadingKetQua = false;
      const el = document.getElementById("modalKetQuaBaiKiemTra");
      if (el && window.bootstrap?.Modal) {
        const M = window.bootstrap.Modal;
        const inst = M.getInstance(el) || new M(el);
        inst.show();
      }
      if (quiz?.id) {
        this.taiKetQuaHocVien(quiz.id);
      }
    },
    taiKetQuaHocVien(baiKiemTraId) {
      this.loadingKetQua = true;
      this.loiKetQua = "";
      axios
        .get(this.apiBase + "/api/teacher/bai-kiem-tra/" + baiKiemTraId + "/ket-qua", {
          headers: this.authHeaders(),
        })
        .then((res) => {
          if (res.data?.status) {
            this.danhSachKetQua = Array.isArray(res.data.data) ? res.data.data : [];
          } else {
            this.danhSachKetQua = [];
            this.loiKetQua = res.data?.message || "Không tải được dữ liệu kết quả.";
          }
        })
        .catch((err) => {
          this.danhSachKetQua = [];
          this.loiKetQua = err.response?.data?.message || "Không tải được dữ liệu kết quả.";
        })
        .finally(() => {
          this.loadingKetQua = false;
        });
    },
    suaQuiz(q) {
      this.$router.push("/teacher/quan-ly-bai-kiem-tra/chinh-sua/" + q.id);
    },
    taiDanhSach() {
      this.loading = true;
      axios
        .get(this.apiBase + "/api/teacher/bai-kiem-tra", { headers: this.authHeaders() })
        .then((res) => {
          if (res.data?.status) this.danh_sach = res.data.data || [];
          else this.danh_sach = [];
        })
        .catch(() => {
          this.danh_sach = [];
        })
        .finally(() => {
          this.loading = false;
        });
    },
    moModalTao() {
      this.bai_hoc_chon = null;
      this.loi_tao = "";
      this.trang_bai_hoc = 1;
      this.tim_bai_hoc = "";
      this.form_tao = {
        tieu_de: "",
        mo_ta_huong_dan: "",
        thoi_gian_gioi_han_giay: 600,
        diem_toi_thieu: 0,
        trang_thai: 0,
      };
      this.taiBaiHocHoatDong();
    },
    debounceTaiBaiHoc() {
      if (_debounce) clearTimeout(_debounce);
      _debounce = setTimeout(() => {
        this.trang_bai_hoc = 1;
        this.taiBaiHocHoatDong();
      }, 350);
    },
    /** Gom mảng lỗi Laravel validation `{ field: ["msg"] }` thành một chuỗi hiển thị. */
    noiDungLoiTuErrors(errors) {
      if (!errors || typeof errors !== "object") return "";
      const msgs = [];
      Object.values(errors).forEach((v) => {
        if (Array.isArray(v)) msgs.push(...v.filter(Boolean));
        else if (typeof v === "string" && v) msgs.push(v);
      });
      return msgs.join(" ");
    },
    taiBaiHocHoatDong() {
      this.loading_bai_hoc = true;
      const params = { page: this.trang_bai_hoc, per_page: 15 };
      if (this.tim_bai_hoc) params.q = this.tim_bai_hoc;
      axios
        .get(this.apiBase + "/api/teacher/bai-hoc-hoat-dong", {
          headers: this.authHeaders(),
          params,
        })
        .then((res) => {
          if (res.data?.status) {
            this.bai_hoc_trang = res.data.data || [];
            this.meta_bai_hoc = res.data.meta || this.meta_bai_hoc;
          } else {
            this.bai_hoc_trang = [];
          }
        })
        .catch(() => {
          this.bai_hoc_trang = [];
        })
        .finally(() => {
          this.loading_bai_hoc = false;
        });
    },
    taoBaiKiemTra() {
      if (!this.bai_hoc_chon) return;
      this.loi_tao = "";
      this.tao_dang_chay = true;
      let thoi = Number(this.form_tao.thoi_gian_gioi_han_giay);
      if (!Number.isFinite(thoi)) thoi = 600;
      thoi = Math.min(86400, Math.max(30, thoi));
      let diem = Number(this.form_tao.diem_toi_thieu);
      if (!Number.isFinite(diem)) diem = 0;
      diem = Math.min(1000, Math.max(0, diem));
      let trangThai = Number(this.form_tao.trang_thai);
      if (!Number.isFinite(trangThai) || (trangThai !== 0 && trangThai !== 1)) trangThai = 0;
      const payload = {
        tieu_de: this.form_tao.tieu_de || null,
        mo_ta_huong_dan: this.form_tao.mo_ta_huong_dan || null,
        thoi_gian_gioi_han_giay: thoi,
        diem_toi_thieu: diem,
        trang_thai: trangThai,
      };
      axios
        .post(this.apiBase + "/api/teacher/bai-hoc/" + this.bai_hoc_chon.id + "/bai-kiem-tra", payload, {
          headers: this.authHeaders(),
        })
        .then((res) => {
          if (res.data?.status && res.data.data?.id) {
            const id = res.data.data.id;
            const el = document.getElementById("modalTaoBaiKiemTra");
            if (el && window.bootstrap?.Modal) {
              const M = window.bootstrap.Modal;
              const inst = M.getInstance(el) || new M(el);
              inst.hide();
            }
            this.$router.push("/teacher/quan-ly-bai-kiem-tra/chinh-sua/" + id).catch(() => {});
          } else {
            const d = res.data;
            this.loi_tao =
              d?.message || this.noiDungLoiTuErrors(d?.errors) || "Không tạo được.";
          }
        })
        .catch((err) => {
          if (!err.response) {
            if (err.code === "ERR_NETWORK" || err.message === "Network Error") {
              this.loi_tao = "Lỗi mạng hoặc máy chủ.";
            }
            return;
          }
          const d = err.response.data;
          this.loi_tao =
            (d && d.message) ||
            this.noiDungLoiTuErrors(d?.errors) ||
            (err.response.status === 404 ? "Bài học không tồn tại hoặc chưa hợp lệ." : "") ||
            "Không tạo được.";
        })
        .finally(() => {
          this.tao_dang_chay = false;
        });
    },
    xoaBaiKiemTra(q) {
      if ((q.phien_kiem_tras_count ?? 0) > 0) return;
      if (!window.confirm("Xóa vĩnh viễn bài kiểm tra này? Thao tác không hoàn tác.")) return;
      this.xoa_dang_chay = q.id;
      axios
        .delete(this.apiBase + "/api/teacher/bai-kiem-tra/" + q.id, { headers: this.authHeaders() })
        .then((res) => {
          if (res.data?.status) this.taiDanhSach();
          else alert(res.data?.message || "Không xóa được.");
        })
        .catch((err) => {
          const msg = err.response?.data?.message || "Không xóa được.";
          alert(msg);
        })
        .finally(() => {
          this.xoa_dang_chay = null;
        });
    },
  },
};
</script>

<style scoped>
.transition-all {
  transition: all 0.2s ease-in-out;
}

.quiz-bh-picker {
  max-height: 220px;
  overflow-y: auto;
}

.quiz-bh-picker::-webkit-scrollbar {
  width: 5px;
}

.quiz-bh-picker::-webkit-scrollbar-thumb {
  background-color: #dee2e6;
  border-radius: 10px;
}

.hover-card-quiz {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-card-quiz:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
  border-color: #e2e8f0 !important;
}
</style>
