<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bai_kiem_tras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bai_hoc_id')->unique()->constrained('bai_hocs')->cascadeOnDelete();
            $table->foreignId('nguoi_tao_id')->constrained('nguoi_dungs')->cascadeOnDelete();
            $table->string('tieu_de', 200)->nullable();
            $table->text('mo_ta_huong_dan')->nullable();
            $table->unsignedInteger('thoi_gian_gioi_han_giay')->default(900);
            $table->unsignedSmallInteger('diem_toi_thieu')->default(0);
            $table->unsignedTinyInteger('trang_thai')->default(0);
            $table->timestamps();
        });

        Schema::create('cau_hoi_kiem_tras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bai_kiem_tra_id')->constrained('bai_kiem_tras')->cascadeOnDelete();
            $table->foreignId('tu_vung_id')->nullable()->constrained('tu_vungs')->nullOnDelete();
            $table->string('loai', 20);
            $table->unsignedSmallInteger('thu_tu')->default(1);
            $table->text('noi_dung_cau')->nullable();
            $table->unsignedTinyInteger('diem_toi_da')->default(10);
            $table->timestamps();
            $table->index(['bai_kiem_tra_id', 'thu_tu']);
        });

        Schema::create('lua_chon_cau_hois', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cau_hoi_kiem_tra_id')->constrained('cau_hoi_kiem_tras')->cascadeOnDelete();
            $table->string('noi_dung', 500);
            $table->boolean('la_dung')->default(false);
            $table->unsignedSmallInteger('thu_tu')->default(1);
            $table->timestamps();
        });

        Schema::create('phien_kiem_tras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dungs')->cascadeOnDelete();
            $table->foreignId('bai_kiem_tra_id')->constrained('bai_kiem_tras')->cascadeOnDelete();
            $table->timestamp('thoi_gian_bat_dau')->nullable();
            $table->timestamp('thoi_gian_ket_thuc')->nullable();
            $table->unsignedSmallInteger('tong_diem')->nullable();
            $table->unsignedTinyInteger('trang_thai')->default(0);
            $table->timestamps();
            $table->index(['nguoi_dung_id', 'bai_kiem_tra_id']);
        });

        Schema::create('chi_tiet_phien_kiem_tras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phien_kiem_tra_id')->constrained('phien_kiem_tras')->cascadeOnDelete();
            $table->foreignId('cau_hoi_kiem_tra_id')->constrained('cau_hoi_kiem_tras')->cascadeOnDelete();
            $table->foreignId('lua_chon_id')->nullable()->constrained('lua_chon_cau_hois')->nullOnDelete();
            $table->string('file_ghi_am_url', 255)->nullable();
            $table->unsignedSmallInteger('diem_dat')->nullable();
            $table->json('phan_hoi')->nullable();
            $table->timestamps();
            $table->unique(['phien_kiem_tra_id', 'cau_hoi_kiem_tra_id'], 'uniq_pkt_cau');
        });

        Schema::table('tien_do_bai_hocs', function (Blueprint $table) {
            $table->decimal('diem_kiem_tra', 6, 2)->nullable();
            $table->unsignedTinyInteger('qua_kiem_tra')->nullable();
            $table->timestamp('thoi_gian_kiem_tra_cuoi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tien_do_bai_hocs', function (Blueprint $table) {
            $table->dropColumn(['diem_kiem_tra', 'qua_kiem_tra', 'thoi_gian_kiem_tra_cuoi']);
        });
        Schema::dropIfExists('chi_tiet_phien_kiem_tras');
        Schema::dropIfExists('phien_kiem_tras');
        Schema::dropIfExists('lua_chon_cau_hois');
        Schema::dropIfExists('cau_hoi_kiem_tras');
        Schema::dropIfExists('bai_kiem_tras');
    }
};
