<?php

use App\Http\Controllers\BaiHocController;
use App\Http\Controllers\DanhMucBaiHocController;
use App\Http\Controllers\NguoiDungController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
});

Route::prefix('/danh-muc-bai-hoc')->group(function () {

    Route::get('/', [DanhMucBaiHocController::class, 'indexPublic']);
});

Route::prefix('/bai-hoc')->group(function () {

    Route::get('/', [BaiHocController::class, 'indexPublic']);
    Route::get('/{baiHoc}', [BaiHocController::class, 'showPublic']);
});

