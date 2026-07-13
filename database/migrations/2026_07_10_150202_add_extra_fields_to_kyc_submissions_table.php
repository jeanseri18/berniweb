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
    Schema::table('kyc_submissions', function (Blueprint $table) {

        // Coordonnées
        $table->string('full_name')->nullable();
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
        $table->text('address')->nullable();

        // Paiement complémentaire
        $table->string('payment_method')->nullable();
        $table->string('payment_account')->nullable();

    });
}

public function down(): void
{
    Schema::table('kyc_submissions', function (Blueprint $table) {
        $table->dropColumn([
            'full_name',
            'phone',
            'email',
            'address',
            'payment_method',
            'payment_account',
        ]);
    });
}
};
