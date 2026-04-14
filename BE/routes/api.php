<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BaiHocController;
use App\Http\Controllers\CauHinhController;
use App\Http\Controllers\DanhMucBaiHocController;
use App\Http\Controllers\KiemDuyetBaiHocConTroller;
use App\Http\Controllers\NguoiDungController;
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


