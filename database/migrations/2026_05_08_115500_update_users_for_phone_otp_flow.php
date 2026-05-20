<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The mobile app signup flow does not collect email.
        // Avoid relying on doctrine/dbal by using vendor-specific SQL where needed.
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL');
        } elseif ($driver === 'sqlite') {
            // SQLite cannot easily alter columns without table rebuild; keep as-is.
            // This project uses MySQL in .env by default.
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'otp_expires_at')) {
                $table->dropColumn('otp_expires_at');
            }
        });

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE `users` MODIFY `email` VARCHAR(255) NOT NULL');
        }
    }
};

