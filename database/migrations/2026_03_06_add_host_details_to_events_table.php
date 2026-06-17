<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('city')->nullable()->after('location');
            $table->string('state')->nullable()->after('city');
            $table->text('full_address')->nullable()->after('state');
            $table->string('map_link')->nullable()->after('full_address');
            $table->string('host_name')->nullable()->after('map_link');
            $table->string('host_email')->nullable()->after('host_name');
            $table->string('host_phone')->nullable()->after('host_email');
            $table->string('host_facebook')->nullable()->after('host_phone');
            $table->string('host_instagram')->nullable()->after('host_facebook');
            $table->string('host_twitter')->nullable()->after('host_instagram');
            $table->string('host_linkedin')->nullable()->after('host_twitter');
            $table->string('host_website')->nullable()->after('host_linkedin');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'city', 'state', 'full_address', 'map_link',
                'host_name', 'host_email', 'host_phone',
                'host_facebook', 'host_instagram', 'host_twitter', 'host_linkedin', 'host_website',
            ]);
        });
    }
};
