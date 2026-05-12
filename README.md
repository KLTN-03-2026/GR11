# 🎧 EchoKids – Nền Tảng Học Phát Âm Tiếng Việt Thông Minh

> Hệ thống học tiếng Việt tập trung vào luyện phát âm, được hỗ trợ bởi AI, dành cho học sinh và kết nối giáo viên – học sinh theo thời gian thực.

---

## 📌 Giới Thiệu

**EchoKids** là một nền tảng học tiếng Việt trực tuyến chuyên về luyện **phát âm**, từ vựng và theo dõi tiến độ học tập. Hệ thống bao gồm:

- 🎓 **Học sinh**: luyện tập phát âm theo bài học, nhận phản hồi từ AI, xem tiến độ và lịch sử lỗi.
- 👨‍🏫 **Giáo viên**: soạn bài học, quản lý từ vựng, theo dõi học sinh, nhắn tin trực tiếp.
- 🛡️ **Admin**: quản lý tài khoản, kiểm duyệt bài học, cấu hình hệ thống, phân quyền, thống kê.

---

## 🏗️ Kiến Trúc Hệ Thống

```text
├── BE/          # Backend – Laravel 12 (PHP 8.2)
└── FE/          # Frontend – Vue 3 + Vite

### Luồng hoạt động tổng thể

```

[Vue 3 SPA] ──REST API──► [Laravel 12 Backend] ──► [MySQL Database]
│
[Pusher / WebSocket] ──► Real-time events
│
[AI Service (Gemini)] ──► Phân tích phát âm
│
[SMTP (Gmail)] ──► Email thông báo

````

---

## 🖥️ Backend – Laravel 12

### Công nghệ sử dụng

| Công nghệ | Phiên bản | Mục đích |
|-----------|-----------|----------|
| PHP | ^8.2 | Ngôn ngữ lập trình |
| Laravel | ^12.0 | Framework backend |
| Laravel Sanctum | ^4.0 | Xác thực API token |
| Pusher PHP Server | ^7.2 | Real-time events |
| Firebase JWT | 7.0 | JWT token |
| Google API Client | ^2.19 | Google OAuth, Gemini AI |
| MySQL | ≥5.7 | Cơ sở dữ liệu |

### Cài Đặt Backend

```bash
# 1. Di chuyển vào thư mục BE
cd BE

# 2. Cài đặt dependencies
composer install

# 3. Tạo file cấu hình môi trường
cp .env.example .env

# 4. Tạo application key
php artisan key:generate

# 5. Cấu hình database trong .env (xem bên dưới)

# 6. Chạy migrations
php artisan migrate

# 7. (Tuỳ chọn) Seed dữ liệu mẫu
php artisan db:seed

# 8. Tạo symbolic link cho storage
php artisan storage:link

# 9. Khởi động server
php artisan serve
````

> Server mặc định chạy tại: `http://127.0.0.1:8000`

### Cấu Hình `.env` Quan Trọng

```env
APP_NAME=EchoKids
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ECHOKIDS
DB_USERNAME=root
DB_PASSWORD=

# Mail (Gmail SMTP)
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="Hệ thống EchoKids"

# AI Service
GEMINI_API_KEY=your_gemini_api_key
AI_SERVICE_URL=http://localhost:8001/api/ai

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id

# Pusher (Real-time)
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=ap1

# reCAPTCHA
RECAPTCHA_SITE_KEY=your_site_key
RECAPTCHA_SECRET_KEY=your_secret_key
```

### Cấu Trúc Backend

```
BE/
├── app/
│   ├── Events/              # Real-time events (Pusher)
│   ├── Http/
│   │   ├── Controllers/     # 31 controllers xử lý nghiệp vụ
│   │   └── Middleware/      # Auth, CORS, role middleware
│   ├── Listeners/           # Event listeners
│   ├── Mail/                # Email templates
│   ├── Models/              # 26 Eloquent models
│   ├── Services/            # Business logic services
│   └── Support/
├── database/
│   ├── migrations/          # 45 migration files
│   ├── factories/
│   └── seeders/
├── routes/
│   ├── api.php              # API routes
│   └── web.php
└── storage/
    └── app/public/          # File uploads (ảnh, audio)
```

### Các Module Chính (Backend)

| Controller                      | Chức năng                     |
| ------------------------------- | ----------------------------- |
| `NguoiDungController`           | Quản lý người dùng, profile   |
| `BaiHocController`              | CRUD bài học                  |
| `TeacherQuanLyBaiHocController` | Giáo viên quản lý bài học     |
| `TeacherTuVungController`       | Giáo viên quản lý từ vựng     |
| `PhienLuyenTapController`       | Phiên luyện tập phát âm       |
| `ChiTietLuyenTapController`     | Chi tiết kết quả luyện tập    |
| `ErrorHistoryController`        | Lịch sử lỗi phát âm           |
| `AdminDashboardController`      | Dashboard & thống kê admin    |
| `KiemDuyetBaiHocController`     | Kiểm duyệt bài học            |
| `HoSoGiaoVienController`        | Hồ sơ đăng ký giáo viên       |
| `QuanHeGvHvController`          | Quan hệ giáo viên – học viên  |
| `ChatController`                | Nhắn tin giáo viên – học viên |
| `StudentChatController`         | Chat AI hỗ trợ học sinh       |
| `ChatBoxAIController`           | Tích hợp AI chatbox           |
| `CauHinhController`             | Cấu hình hệ thống             |
| `TienDoBaiHocController`        | Tiến độ học bài               |
| `TtsController`                 | Text-to-Speech                |
| `AdminController`               | Quản lý admin                 |
| `VaiTroController`              | Vai trò người dùng            |

---

## 🌐 Frontend – Vue 3

### Công nghệ sử dụng

| Công nghệ            | Phiên bản | Mục đích                |
| -------------------- | --------- | ----------------------- |
| Vue 3                | ^3.3.4    | Framework frontend      |
| Vue Router           | ^4.0.13   | Điều hướng SPA          |
| Vite                 | ^4.4.5    | Build tool              |
| Axios                | ^1.15.0   | HTTP client             |
| Laravel Echo         | ^1.15.0   | WebSocket client        |
| Pusher JS            | ^8.0.0    | Real-time notifications |
| Chart.js             | ^4.5.1    | Biểu đồ thống kê        |
| vue3-google-login    | ^2.0.37   | Google OAuth            |
| @meforma/vue-toaster | ^1.3.0    | Toast notifications     |

### Cài Đặt Frontend

```bash
# 1. Di chuyển vào thư mục FE
cd FE

# 2. Cài đặt dependencies
npm install

# 3. Cấu hình .env
# Tạo file .env với nội dung:
# VITE_API_URL=http://127.0.0.1:8000

# 4. Khởi động dev server
npm run dev
```

> Frontend mặc định chạy tại: `http://localhost:5173`

### Cấu Trúc Frontend

```
FE/
├── src/
│   ├── components/
│   │   ├── AHeThong/
│   │   │   ├── Admin/           # Giao diện Admin
│   │   │   │   ├── Dashboard/
│   │   │   │   ├── KiemDuyet/
│   │   │   │   ├── PhanQuyen/
│   │   │   │   ├── QuanLyCauHinhHeThong/
│   │   │   │   ├── QuanLyHoSoGiaoVien/
│   │   │   │   ├── QuanLyTaiKhoan/
│   │   │   │   └── ThongKe/
│   │   │   └── Teach/           # Giao diện Giáo viên
│   │   │       ├── Dashboard/
│   │   │       ├── BaoCaoThongKe/
│   │   │       ├── QuanLyBaiHoc/
│   │   │       ├── QuanLyHocSinh/
│   │   │       └── TuVung/
│   │   ├── Client/              # Giao diện Học sinh / Khách
│   │   │   ├── TrangChu/
│   │   │   ├── DangNhap/
│   │   │   ├── DangKy/
│   │   │   ├── QuenMatKhau/
│   │   │   ├── Profile/
│   │   │   ├── DangKyGiaoVien/
│   │   │   ├── ChatBox/
│   │   │   ├── ChinhSach/
│   │   │   └── FAQ/
│   │   └── KhachHang/           # Trang học tập học sinh
│   │       ├── BaiHoc/
│   │       ├── ChiTietBaiHoc/
│   │       ├── ChuDe/
│   │       ├── LuyenTap/
│   │       ├── BaiKiemTra/
│   │       ├── TienDo/
│   │       ├── XepHang/
│   │       ├── OnTapLoiSai/
│   │       ├── ChuoiNgayHoc/
│   │       └── HoanThanhKT/
│   ├── composables/             # Shared logic (hooks)
│   ├── layout/                  # Layout: client, admin, teacher
│   ├── router/
│   │   ├── index.js             # Định nghĩa routes
│   │   ├── checkClient.js       # Guard: học sinh
│   │   ├── checkAdmin.js        # Guard: admin
│   │   └── checkTeacher.js      # Guard: giáo viên
│   ├── echo.js                  # Laravel Echo config
│   ├── main.js                  # Entry point
│   └── style.css
└── index.html
```

---

## 🗺️ Sơ Đồ Tính Năng

### 👤 Học Sinh (Student)

- Đăng ký / Đăng nhập (Email + Google OAuth)
- Duyệt bài học theo chủ đề
- Luyện tập phát âm (ghi âm + AI chấm điểm)
- Xem tiến độ học tập & chuỗi ngày học
- Ôn tập lỗi sai phát âm
- Xếp hạng học sinh
- Chat với giáo viên
- Chat với AI Chatbox
- Quản lý hồ sơ cá nhân & ví tiền

### 👨‍🏫 Giáo Viên (Teacher)

- Dashboard tổng quan
- Đăng ký trở thành giáo viên (KYC)
- Soạn & quản lý bài học, từ vựng
- Quản lý học sinh (xem tiến độ, thành tích)
- Nhắn tin với học sinh
- Báo cáo thống kê lớp học
- Nhận thông báo real-time từ Admin

### 🛡️ Admin

- Dashboard thống kê toàn hệ thống
- Quản lý tài khoản người dùng
- Kiểm duyệt bài học (duyệt / từ chối)
- Quản lý hồ sơ giáo viên (duyệt đăng ký)
- Phân quyền vai trò
- Cấu hình hệ thống (tên, logo, liên hệ, banner)
- Gửi thông báo real-time cho giáo viên

---

## 🗄️ Cơ Sở Dữ Liệu

### Các bảng chính

| Bảng                   | Mô tả                                  |
| ---------------------- | -------------------------------------- |
| `nguoi_dungs`          | Thông tin người dùng, vai trò, ví tiền |
| `vai_tros`             | Vai trò: Student, Teacher, Admin       |
| `bai_hocs`             | Bài học tiếng Anh                      |
| `tu_vungs`             | Từ vựng trong bài học                  |
| `phien_luyen_taps`     | Phiên luyện tập phát âm                |
| `chi_tiet_luyen_taps`  | Chi tiết kết quả từng từ               |
| `lich_su_loi_phat_ams` | Lịch sử lỗi phát âm                    |
| `diem_danh_lois`       | Điểm danh lỗi theo loại                |
| `tien_do_bai_hocs`     | Tiến độ học bài của học sinh           |
| `tien_do_hoc_taps`     | Tiến độ học tập tổng quan              |
| `quan_he_gv_hvs`       | Quan hệ giáo viên – học viên           |
| `ho_so_giao_viens`     | Hồ sơ đăng ký giáo viên (KYC)          |
| `chat_sessions`        | Phiên chat                             |
| `chat_messages`        | Tin nhắn                               |
| `lich_su_chat_ais`     | Lịch sử chat AI                        |
| `cau_hinh_he_thongs`   | Cấu hình hệ thống                      |
| `banners`              | Banner trang chủ                       |
| `vis`                  | Ví tiền học sinh                       |
| `giao_dich_vis`        | Giao dịch ví                           |
| `don_nap_tiens`        | Đơn nạp tiền                           |
| `yeu_cau_rut_tiens`    | Yêu cầu rút tiền                       |
| `goi_premiums`         | Gói premium                            |
| `goi_nguoi_dungs`      | Đăng ký gói của người dùng             |
| `lo_trinh_ca_nhans`    | Lộ trình học cá nhân                   |
| `thong_baos`           | Thông báo                              |

---

## 🔐 Phân Quyền

| Vai trò     | Quyền truy cập                                 |
| ----------- | ---------------------------------------------- |
| **Guest**   | Trang chủ, FAQ, Chính sách                     |
| **Student** | Tất cả trang học tập, profile, ví tiền         |
| **Teacher** | Dashboard giáo viên, quản lý bài học, học sinh |
| **Admin**   | Toàn bộ hệ thống                               |

---

## ⚡ Real-Time Features

Hệ thống sử dụng **Pusher** + **Laravel Echo** để hỗ trợ:

- 🔔 Thông báo kiểm duyệt bài học cho giáo viên
- 💬 Tin nhắn chat thời gian thực (giáo viên ↔ học sinh)
- 📨 Thông báo đăng ký hồ sơ giáo viên
- 🤖 Gợi ý luyện tập từ AI

---

## 🛠️ Yêu Cầu Hệ Thống

| Công cụ  | Phiên bản tối thiểu |
| -------- | ------------------- |
| PHP      | 8.2+                |
| Composer | Latest              |
| Node.js  | 18+                 |
| npm      | 8+                  |
| MySQL    | 5.7+ / MariaDB 10+  |

---

## 🚀 Chạy Toàn Bộ Project

```bash
# Terminal 1 – Backend
cd BE
php artisan serve

# Terminal 2 – Queue Worker (real-time jobs)
cd BE
php artisan queue:listen --tries=1

# Terminal 3 – Frontend
cd FE
npm run dev
```

Hoặc dùng lệnh tích hợp trong backend:

```bash
cd BE
composer run dev
```

---

## 📧 Liên Hệ & Hỗ Trợ

- Email: supportechokids@gmail.com
- Hệ thống: EchoKids v1.0

---

## 📄 Giấy Phép

Dự án được xây dựng cho mục đích học thuật – Đồ án tốt nghiệp nhóm GR11 (2026).
