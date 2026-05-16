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
        Schema::create('goi_premiums', function (Blueprint $table) {
            $table->id();
            $table->string('ten_goi', 100);
            $table->string('doi_tuong', 20);
            $table->text('mo_ta')->nullable();
            $table->unsignedBigInteger('gia');
            $table->unsignedInteger('thoi_han_ngay');
            $table->json('tinh_nang');
            $table->integer('trang_thai')->default(1); // 0 = tam_ngung, 1 = dang_hoat_dong
            $table->timestamps();

            $table->index('doi_tuong');
            $table->index('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goi_premiums');
    }
};
