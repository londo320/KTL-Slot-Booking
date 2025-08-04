<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('slot_generation_settings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('depot_id')->constrained()->cascadeOnDelete();
        $table->time('start_time');
        $table->time('end_time');
        $table->unsignedInteger('interval_minutes')->default(60);
        $table->unsignedInteger('slots_per_block')->default(1);
        $table->unsignedInteger('default_capacity')->default(1);
        $table->json('days_active')->nullable(); // e.g. ["mon","tue"]
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot_generation_settings');
    }
};
