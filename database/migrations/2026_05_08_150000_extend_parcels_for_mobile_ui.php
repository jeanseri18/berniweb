<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            if (!Schema::hasColumn('parcels', 'category')) {
                $table->string('category')->nullable()->after('description');
            }
            if (!Schema::hasColumn('parcels', 'fragile')) {
                $table->boolean('fragile')->default(true)->after('weight');
            }
            if (!Schema::hasColumn('parcels', 'recipient_note')) {
                $table->text('recipient_note')->nullable()->after('recipient_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            if (Schema::hasColumn('parcels', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('parcels', 'fragile')) {
                $table->dropColumn('fragile');
            }
            if (Schema::hasColumn('parcels', 'recipient_note')) {
                $table->dropColumn('recipient_note');
            }
        });
    }
};

