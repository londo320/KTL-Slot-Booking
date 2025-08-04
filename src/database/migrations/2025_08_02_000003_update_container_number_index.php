<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop old index with trailer_number
            $table->dropIndex(['vehicle_registration', 'trailer_number']);
            
            // Add new index with container_number
            $table->index(['vehicle_registration', 'container_number']);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop new index
            $table->dropIndex(['vehicle_registration', 'container_number']);
            
            // Restore old index (if reverting)
            $table->index(['vehicle_registration', 'trailer_number']);
        });
    }
};