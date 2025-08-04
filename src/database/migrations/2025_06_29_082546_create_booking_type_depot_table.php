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
        Schema::create('booking_type_depot', function (Blueprint $table) {
        $table->id();
        $table->foreignId('depot_id')->constrained()->cascadeOnDelete();
        $table->foreignId('booking_type_id')->constrained()->cascadeOnDelete();
        $table->unsignedInteger('duration_minutes');
        $table->timestamps();

        $table->unique(['depot_id', 'booking_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_type_depot');
    }
};
