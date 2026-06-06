<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained();
            $table->datetime('transaction_date');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->decimal('paid_amount', 12, 2);
            $table->decimal('change_amount', 12, 2);
            $table->enum('payment_method', ['cash', 'qris', 'debit', 'credit'])->default('cash');
            $table->string('payment_reference')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('completed');
            $table->integer('points_earned')->default(0);
            $table->integer('points_used')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('invoice_number');
            $table->index('transaction_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};