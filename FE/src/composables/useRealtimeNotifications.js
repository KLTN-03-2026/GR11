/**
 * useRealtimeNotifications.js
 * Composable quản lý đăng ký/hủy kênh WebSocket (Pusher) cho từng vai trò.
 * 
 * Cách dùng:
 *   const { setupAdminChannel, setupTeacherChannel, setupStudentChannel, teardown } = useRealtimeNotifications()
 */

import echo from '../echo'

/**
 * Trả về token đang lưu trong localStorage theo thứ tự ưu tiên vai trò.
 */
function getToken() {
  return (
    localStorage.getItem('token_admin') ||
    localStorage.getItem('token_teacher') ||
    localStorage.getItem('token_nguoi_dung') ||
    ''
  )
}

/**
 * Cập nhật auth header của Echo khi token thay đổi (ví dụ sau khi login).
 * Echo của Pusher lấy token 1 lần khi khởi tạo, hàm này giúp làm mới.
 */
function refreshEchoAuth() {
  if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
    const token = getToken()
    if (token) {
      window.Echo.connector.pusher.config.auth = {
        headers: { Authorization: 'Bearer ' + token },
      }
    }
  }
}

export function useRealtimeNotifications() {
  const subscriptions = []

  /**
   * Admin: lắng nghe kênh public 'admin'
   * Event: GiaoVienNopHoSo
   * @param {Function} onNewHoSo  - callback({ ho_so_id, ho_ten, email, chuyen_mon, trang_thai, created_at })
   */
  function setupAdminChannel(onNewHoSo) {
    if (!window.Echo) return
    refreshEchoAuth()

    const channel = window.Echo.private('admin')
    channel.listen('.GiaoVienNopHoSo', (data) => {
      if (typeof onNewHoSo === 'function') onNewHoSo(data)
    })

    subscriptions.push({ type: 'private', name: 'admin' })
  }

  /**
   * Giáo viên: lắng nghe kênh private 'teacher.{id}'
   * Event: AdminDuyetBaiHoc
   * @param {number}   teacherId          - ID giáo viên đang đăng nhập
   * @param {Function} onBaiHocDuyet      - callback({ bai_hoc_id, tieu_de, trang_thai, trang_thai_label, updated_at })
   */
  function setupTeacherChannel(teacherId, onBaiHocDuyet) {
    if (!window.Echo || !teacherId) return
    refreshEchoAuth()

    const channel = window.Echo.private(`teacher.${teacherId}`)
    channel.listen('.AdminDuyetBaiHoc', (data) => {
      if (typeof onBaiHocDuyet === 'function') onBaiHocDuyet(data)
    })

    subscriptions.push({ type: 'private', name: `teacher.${teacherId}` })
  }

  /**
   * Học viên: lắng nghe kênh private 'student.{id}'
   * Event: GiaoVienGuiGoiY
   * @param {number}   studentId          - ID học viên đang đăng nhập
   * @param {Function} onGoiYNhan         - callback({ bai_hoc_id, tieu_de, ten_giao_vien, uu_tien, loi_nhan, thoi_gian })
   */
  function setupStudentChannel(studentId, onGoiYNhan) {
    if (!window.Echo || !studentId) return
    refreshEchoAuth()

    const channel = window.Echo.private(`student.${studentId}`)
    channel.listen('.GiaoVienGuiGoiY', (data) => {
      if (typeof onGoiYNhan === 'function') onGoiYNhan(data)
    })

    subscriptions.push({ type: 'private', name: `student.${studentId}` })
  }

  /**
   * Hủy tất cả các kênh đã đăng ký.
   * Gọi trong onBeforeUnmount() hoặc onUnmounted().
   */
  function teardown() {
    if (!window.Echo) return
    subscriptions.forEach(({ name }) => {
      try {
        window.Echo.leave(name)
      } catch (_) { /* bỏ qua */ }
    })
    subscriptions.length = 0
  }

  return { setupAdminChannel, setupTeacherChannel, setupStudentChannel, teardown }
}
