<template>
  <div class="container-fluid" style="background-color: #f8f9fa; min-height: 100vh;">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-1" style="color: #2b3445;">Quản lý Tài khoản</h4>
      </div>

      <button
        class="btn btn-danger rounded-3 px-4 shadow-sm"
        data-bs-toggle="modal"
        data-bs-target="#createUserModal"
      >
        <i class="fa-solid fa-user-plus me-1"></i> Cấp tài khoản
      </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4" style="background: #fff;">
      <div class="card-body p-3">
        <div class="row g-2 align-items-center">
          <div class="col-12 col-md-4 position-relative">
            <input
              type="text"
              class="form-control ps-4"
              placeholder="Tìm theo tên, email, sđt..."
              v-model="search.keyword"
            >
          </div>
          <div class="col-12 col-md-3">
            <select class="form-select" v-model="search.vai_tro_id" style="cursor: pointer;">
              <option value="">Tất cả vai trò</option>
              <option value="1">Admin</option>
              <option value="2">Giáo viên</option>
              <option value="3">Học viên</option>
            </select>
          </div>
          <div class="col-12 col-md-3">
            <select class="form-select" v-model="search.is_block" style="cursor: pointer;">
              <option value="">Tất cả trạng thái</option>
              <option value="0">Đang hoạt động</option>
              <option value="1">Tạm khóa</option>
            </select>
          </div>
          <div class="col-12 col-md-2">
            <button class="btn btn-light w-100 border" @click="datLaiBoLoc">
              <i class="fa-solid fa-rotate-right me-1"></i> Đặt lại
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4" style="background: #fff;">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light" style="font-size: 0.85rem;">
              <tr>
                <th class="ps-4 py-3 border-bottom-0">Người dùng</th>
                <th class="py-3 border-bottom-0">Thông tin liên hệ</th>
                <th class="py-3 border-bottom-0">Vai trò</th>
                <th class="py-3 border-bottom-0">Ngày tạo</th>
                <th class="py-3 border-bottom-0">Trạng thái</th>
                <th class="pe-4 py-3 border-bottom-0 text-end">Hành động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in danh_sach_nguoi_dung" :key="user.id">
                <td class="ps-4 py-3">
                  <div class="d-flex align-items-center">
                    <div
                      class="rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 text-white shadow-sm flex-shrink-0"
                      :style="`width: 40px; height: 40px; background-color: ${layMauDaiDien(user.role)};`"
                    >
                      {{ (user.name || '?').charAt(0) }}
                    </div>
                    <div>
                      <h6 class="mb-0 fw-semibold" style="color: #2b3445;">{{ user.name }}</h6>
                    </div>
                  </div>
                </td>

                <td class="py-3">
                  <div class="text-dark mb-1" style="font-size: 0.9rem;">
                    <i class="fa-solid fa-envelope text-muted me-1" style="width: 16px;"></i> {{ user.email }}
                  </div>
                  <div class="text-dark" style="font-size: 0.9rem;">
                    <i class="fa-solid fa-phone text-muted me-1" style="width: 16px;"></i> {{ user.phone || '—' }}
                  </div>
                </td>

                <td class="py-3">
                  <span class="badge mb-1" :class="layLopHuyHieuVaiTro(user.role)">
                    <i class="fa-solid me-1" :class="layBieuTuongVaiTro(user.role)"></i> {{ user.role }}
                  </span>
                </td>

                <td class="py-3 text-muted" style="font-size: 0.9rem;">
                  {{ user.joinedAt }}
                </td>

                <td class="py-3">
                  <span class="badge rounded-pill" :class="user.status === 'Đang hoạt động' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">
                    <i class="fa-solid fa-circle me-1" style="font-size: 0.4rem; vertical-align: middle;"></i>
                    {{ user.status }}
                  </span>
                </td>

                <td class="pe-4 py-3 text-end">
                  <button
                    type="button"
                    class="btn btn-sm btn-light border me-1"
                    title="Sửa"
                    data-bs-toggle="modal"
                    data-bs-target="#editUserModal"
                    @click="chuanBiSua(user)"
                  >
                    <i class="fa-solid fa-pen text-primary"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-light border"
                    :title="user.status === 'Đang hoạt động' ? 'Khóa tài khoản' : 'Mở khóa'"
                    @click="chuanBiDaoTrangThai(user)"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmStatusModal"
                  >
                    <i class="fa-solid" :class="user.status === 'Đang hoạt động' ? 'fa-unlock text-success' : 'fa-lock text-danger'"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="danh_sach_nguoi_dung.length === 0" class="text-center py-5">
          <div class="mb-3" style="font-size: 3rem; color: #dee2e6;">
            <i class="fa-solid fa-folder-open"></i>
          </div>
          <h6 class="fw-bold mb-2">Không tìm thấy tài khoản</h6>
          <p class="text-muted mb-0">Thử thay đổi từ khóa hoặc bộ lọc.</p>
        </div>
      </div>
    </div>

    <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-danger">
            <h1 class="modal-title text-white fs-5" id="createUserModalLabel">Cấp tài khoản mới</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="datLaiFormCreate"></button>
          </div>
          <div class="modal-body text-start">
            <form @submit.prevent="themMoi">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label text-muted small fw-semibold mb-1">Vai trò hệ thống <span class="text-danger">*</span></label>
                  <select class="form-select shadow-none" v-model.number="create.vai_tro_id" required>
                    <option value="" disabled>-- Chọn vai trò --</option>
                    <option :value="1">Admin</option>
                    <option :value="2">Giáo viên</option>
                    <option :value="3">Học viên</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label text-muted small fw-semibold mb-1">Họ và tên <span class="text-danger">*</span></label>
                  <input type="text" class="form-control shadow-none" v-model="create.ho_ten" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label text-muted small fw-semibold mb-1">Số điện thoại</label>
                  <input type="tel" class="form-control shadow-none" v-model="create.sdt" maxlength="10">
                </div>
                <div class="col-md-6">
                  <label class="form-label text-muted small fw-semibold mb-1">Email <span class="text-danger">*</span></label>
                  <input type="email" class="form-control shadow-none" v-model="create.email" required>
                </div>
                <div class="col-md-12">
                  <label class="form-label text-muted small fw-semibold mb-1">Mật khẩu khởi tạo <span class="text-danger">*</span></label>
                  <input type="password" class="form-control shadow-none" v-model="create.mat_khau">
                </div>
                <div class="col-12 mt-3 ms-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="statusSwitchCreate" v-model="create.kich_hoat">
                    <label class="form-check-label text-muted small" for="statusSwitchCreate">Kích hoạt (không khóa)</label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="datLaiFormCreate">Hủy bỏ</button>
            <button type="button" class="btn btn-danger" @click="themMoi">
              <i class="fa-solid fa-user-plus me-1"></i> Tạo tài khoản
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Cập nhật tài khoản</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-start">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label text-muted small fw-semibold mb-1">Vai trò <span class="text-danger">*</span></label>
                <select class="form-select shadow-none" v-model.number="edit.vai_tro_id" required>
                  <option :value="1">Admin</option>
                  <option :value="2">Giáo viên</option>
                  <option :value="3">Học viên</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted small fw-semibold mb-1">Họ và tên</label>
                <input type="text" class="form-control shadow-none" v-model="edit.ho_ten">
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted small fw-semibold mb-1">Số điện thoại</label>
                <input type="tel" class="form-control shadow-none" v-model="edit.sdt" maxlength="10">
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted small fw-semibold mb-1">Email</label>
                <input type="email" class="form-control shadow-none" v-model="edit.email">
              </div>
              <div class="col-md-12">
                <label class="form-label text-muted small fw-semibold mb-1">Mật khẩu mới</label>
                <input type="password" class="form-control shadow-none" v-model="edit.mat_khau" placeholder="Để trống nếu không đổi">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-primary" @click="capNhat">Lưu</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="confirmStatusModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header border-bottom-0 pb-0">
            <h5 class="modal-title fw-bold">Xác nhận thao tác</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center py-4" v-if="nguoi_dung_can_doi_trang_thai">
            <div
              class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 text-white"
              :class="nguoi_dung_can_doi_trang_thai.status === 'Đang hoạt động' ? 'bg-danger' : 'bg-success'"
              style="width: 70px; height: 70px; font-size: 2rem;"
            >
              <i class="fa-solid" :class="nguoi_dung_can_doi_trang_thai.status === 'Đang hoạt động' ? 'fa-lock' : 'fa-unlock'"></i>
            </div>
            <h5 class="fw-semibold">
              Bạn có chắc chắn muốn <span :class="nguoi_dung_can_doi_trang_thai.status === 'Đang hoạt động' ? 'text-danger' : 'text-success'">{{ nguoi_dung_can_doi_trang_thai.status === 'Đang hoạt động' ? 'khóa' : 'mở khóa' }}</span>
            </h5>
            <p class="text-muted mb-0">tài khoản <strong>{{ nguoi_dung_can_doi_trang_thai.name }}</strong> không?</p>
          </div>
          <div class="modal-footer border-top-0 d-flex justify-content-center pt-0 pb-4">
            <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Hủy bỏ</button>
            <button
              type="button"
              class="btn px-4 text-white"
              :class="nguoi_dung_can_doi_trang_thai?.status === 'Đang hoạt động' ? 'btn-danger' : 'btn-success'"
              @click="thucHienDaoTrangThai"
            >
              Đồng ý
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/admin/quan-ly-tai-khoan'

export default {
  data() {
    return {
      list: [],
      search: {
        keyword: '',
        vai_tro_id: '',
        is_block: '',
      },
      create: {
        ho_ten: '',
        email: '',
        mat_khau: '',
        sdt: '',
        vai_tro_id: '',
        kich_hoat: true,
      },
      edit: {
        id: '',
        ho_ten: '',
        email: '',
        sdt: '',
        vai_tro_id: '',
        mat_khau: '',
      },
      nguoi_dung_can_doi_trang_thai: null,
    }
  },

  computed: {
    danh_sach_nguoi_dung() {
      const isBlockFilter = this.search.is_block
      return (this.list || [])
        .map((item) => this.chuanHoaDuLieuNguoiDung(item))
        .filter((user) => {
          if (isBlockFilter === '' || isBlockFilter == null) {
            return true
          }
          return Number(user.is_block) === Number(isBlockFilter)
        })
    },
  },

  mounted() {
    this.fetchDanhSach()
  },

  watch: {
    search: {
      handler() {
        this.fetchDanhSach()
      },
      deep: true,
    },
  },

  methods: {
    authHeaders() {
      return {
        Authorization: 'Bearer ' + (localStorage.getItem('token_admin') || ''),
      }
    },
    chuyenVaiTro(vai_tro_id) {
      if (Number(vai_tro_id) === 1) return 'Admin'
      if (Number(vai_tro_id) === 2) return 'Giáo viên'
      return 'Học viên'
    },

    chuanHoaDuLieuNguoiDung(item) {
      return {
        id: item.id,
        vai_tro_id: item.vai_tro_id,
        name: item.ho_ten ?? '',
        email: item.email ?? '',
        phone: item.sdt ?? '',
        role: this.chuyenVaiTro(item.vai_tro_id),
        is_block: Number(item.is_block ?? 0),
        status: Number(item.is_block ?? 0) === 1 ? 'Tạm khóa' : 'Đang hoạt động',
        joinedAt: item.ngay_tao ? new Date(item.ngay_tao).toLocaleDateString('vi-VN') : '',
      }
    },

    xuLyLoiAxios(res) {
      const errs = res.response?.data?.errors
      if (errs) {
        const list = Object.values(errs)
        list.forEach((v) => {
          this.$toast.error(v[0])
        })
        return
      }
      this.$toast.error(res.response?.data?.message || 'Có lỗi xảy ra')
    },

    thamSoTruyVan() {
      const params = {}
      const kw = (this.search.keyword || '').trim()
      if (kw !== '') {
        params.key = kw
      }
      if (this.search.vai_tro_id !== '' && this.search.vai_tro_id != null) {
        params.vai_tro_id = this.search.vai_tro_id
      }
      return params
    },

    fetchDanhSach() {
      const params = this.thamSoTruyVan()
      const coTuKhoa = params.key !== undefined && params.key !== ''
      const coVaiTro = params.vai_tro_id !== undefined && params.vai_tro_id !== ''

      let url = `${API_URL}/data`
      let requestMethod = 'get'
      let payload = {}

      if (coTuKhoa) {
        url = `${API_URL}/tim-kiem`
        requestMethod = 'post'
        payload = { key: params.key }
      } else if (coVaiTro) {
        url = `${API_URL}/filter-by-role`
        requestMethod = 'post'
        payload = { vai_tro_id: Number(params.vai_tro_id) }
      }

      axios({
        method: requestMethod,
        url,
        data: payload,
        headers: this.authHeaders(),
      })
        .then((res) => {
          let data = res.data.data || []
          if (coTuKhoa && coVaiTro) {
            data = data.filter((item) => Number(item.vai_tro_id) === Number(params.vai_tro_id))
          }
          this.list = data
        })
        .catch((res) => {
          this.xuLyLoiAxios(res)
        })
    },

    themMoi() {
      if (!this.create.ho_ten || !this.create.email || !this.create.vai_tro_id || !this.create.mat_khau) {
        this.$toast.error('Vui lòng điền đủ các trường bắt buộc')
        return
      }

      const payload = {
        ho_ten: this.create.ho_ten,
        email: this.create.email,
        sdt: this.create.sdt || null,
        vai_tro_id: this.create.vai_tro_id,
        mat_khau: this.create.mat_khau,
        is_block: this.create.kich_hoat ? 0 : 1,
      }

      axios
        .post(`${API_URL}/create`, payload, {
          headers: this.authHeaders(),
        })
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message)
            this.fetchDanhSach()
            this.create = {
              ho_ten: '',
              email: '',
              mat_khau: '',
              sdt: '',
              vai_tro_id: '',
              kich_hoat: true,
            }
            const modalEl = document.getElementById('createUserModal')
            const modal = bootstrap.Modal.getInstance(modalEl)
            if (modal) modal.hide()
          } else {
            this.$toast.error(res.data.message)
          }
        })
        .catch((res) => {
          this.xuLyLoiAxios(res)
        })
    },

    chuanBiSua(user) {
      const raw = (this.list || []).find((x) => x.id === user.id)
      this.edit = {
        id: user.id,
        ho_ten: raw?.ho_ten ?? user.name,
        email: raw?.email ?? user.email,
        sdt: raw?.sdt ?? user.phone ?? '',
        vai_tro_id: raw?.vai_tro_id ?? user.vai_tro_id,
        mat_khau: '',
      }
    },

    capNhat() {
      if (!this.edit.id) return

      const payload = {
        ho_ten: this.edit.ho_ten,
        email: this.edit.email,
        sdt: this.edit.sdt || null,
        vai_tro_id: this.edit.vai_tro_id,
      }
      if (this.edit.mat_khau) {
        payload.mat_khau = this.edit.mat_khau
      }

      axios
        .post(`${API_URL}/update`, { id: this.edit.id, ...payload }, {
          headers: this.authHeaders(),
        })
        .then((res) => {
          if (res.data.status) {
            this.fetchDanhSach()
            this.$toast.success(res.data.message)
            const modalEl = document.getElementById('editUserModal')
            const modal = bootstrap.Modal.getInstance(modalEl)
            if (modal) modal.hide()
          } else {
            this.$toast.error(res.data.message)
          }
        })
        .catch((res) => {
          this.xuLyLoiAxios(res)
        })
    },

    chuanBiDaoTrangThai(user) {
      this.nguoi_dung_can_doi_trang_thai = this.danh_sach_nguoi_dung.find((u) => u.id === user.id)
    },

    thucHienDaoTrangThai() {
      if (!this.nguoi_dung_can_doi_trang_thai) return

      const u = this.nguoi_dung_can_doi_trang_thai

      axios
        .post(`${API_URL}/change-status`, { id: u.id }, {
          headers: this.authHeaders(),
        })
        .then((res) => {
          if (res.data.status) {
            this.fetchDanhSach()
            this.$toast.success(res.data.message)
          } else {
            this.$toast.error(res.data.message)
          }

          const confirmModalEl = document.getElementById('confirmStatusModal')
          const confirmModal = bootstrap.Modal.getInstance(confirmModalEl)
          if (confirmModal) confirmModal.hide()
        })
        .catch((res) => {
          this.xuLyLoiAxios(res)
        })
    },

    datLaiFormCreate() {
      this.create = {
        ho_ten: '',
        email: '',
        mat_khau: '',
        sdt: '',
        vai_tro_id: '',
        kich_hoat: true,
      }
    },

    datLaiBoLoc() {
      this.search = {
        keyword: '',
        vai_tro_id: '',
        is_block: '',
      }
    },

    layMauDaiDien(role) {
      if (role === 'Admin') return '#dc3545'
      if (role === 'Giáo viên') return '#0d6efd'
      return '#198754'
    },

    layLopHuyHieuVaiTro(role) {
      if (role === 'Admin') return 'bg-danger text-white'
      if (role === 'Giáo viên') return 'bg-primary text-white'
      return 'bg-success text-white'
    },

    layBieuTuongVaiTro(role) {
      if (role === 'Admin') return 'fa-shield-halved'
      if (role === 'Giáo viên') return 'fa-chalkboard-user'
      return 'fa-child-reaching'
    },
  },
}
</script>
