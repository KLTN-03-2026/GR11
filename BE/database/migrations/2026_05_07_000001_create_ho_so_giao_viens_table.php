<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ho_so_giao_viens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nguoi_dung_id');
            $table->string('ho_ten');
            $table->string('email');
            $table->string('so_dien_thoai')->nullable();
            $table->string('chuyen_mon');
            $table->text('mo_ta')->nullable();
            // 0 = chờ duyệt, 1 = đã duyệt, 2 = từ chối
            $table->tinyInteger('trang_thai')->default(0);
            $table->text('ghi_chu_admin')->nullable();
            $table->timestamps();

            $table->foreign('nguoi_dung_id')
                ->references('id')
                ->on('nguoi_dungs')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ho_so_giao_viens');
    }
};
