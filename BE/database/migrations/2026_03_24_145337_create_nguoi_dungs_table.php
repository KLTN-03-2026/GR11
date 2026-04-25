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
        Schema::create('nguoi_dungs', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten', 100);
            $table->string('email', 150)->unique();
            $table->string('mat_khau', 255);
            $table->string('sdt', 10)->nullable();
            $table->foreignId('vai_tro_id')->constrained('vai_tros')->onDelete('cascade');
            $table->date('ngay_sinh')->nullable();
            $table->string('anh_dai_dien', 255)->nullable();
            $table->timestamp('ngay_tao')->useCurrent();
            $table->integer('trang_thai')->default(0);
            $table->integer('type_account')->default(0);
            $table->string('content_block')->nullable();
            $table->string('hash_active')->nullable();
            $table->string('hash_reset')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoi_dungs');
    }
};
