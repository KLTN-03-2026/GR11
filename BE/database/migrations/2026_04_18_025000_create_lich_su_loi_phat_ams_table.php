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
        Schema::create('lich_su_loi_phat_ams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dungs')->onDelete('cascade');
            $table->foreignId('tu_vung_id')->constrained('tu_vungs')->onDelete('cascade');
            $table->foreignId('phien_id')->nullable()->constrained('phien_luyen_taps')->onDelete('set null');
            $table->string('loai_loi', 50); // 'am_dau', 'van', 'thanh_dieu'
            $table->integer('so_lan_mac_loi')->default(1); // Số lần trẻ em mắc lỗi này
            $table->timestamp('lan_mac_loi_gan_nhat')->useCurrent(); // Lần gần nhất mắc lỗi
            $table->text('chi_tiet_loi')->nullable(); // Chi tiết về lỗi
            $table->string('trang_thai', 50)->default('chua_on_tap'); // 'chua_on_tap', 'dang_on_tap', 'da_phat_hien_on_tap'
            $table->timestamp('ngay_tao')->useCurrent();
            $table->timestamp('ngay_cap_nhat')->useCurrent()->useCurrentOnUpdate();
            
            // Indexes để tối ưu query
            $table->index('nguoi_dung_id');
            $table->index('tu_vung_id');
            $table->index('trang_thai');
            $table->unique(['nguoi_dung_id', 'tu_vung_id', 'loai_loi']); // Mỗi người chỉ có một bản ghi cho mỗi từ + loại lỗi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_loi_phat_ams');
    }
};
