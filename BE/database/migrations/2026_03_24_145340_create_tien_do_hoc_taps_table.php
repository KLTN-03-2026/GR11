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
        Schema::create('tien_do_hoc_taps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dungs')->onDelete('cascade');
            $table->foreignId('tu_vung_id')->constrained('tu_vungs')->onDelete('cascade');
            $table->integer('trang_thai')->nullable(); // 0 = chua_on_tap, 1 = dang_on_tap, 2 = da_hoan_thanh
            $table->integer('so_lan_luyen_tap')->default(0);
            $table->integer('diem_cao_nhat')->default(0);
            $table->timestamp('ngay_cap_nhat_cuoi')->useCurrent()->useCurrentOnUpdate();
            $table->unique(['nguoi_dung_id', 'tu_vung_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tien_do_hoc_taps');
    }
};
