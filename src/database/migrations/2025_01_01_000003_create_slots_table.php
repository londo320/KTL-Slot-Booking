<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlotsTable extends Migration
{
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('depot_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('booking_type_id')
                  ->nullable()
                  ->constrained('booking_types')
                  ->nullOnDelete();

            // 15‑minute interval boundaries
            $table->dateTime('start_at');
            $table->dateTime('end_at');

            // Slot can be blocked independent of bookings
            $table->boolean('is_blocked')->default(false);

            $table->timestamps();

            // Index for fast availability look‑ups per depot & day
            $table->index(['depot_id', 'start_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
}