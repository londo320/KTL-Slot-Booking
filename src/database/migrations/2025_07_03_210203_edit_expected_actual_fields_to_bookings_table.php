<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['case_count', 'expected_case_count', 'pallet_case_variance']);

            $table->integer('actual_cases')->nullable()->after('notes');
            $table->integer('expected_cases')->nullable()->after('actual_cases');
            $table->integer('case_variance')->nullable()->after('expected_cases');

            $table->integer('pallet_variance')->nullable()->after('actual_pallets');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('case_count')->nullable();
            $table->integer('expected_case_count')->nullable();
            $table->integer('pallet_case_variance')->nullable();

            $table->dropColumn([
                'actual_cases',
                'expected_cases',
                'case_variance',
                'pallet_variance',
            ]);
        });
    }
};