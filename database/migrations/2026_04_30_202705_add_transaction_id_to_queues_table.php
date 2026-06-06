<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            if (!Schema::hasColumn('queues', 'transaction_id')) {
                $table->foreignId('transaction_id')->nullable()->after('barber_id')
                      ->constrained('transactions')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            if (Schema::hasColumn('queues', 'transaction_id')) {
                $table->dropForeign(['transaction_id']);
                $table->dropColumn('transaction_id');
            }
        });
    }
};