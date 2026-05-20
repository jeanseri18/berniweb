<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('payment_kind', 20)->nullable()->after('courier_status');
            $table->string('momo_number', 30)->nullable()->after('payment_kind');
        });

        $subs = DB::table('kyc_submissions')
            ->select('user_id', 'payment_kind', 'momo_number')
            ->whereNotNull('payment_kind')
            ->orderByDesc('id')
            ->get()
            ->unique('user_id');

        foreach ($subs as $sub) {
            DB::table('users')->where('id', $sub->user_id)->update([
                'payment_kind' => $sub->payment_kind,
                'momo_number' => $sub->momo_number,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['payment_kind', 'momo_number']);
        });
    }
};
