<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->unique()->after('email');
            $table->string('otp_code')->nullable()->after('password');
            $table->boolean('is_verified')->default(false)->after('otp_code');
            $table->enum('role', ['admin', 'user'])->default('user')->after('is_verified');
            $table->boolean('is_courier')->default(false)->after('role');
            $table->enum('courier_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('is_courier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'otp_code', 'is_verified', 'role', 'is_courier', 'courier_status']);
        });
    }
};
