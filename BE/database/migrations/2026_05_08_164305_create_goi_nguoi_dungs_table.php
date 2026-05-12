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
        Schema::create('goi_nguoi_dungs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')
                ->constrained('nguoi_dungs')
                ->onDelete('cascade');
            $table->foreignId('goi_id')
                ->constrained('goi_premiums')
                ->onDelete('cascade');
            $table->unsignedBigInteger('gia_da_mua');
            $table->timestamp('ngay_kich_hoat')->useCurrent();
            $table->dateTime('ngay_het_han');
            $table->integer('trang_thai')->default(1); // 0 = het_han, 1 = dang_hoat_dong
            $table->timestamps();

            $table->index('nguoi_dung_id');
            $table->index('trang_thai');
            $table->index('ngay_het_han');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goi_nguoi_dungs');
    }
};
