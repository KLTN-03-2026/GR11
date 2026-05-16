<template>
  <div class="container-fluid" style="background-color: #f8f9fa; min-height: 100vh">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
      <div>
        <router-link to="/teacher/quan-ly-bai-kiem-tra" class="small text-decoration-none text-muted d-inline-block mb-1">
          <i class="fa-solid fa-arrow-left me-1"></i>Danh sách bài kiểm tra
        </router-link>
        <h4 class="fw-bold mb-0 text-primary" style="color: #2b3445">
          <i class="fa-regular fa-pen-to-square me-2"></i>Soạn đề
        </h4>
        <p v-if="quiz" class="text-muted small mb-0">
          Bài học: <span class="fw-medium text-dark">{{ quiz.bai_hoc?.tieu_de || "—" }}</span>
        </p>
      </div>
      <button type="button" class="btn btn-primary btn-sm shadow-sm" :disabled="dang_luu || loading" @click="luu">
        <span v-if="dang_luu" class="spinner-border spinner-border-sm me-1"></span>Lưu
      </button>
    </div>

    <div v-if="loading" class="text-center py-5 text-muted">
      <span class="spinner-border spinner-border-sm me-2"></span>Đang tải…
    </div>
    <div v-else-if="loi_tai" class="alert alert-danger rounded-3">{{ loi_tai }}</div>
    <template v-else-if="quiz">
      <div class="alert alert-info rounded-3 small py-2" v-if="phien_ton_tai">
        Đã có học viên làm bài — chỉ có thể sửa tiêu đề, thời gian, điểm đạt, trạng thái (không đổi danh sách câu).
      </div>

      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Thông tin đề</h6>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label small">Tiêu đề</label>
              <input v-model.trim="meta.tieu_de" type="text" class="form-control form-control-sm" maxlength="200" />
            </div>
            <div class="col-md-3">
              <label class="form-label small">Thời gian (giây)</label>
              <input v-model.number="meta.thoi_gian_gioi_han_giay" type="number" min="30" max="86400" class="form-control form-control-sm" />
            </div>
            <div class="col-md-3">
              <label class="form-label small">Điểm tối thiểu</label>
              <input v-model.number="meta.diem_toi_thieu" type="number" min="0" max="1000" class="form-control form-control-sm" />
            </div>
            <div class="col-md-3">
              <label class="form-label small">Trạng thái</label>
              <select v-model.number="meta.trang_thai" class="form-select form-select-sm">
                <option :value="0">Nháp</option>
                <option :value="1">Xuất bản</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label small">Hướng dẫn</label>
              <textarea v-model.trim="meta.mo_ta_huong_dan" class="form-control form-control-sm" rows="2"></textarea>
            </div>
          </div>
        </div>
      </div>

      <div v-if="!chinh_sua_cau" class="card border-0 shadow-sm rounded-3">
        <div class="card-body text-muted small">Chỉ cập nhật metadata — bấm Lưu để áp dụng.</div>
      </div>

      <div v-else class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
          <h6 class="fw-bold mb-0">Câu hỏi</h6>
          <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-outline-primary" @click="themCau('mcq')">
              <i class="fa-solid fa-list-ul me-1"></i>Thêm MCQ
            </button>
            <button type="button" class="btn btn-outline-success" @click="themCau('phat_am')">
              <i class="fa-solid fa-microphone me-1"></i>Thêm phát âm
            </button>
          </div>
        </div>
        <div class="card-body">
          <div v-if="!cau_hoi.length" class="text-muted small py-3 text-center">
            Chưa có câu. Thêm MCQ hoặc câu phát âm (gắn từ vựng của bài học).
          </div>
          <div v-for="(cau, idx) in cau_hoi" :key="idx" class="border rounded-3 p-3 mb-3 bg-light-subtle">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="badge bg-secondary">#{{ idx + 1 }}</span>
              <button type="button" class="btn btn-link btn-sm text-danger p-0" @click="xoaCau(idx)">Xoá</button>
            </div>
            <div v-if="cau.loai === 'mcq'" class="row g-2">
              <div class="col-12">
                <label class="form-label small">Nội dung câu</label>
                <input v-model.trim="cau.noi_dung_cau" type="text" class="form-control form-control-sm" />
              </div>
              <div class="col-md-3">
                <label class="form-label small">Điểm tối đa</label>
                <input v-model.number="cau.diem_toi_da" type="number" min="1" max="100" class="form-control form-control-sm" />
              </div>
              <div class="col-12">
                <label class="form-label small">Lựa chọn (chọn 1 đáp án đúng)</label>
                <div v-for="(lc, j) in cau.lua_chon" :key="j" class="input-group input-group-sm mb-1">
                  <div class="input-group-text">
                    <input
                      class="form-check-input mt-0"
                      type="radio"
                      :name="'mcq-dung-' + idx"
                      :checked="lc.la_dung === true"
                      @change="chonDungMcq(cau, j)"
                    />
                  </div>
                  <input v-model.trim="lc.noi_dung" type="text" class="form-control" placeholder="Nội dung đáp án" />
                  <button type="button" class="btn btn-outline-secondary" @click="xoaLuaChon(cau, j)" :disabled="cau.lua_chon.length <= 2">—</button>
                </div>
                <button type="button" class="btn btn-link btn-sm px-0" @click="themLuaChon(cau)">+ Thêm đáp án</button>
              </div>
            </div>
            <div v-else class="row g-2">
              <div class="col-md-4">
                <label class="form-label small">Điểm tối đa</label>
                <input v-model.number="cau.diem_toi_da" type="number" min="1" max="100" class="form-control form-control-sm" />
              </div>
              <div class="col-md-8">
                <label class="form-label small">Từ vựng (bài học đã gắn)</label>
                <select v-model="cau.tu_vung_id" class="form-select form-select-sm">
                  <option value="" disabled>Chọn từ…</option>
                  <option v-for="tv in tu_vung_list" :key="tv.id" :value="String(tv.id)">{{ tv.tu_chuan }}</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <p v-if="loi_luu" class="text-danger small mt-2">{{ loi_luu }}</p>
      <p v-if="thong_bao_ok" class="text-success small mt-2">{{ thong_bao_ok }}</p>
    </template>
  </div>
</template>

<script>
import { API_BASE } from '../../../../api/http.js';
import axios from "axios";

export default {
  name: "ChinhSuaBaiKiemTra",
  data() {
    return {
      apiBase: (API_BASE).replace(/\/$/, ""),
      loading: true,
      loi_tai: "",
      quiz: null,
      meta: {
        tieu_de: "",
        mo_ta_huong_dan: "",
        thoi_gian_gioi_han_giay: 600,
        diem_toi_thieu: 0,
        trang_thai: 0,
      },
      cau_hoi: [],
      tu_vung_list: [],
      ban_dau_co_cau: false,
      phien_ton_tai: false,
      dang_luu: false,
      loi_luu: "",
      thong_bao_ok: "",
    };
  },
  computed: {
    chinh_sua_cau() {
      if (this.phien_ton_tai) return false;
      return true;
    },
  },
  watch: {
    "$route.params.id"() {
      this.taiTrang();
    },
  },
  mounted() {
    this.taiTrang();
  },
  methods: {
    authHeaders() {
      const t = localStorage.getItem("token_teacher");
      return { Authorization: "Bearer " + (t || "") };
    },
    chonDungMcq(cau, j) {
      cau.lua_chon.forEach((lc, i) => {
        lc.la_dung = i === j;
      });
    },
    themLuaChon(cau) {
      cau.lua_chon.push({ noi_dung: "", la_dung: false, thu_tu: cau.lua_chon.length + 1 });
    },
    xoaLuaChon(cau, j) {
      if (cau.lua_chon.length <= 2) return;
      const was = cau.lua_chon[j].la_dung;
      cau.lua_chon.splice(j, 1);
      if (was && cau.lua_chon.length) cau.lua_chon[0].la_dung = true;
    },
    themCau(loai) {
      if (loai === "mcq") {
        this.cau_hoi.push({
          loai: "mcq",
          noi_dung_cau: "",
          diem_toi_da: 10,
          lua_chon: [
            { noi_dung: "", la_dung: true, thu_tu: 1 },
            { noi_dung: "", la_dung: false, thu_tu: 2 },
          ],
        });
      } else {
        this.cau_hoi.push({
          loai: "phat_am",
          noi_dung_cau: "",
          diem_toi_da: 10,
          tu_vung_id: "",
        });
      }
    },
    xoaCau(idx) {
      this.cau_hoi.splice(idx, 1);
    },
    mapCauFromApi(ch) {
      if (ch.loai === "mcq") {
        const lua = (ch.lua_chons || []).map((lc, i) => ({
          noi_dung: lc.noi_dung,
          la_dung: !!lc.la_dung,
          thu_tu: lc.thu_tu ?? i + 1,
        }));
        return {
          loai: "mcq",
          noi_dung_cau: ch.noi_dung_cau || "",
          diem_toi_da: ch.diem_toi_da ?? 10,
          lua_chon:
            lua.length >= 2
              ? lua
              : [
                  { noi_dung: "", la_dung: true, thu_tu: 1 },
                  { noi_dung: "", la_dung: false, thu_tu: 2 },
                ],
        };
      }
      return {
        loai: "phat_am",
        noi_dung_cau: ch.noi_dung_cau || "",
        diem_toi_da: ch.diem_toi_da ?? 10,
        tu_vung_id: ch.tu_vung_id != null && ch.tu_vung_id !== "" ? String(ch.tu_vung_id) : "",
      };
    },
    taiTuVung(baiHocId) {
      axios
        .get(this.apiBase + "/api/teacher/bai-hoc/" + baiHocId + "/tu-vung-cho-quiz", { headers: this.authHeaders() })
        .then((res) => {
          if (res.data?.status) this.tu_vung_list = res.data.data || [];
          else this.tu_vung_list = [];
        })
        .catch(() => {
          this.tu_vung_list = [];
        });
    },
    taiTrang() {
      const id = this.$route.params.id;
      if (!id) return;
      this.loading = true;
      this.loi_tai = "";
      this.thong_bao_ok = "";
      axios
        .get(this.apiBase + "/api/teacher/bai-kiem-tra/" + id, { headers: this.authHeaders() })
        .then((res) => {
          if (!res.data?.status || !res.data.data) {
            this.loi_tai = res.data?.message || "Không tải được đề.";
            this.quiz = null;
            return;
          }
          const q = res.data.data;
          this.quiz = q;
          this.meta = {
            tieu_de: q.tieu_de || "",
            mo_ta_huong_dan: q.mo_ta_huong_dan || "",
            thoi_gian_gioi_han_giay: q.thoi_gian_gioi_han_giay,
            diem_toi_thieu: q.diem_toi_thieu,
            trang_thai: q.trang_thai,
          };
          const raw = q.cau_hois || [];
          this.ban_dau_co_cau = raw.length > 0;
          this.phien_ton_tai = Number(q.phien_kiem_tras_count || 0) > 0;
          this.cau_hoi = raw.map((ch) => this.mapCauFromApi(ch));
          if (q.bai_hoc_id) this.taiTuVung(q.bai_hoc_id);
        })
        .catch((err) => {
          this.loi_tai = err.response?.data?.message || "Không tải được đề.";
          this.quiz = null;
        })
        .finally(() => {
          this.loading = false;
        });
    },
    validateClient() {
      for (let i = 0; i < this.cau_hoi.length; i++) {
        const c = this.cau_hoi[i];
        if (c.loai === "mcq") {
          if (!c.lua_chon || c.lua_chon.length < 2) return "MCQ #" + (i + 1) + ": cần ít nhất 2 đáp án.";
          const dung = c.lua_chon.filter((lc) => lc.la_dung).length;
          if (dung !== 1) return "MCQ #" + (i + 1) + ": cần đúng một đáp án đúng.";
          for (let j = 0; j < c.lua_chon.length; j++) {
            if (!String(c.lua_chon[j].noi_dung || "").trim()) return "MCQ #" + (i + 1) + ": đáp án " + (j + 1) + " trống.";
          }
        } else {
          if (!c.tu_vung_id || c.tu_vung_id === "") return "Câu phát âm #" + (i + 1) + ": chọn từ vựng.";
        }
      }
      return "";
    },
    buildPayloadCauHoi() {
      return this.cau_hoi.map((c, i) => {
        const thu_tu = i + 1;
        if (c.loai === "mcq") {
          return {
            loai: "mcq",
            thu_tu,
            noi_dung_cau: c.noi_dung_cau || null,
            diem_toi_da: Number(c.diem_toi_da),
            lua_chon: c.lua_chon.map((lc, j) => ({
              noi_dung: lc.noi_dung,
              la_dung: !!lc.la_dung,
              thu_tu: j + 1,
            })),
          };
        }
        return {
          loai: "phat_am",
          thu_tu,
          noi_dung_cau: c.noi_dung_cau || null,
          diem_toi_da: Number(c.diem_toi_da),
          tu_vung_id: Number(c.tu_vung_id),
        };
      });
    },
    luu() {
      this.loi_luu = "";
      this.thong_bao_ok = "";
      const id = this.$route.params.id;
      const metaBody = {
        tieu_de: this.meta.tieu_de || null,
        mo_ta_huong_dan: this.meta.mo_ta_huong_dan || null,
        thoi_gian_gioi_han_giay: Number(this.meta.thoi_gian_gioi_han_giay),
        diem_toi_thieu: Number(this.meta.diem_toi_thieu),
        trang_thai: Number(this.meta.trang_thai),
      };

      if (!this.chinh_sua_cau) {
        this.dang_luu = true;
        axios
          .put(
            this.apiBase + "/api/teacher/bai-kiem-tra/" + id,
            { ...metaBody, chi_cap_nhat_meta: true },
            { headers: this.authHeaders() },
          )
          .then((res) => {
            if (res.data?.status) this.thong_bao_ok = "Đã lưu.";
            else this.loi_luu = res.data?.message || "Lỗi.";
          })
          .catch((err) => {
            this.loi_luu = err.response?.data?.message || JSON.stringify(err.response?.data?.errors || {});
          })
          .finally(() => {
            this.dang_luu = false;
          });
        return;
      }

      if (this.cau_hoi.length === 0) {
        if (this.ban_dau_co_cau) {
          this.loi_luu = "Không thể xoá hết câu đã có trên máy chủ bằng giao diện này. Thêm ít nhất một câu.";
          return;
        }
        this.dang_luu = true;
        axios
          .put(
            this.apiBase + "/api/teacher/bai-kiem-tra/" + id,
            { ...metaBody, chi_cap_nhat_meta: true },
            { headers: this.authHeaders() },
          )
          .then((res) => {
            if (res.data?.status) this.thong_bao_ok = "Đã lưu thông tin đề (chưa có câu hỏi).";
            else this.loi_luu = res.data?.message || "Lỗi.";
          })
          .catch((err) => {
            this.loi_luu = err.response?.data?.message || "Lỗi.";
          })
          .finally(() => {
            this.dang_luu = false;
          });
        return;
      }

      const err = this.validateClient();
      if (err) {
        this.loi_luu = err;
        return;
      }

      const body = {
        ...metaBody,
        cau_hoi: this.buildPayloadCauHoi(),
      };
      this.dang_luu = true;
      axios
        .put(this.apiBase + "/api/teacher/bai-kiem-tra/" + id, body, { headers: this.authHeaders() })
        .then((res) => {
          if (res.data?.status) {
            this.thong_bao_ok = "Đã lưu bài kiểm tra.";
            this.taiTrang();
          } else this.loi_luu = res.data?.message || "Lỗi.";
        })
        .catch((err) => {
          this.loi_luu = err.response?.data?.message || JSON.stringify(err.response?.data?.errors || {});
        })
        .finally(() => {
          this.dang_luu = false;
        });
    },
  },
};
</script>
