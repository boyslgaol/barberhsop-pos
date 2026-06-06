<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained();
            $table->foreignId('barber_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};