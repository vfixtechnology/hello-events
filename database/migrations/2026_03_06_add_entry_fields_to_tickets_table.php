<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('max_entries')->default(1)->after('checked_in_at');
            $table->integer('check_in_count')->default(0)->after('max_entries');
            $table->timestamp('first_check_in_at')->nullable()->after('check_in_count');
            $table->timestamp('last_check_in_at')->nullable()->after('first_check_in_at');
            $table->enum('status', ['valid', 'used', 'cancelled', 'refunded'])->default('valid')->after('last_check_in_at');
            $table->text('cancellation_reason')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'max_entries', 'check_in_count', 'first_check_in_at',
                'last_check_in_at', 'status', 'cancellation_reason',
            ]);
        });
    }
};
