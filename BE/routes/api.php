<?php

use App\Http\Controllers\BaiHocController;
use App\Http\Controllers\DanhMucBaiHocController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\TtsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
Route::get('/check-token', [NguoiDungController::class, 'checkToken']);
Route::post('/login-google', [NguoiDungController::class, 'loginGoogle']);
Route::post('/dang-nhap', [NguoiDungController::class, 'login']);
Route::post('/dang-ky', [NguoiDungController::class, 'register']);

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
