<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek dan tambahkan kolom phone jika belum ada
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            
            // Cek dan tambahkan kolom address jika belum ada
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            
            // Cek dan tambahkan kolom role jika belum ada
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'kasir', 'owner'])->default('kasir')->after('address');
            }
            
            // Cek dan tambahkan kolom is_active jika belum ada
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
            
            // Cek dan tambahkan kolom avatar jika belum ada
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('is_active');
            }
            
            // Cek dan tambahkan kolom last_login_at jika belum ada
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('avatar');
            }
            
            // Cek dan tambahkan kolom last_login_ip jika belum ada
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['phone', 'address', 'role', 'is_active', 'avatar', 'last_login_at', 'last_login_ip'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}