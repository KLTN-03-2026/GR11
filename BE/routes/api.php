<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDepositController;
use App\Http\Controllers\BaiHocController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CauHinhController;
use App\Http\Controllers\ChatBoxAIController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChiTietLuyenTapController;
use App\Http\Controllers\DanhMucBaiHocController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ErrorHistoryController;
use App\Http\Controllers\GoiPremiumController;
use App\Http\Controllers\HocVienLoTrinhController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HoSoGiaoVienController;
use App\Http\Controllers\KiemDuyetBaiHocConTroller;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\PhienKiemTraController;
use App\Http\Controllers\PhienLuyenTapController;
use App\Http\Controllers\QuanHeGvHvController;
use App\Http\Controllers\StudentChatController;
use App\Http\Controllers\TeacherBaiKiemTraController;
use App\Http\Controllers\TeacherLoTrinhController;
use App\Http\Controllers\TeacherQuanLyBaiHocController;
use App\Http\Controllers\TeacherTuVungController;
use App\Http\Controllers\ThongTinHocVienController;
use App\Http\Controllers\TienDoBaiHocController;
use App\Http\Controllers\TrangChuController;
use App\Http\Controllers\TtsController;
use App\Http\Controllers\VaiTroController;
use App\Http\Controllers\VaiTroQuyenController;
use App\Http\Controllers\ViController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

/*
|--------------------------------------------------------------------------
| Công khai — cấu hình, trang chủ, nội dung học tập, TTS
|--------------------------------------------------------------------------
*/

Route::get('/cau-hinh/footer/data', [CauHinhController::class, 'getFooterSettings']);
Route::get('/cau-hinh/thong-bao', [CauHinhController::class, 'getAlertSettings']);
Route::get('/leaderboard', [ThongTinHocVienController::class, 'leaderboard']);
Route::get('/trang-chu', [TrangChuController::class, 'index']);

Route::prefix('/homepage')->group(function () {
    Route::get('/data-open', [HomeController::class, 'dataOpen']);
});

Route::prefix('/danh-muc-bai-hoc')->group(function () {
    Route::get('/', [DanhMucBaiHocController::class, 'indexPublic']);
});

Route::get('/chu-de', [DanhMucBaiHocController::class, 'indexPublic']);

Route::prefix('/bai-hoc')->group(function () {
    Route::get('/', [BaiHocController::class, 'indexPublic']);
    Route::get('/{baiHoc}', [BaiHocController::class, 'showPublic']);
});

Route::prefix('/tts-vi')->group(function () {
    Route::get('/', [TtsController::class, 'vietnamese']);
});

/*
|--------------------------------------------------------------------------
| Công khai — xác thực & tài khoản (không cần token)
|--------------------------------------------------------------------------
*/

Route::get('/check-token', [NguoiDungController::class, 'checkToken']);
Route::post('/login-google', [NguoiDungController::class, 'loginGoogle']);
Route::post('/dang-nhap', [NguoiDungController::class, 'login']);
Route::post('/dang-ky', [NguoiDungController::class, 'register']);
Route::post('/quen-mat-khau', [NguoiDungController::class, 'forgotPassword']);
Route::post('/dat-lai-mat-khau', [NguoiDungController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::prefix('/admin')->group(function () {
    Route::prefix('/dashboard')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/realtime', [AdminDashboardController::class, 'realtime']);
        Route::get('/performance', [AdminDashboardController::class, 'performance']);
        Route::get('/reports', [AdminDashboardController::class, 'reports']);
        Route::get('/export', [AdminDashboardController::class, 'export']);
    });

    Route::prefix('/quan-ly-tai-khoan')->group(function () {
        Route::post('/create', [AdminController::class, 'store']);
        Route::get('/data', [AdminController::class, 'getdata']);
        Route::get('/export', [AdminController::class, 'export']);
        Route::post('/update', [AdminController::class, 'update']);
        Route::post('/change-status', [AdminController::class, 'changeStatus']);
        Route::post('/tim-kiem', [AdminController::class, 'search']);
        Route::post('/filter-by-role', [AdminController::class, 'filterByRole']);
    });

    Route::prefix('/danh-muc-bai-hoc')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [KiemDuyetBaiHocConTroller::class, 'index']);
        Route::post('/', [KiemDuyetBaiHocConTroller::class, 'store']);
        Route::put('/{id}', [KiemDuyetBaiHocConTroller::class, 'update']);
        Route::delete('/{id}', [KiemDuyetBaiHocConTroller::class, 'destroy']);
        Route::patch('/{id}/trang-thai', [KiemDuyetBaiHocConTroller::class, 'changeStatus']);
        Route::get('/{id}/bai-hoc', [KiemDuyetBaiHocConTroller::class, 'getBaiHoc']);
    });

    Route::prefix('/kiem-duyet-bai-hoc')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [KiemDuyetBaiHocConTroller::class, 'index']);
        Route::patch('/{id}/trang-thai', [KiemDuyetBaiHocConTroller::class, 'changeStatus']);
        Route::get('/{id}/tu-vung', [KiemDuyetBaiHocConTroller::class, 'getTuVung']);
    });

    Route::prefix('/vai-tro')->middleware('auth:sanctum')->group(function () {
        Route::get('/data', [VaiTroController::class, 'getData']);
        Route::post('/create', [VaiTroController::class, 'store']);
    });

    Route::prefix('/phan-quyen')->middleware('auth:sanctum')->group(function () {
        Route::get('/data', [VaiTroQuyenController::class, 'getData']);
        Route::post('/dong-bo', [VaiTroQuyenController::class, 'sync']);
    });

    Route::prefix('/ho-so-giao-vien')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [HoSoGiaoVienController::class, 'index']);
        Route::patch('/{id}/approve', [HoSoGiaoVienController::class, 'approve']);
        Route::patch('/{id}/reject', [HoSoGiaoVienController::class, 'reject']);
    });

    Route::prefix('/cau-hinh')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/chung/data', [CauHinhController::class, 'getGeneralSettings']);
        Route::post('/chung/update', [CauHinhController::class, 'updateGeneralSettings']);
        Route::post('/chung/update-logo', [CauHinhController::class, 'updateGeneralLogo']);
        Route::get('/ai/data', [CauHinhController::class, 'getAiSettings']);
        Route::put('/ai/update', [CauHinhController::class, 'updateAiSettings']);
        Route::get('/thong-bao/data', [CauHinhController::class, 'getAlertSettings']);
        Route::put('/thong-bao/update', [CauHinhController::class, 'updateAlertSettings']);
        Route::get('/banners/data', [CauHinhController::class, 'index']);
        Route::post('/banners/create', [CauHinhController::class, 'store']);
        Route::patch('/banners/update/{id}', [CauHinhController::class, 'update']);
        Route::delete('/banners/delete/{id}', [CauHinhController::class, 'destroy']);
    });

    Route::prefix('/deposits')->middleware(['auth:sanctum', 'role:1'])->group(function () {
        Route::get('/', [AdminDepositController::class, 'index']);
        Route::post('/{id}/confirm', [AdminDepositController::class, 'confirm']);
        Route::post('/{id}/reject', [AdminDepositController::class, 'reject']);
    });
});

/*
|--------------------------------------------------------------------------
| Giáo viên (role:2)
|--------------------------------------------------------------------------
*/

Route::prefix('/teacher')->middleware(['auth:sanctum', 'role:2'])->group(function () {
    Route::get('/bai-hoc-hoat-dong', [TeacherBaiKiemTraController::class, 'baiHocHoatDong']);

    Route::prefix('/danh-muc-bai-hoc')->group(function () {
        Route::get('/', [TeacherQuanLyBaiHocController::class, 'indexDanhMuc']);
        Route::put('/{id}', [TeacherQuanLyBaiHocController::class, 'updateDanhMuc']);
        Route::delete('/{id}', [TeacherQuanLyBaiHocController::class, 'destroyDanhMuc']);
        Route::get('/{id}/bai-hoc', [TeacherQuanLyBaiHocController::class, 'indexBaiHocTheoDanhMuc']);
    });

    Route::prefix('/bai-hoc')->group(function () {
        Route::get('/{id}/tu-vung-cho-quiz', [TeacherBaiKiemTraController::class, 'tuVungChoQuiz']);
        Route::get('/{id}/tu-vung', [TeacherTuVungController::class, 'index']);
        Route::post('/{id}/tu-vung/import-excel', [TeacherTuVungController::class, 'importFromExcel']);
        Route::post('/{id}/tu-vung', [TeacherTuVungController::class, 'store']);
        Route::post('/', [TeacherQuanLyBaiHocController::class, 'storeBaiHoc']);
        Route::put('/{id}', [TeacherQuanLyBaiHocController::class, 'updateBaiHoc']);
        Route::delete('/{id}', [TeacherQuanLyBaiHocController::class, 'destroyBaiHoc']);
    });

    Route::prefix('/tu-vung')->group(function () {
        Route::put('/{id}', [TeacherTuVungController::class, 'update']);
        Route::delete('/{id}', [TeacherTuVungController::class, 'destroy']);
    });

    Route::get('/bai-kiem-tra', [TeacherBaiKiemTraController::class, 'index']);
    Route::post('/bai-hoc/{baiHocId}/bai-kiem-tra', [TeacherBaiKiemTraController::class, 'store']);
    Route::get('/bai-kiem-tra/{baiKiemTraId}', [TeacherBaiKiemTraController::class, 'show']);
    Route::put('/bai-kiem-tra/{baiKiemTraId}', [TeacherBaiKiemTraController::class, 'update']);
    Route::delete('/bai-kiem-tra/{baiKiemTraId}', [TeacherBaiKiemTraController::class, 'destroy']);

    Route::prefix('/gv-hv')->group(function () {
        Route::get('/dashboard', [QuanHeGvHvController::class, 'dashboardTongQuan']);
        Route::get('/hoc-vien', [QuanHeGvHvController::class, 'danhSachHocVien']);
        Route::get('/hoc-vien/{id}', [QuanHeGvHvController::class, 'chiTietHocVien']);
        Route::get('/loi-phat-am-lich-su', [QuanHeGvHvController::class, 'thongKeLichSuLoiPhatAmTheoHocVien']);
        Route::get('/bai-hoc-goi-y', [QuanHeGvHvController::class, 'danhSachBaiHocGoiY']);
        Route::post('/goi-y', [QuanHeGvHvController::class, 'guiGoiY']);
    });

    Route::prefix('/lo-trinh')->group(function () {
        Route::get('/', [TeacherLoTrinhController::class, 'index']);
        Route::post('/', [TeacherLoTrinhController::class, 'store']);
        Route::get('/{loTrinh}', [TeacherLoTrinhController::class, 'show']);
        Route::put('/{loTrinh}', [TeacherLoTrinhController::class, 'update']);
        Route::delete('/{loTrinh}', [TeacherLoTrinhController::class, 'destroy']);
        Route::put('/{loTrinh}/chi-tiet', [TeacherLoTrinhController::class, 'syncChiTiet']);
        Route::put('/{loTrinh}/tra-phi', [TeacherLoTrinhController::class, 'upsertTraPhi']);
    });

    Route::prefix('/chat')->group(function () {
        Route::get('/unread-count', [ChatController::class, 'unreadCount']);
        Route::get('/sessions', [ChatController::class, 'getSessionsForTeacher']);
        Route::get('/session/{sessionId}/messages', [ChatController::class, 'getMessages']);
        Route::post('/session/{sessionId}/send', [ChatController::class, 'sendMessage']);
        Route::delete('/session/{sessionId}', [ChatController::class, 'deleteSession']);
    });
});

/*
|--------------------------------------------------------------------------
| Đã đăng nhập (Sanctum) — học viên / hồ sơ / chat / thanh toán / tiện ích
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('/homepage')->group(function () {
        Route::post('/ho-so-giao-vien', [HoSoGiaoVienController::class, 'store']);
        Route::get('/ho-so-giao-vien/my-status', [HoSoGiaoVienController::class, 'myStatus']);
    });

    // Chat học viên ↔ hệ thống
    Route::get('/student/chat/unread-count', [StudentChatController::class, 'unreadCount']);
    Route::get('/student/chat/sessions', [StudentChatController::class, 'getSessions']);
    Route::post('/student/chat/session', [StudentChatController::class, 'createSession']);
    Route::post('/student/chat/session/{sessionId}/send', [StudentChatController::class, 'sendMessage']);
    Route::get('/student/chat/session/{sessionId}/messages', [StudentChatController::class, 'getMessages']);

    // Chat AI (hệ thống)
    Route::post('/chat/system', [ChatBoxAIController::class, 'chatSystem']);
    Route::prefix('/chat/system')->group(function () {
        Route::post('/session', [ChatBoxAIController::class, 'session']);
        Route::post('/chat', [ChatBoxAIController::class, 'chatSystem']);
        Route::get('/history', [ChatBoxAIController::class, 'history']);
        Route::get('/greeting', [ChatBoxAIController::class, 'greeting']);
    });

    // Phiên luyện tập
    Route::post('/phien-luyen-taps/start', [PhienLuyenTapController::class, 'start']);
    Route::post('/phien-luyen-taps/end', [PhienLuyenTapController::class, 'end']);
    Route::post('/phien-luyen-taps/hoan-thanh', [PhienLuyenTapController::class, 'hoanThanh']);

    // Bài kiểm tra (học viên)
    Route::get('/hoc-vien/bai-kiem-tra', [PhienKiemTraController::class, 'danhSachChoHocVien']);
    Route::get('/bai-kiem-tra/{baiKiemTraId}/lam-bai', [PhienKiemTraController::class, 'deLamBai']);
    Route::post('/phien-kiem-tra/start', [PhienKiemTraController::class, 'start']);
    Route::post('/phien-kiem-tra/luu-cau', [PhienKiemTraController::class, 'luuCau']);
    Route::post('/phien-kiem-tra/cham-phat-am', [PhienKiemTraController::class, 'chamPhatAm']);
    Route::post('/phien-kiem-tra/nop-bai', [PhienKiemTraController::class, 'nopBai']);

    // Hồ sơ & tài khoản
    Route::post('/dang-xuat', [NguoiDungController::class, 'logOut']);
    Route::get('/profile', [NguoiDungController::class, 'profile']);
    Route::post('/profile/update', [NguoiDungController::class, 'updateProfile']);
    Route::post('/profile/update-avatar', [NguoiDungController::class, 'updateProfileAvatar']);
    Route::post('/profile/change-password', [NguoiDungController::class, 'changeProfilePassword']);
    Route::get('/thong-tin-hoc-vien/me', [ThongTinHocVienController::class, 'me']);

    Route::prefix('/tien-do-bai-hoc')->group(function () {
        Route::get('/tong-quan', [TienDoBaiHocController::class, 'tongQuan']);
    });

    Route::prefix('/hoc-vien/lo-trinh-ca-nhan')->group(function () {
        Route::get('/', [HocVienLoTrinhController::class, 'index']);
        Route::post('/{loTrinh}/mua', [HocVienLoTrinhController::class, 'mua']);
        Route::get('/{loTrinh}', [HocVienLoTrinhController::class, 'show']);
    });

    Route::post('/cham-diem-phat-am', [ChiTietLuyenTapController::class, 'chamDiemPhatAm']);

    Route::prefix('/deposit')->group(function () {
        Route::post('/create', [DepositController::class, 'create']);
        Route::get('/history', [DepositController::class, 'history']);
        Route::post('/don/{id}/chung-tu', [DepositController::class, 'uploadChungTu']);
    });

    Route::prefix('/goi-premium')->group(function () {
        Route::get('/goi-hien-tai', [GoiPremiumController::class, 'goiHienTai']);
        Route::post('/mua', [GoiPremiumController::class, 'mua']);
    });

    Route::prefix('/vi')->group(function () {
        Route::get('/so-du', [ViController::class, 'soDu']);
        Route::post('/nap-tien', [ViController::class, 'napTien']);
        Route::get('/nap-tien/{maDon}/trang-thai', [ViController::class, 'napTienTrangThai']);
        Route::post('/nap-tien/{maDon}/sau-quet-ma', [ViController::class, 'napTienSauQuetMa']);
        Route::post('/nap-tien/{maDon}/xac-nhan-thanh-cong', [ViController::class, 'napTienXacNhanThanhCong']);
        Route::post('/rut-tien', [ViController::class, 'rutTien']);
        Route::get('/lich-su', [ViController::class, 'lichSu']);
    });

    Route::prefix('/error-history')->group(function () {
        Route::get('/all', [ErrorHistoryController::class, 'getAllErrors']);
        Route::get('/by-status', [ErrorHistoryController::class, 'getErrorsByStatus']);
        Route::get('/statistics', [ErrorHistoryController::class, 'getErrorStatistics']);
        Route::patch('/{error}/status', [ErrorHistoryController::class, 'updateErrorStatus']);
        Route::delete('/{error}', [ErrorHistoryController::class, 'deleteError']);
    });

    Route::prefix('/bookmarks')->group(function () {
        Route::get('/statistics', [BookmarkController::class, 'getBookmarkStatistics']);
        Route::patch('/{bookmark}/mark-completed', [BookmarkController::class, 'markAsCompleted']);
        Route::patch('/{bookmark}', [BookmarkController::class, 'updateBookmark']);
        Route::delete('/{bookmark}', [BookmarkController::class, 'deleteBookmark']);
        Route::get('/', [BookmarkController::class, 'getAllBookmarks']);
        Route::post('/', [BookmarkController::class, 'createBookmark']);
    });
});
