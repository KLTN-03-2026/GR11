<template>
  <div class="error-review-page">
    <div class="header">
      <h1>Ôn Tập Lỗi Sai</h1>
      <p>Xem lại những lỗi phát âm của bạn và ôn tập để cải thiện kỹ năng</p>
    </div>

    <div v-if="statistics" class="statistics-section">
      <div class="stat-card">
        <div class="stat-number">{{ statistics.tong_so_loi }}</div>
        <div class="stat-label">Tổng Lỗi</div>
      </div>
      <div class="stat-card warning">
        <div class="stat-number">{{ statistics.chua_on_tap }}</div>
        <div class="stat-label">Chưa Ôn Tập</div>
      </div>
      <div class="stat-card info">
        <div class="stat-number">{{ statistics.dang_on_tap }}</div>
        <div class="stat-label">Đang Ôn Tập</div>
      </div>
      <div class="stat-card success">
        <div class="stat-number">{{ statistics.da_phat_hien_on_tap }}</div>
        <div class="stat-label">Đã Ôn Tập</div>
      </div>
    </div>

    <div class="filter-section">
      <div class="filter-group">
        <label>Trạng Thái</label>
        <select v-model="filterStatus" class="filter-select">
          <option value="">Tất Cả</option>
          <option value="chua_on_tap">Chưa Ôn Tập</option>
          <option value="dang_on_tap">Đang Ôn Tập</option>
          <option value="da_phat_hien_on_tap">Đã Ôn Tập</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Loại Lỗi</label>
        <select v-model="filterErrorType" class="filter-select">
          <option value="">Tất Cả</option>
          <option value="am_dau">Lỗi Âm Đầu</option>
          <option value="van">Lỗi Vần</option>
          <option value="thanh_dieu">Lỗi Thanh Điệu</option>
        </select>
      </div>
      <button type="button" class="btn-reset" @click="resetFilters">Xóa Bộ Lọc</button>
    </div>

    <div class="tabs-section">
      <button
        type="button"
        :class="['tab-btn', { active: activeTab === 'errors' }]"
        @click="activeTab = 'errors'"
      >
        Danh Sách Lỗi
      </button>
      <button
        type="button"
        :class="['tab-btn', { active: activeTab === 'bookmarks' }]"
        @click="activeTab = 'bookmarks'"
      >
        Bookmark ({{ bookmarkStats?.tong_so_bookmark || 0 }})
      </button>
    </div>

    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Đang tải dữ liệu...</p>
    </div>

    <div v-else-if="error" class="error-message">
      <p>{{ error }}</p>
      <button type="button" class="btn-retry" @click="loadData">Thử Lại</button>
    </div>

    <div v-else class="content">
      <div v-if="activeTab === 'errors'" class="errors-list">
        <div v-if="errors.length === 0" class="empty-state">
          <p>Không có lỗi nào để ôn tập</p>
        </div>
        <div v-else class="errors-grid">
          <div
            v-for="err in errors"
            :key="err.id"
            class="error-card"
          >
            <div class="card-header">
              <div class="word-info">
                <div v-if="err.tu_vung.hinh_anh_url" class="word-image">
                  <img :src="err.tu_vung.hinh_anh_url" :alt="err.tu_vung.tu_chuan" />
                </div>
                <div class="word-text">
                  <h3>{{ err.tu_vung.tu_chuan }}</h3>
                  <p class="phien-am">{{ err.tu_vung.phien_am }}</p>
                  <p class="bai-hoc">{{ err.bai_hoc.tieu_de }}</p>
                </div>
              </div>
              <div :class="['status-badge', statusClass(err.trang_thai)]">
                {{ statusLabel(err.trang_thai) }}
              </div>
            </div>

            <div class="card-body">
              <div class="error-info">
                <div class="info-item">
                  <span class="label">Loại Lỗi:</span>
                  <span class="value">{{ errorTypeLabel(err.loai_loi) }}</span>
                </div>
                <div class="info-item">
                  <span class="label">Số Lần Mắc:</span>
                  <span class="value">{{ err.so_lan_mac_loi }} lần</span>
                </div>
                <div class="info-item">
                  <span class="label">Lần Gần Nhất:</span>
                  <span class="value">{{ formatDateVi(err.lan_mac_loi_gan_nhat) }}</span>
                </div>
              </div>
              <div v-if="err.chi_tiet_loi" class="detail-block error-detail">
                <p class="detail-label">Chi Tiết Lỗi:</p>
                <p class="detail-text">{{ err.chi_tiet_loi }}</p>
              </div>
              <div v-if="err.tu_vung.am_thanh_mau_url" class="audio-controls">
                <button type="button" class="btn-play btn-play-primary" @click="playUrl(err.tu_vung.am_thanh_mau_url)">
                  <span class="icon">🔊</span>
                  Nghe Phát Âm Chuẩn
                </button>
              </div>
            </div>

            <div class="card-actions error-actions">
              <div class="action-group">
                <button
                  v-if="err.trang_thai === 'chua_on_tap'"
                  type="button"
                  class="btn-action btn-start"
                  :disabled="statusUpdatingErrorId === err.id"
                  @click="updateErrorStatus(err, 'dang_on_tap')"
                >
                  <span v-if="statusUpdatingErrorId !== err.id">Bắt Đầu Ôn Tập</span>
                  <span v-else>Đang xử lý...</span>
                </button>
                <button
                  v-else-if="err.trang_thai === 'dang_on_tap'"
                  type="button"
                  class="btn-action btn-completed"
                  :disabled="statusUpdatingErrorId === err.id"
                  @click="updateErrorStatus(err, 'da_phat_hien_on_tap')"
                >
                  <span v-if="statusUpdatingErrorId !== err.id">✓ Hoàn Thành Ôn Tập</span>
                  <span v-else>Đang xử lý...</span>
                </button>
                <button
                  v-else
                  type="button"
                  class="btn-action btn-reviewed"
                  disabled
                >
                  ✓ Đã Ôn Tập
                </button>
              </div>
              <button
                type="button"
                class="btn-action btn-bookmark"
                :disabled="bookmarkBusyTuVungId === err.tu_vung.id"
                @click="toggleErrorBookmark(err)"
              >
                {{ bookmarkIdForTuVung(err.tu_vung.id) ? '★ Đã Bookmark' : '☆ Bookmark' }}
              </button>
              <button
                type="button"
                class="btn-action btn-delete"
                :disabled="deletingErrorId === err.id"
                @click="deleteErrorItem(err)"
              >
                <span v-if="deletingErrorId !== err.id">🗑️ Xóa</span>
                <span v-else>Đang xóa...</span>
              </button>
            </div>
          </div>
        </div>
        <div v-if="pagination && pagination.last_page > 1" class="pagination">
          <button
            v-if="pagination.current_page > 1"
            type="button"
            class="btn-pagination"
            @click="previousPage"
          >
            ← Trang Trước
          </button>
          <span class="page-info">
            Trang {{ pagination.current_page }} / {{ pagination.last_page }}
          </span>
          <button
            v-if="pagination.current_page < pagination.last_page"
            type="button"
            class="btn-pagination"
            @click="nextPage"
          >
            Trang Sau →
          </button>
        </div>
      </div>

      <div v-if="activeTab === 'bookmarks'" class="bookmarks-list">
        <div v-if="bookmarks.length === 0" class="empty-state">
          <p>Bạn chưa bookmark từ nào</p>
        </div>
        <div v-else class="bookmarks-grid">
          <div
            v-for="bm in bookmarks"
            :key="bm.id"
            class="bookmark-card"
          >
            <div class="card-header">
              <div class="word-info">
                <div v-if="bm.tu_vung.hinh_anh_url" class="word-image">
                  <img :src="bm.tu_vung.hinh_anh_url" :alt="bm.tu_vung.tu_chuan" />
                </div>
                <div class="word-text">
                  <h3>{{ bm.tu_vung.tu_chuan }}</h3>
                  <p class="phien-am">{{ bm.tu_vung.phien_am }}</p>
                  <p class="bai-hoc">{{ bm.bai_hoc.tieu_de }}</p>
                </div>
              </div>
              <div :class="['priority-badge', priorityClass(bm.muc_do_uu_tien)]">
                {{ priorityLabel(bm.muc_do_uu_tien) }}
              </div>
            </div>

            <div class="card-body">
              <div class="bookmark-info">
                <div class="info-item">
                  <span class="label">Ngày Đánh Dấu:</span>
                  <span class="value">{{ formatDateVi(bm.ngay_danh_dau) }}</span>
                </div>
                <div class="info-item">
                  <span class="label">Trạng Thái:</span>
                  <span :class="['status', bm.da_hoan_thanh ? 'completed' : 'pending']">
                    {{ bm.da_hoan_thanh ? '✓ Đã Hoàn Thành' : 'Chưa Hoàn Thành' }}
                  </span>
                </div>
              </div>
              <div v-if="bm.ghi_chu" class="detail-block bookmark-note">
                <p class="detail-label">Ghi Chú:</p>
                <p class="detail-text">{{ bm.ghi_chu }}</p>
              </div>
              <div v-if="bm.tu_vung.am_thanh_mau_url" class="audio-controls">
                <button type="button" class="btn-play btn-play-teal" @click="playUrl(bm.tu_vung.am_thanh_mau_url)">
                  <span class="icon">🔊</span>
                  Nghe Phát Âm
                </button>
              </div>
            </div>

            <div class="card-actions bookmark-actions">
              <button
                v-if="!bm.da_hoan_thanh"
                type="button"
                class="btn-action btn-mark-completed"
                @click="startBookmarkReview(bm)"
              >
                Ôn Tập Ngay
              </button>
              <button
                v-else
                type="button"
                class="btn-action btn-mark-completed completed"
                disabled
              >
                ✓ Đã Hoàn Thành
              </button>
              <button type="button" class="btn-action btn-edit" @click="openBookmarkEdit(bm)">
                ✎ Chỉnh Sửa
              </button>
              <button
                type="button"
                class="btn-action btn-delete"
                :disabled="bookmarkRowDeletingId === bm.id"
                @click="deleteBookmarkRow(bm)"
              >
                <span v-if="bookmarkRowDeletingId !== bm.id">🗑️ Xóa</span>
                <span v-else>Đang xóa...</span>
              </button>
            </div>
          </div>
        </div>
        <div v-if="bookmarkPagination && bookmarkPagination.last_page > 1" class="pagination">
          <button
            v-if="bookmarkPagination.current_page > 1"
            type="button"
            class="btn-pagination"
            @click="previousBookmarkPage"
          >
            ← Trang Trước
          </button>
          <span class="page-info">
            Trang {{ bookmarkPagination.current_page }} / {{ bookmarkPagination.last_page }}
          </span>
          <button
            v-if="bookmarkPagination.current_page < bookmarkPagination.last_page"
            type="button"
            class="btn-pagination"
            @click="nextBookmarkPage"
          >
            Trang Sau →
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: bookmark từ thẻ lỗi -->
    <div v-if="showErrorBookmarkModal" class="modal-overlay" @click="closeErrorBookmarkModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Bookmark Từ Vựng</h3>
          <button type="button" class="btn-close" @click="closeErrorBookmarkModal">✕</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Mức Độ Ưu Tiên</label>
            <select v-model="errorBookmarkForm.muc_do_uu_tien" class="form-control">
              <option value="thap">Thấp</option>
              <option value="binh_thuong">Bình Thường</option>
              <option value="cao">Cao</option>
            </select>
          </div>
          <div class="form-group">
            <label>Ghi Chú (Tùy Chọn)</label>
            <textarea
              v-model="errorBookmarkForm.ghi_chu"
              class="form-control"
              rows="3"
              placeholder="Viết ghi chú về từ này..."
            ></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" @click="closeErrorBookmarkModal">Hủy</button>
          <button type="button" class="btn-primary" :disabled="errorBookmarkSaving" @click="saveErrorBookmark">
            <span v-if="!errorBookmarkSaving">Lưu Bookmark</span>
            <span v-else>Đang lưu...</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: chỉnh sửa bookmark -->
    <div v-if="showBookmarkEditModal" class="modal-overlay" @click="closeBookmarkEditModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Chỉnh Sửa Bookmark</h3>
          <button type="button" class="btn-close" @click="closeBookmarkEditModal">✕</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Mức Độ Ưu Tiên</label>
            <select v-model="editBookmarkForm.muc_do_uu_tien" class="form-control">
              <option value="thap">Thấp</option>
              <option value="binh_thuong">Bình Thường</option>
              <option value="cao">Cao</option>
            </select>
          </div>
          <div class="form-group">
            <label>Ghi Chú (Tùy Chọn)</label>
            <textarea
              v-model="editBookmarkForm.ghi_chu"
              class="form-control"
              rows="3"
              placeholder="Viết ghi chú về từ này..."
            ></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" @click="closeBookmarkEditModal">Hủy</button>
          <button type="button" class="btn-primary-teal" :disabled="editBookmarkSaving" @click="saveBookmarkEdit">
            <span v-if="!editBookmarkSaving">Lưu Thay Đổi</span>
            <span v-else>Đang lưu...</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { API_BASE } from '../../../api/http.js';
import axios from 'axios';

export default {
  data() {
    return {
      activeTab: 'errors',
      errors: [],
      bookmarks: [],
      statistics: null,
      bookmarkStats: null,
      loading: false,
      error: null,
      filterStatus: '',
      filterErrorType: '',
      currentPage: 1,
      pagination: null,
      bookmarkPage: 1,
      bookmarkPagination: null,
      bookmarkIdByTuVungId: {},
      statusUpdatingErrorId: null,
      deletingErrorId: null,
      bookmarkBusyTuVungId: null,
      errorBookmarkSaving: false,
      showErrorBookmarkModal: false,
      errorBookmarkTarget: null,
      errorBookmarkForm: {
        muc_do_uu_tien: 'binh_thuong',
        ghi_chu: '',
      },
      bookmarkRowDeletingId: null,
      showBookmarkEditModal: false,
      editBookmarkTarget: null,
      editBookmarkSaving: false,
      editBookmarkForm: {
        muc_do_uu_tien: 'binh_thuong',
        ghi_chu: '',
      },
    };
  },
  watch: {
    filterStatus() {
      this.currentPage = 1;
      this.loadErrors();
    },
    filterErrorType() {
      this.currentPage = 1;
      this.loadErrors();
    },
    activeTab(newTab) {
      if (newTab === 'bookmarks') {
        this.loadBookmarks();
      }
    },
  },
  mounted() {
    this.loadData();
  },
  methods: {
    authHeaders() {
      const token =
        localStorage.getItem('token_nguoi_dung') ||
        localStorage.getItem('token_admin') ||
        localStorage.getItem('token_teacher') ||
        '';
      const headers = { Accept: 'application/json' };
      if (token) {
        headers.Authorization = `Bearer ${token}`;
      }
      return headers;
    },
    bookmarkIdForTuVung(tuVungId) {
      return this.bookmarkIdByTuVungId[tuVungId] ?? null;
    },
    mergeBookmarkIdsFromBookmarks(list) {
      list.forEach((b) => {
        if (b.tu_vung?.id != null) {
          this.bookmarkIdByTuVungId[b.tu_vung.id] = b.id;
        }
      });
    },
    async loadData() {
      this.loading = true;
      this.error = null;
      try {
        await Promise.all([
          this.loadErrors(),
          this.loadStatistics(),
          this.loadBookmarkStats(),
        ]);
      } catch (err) {
        this.error = 'Có lỗi xảy ra. Vui lòng thử lại.';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    async loadErrors() {
      try {
        const params = {
          page: this.currentPage,
        };
        if (this.filterStatus) {
          params.trang_thai = this.filterStatus;
        }
        if (this.filterErrorType) {
          params.loai_loi = this.filterErrorType;
        }

        const { data: body } = await axios.get(`${API_BASE}/api/error-history/by-status`, {
          params,
          headers: this.authHeaders(),
        });
        if (body.status) {
          this.errors = body.data;
          this.pagination = body.pagination;
        } else {
          this.error = body.message || 'Lỗi khi tải danh sách lỗi';
        }
      } catch (err) {
        const msg = err.response?.data?.message;
        this.error = msg || 'Lỗi khi tải danh sách lỗi';
        console.error(err);
      }
    },
    async loadStatistics() {
      try {
        const { data: body } = await axios.get(`${API_BASE}/api/error-history/statistics`, {
          headers: this.authHeaders(),
        });
        if (body.status) {
          this.statistics = body.data;
        }
      } catch (err) {
        console.error(err);
      }
    },
    async loadBookmarks() {
      try {
        const { data: body } = await axios.get(`${API_BASE}/api/bookmarks`, {
          params: { page: this.bookmarkPage },
          headers: this.authHeaders(),
        });
        if (body.status) {
          this.bookmarks = body.data;
          this.bookmarkPagination = body.pagination;
          this.mergeBookmarkIdsFromBookmarks(this.bookmarks);
        } else {
          this.error = body.message || 'Lỗi khi tải bookmark';
        }
      } catch (err) {
        this.error = 'Lỗi khi tải bookmark';
        console.error(err);
      }
    },
    async loadBookmarkStats() {
      try {
        const { data: body } = await axios.get(`${API_BASE}/api/bookmarks/statistics`, {
          headers: this.authHeaders(),
        });
        if (body.status) {
          this.bookmarkStats = body.data;
        }
      } catch (err) {
        console.error(err);
      }
    },
    resetFilters() {
      this.filterStatus = '';
      this.filterErrorType = '';
      this.currentPage = 1;
    },
    nextPage() {
      if (this.pagination && this.currentPage < this.pagination.last_page) {
        this.currentPage += 1;
        this.loadErrors();
      }
    },
    previousPage() {
      if (this.currentPage > 1) {
        this.currentPage -= 1;
        this.loadErrors();
      }
    },
    nextBookmarkPage() {
      if (this.bookmarkPagination && this.bookmarkPage < this.bookmarkPagination.last_page) {
        this.bookmarkPage += 1;
        this.loadBookmarks();
      }
    },
    previousBookmarkPage() {
      if (this.bookmarkPage > 1) {
        this.bookmarkPage -= 1;
        this.loadBookmarks();
      }
    },
    async updateErrorStatus(err, newStatus) {
      this.statusUpdatingErrorId = err.id;
      const reviewContext = { from: 'error', errorId: err.id };
      try {
        if (newStatus === 'da_phat_hien_on_tap') {
          this.goToVocabularyReview(err.tu_vung?.id, err.bai_hoc?.id, reviewContext);
          return;
        }
        const { data: body } = await axios.patch(
          `${API_BASE}/api/error-history/${err.id}/status`,
          { trang_thai: newStatus },
          { headers: this.authHeaders() },
        );
        if (body.status) {
          const idx = this.errors.findIndex((e) => e.id === err.id);
          if (idx !== -1) {
            this.errors.splice(idx, 1);
          }
          await this.loadStatistics();
          if (newStatus === 'dang_on_tap') {
            this.goToVocabularyReview(err.tu_vung?.id, err.bai_hoc?.id, reviewContext);
          }
        } else {
          alert(body.message || 'Lỗi khi cập nhật trạng thái');
        }
      } catch (e) {
        console.error(e);
        alert('Lỗi khi cập nhật trạng thái');
      } finally {
        this.statusUpdatingErrorId = null;
      }
    },
    async deleteErrorItem(err) {
      if (!confirm('Bạn chắc chắn muốn xóa lỗi này?')) {
        return;
      }
      this.deletingErrorId = err.id;
      try {
        const { data: body } = await axios.delete(`${API_BASE}/api/error-history/${err.id}`, {
          headers: this.authHeaders(),
        });
        if (body.status) {
          await this.loadData();
        } else {
          alert(body.message || 'Lỗi khi xóa');
        }
      } catch (e) {
        console.error(e);
        alert('Lỗi khi xóa');
      } finally {
        this.deletingErrorId = null;
      }
    },
    toggleErrorBookmark(err) {
      const bid = this.bookmarkIdForTuVung(err.tu_vung.id);
      if (bid) {
        this.removeBookmarkForTuVung(err.tu_vung.id, bid);
      } else {
        this.errorBookmarkTarget = err;
        this.errorBookmarkForm = {
          muc_do_uu_tien: 'binh_thuong',
          ghi_chu: '',
        };
        this.showErrorBookmarkModal = true;
      }
    },
    closeErrorBookmarkModal() {
      this.showErrorBookmarkModal = false;
      this.errorBookmarkTarget = null;
      this.errorBookmarkForm = {
        muc_do_uu_tien: 'binh_thuong',
        ghi_chu: '',
      };
    },
    async saveErrorBookmark() {
      if (!this.errorBookmarkTarget) {
        return;
      }
      this.errorBookmarkSaving = true;
      try {
        const { data: body } = await axios.post(
          `${API_BASE}/api/bookmarks`,
          {
            tu_vung_id: this.errorBookmarkTarget.tu_vung.id,
            bai_hoc_id: this.errorBookmarkTarget.bai_hoc.id,
            muc_do_uu_tien: this.errorBookmarkForm.muc_do_uu_tien,
            ghi_chu: this.errorBookmarkForm.ghi_chu,
          },
          { headers: this.authHeaders() },
        );
        if (body.status && body.data?.id != null) {
          this.bookmarkIdByTuVungId[this.errorBookmarkTarget.tu_vung.id] = body.data.id;
          this.closeErrorBookmarkModal();
          await this.loadBookmarkStats();
        } else {
          alert(body.message || 'Lỗi khi bookmark');
        }
      } catch (e) {
        console.error(e);
        alert('Lỗi khi bookmark');
      } finally {
        this.errorBookmarkSaving = false;
      }
    },
    async removeBookmarkForTuVung(tuVungId, bookmarkId) {
      if (!confirm('Bạn muốn bỏ bookmark từ này?')) {
        return;
      }
      this.bookmarkBusyTuVungId = tuVungId;
      try {
        const { data: body } = await axios.delete(`${API_BASE}/api/bookmarks/${bookmarkId}`, {
          headers: this.authHeaders(),
        });
        if (body.status) {
          delete this.bookmarkIdByTuVungId[tuVungId];
          await this.loadBookmarkStats();
        } else {
          alert(body.message || 'Lỗi khi bỏ bookmark');
        }
      } catch (e) {
        console.error(e);
        alert('Lỗi khi bỏ bookmark');
      } finally {
        this.bookmarkBusyTuVungId = null;
      }
    },
    playUrl(url) {
      if (url) {
        new Audio(url).play();
      }
    },
    goToVocabularyReview(tuVungId, baiHocId, reviewContext = {}) {
      const vocabId = Number(tuVungId);
      const lessonId = Number(baiHocId);
      if (!vocabId || !lessonId) {
        alert('Không tìm thấy bài học hoặc từ vựng để ôn tập.');
        return;
      }
      const query = {
        tu_vung_id: String(vocabId),
        section: 'tu-vung',
      };
      if (reviewContext.from) {
        query.review_from = reviewContext.from;
      }
      if (reviewContext.errorId) {
        query.error_id = String(reviewContext.errorId);
      }
      if (reviewContext.bookmarkId) {
        query.bookmark_id = String(reviewContext.bookmarkId);
      }
      this.$router.push({
        path: `/chi-tiet-bai-hoc/${lessonId}`,
        query,
      });
    },
    statusLabel(status) {
      const labels = {
        chua_on_tap: 'Chưa Ôn Tập',
        dang_on_tap: 'Đang Ôn Tập',
        da_phat_hien_on_tap: 'Đã Ôn Tập',
      };
      return labels[status] || status;
    },
    statusClass(status) {
      const map = {
        chua_on_tap: 'warning',
        dang_on_tap: 'info',
        da_phat_hien_on_tap: 'success',
      };
      return map[status] || '';
    },
    errorTypeLabel(type) {
      const labels = {
        am_dau: 'Lỗi Âm Đầu',
        van: 'Lỗi Vần',
        thanh_dieu: 'Lỗi Thanh Điệu',
      };
      return labels[type] || type;
    },
    formatDateVi(dateVal) {
      if (!dateVal) {
        return '-';
      }
      const d = new Date(dateVal);
      return d.toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
      });
    },
    priorityLabel(priority) {
      const labels = {
        thap: 'Thấp',
        binh_thuong: 'Bình Thường',
        cao: 'Cao',
      };
      return labels[priority] || priority;
    },
    priorityClass(priority) {
      const map = {
        thap: 'low',
        binh_thuong: 'medium',
        cao: 'high',
      };
      return map[priority] || '';
    },
    startBookmarkReview(bm) {
      this.goToVocabularyReview(bm.tu_vung?.id, bm.bai_hoc?.id, {
        from: 'bookmark',
        bookmarkId: bm.id,
      });
    },
    async deleteBookmarkRow(bm) {
      if (!confirm('Bạn chắc chắn muốn xóa bookmark này?')) {
        return;
      }
      this.bookmarkRowDeletingId = bm.id;
      try {
        const { data: body } = await axios.delete(`${API_BASE}/api/bookmarks/${bm.id}`, {
          headers: this.authHeaders(),
        });
        if (body.status) {
          if (bm.tu_vung?.id != null) {
            delete this.bookmarkIdByTuVungId[bm.tu_vung.id];
          }
          await this.loadBookmarks();
          await this.loadBookmarkStats();
        } else {
          alert(body.message || 'Lỗi khi xóa');
        }
      } catch (e) {
        console.error(e);
        alert('Lỗi khi xóa');
      } finally {
        this.bookmarkRowDeletingId = null;
      }
    },
    openBookmarkEdit(bm) {
      this.editBookmarkTarget = bm;
      this.editBookmarkForm = {
        muc_do_uu_tien: bm.muc_do_uu_tien,
        ghi_chu: bm.ghi_chu || '',
      };
      this.showBookmarkEditModal = true;
    },
    closeBookmarkEditModal() {
      this.showBookmarkEditModal = false;
      this.editBookmarkTarget = null;
    },
    async saveBookmarkEdit() {
      if (!this.editBookmarkTarget) {
        return;
      }
      this.editBookmarkSaving = true;
      try {
        const { data: body } = await axios.patch(
          `${API_BASE}/api/bookmarks/${this.editBookmarkTarget.id}`,
          {
            muc_do_uu_tien: this.editBookmarkForm.muc_do_uu_tien,
            ghi_chu: this.editBookmarkForm.ghi_chu,
          },
          { headers: this.authHeaders() },
        );
        if (body.status) {
          this.closeBookmarkEditModal();
          await this.loadBookmarks();
          await this.loadBookmarkStats();
        } else {
          alert(body.message || 'Lỗi khi cập nhật');
        }
      } catch (e) {
        console.error(e);
        alert('Lỗi khi cập nhật');
      } finally {
        this.editBookmarkSaving = false;
      }
    },
  },
};
</script>

<style scoped>
.error-review-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  background: #f8f9fa;
  min-height: 100vh;
}

.header {
  text-align: center;
  margin-bottom: 40px;
}

.header h1 {
  font-size: 32px;
  color: #333;
  margin-bottom: 8px;
}

.header p {
  font-size: 16px;
  color: #666;
}

.statistics-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  text-align: center;
  border-left: 4px solid #007bff;
}

.stat-card.warning {
  border-left-color: #ffc107;
}

.stat-card.info {
  border-left-color: #17a2b8;
}

.stat-card.success {
  border-left-color: #28a745;
}

.stat-number {
  font-size: 28px;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 14px;
  color: #666;
}

.filter-section {
  background: white;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 30px;
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  align-items: flex-end;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.filter-group label {
  font-weight: 600;
  color: #333;
  font-size: 14px;
}

.filter-select {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  min-width: 150px;
  background-color: white;
  cursor: pointer;
}

.filter-select:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.btn-reset {
  padding: 8px 16px;
  background: #6c757d;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.3s;
}

.btn-reset:hover {
  background: #5a6268;
}

.tabs-section {
  display: flex;
  gap: 8px;
  margin-bottom: 30px;
  border-bottom: 2px solid #e0e0e0;
  background: white;
  border-radius: 8px 8px 0 0;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.tab-btn {
  flex: 1;
  padding: 12px 16px;
  background: #f8f9fa;
  border: none;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  color: #666;
  transition: all 0.3s;
  border-bottom: 3px solid transparent;
}

.tab-btn:hover {
  color: #333;
}

.tab-btn.active {
  background: white;
  color: #007bff;
  border-bottom-color: #007bff;
}

.loading {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 8px;
}

.spinner {
  display: inline-block;
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.error-message {
  background: #f8d7da;
  color: #721c24;
  padding: 16px;
  border-radius: 8px;
  text-align: center;
}

.btn-retry {
  margin-top: 12px;
  padding: 8px 16px;
  background: #721c24;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
}

.btn-retry:hover {
  background: #5a0f15;
}

.content {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.errors-list,
.bookmarks-list {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #999;
}

.empty-state p {
  font-size: 16px;
}

.errors-grid,
.bookmarks-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e0e0e0;
}

.btn-pagination {
  padding: 8px 16px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.3s;
}

.btn-pagination:hover {
  background: #0056b3;
}

.page-info {
  font-size: 14px;
  color: #666;
  font-weight: 600;
}

.error-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
}

.error-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.bookmark-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
  border-left: 4px solid #17a2b8;
}

.bookmark-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.error-card .card-header,
.bookmark-card .card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 16px;
  background: #f8f9fa;
  border-bottom: 1px solid #e0e0e0;
}

.word-info {
  display: flex;
  gap: 12px;
  flex: 1;
}

.word-image {
  width: 60px;
  height: 60px;
  border-radius: 4px;
  overflow: hidden;
  background: #e0e0e0;
}

.word-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.word-text h3 {
  margin: 0;
  font-size: 18px;
  color: #333;
  font-weight: 700;
}

.phien-am {
  margin: 4px 0;
  font-size: 14px;
  color: #666;
  font-style: italic;
}

.bai-hoc {
  margin: 4px 0;
  font-size: 12px;
  color: #999;
}

.status-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}

.status-badge.warning {
  background: #fff3cd;
  color: #856404;
}

.status-badge.info {
  background: #d1ecf1;
  color: #0c5460;
}

.status-badge.success {
  background: #d4edda;
  color: #155724;
}

.priority-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}

.priority-badge.low {
  background: #d1ecf1;
  color: #0c5460;
}

.priority-badge.medium {
  background: #fff3cd;
  color: #856404;
}

.priority-badge.high {
  background: #f8d7da;
  color: #721c24;
}

.card-body {
  padding: 16px;
}

.error-info,
.bookmark-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 16px;
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 8px;
  background: #f8f9fa;
  border-radius: 4px;
  font-size: 14px;
}

.info-item .label {
  font-weight: 600;
  color: #666;
}

.info-item .value,
.info-item .status {
  color: #333;
}

.status {
  font-weight: 600;
}

.status.pending {
  color: #ffc107;
}

.status.completed {
  color: #28a745;
}

.detail-block {
  padding: 12px;
  border-radius: 4px;
  margin-bottom: 16px;
}

.error-detail {
  background: #f0f7ff;
  border-left: 3px solid #007bff;
}

.bookmark-note {
  background: #f0f7ff;
  border-left: 3px solid #17a2b8;
}

.detail-label {
  margin: 0 0 8px 0;
  font-weight: 600;
  font-size: 12px;
  color: #666;
}

.detail-text {
  margin: 0;
  font-size: 14px;
  color: #333;
  line-height: 1.5;
}

.audio-controls {
  margin-bottom: 16px;
}

.btn-play {
  width: 100%;
  padding: 10px;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: background 0.3s;
}

.btn-play-primary {
  background: #007bff;
}

.btn-play-primary:hover {
  background: #0056b3;
}

.btn-play-teal {
  background: #17a2b8;
}

.btn-play-teal:hover {
  background: #138496;
}

.card-actions {
  display: flex;
  gap: 8px;
  padding: 12px 16px;
  background: #f8f9fa;
  border-top: 1px solid #e0e0e0;
  flex-wrap: wrap;
}

.error-actions .action-group {
  flex: 1;
  min-width: 150px;
}

.btn-action {
  padding: 8px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  font-size: 12px;
  transition: all 0.3s;
  white-space: nowrap;
}

.error-actions .btn-action {
  flex: 1;
}

.btn-start {
  background: #ffc107;
  color: #333;
}

.btn-start:hover {
  background: #e0a800;
}

.btn-completed {
  background: #28a745;
  color: white;
}

.btn-completed:hover {
  background: #218838;
}

.btn-reviewed {
  background: #d4edda;
  color: #155724;
}

.btn-bookmark {
  background: #17a2b8;
  color: white;
  padding: 8px 12px;
}

.btn-bookmark:hover {
  background: #138496;
}

.btn-delete {
  background: #dc3545;
  color: white;
  padding: 8px 12px;
}

.btn-delete:hover {
  background: #c82333;
}

.btn-action:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.bookmark-actions .btn-action {
  flex: 1;
}

.btn-mark-completed {
  background: #28a745;
  color: white;
}

.btn-mark-completed:hover {
  background: #218838;
}

.btn-mark-completed.completed {
  background: #d4edda;
  color: #155724;
}

.btn-edit {
  background: #17a2b8;
  color: white;
}

.btn-edit:hover {
  background: #138496;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  width: 90%;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #e0e0e0;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
  color: #333;
}

.btn-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
}

.btn-close:hover {
  color: #333;
}

.modal-body {
  padding: 20px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #333;
  font-size: 14px;
}

.form-control {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  font-family: inherit;
}

.form-control:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.modal-footer {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding: 20px;
  border-top: 1px solid #e0e0e0;
}

.btn-secondary,
.btn-primary,
.btn-primary-teal {
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-primary:hover {
  background: #0056b3;
}

.btn-primary-teal {
  background: #17a2b8;
  color: white;
}

.btn-primary-teal:hover {
  background: #138496;
}

.btn-primary:disabled,
.btn-primary-teal:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
