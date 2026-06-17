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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percent']); // Fixed amount or percentage
            $table->decimal('value'); // The discount value or percentage
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('max_uses')->nullable(); // Total times the coupon can be used
            $table->unsignedInteger('uses')->default(0); // How many times it has been used
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
