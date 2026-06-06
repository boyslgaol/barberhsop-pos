<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->date('birthdate')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->integer('points')->default(0);
            $table->string('member_code')->unique();
            $table->enum('member_level', ['regular', 'silver', 'gold', 'platinum'])->default('regular');
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->integer('visit_count')->default(0);
            $table->date('last_visit')->nullable();
            $table->timestamps();
            
            $table->index('phone');
            $table->index('member_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};