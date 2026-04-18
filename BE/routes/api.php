<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BaiHocController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CauHinhController;
use App\Http\Controllers\DanhMucBaiHocController;
use App\Http\Controllers\ErrorHistoryController;
use App\Http\Controllers\KiemDuyetBaiHocConTroller;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\QuanHeGvHvController;
use App\Http\Controllers\TeacherQuanLyBaiHocController;
use App\Http\Controllers\TeacherTuVungController;
use App\Http\Controllers\TtsController;
use App\Http\Controllers\VaiTroController;
use App\Http\Controllers\VaiTroQuyenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

//---------------------------------------------ADMIN--------------------------------------------------------------

Route::prefix('/admin')->group(function () {
    Route::prefix('/quan-ly-tai-khoan')->group(function () {
        Route::post('/create', [AdminController::class, 'store']);
        Route::get('/data', [AdminController::class, 'getdata']);
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

    Route::prefix('/cau-hinh')->group(function () {
        Route::get('/chung/data', [CauHinhController::class, 'getGeneralSettings']);
        Route::post('/chung/update', [CauHinhController::class, 'updateGeneralSettings']);

        Route::get('/ai/data', [CauHinhController::class, 'getAiSettings']);
        Route::put('/ai/update', [CauHinhController::class, 'updateAiSettings']);

        Route::get('/thong-bao/data', [CauHinhController::class, 'getAlertSettings']);
        Route::put('/thong-bao/update', [CauHinhController::class, 'updateAlertSettings']);

        Route::get('/banners/data', [CauHinhController::class, 'index']);
        Route::post('/banners/create', [CauHinhController::class, 'store']);
        Route::patch('/banners/update/{id}', [CauHinhController::class, 'update']);
        Route::delete('/banners/delete/{id}', [CauHinhController::class, 'destroy']);
    });
});

Route::get('/cau-hinh/footer/data', [CauHinhController::class, 'getFooterSettings']);
Route::get('/cau-hinh/thong-bao', [CauHinhController::class, 'getAlertSettings']);

//---------------------------------------------Teacher--------------------------------------------------------------

Route::prefix('/teacher')->middleware(['auth:sanctum', 'role:2'])->group(function () {
    Route::prefix('/danh-muc-bai-hoc')->group(function () {
        Route::get('/', [TeacherQuanLyBaiHocController::class, 'indexDanhMuc']);
        Route::post('/', [TeacherQuanLyBaiHocController::class, 'storeDanhMuc']);
        Route::put('/{id}', [TeacherQuanLyBaiHocController::class, 'updateDanhMuc']);
        Route::delete('/{id}', [TeacherQuanLyBaiHocController::class, 'destroyDanhMuc']);
        Route::get('/{id}/bai-hoc', [TeacherQuanLyBaiHocController::class, 'indexBaiHocTheoDanhMuc']);
    });

    Route::prefix('/bai-hoc')->group(function () {
        Route::get('/{id}/tu-vung', [TeacherTuVungController::class, 'index']);
        Route::post('/{id}/tu-vung', [TeacherTuVungController::class, 'store']);
        Route::post('/', [TeacherQuanLyBaiHocController::class, 'storeBaiHoc']);
        Route::put('/{id}', [TeacherQuanLyBaiHocController::class, 'updateBaiHoc']);
        Route::delete('/{id}', [TeacherQuanLyBaiHocController::class, 'destroyBaiHoc']);
    });

    Route::prefix('/tu-vung')->group(function () {
        Route::put('/{id}', [TeacherTuVungController::class, 'update']);
        Route::delete('/{id}', [TeacherTuVungController::class, 'destroy']);
    });

    Route::prefix('/gv-hv')->group(function () {
        Route::get('/dashboard', [QuanHeGvHvController::class, 'dashboardTongQuan']);
        Route::get('/hoc-vien', [QuanHeGvHvController::class, 'danhSachHocVien']);
        Route::get('/hoc-vien/{id}', [QuanHeGvHvController::class, 'chiTietHocVien']);
        Route::get('/bai-hoc-goi-y', [QuanHeGvHvController::class, 'danhSachBaiHocGoiY']);
        Route::post('/goi-y', [QuanHeGvHvController::class, 'guiGoiY']);
    });
});


//------------------------------------------------Auth-----------------------------------------------------------

Route::get('/check-token', [NguoiDungController::class, 'checkToken']);
Route::post('/login-google', [NguoiDungController::class, 'loginGoogle']);
Route::post('/dang-nhap', [NguoiDungController::class, 'login']);
Route::post('/dang-ky', [NguoiDungController::class, 'register']);

Route::post('/quen-mat-khau', [NguoiDungController::class, 'forgotPassword']);
Route::post('/dat-lai-mat-khau', [NguoiDungController::class, 'resetPassword']);



//---------------------------------------------CLIENT--------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/dang-xuat', [NguoiDungController::class, 'logOut']);
    Route::get('/profile', [NguoiDungController::class, 'profile']);
    Route::post('/profile/update', [NguoiDungController::class, 'updateProfile']);
    Route::post('/profile/update-avatar', [NguoiDungController::class, 'updateProfileAvatar']);
    Route::post('/profile/change-password', [NguoiDungController::class, 'changeProfilePassword']);
});

Route::prefix('/danh-muc-bai-hoc')->group(function () {

    Route::get('/', [DanhMucBaiHocController::class, 'indexPublic']);
});

Route::prefix('/bai-hoc')->group(function () {

    Route::get('/', [BaiHocController::class, 'indexPublic']);
    Route::get('/{baiHoc}', [BaiHocController::class, 'showPublic']);
});

Route::prefix('/tts-vi')->group(function () {

    Route::get('/', [TtsController::class, 'vietnamese']);
});

//---------------------------------------------ERROR REVIEW--------------------------------------------------------------
Route::prefix('/error-history')->middleware('auth:sanctum')->group(function () {
    Route::get('/all', [ErrorHistoryController::class, 'getAllErrors']);
    Route::get('/by-status', [ErrorHistoryController::class, 'getErrorsByStatus']);
    Route::get('/statistics', [ErrorHistoryController::class, 'getErrorStatistics']);
    Route::patch('/{error}/status', [ErrorHistoryController::class, 'updateErrorStatus']);
    Route::delete('/{error}', [ErrorHistoryController::class, 'deleteError']);
});

Route::prefix('/bookmarks')->middleware('auth:sanctum')->group(function () {
    Route::get('/statistics', [BookmarkController::class, 'getBookmarkStatistics']);
    Route::patch('/{bookmark}/mark-completed', [BookmarkController::class, 'markAsCompleted']);
    Route::patch('/{bookmark}', [BookmarkController::class, 'updateBookmark']);
    Route::delete('/{bookmark}', [BookmarkController::class, 'deleteBookmark']);
    Route::get('/', [BookmarkController::class, 'getAllBookmarks']);
    Route::post('/', [BookmarkController::class, 'createBookmark']);
});

