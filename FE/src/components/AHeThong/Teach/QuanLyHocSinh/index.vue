<template>
    <div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
  
      <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
          <h4 class="fw-bold mb-1 text-dark">
            <i class="fa-solid fa-user-graduate text-primary me-2"></i>Quản lý Học viên
          </h4>
          <p class="text-muted mb-0" style="font-size: 0.95rem;">
            Danh sách các học viên do bạn phụ trách hướng dẫn luyện âm
          </p>
        </div>
        <div class="d-flex gap-3 flex-wrap align-items-center">
          <div class="position-relative">
            <i class="fa-solid fa-magnifying-glass position-absolute text-muted" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
            <input type="text" class="form-control border-0 shadow-sm" placeholder="Tìm tên, email..." v-model="searchQuery"
                   style="width: 280px; padding-left: 40px; border-radius: 50rem; background-color: #ffffff; padding-top: 0.6rem; padding-bottom: 0.6rem;">
          </div>
          <button type="button" class="btn bg-white border-0 shadow-sm text-primary fw-medium rounded-pill px-4 py-2" style="transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f1f5f9'" onmouseout="this.style.backgroundColor='#ffffff'" title="Tải lại danh sách" @click="taiDanhSach" :disabled="loading">
            <i class="fa-solid fa-rotate-right me-1" :class="{'fa-spin': loading}"></i> Làm mới
          </button>
        </div>
      </div>
  
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background-color: #ffffff;">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0 border-0">
              <thead style="background-color: #f1f5f9;">
                <tr>
                  <th class="ps-4 py-3 text-secondary fw-bold text-uppercase border-0" style="font-size: 0.75rem; letter-spacing: 0.5px;">Học viên</th>
                  <th class="py-3 text-secondary fw-bold text-uppercase border-0" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tiến độ luyện tập</th>
                  <th class="py-3 text-secondary fw-bold text-uppercase border-0" style="font-size: 0.75rem; letter-spacing: 0.5px;">Lỗi cần chú ý</th>
                  <th class="py-3 text-secondary fw-bold text-uppercase border-0 text-center" style="font-size: 0.75rem; letter-spacing: 0.5px;">Hoạt động cuối</th>
                  <th class="pe-4 py-3 text-secondary fw-bold text-uppercase border-0 text-end" style="font-size: 0.75rem; letter-spacing: 0.5px;">Thao tác</th>
                </tr>
              </thead>
  
              <tbody>
                <tr v-if="loading">
                  <td colspan="5" class="text-center py-5 text-muted border-0">
                    <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
                    <div class="fw-medium">Đang tải danh sách học viên...</div>
                  </td>
                </tr>
  
                <tr v-else-if="filteredStudents.length === 0">
                  <td colspan="5" class="text-center py-5 border-0">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 70px; height: 70px;">
                      <i class="fa-solid fa-users-slash fs-2 text-secondary opacity-50"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Không có dữ liệu</h6>
                    <p class="text-muted small mb-0" v-if="students.length === 0">Bạn chưa được phân công phụ trách học viên nào.</p>
                    <p class="text-muted small mb-0" v-else>Không tìm thấy học viên phù hợp với từ khóa "{{ searchQuery }}".</p>
                  </td>
                </tr>
  
                <tr v-for="student in filteredStudents" :key="student.id" style="transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                  <td class="ps-4 py-3" style="border-bottom: 1px solid #f1f5f9;">
                    <div class="d-flex align-items-center">
                      <img :src="avatarUrl(student)" alt="avatar" class="rounded-circle me-3 shadow-sm border" width="48" height="48" style="object-fit: cover;">
                      <div>
                        <h6 class="mb-0 fw-bold text-dark">{{ student.name }}</h6>
                        <small class="text-muted"><i class="fa-regular fa-envelope me-1"></i>{{ student.email }}</small>
                      </div>
                    </div>
                  </td>
  
                  <td class="py-3" style="border-bottom: 1px solid #f1f5f9;">
                    <div class="d-flex align-items-center mb-1">
                      <span class="text-muted small me-2" style="width: 60px;">Điểm TB:</span>
                      <span class="badge rounded-pill fw-bold" :class="getScoreColor(student.score)">
                        {{ student.score }}%
                      </span>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="text-muted small me-2" style="width: 60px;">Số bài:</span>
                      <span class="fw-medium text-dark small">{{ student.sessions }} phiên</span>
                    </div>
                  </td>
  
                  <td class="py-3" style="border-bottom: 1px solid #f1f5f9;">
                    <div class="d-flex flex-wrap gap-1">
                      <span v-for="(err, index) in student.errors" :key="index" class="badge rounded-pill fw-medium border" :class="err.class" style="padding: 5px 10px;">
                        {{ err.text }}
                      </span>
                    </div>
                    <span v-if="!student.errors || student.errors.length === 0" class="badge rounded-pill bg-light text-secondary border fw-medium px-3 py-1">
                      <i class="fa-solid fa-check text-success me-1"></i>Ổn định
                    </span>
                  </td>
  
                  <td class="py-3 text-center" style="border-bottom: 1px solid #f1f5f9;">
                    <div class="fw-semibold" :class="student.lastActiveColor" style="font-size: 0.9rem;">{{ student.lastActiveLabel }}</div>
                    <small class="text-muted">{{ student.lastActiveTime }}</small>
                  </td>
  
                  <td class="pe-4 py-3 text-end" style="border-bottom: 1px solid #f1f5f9;">
                    <div class="d-flex justify-content-end gap-2">
                      <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center border-0 bg-transparent text-primary" 
                              style="width: 38px; height: 38px; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#e0e7ff'" onmouseout="this.style.backgroundColor='transparent'"
                              data-bs-toggle="modal" data-bs-target="#chiTietModal" title="Xem chi tiết hồ sơ" @click="xemChiTiet(student)">
                        <i class="fa-regular fa-file-lines fs-5"></i>
                      </button>
                      <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center border-0 bg-transparent text-success" 
                              style="width: 38px; height: 38px; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#dcfce7'" onmouseout="this.style.backgroundColor='transparent'"
                              data-bs-toggle="modal" data-bs-target="#goiYModal" title="Gợi ý bài học" @click="goiYChoHocVien(student)">
                        <i class="fa-regular fa-lightbulb fs-5"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
  
      <div class="modal fade" id="chiTietModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content border-0 shadow-lg rounded-4" v-if="selectedStudent">
            
            <div class="modal-header bg-light border-bottom-0 p-4">
              <div class="d-flex align-items-center">
                <img :src="avatarUrl(selectedStudent)" alt="avatar" class="rounded-circle me-3 shadow-sm border border-2 border-white" width="65" height="65" style="object-fit: cover;">
                <div>
                  <h5 class="modal-title fw-bold text-dark mb-1">{{ selectedStudent.name }}</h5>
                  <p class="text-muted mb-0 small fw-medium">
                    <i class="fa-regular fa-envelope me-1"></i> {{ selectedStudent.email }}
                    <span class="mx-2 text-light">|</span>
                    <i class="fa-solid fa-phone me-1"></i> {{ selectedStudent.phone || 'Chưa cập nhật' }}
                  </p>
                </div>
              </div>
              <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
  
            <div class="modal-body p-4">
              <div v-if="loadingDetail" class="text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-2" role="status"></div>
                <div>Đang tải hồ sơ chi tiết...</div>
              </div>
              
              <template v-else>
                <div class="row g-3 mb-4">
                  <div class="col-md-4">
                    <div class="p-3 bg-white rounded-4 border shadow-sm h-100 d-flex align-items-center">
                      <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-star text-primary fs-5"></i>
                      </div>
                      <div>
                        <h6 class="text-muted mb-1 small fw-semibold text-uppercase">Điểm Trung Bình</h6>
                        <h4 class="fw-bold mb-0" :class="getScoreColor(selectedStudent.score)">{{ selectedStudent.score }}%</h4>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="p-3 bg-white rounded-4 border shadow-sm h-100 d-flex align-items-center">
                      <div class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-headphones text-success fs-5"></i>
                      </div>
                      <div>
                        <h6 class="text-muted mb-1 small fw-semibold text-uppercase">Đã Luyện Tập</h6>
                        <h4 class="text-dark fw-bold mb-0">{{ selectedStudent.sessions }} <span class="fs-6 text-muted fw-normal">phiên</span></h4>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="p-3 bg-white rounded-4 border shadow-sm h-100 d-flex align-items-center">
                      <div class="rounded-circle bg-warning-subtle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-clock-rotate-left text-warning fs-5"></i>
                      </div>
                      <div>
                        <h6 class="text-muted mb-1 small fw-semibold text-uppercase">Thời gian tích lũy</h6>
                        <h4 class="text-dark fw-bold mb-0">{{ selectedStudent.totalTime || '0p' }}</h4>
                      </div>
                    </div>
                  </div>
                </div>
  
                <div class="mb-4 bg-light p-3 rounded-4 border border-warning-subtle">
                  <h6 class="fw-bold text-dark mb-3">
                    <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>Lỗi phát âm cần khắc phục
                  </h6>
                  <div class="d-flex flex-wrap gap-2">
                    <span v-for="(err, index) in selectedStudent.commonMistakes" :key="index" class="badge bg-white text-danger border border-danger-subtle px-3 py-2 fw-medium shadow-sm">
                      {{ err }}
                    </span>
                    <span v-if="!selectedStudent.commonMistakes || selectedStudent.commonMistakes.length === 0" class="text-success fw-medium small">
                      <i class="fa-solid fa-check-circle me-1"></i>Học viên phát âm khá tốt, chưa ghi nhận lỗi hệ thống.
                    </span>
                  </div>
                </div>
  
                <div>
                  <h6 class="fw-bold text-dark mb-3">
                    <i class="fa-solid fa-timeline text-secondary me-2"></i>Lịch sử luyện tập gần đây
                  </h6>
                  <div class="list-group list-group-flush border rounded-4 overflow-hidden shadow-sm">
                    <div v-for="(history, index) in selectedStudent.history" :key="index" class="list-group-item p-3 d-flex justify-content-between align-items-center border-bottom" :class="{ 'bg-light': index % 2 !== 0 }">
                      <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                          <i class="fa-solid fa-microphone-lines text-primary small"></i>
                        </div>
                        <div>
                          <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">{{ history.title }}</h6>
                          <small class="text-muted">{{ history.time }}</small>
                        </div>
                      </div>
                      <span class="badge rounded-pill px-3 py-2 border" :class="history.score >= 80 ? 'bg-success-subtle text-success border-success-subtle' : 'bg-warning-subtle text-warning border-warning-subtle text-darken'">
                        {{ history.score }} / 100đ
                      </span>
                    </div>
                    <div v-if="!selectedStudent.history || selectedStudent.history.length === 0" class="list-group-item text-muted text-center py-4 bg-light">
                      Chưa có dữ liệu phiên luyện tập nào.
                    </div>
                  </div>
                </div>
              </template>
            </div>
            <div class="modal-footer bg-light border-top-0 p-3">
              <button type="button" class="btn btn-light border px-4 fw-medium rounded-pill" data-bs-dismiss="modal">Đóng</button>
              <button type="button" class="btn text-white px-4 fw-medium rounded-pill shadow-sm" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#goiYModal" @click="goiYChoHocVien(selectedStudent)" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fa-regular fa-lightbulb me-1"></i> Gợi ý bài học
              </button>
            </div>
          </div>
        </div>
      </div>
  
      <div class="modal fade" id="goiYModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg rounded-4">
            
            <div class="modal-header bg-light border-bottom-0 p-4 pb-3">
              <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                <div class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                  <i class="fa-regular fa-lightbulb text-success fs-5"></i>
                </div>
                <div>
                  Giao bài luyện tập
                  <div v-if="selectedStudent" class="text-muted fw-medium" style="font-size: 0.85rem;">Học viên: <span class="text-primary">{{ selectedStudent.name }}</span></div>
                </div>
              </h5>
              <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
  
            <div class="modal-body p-4 pt-2">
              <div class="alert bg-primary-subtle border-0 text-primary d-flex align-items-start p-3 mb-4 rounded-3">
                <i class="fa-solid fa-circle-info fs-5 me-2 mt-1"></i>
                <div class="small fw-medium">
                  Bài học được gợi ý sẽ xuất hiện nổi bật trên trang chủ của học viên, giúp bé tập trung khắc phục lỗi nhanh hơn.
                </div>
              </div>
  
              <div v-if="loadingBaiHoc" class="text-center text-muted mb-3">
                <span class="spinner-border spinner-border-sm me-2"></span>Đang tải thư viện bài học...
              </div>
  
              <form @submit.prevent="guiGoiY">
                <div class="mb-4">
                  <label class="form-label fw-bold text-dark">Chọn bài học <span class="text-danger">*</span></label>
                  <select class="form-select rounded-3 bg-light border-0 shadow-none py-2" v-model.number="goiYForm.bai_hoc_id" required :disabled="loadingBaiHoc || nhomBaiHoc.length === 0" style="cursor: pointer;">
                    <option :value="null" disabled>-- Nhấp để chọn bài học --</option>
                    <optgroup v-for="(nhom, idx) in nhomBaiHoc" :key="idx" :label="nhom.ten_danh_muc">
                      <option v-for="bh in nhom.bai_hoc" :key="bh.id" :value="bh.id">{{ bh.tieu_de }}</option>
                    </optgroup>
                  </select>
                </div>
  
                <div class="mb-4">
                  <label class="form-label fw-bold text-dark d-block">Mức độ ưu tiên</label>
                  <div class="d-flex gap-3 mt-2">
                    <label class="btn btn-outline-danger rounded-pill px-4 fw-medium" :class="{'active': goiYForm.uu_tien === 'cao'}">
                      <input class="form-check-input d-none" type="radio" v-model="goiYForm.uu_tien" value="cao">
                      <i class="fa-solid fa-fire-flame-curved me-1"></i> Ưu tiên cao
                    </label>
                    <label class="btn btn-outline-success rounded-pill px-4 fw-medium" :class="{'active': goiYForm.uu_tien === 'binh_thuong'}">
                      <input class="form-check-input d-none" type="radio" v-model="goiYForm.uu_tien" value="binh_thuong">
                      <i class="fa-regular fa-circle-check me-1"></i> Bình thường
                    </label>
                  </div>
                </div>
  
                <div class="mb-2">
                  <label class="form-label fw-bold text-dark">Lời nhắn giáo viên (Tùy chọn)</label>
                  <textarea class="form-control rounded-3 bg-light border-0 shadow-none p-3" rows="3" v-model="goiYForm.loi_nhan"
                    placeholder="Ví dụ: Bé nhớ mở khẩu hình miệng to hơn khi đọc chữ A nhé..." style="resize: none;"></textarea>
                </div>
              </form>
            </div>
  
            <div class="modal-footer bg-light border-top-0 p-3">
              <button type="button" class="btn btn-light border px-4 fw-medium rounded-pill" data-bs-dismiss="modal">Hủy</button>
              <button type="button" class="btn text-white px-4 fw-medium rounded-pill shadow-sm" :disabled="sendingGoiY || !goiYForm.hoc_vien_id" @click="guiGoiY" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <span v-if="sendingGoiY" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="fa-regular fa-paper-plane me-2"></i> Gửi bài cho bé
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
  name: 'StudentManagement',
  data() {
    return {
      apiBase: (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, ''),
      searchQuery: '',
      students: [],
      loading: false,
      loadingDetail: false,
      selectedStudent: null,
      nhomBaiHoc: [],
      loadingBaiHoc: false,
      sendingGoiY: false,
      goiYForm: {
        hoc_vien_id: null,
        bai_hoc_id: null,
        uu_tien: 'cao',
        loi_nhan: '',
      },
    };
  },
  computed: {
    filteredStudents() {
      if (!this.searchQuery.trim()) {
        return this.students;
      }
      const q = this.searchQuery.toLowerCase().trim();
      return this.students.filter(
        (s) =>
          (s.name && s.name.toLowerCase().includes(q)) ||
          (s.email && s.email.toLowerCase().includes(q))
      );
    },
  },
  mounted() {
    this.taiDanhSach();
    this.taiNhomBaiHoc();
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
    avatarUrl(student) {
      if (student && student.avatar) {
        return student.avatar;
      }
      const n = encodeURIComponent((student && student.name) || 'HV');
      return `https://ui-avatars.com/api/?name=${n}&background=random`;
    },
    taiDanhSach() {
      this.loading = true;
      axios
        .get(this.apiBase + '/api/teacher/gv-hv/hoc-vien', {
          headers: this.authHeaders(),
        })
        .then((res) => {
          if (res.data.status) {
            this.students = res.data.data || [];
          } else {
            this.$toast.error(res.data.message || 'Không tải được danh sách.');
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
          this.loading = false;
        });
    },
    taiNhomBaiHoc() {
      this.loadingBaiHoc = true;
      axios
        .get(this.apiBase + '/api/teacher/gv-hv/bai-hoc-goi-y', { headers: this.authHeaders() })
        .then((res) => {
          if (res.data.status) {
            this.nhomBaiHoc = res.data.data || [];
          }
        })
        .catch(() => {
          this.nhomBaiHoc = [];
        })
        .finally(() => {
          this.loadingBaiHoc = false;
        });
    },
    getScoreColor(score) {
      if (score >= 80) return 'text-success';
      if (score >= 60) return 'text-warning';
      return 'text-danger';
    },
    xemChiTiet(student) {
      this.selectedStudent = {
        ...student,
        commonMistakes: student.commonMistakes || [],
        history: student.history || [],
        totalTime: student.totalTime || '0p',
      };
      this.loadingDetail = true;
      axios
        .get(this.apiBase + '/api/teacher/gv-hv/hoc-vien/' + student.id, {
          headers: this.authHeaders(),
        })
        .then((res) => {
          if (res.data.status && res.data.data) {
            this.selectedStudent = res.data.data;
          } else {
            this.$toast.error(res.data.message || 'Không tải được chi tiết.');
          }
        })
        .catch((err) => {
          this.toastLoiAxios(err);
        })
        .finally(() => {
          this.loadingDetail = false;
        });
    },
    goiYChoHocVien(student) {
      this.selectedStudent = student;
      this.goiYForm.hoc_vien_id = student.id;
      this.goiYForm.bai_hoc_id = null;
      this.goiYForm.uu_tien = 'cao';
      this.goiYForm.loi_nhan = '';
    },
    guiGoiY() {
      if (!this.goiYForm.hoc_vien_id) {
        this.$toast.error('Chưa chọn học viên.');
        return;
      }
      if (this.goiYForm.bai_hoc_id == null) {
        this.$toast.error('Vui lòng chọn bài học.');
        return;
      }
      this.sendingGoiY = true;
      axios
        .post(
          this.apiBase + '/api/teacher/gv-hv/goi-y',
          {
            hoc_vien_id: this.goiYForm.hoc_vien_id,
            bai_hoc_id: this.goiYForm.bai_hoc_id,
            uu_tien: this.goiYForm.uu_tien,
            loi_nhan: (this.goiYForm.loi_nhan || '').trim() || null,
          },
          { headers: this.authHeaders() }
        )
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message || 'Đã gửi gợi ý.');
            this.dongModalTheoId('goiYModal');
          } else {
            this.$toast.error(res.data.message || 'Không gửi được.');
          }
        })
        .catch((err) => {
          this.toastLoiAxios(err);
        })
        .finally(() => {
          this.sendingGoiY = false;
        });
    },
  },
};
</script>

<style scoped>
.table th {
  font-weight: 600;
  color: #555;
  padding-top: 15px;
  padding-bottom: 15px;
}

.table td {
  vertical-align: middle;
  padding-top: 12px;
  padding-bottom: 12px;
}

.badge {
  font-weight: 500;
}
</style>
