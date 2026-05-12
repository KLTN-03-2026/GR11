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
        Schema::create('giao_dich_vis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vi_id')
                ->constrained('vis')
                ->onDelete('cascade');
            $table->string('loai_giao_dich', 50);
            $table->string('chieu_giao_dich', 4);
            $table->unsignedBigInteger('so_tien');
            $table->unsignedBigInteger('so_du_truoc');
            $table->unsignedBigInteger('so_du_sau');
            $table->nullableMorphs('tham_chieu');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->index('vi_id');
            $table->index('loai_giao_dich');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giao_dich_vis');
    }
};
