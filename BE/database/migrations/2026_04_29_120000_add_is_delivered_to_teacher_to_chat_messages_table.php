<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->boolean('is_delivered_to_teacher')
                ->default(false)
                ->after('content');
            $table->index(['role', 'is_delivered_to_teacher'], 'chat_messages_role_delivered_idx');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('chat_messages_role_delivered_idx');
            $table->dropColumn('is_delivered_to_teacher');
        });
    }
};
