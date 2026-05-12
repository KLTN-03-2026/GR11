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
        Schema::create('lo_trinh_ca_nhans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_vien_id')->constrained('nguoi_dungs')->onDelete('cascade');
            $table->foreignId('giao_vien_id')->constrained('nguoi_dungs')->onDelete('cascade');
            $table->string('ten_lo_trinh');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lo_trinh_ca_nhans');
    }
};
