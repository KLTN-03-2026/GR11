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
        Schema::create('quan_he_gv_hvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('giao_vien_id')->constrained('nguoi_dungs')->onDelete('cascade');
            $table->foreignId('hoc_vien_id')->constrained('nguoi_dungs')->onDelete('cascade');
            $table->string('trang_thai')->default('dang_theo_doi');
            $table->timestamp('ngay_ket_noi')->useCurrent();
            $table->timestamps();
            $table->unique(['giao_vien_id', 'hoc_vien_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quan_he_gv_hvs');
    }
};
