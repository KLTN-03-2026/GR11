<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BaiHocController;
use App\Http\Controllers\DanhMucBaiHocController;
use App\Http\Controllers\KiemDuyetBaiHocConTroller;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\TtsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
//---------------------------------------------ADMIN--------------------------------------------------------------
Route::prefix('/admin/quan-ly-tai-khoan')->group(function () {
    Route::post('/create', [AdminController::class, 'store']);
    Route::get('/data', [AdminController::class, 'getdata']);
    Route::post('/update', [AdminController::class, 'update']);
    Route::post('/change-status', [AdminController::class, 'changeStatus']);
    Route::post('/tim-kiem', [AdminController::class, 'search']);
    Route::post('/filter-by-role', [AdminController::class, 'filterByRole']);
});


Route::prefix('/admin/danh-muc-bai-hoc')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [KiemDuyetBaiHocConTroller::class, 'index']);
    Route::post('/', [KiemDuyetBaiHocConTroller::class, 'store']);
    Route::put('/{id}', [KiemDuyetBaiHocConTroller::class, 'update']);
    Route::delete('/{id}', [KiemDuyetBaiHocConTroller::class, 'destroy']);
    Route::patch('/{id}/trang-thai', [KiemDuyetBaiHocConTroller::class, 'changeStatus']);

    Route::get('/{id}/bai-hoc', [KiemDuyetBaiHocConTroller::class, 'getBaiHoc']);
});


Route::prefix('/admin/kiem-duyet-bai-hoc')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [KiemDuyetBaiHocConTroller::class, 'index']);
    Route::patch('/{id}/trang-thai', [KiemDuyetBaiHocConTroller::class, 'changeStatus']);

    Route::get('/{id}/tu-vung', [KiemDuyetBaiHocConTroller::class, 'getTuVung']);
});



//---------------------------------------------CLIENT--------------------------------------------------------------
Route::get('/check-token', [NguoiDungController::class, 'checkToken']);
Route::post('/login-google', [NguoiDungController::class, 'loginGoogle']);
Route::post('/dang-nhap', [NguoiDungController::class, 'login']);
Route::post('/login-google', [NguoiDungController::class, 'loginGoogle']);
Route::post('/dang-ky', [NguoiDungController::class, 'register']);

Route::post('/quen-mat-khau', [NguoiDungController::class, 'forgotPassword']);
Route::post('/dat-lai-mat-khau', [NguoiDungController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/check-token', [NguoiDungController::class, 'checkToken']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/dang-xuat', [NguoiDungController::class, 'logOut']);

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
