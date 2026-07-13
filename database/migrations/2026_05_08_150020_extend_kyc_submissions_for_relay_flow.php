<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kyc_submissions', function (Blueprint $table) {
            foreach ([
                'transport_mode',
                'transport_model',
                'transport_plate',
                'zone_hint',
                'availability_hint',
                'payment_kind',
                'momo_number',
            ] as $col) {
                if (!Schema::hasColumn('kyc_submissions', $col)) {
                    $table->string($col)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('kyc_submissions', function (Blueprint $table) {
            foreach ([
                'transport_mode',
                'transport_model',
                'transport_plate',
                'zone_hint',
                'availability_hint',
                'payment_kind',
                'momo_number',
            ] as $col) {
                if (Schema::hasColumn('kyc_submissions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

