<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kyc_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('kyc_submissions', 'transport_photo')) {
                $table->string('transport_photo')->nullable()->after('transport_plate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kyc_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('kyc_submissions', 'transport_photo')) {
                $table->dropColumn('transport_photo');
            }
        });
    }
};
