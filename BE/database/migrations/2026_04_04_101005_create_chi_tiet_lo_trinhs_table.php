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
        Schema::create('chi_tiet_lo_trinhs', function (Blueprint $table) {
            // Khai báo Foreign Keys
            $table->foreignId('lo_trinh_id')->constrained('lo_trinh_ca_nhans')->onDelete('cascade');
            $table->foreignId('bai_hoc_id')->constrained('bai_hocs')->onDelete('cascade');
            // Các trường thông tin bổ sung
            $table->integer('thu_tu_uu_tien')->default(1)->comment('Thứ tự thực hiện bài học trong lộ trình');
            $table->text('ghi_chu_gv')->nullable()->comment('Dặn dò riêng của giáo viên cho bài học này');
            // Thiết lập Primary Key phức hợp (Tự động tạo Unique Index)
            $table->primary(['lo_trinh_id', 'bai_hoc_id']);
            // Index đơn cho bài học để truy vấn ngược nhanh hơn (VD: Bài học này thuộc những lộ trình nào?)
            $table->index('bai_hoc_id', 'idx_chitiet_bai_hoc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_lo_trinhs');
    }
};
