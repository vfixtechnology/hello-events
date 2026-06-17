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
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., "GST", "VAT", "Service Tax"
            $table->decimal('rate', 5, 2);   // The tax percentage stored as a decimal. e.g., 18.00 for 18%
            $table->boolean('is_active')->default(true);  // A flag to easily enable or disable a tax rate
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
