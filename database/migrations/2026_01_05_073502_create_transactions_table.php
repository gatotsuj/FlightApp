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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('flight_id')->constrained()->onDelete('cascade');
            $table->foreignId('flight_class_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->integer('number_of_passengers', false, true);
            $table->foreignId('promo_code_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('grand_total', 10, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
