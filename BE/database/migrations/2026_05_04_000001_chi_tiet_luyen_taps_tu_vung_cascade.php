<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chi_tiet_luyen_taps', function (Blueprint $table) {
            $table->dropForeign(['tu_vung_id']);
        });

        Schema::table('chi_tiet_luyen_taps', function (Blueprint $table) {
            $table->foreign('tu_vung_id')
                ->references('id')
                ->on('tu_vungs')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('chi_tiet_luyen_taps', function (Blueprint $table) {
            $table->dropForeign(['tu_vung_id']);
        });

        Schema::table('chi_tiet_luyen_taps', function (Blueprint $table) {
            $table->foreign('tu_vung_id')
                ->references('id')
                ->on('tu_vungs');
        });
    }
};
