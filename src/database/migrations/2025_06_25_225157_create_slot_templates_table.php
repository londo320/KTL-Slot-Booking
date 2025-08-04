<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('slot_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depot_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('day_of_week'); // e.g. 'Monday'
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slot_templates');
    }
};
