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
        Schema::create('lo_trinh_tra_phis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lo_trinh_id')
                ->unique()
                ->constrained('lo_trinh_ca_nhans')
                ->onDelete('cascade');
            $table->unsignedBigInteger('gia');
            $table->text('mo_ta_ban')->nullable();
            $table->integer('trang_thai')->default(0); // 0 = cho_duyet, 1 = da_duyet, 2 = tu_choi
            $table->timestamp('ngay_duyet')->nullable();
            $table->timestamps();

            $table->index('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lo_trinh_tra_phis');
    }
};
