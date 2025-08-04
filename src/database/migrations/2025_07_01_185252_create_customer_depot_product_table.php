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
Schema::create('customer_depot_product', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained()->onDelete('cascade');
    $table->foreignId('depot_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->unsignedInteger('min_cases')->nullable();
    $table->unsignedInteger('max_cases')->nullable();
    $table->unsignedInteger('override_duration_minutes')->nullable();
    $table->timestamps();

    $table->unique(['customer_id', 'depot_id', 'product_id'], 'cdp_unique');
});
    }

    public function down(): void {
        Schema::dropIfExists('customer_depot_product');
    }
};