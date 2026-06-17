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
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('title'); // e.g., VIP, Basic
            $table->mediumText('body')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('compare_at_price', 8, 2)->nullable(); // For display purposes
            $table->integer('quantity'); ///over all quantity
            $table->integer('min_quantity')->default(1); // Minimum tickets required
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};
