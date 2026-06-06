<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_busy')) {
                $table->boolean('is_busy')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('users', 'current_queue_id')) {
                $table->foreignId('current_queue_id')->nullable()->after('is_busy');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_busy', 'current_queue_id']);
        });
    }
};