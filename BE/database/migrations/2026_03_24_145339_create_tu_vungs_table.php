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
        Schema::create('tu_vungs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bai_hoc_id')->constrained('bai_hocs')->onDelete('cascade');
            $table->string('tu_chuan', 100);
            $table->string('phien_am', 100)->nullable();
            $table->string('cap_do', 20)->nullable();
            $table->string('hinh_anh_url', 255)->nullable();
            $table->string('am_thanh_mau_url', 255)->nullable();
            $table->integer('thu_tu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tu_vungs');
    }
};
