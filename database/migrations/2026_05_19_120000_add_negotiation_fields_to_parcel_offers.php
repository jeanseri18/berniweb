<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parcel_offers', function (Blueprint $table) {
            $table->decimal('courier_amount', 12, 2)->nullable()->after('amount');
            $table->decimal('sender_amount', 12, 2)->nullable()->after('courier_amount');
            $table->string('last_counter_by', 10)->default('courier')->after('sender_amount');
        });

        foreach (DB::table('parcel_offers')->orderBy('id')->cursor() as $row) {
            $turns = (int) $row->turns_used;
            $last = $turns % 2 === 0 ? 'courier' : 'sender';
            DB::table('parcel_offers')->where('id', $row->id)->update([
                'courier_amount' => $row->amount,
                'sender_amount' => $turns % 2 === 1 ? $row->amount : null,
                'last_counter_by' => $last,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('parcel_offers', function (Blueprint $table) {
            $table->dropColumn(['courier_amount', 'sender_amount', 'last_counter_by']);
        });
    }
};
