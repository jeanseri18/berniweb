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
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('courier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('description');
            $table->string('departure_address');
            $table->string('destination_address');
            $table->dateTime('departure_date');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->decimal('price', 10, 2);
            $table->decimal('weight', 8, 2);
            $table->enum('status', ['published', 'assigned', 'picked_up', 'in_transit', 'delivered', 'completed', 'cancelled'])->default('published');
            $table->string('verification_code')->nullable();
            $table->enum('payment_status', ['pending', 'sequestered', 'released', 'refunded'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcels');
    }
};
