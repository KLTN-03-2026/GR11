<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ho_so_giao_viens', function (Blueprint $table) {
            // 1. Profile Info
            $table->string('anh_dai_dien')->nullable()->after('so_dien_thoai');
            // 2. Identity Verification (KYC)
            $table->string('cccd_mat_truoc')->nullable()->after('anh_dai_dien');
            $table->string('cccd_mat_sau')->nullable()->after('cccd_mat_truoc');
            // 3. Professional Qualifications
            $table->string('bang_cap')->nullable()->after('cccd_mat_sau');
        });
    }

    public function down(): void
    {
        Schema::table('ho_so_giao_viens', function (Blueprint $table) {
            $table->dropColumn(['anh_dai_dien', 'cccd_mat_truoc', 'cccd_mat_sau', 'bang_cap']);
        });
    }
};
