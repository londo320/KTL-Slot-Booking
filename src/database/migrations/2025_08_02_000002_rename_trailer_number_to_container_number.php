<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Rename trailer_number to container_number
            $table->renameColumn('trailer_number', 'container_number');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Revert back to trailer_number
            $table->renameColumn('container_number', 'trailer_number');
        });
    }
};