<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('danh_muc_bai_hocs', function (Blueprint $table) {
            $table->id();
            $table->string('ten_danh_muc', 100);
            $table->string('slug_danh_muc', 100)->unique();
            $table->text('mo_ta')->nullable();
            $table->integer('trang_thai')->default(0); // 0 = hiển thị, 1 = tạm ẩn
            $table->integer('thu_tu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_muc_bai_hocs');
    }
};
