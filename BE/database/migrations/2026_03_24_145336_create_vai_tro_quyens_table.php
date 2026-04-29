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
        Schema::create('vai_tro_quyens', function (Blueprint $table) {
            $table->foreignId('vai_tro_id')->constrained('vai_tros')->onDelete('cascade');
            $table->foreignId('quyen_id')->constrained('quyens')->onDelete('cascade');
            $table->primary(['vai_tro_id', 'quyen_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vai_tro_quyens');
    }
};
