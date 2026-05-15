<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_report_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dungs')->cascadeOnDelete();
            $table->string('loai_bao_cao', 50);
            $table->date('tu_ngay');
            $table->date('den_ngay');
            $table->json('payload_json');
            $table->string('file_path')->nullable();
            $table->timestamps();

            $table->index(['nguoi_dung_id', 'loai_bao_cao', 'tu_ngay']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_report_snapshots');
    }
};
