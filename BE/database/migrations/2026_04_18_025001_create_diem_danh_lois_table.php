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
        Schema::create('diem_danh_lois', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dungs')->onDelete('cascade');
            $table->foreignId('tu_vung_id')->constrained('tu_vungs')->onDelete('cascade');
            $table->string('muc_do_uu_tien', 50)->default('binh_thuong'); // 'thap', 'binh_thuong', 'cao'
            $table->text('ghi_chu')->nullable(); // Ghi chú của trẻ em
            $table->boolean('da_hoan_thanh')->default(false); // Đã hoàn thành ôn tập chưa
            $table->timestamp('ngay_danh_dau')->useCurrent(); // Ngày đánh dấu
            $table->timestamp('ngay_hoan_thanh')->nullable(); // Ngày hoàn thành ôn tập
            $table->timestamp('ngay_tao')->useCurrent();
            $table->timestamp('ngay_cap_nhat')->useCurrent()->useCurrentOnUpdate();
            
            // Indexes để tối ưu query
            $table->index('nguoi_dung_id');
            $table->index('tu_vung_id');
            $table->index('da_hoan_thanh');
            $table->index('muc_do_uu_tien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diem_danh_lois');
    }
};
