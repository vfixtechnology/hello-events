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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Buyer Information
            $table->string('buyer_name');
            $table->string('buyer_email');
            $table->string('buyer_phone')->nullable();

            // buyer billing details
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('pincode')->nullable();

            // Financial Breakdown
            $table->decimal('subtotal', 10, 2); // Price before discounts and taxes
            $table->string('coupon_code')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2); // The final amount charged

            // Order & Payment Status
            $table->string('status')->default('pending'); // e.g., pending, completed, failed
            $table->string('payment_id')->nullable(); // For payment gateway reference
            $table->string('payment_method')->nullable();  // payment gateway name
            $table->string('currency', 3)->default('INR');  // get currecy code
            $table->json('other')->nullable(); // For any extra data

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
