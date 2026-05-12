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
        Schema::create('yeu_cau_rut_tiens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('giao_vien_id')
                ->constrained('nguoi_dungs')
                ->onDelete('cascade');
            $table->unsignedBigInteger('so_tien');
            $table->string('ten_ngan_hang_snapshot', 100);
            $table->string('so_tai_khoan_snapshot', 30);
            $table->string('chu_tai_khoan_snapshot', 100);
            $table->integer('trang_thai')->default(0); // 0 = cho_duyet, 1 = da_duyet, 2 = tu_choi
            $table->text('ly_do_tu_choi')->nullable();
            $table->foreignId('admin_xu_ly_id')
                ->nullable()
                ->constrained('nguoi_dungs')
                ->onDelete('set null');
            $table->timestamp('ngay_xu_ly')->nullable();
            $table->timestamps();

            $table->index('giao_vien_id');
            $table->index('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_rut_tiens');
    }
};
