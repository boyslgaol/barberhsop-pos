<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('queue_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->foreignId('service_id')->constrained();
            $table->foreignId('barber_id')->nullable()->constrained('users');
            $table->decimal('estimated_price', 10, 2);
            $table->integer('estimated_duration'); // in minutes
            $table->integer('position')->nullable();
            $table->enum('status', ['waiting', 'calling', 'in_service', 'completed', 'cancelled'])->default('waiting');
            $table->datetime('queue_time');
            $table->datetime('call_time')->nullable();
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index('queue_number');
            $table->index('status');
            $table->index('queue_time');
        });
        
        // Create queue settings table
        Schema::create('queue_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
        Schema::dropIfExists('queue_settings');
    }
};