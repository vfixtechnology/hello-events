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
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique();
        $table->mediumText('excerpt')->nullable();
        $table->longText('body');
        $table->string('venue')->nullable();
        $table->string('location')->nullable();
        $table->longText('map')->nullable();
        $table->string('video')->nullable();
        $table->dateTime('start_datetime');
        $table->dateTime('end_datetime');
        $table->string('timezone')->nullable();
        $table->boolean('featured')->default(false);
        $table->boolean('published')->default(true);
        $table->foreignId('tax_rate_id')
                  ->nullable()
                  ->constrained('tax_rates')
                  ->onDelete('set null'); // If a tax rate is deleted, set this to NULL
        $table->json('other')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });
}

/**
* Reverse the migrations.
*/
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
