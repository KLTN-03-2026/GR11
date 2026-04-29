<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->boolean('is_delivered_to_student')
                ->default(false)
                ->after('is_read_by_teacher');
            $table->boolean('is_read_by_student')
                ->default(false)
                ->after('is_delivered_to_student');
            $table->index(['role', 'is_read_by_student'], 'chat_messages_role_read_student_idx');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('chat_messages_role_read_student_idx');
            $table->dropColumn(['is_delivered_to_student', 'is_read_by_student']);
        });
    }
};
