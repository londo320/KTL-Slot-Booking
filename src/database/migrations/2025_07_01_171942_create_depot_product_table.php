<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration

    /**
     * Run the migrations.
     */
{
    public function up(): void {
        Schema::create('depot_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depot_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('expected_case_count')->nullable();
             $table->integer('min_cases')->nullable();
            $table->integer('max_cases')->nullable();
            $table->unsignedInteger('override_duration_minutes')->nullable();
             $table->integer('duration_override_minutes')->nullable();
            $table->timestamps();

            $table->unique(['depot_id', 'product_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('depot_product');
    }
};