<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('description');
            $table->integer('default_case_count')->nullable();
            $table->integer('default_pallets')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('po_reference')->nullable();
            $table->integer('cases')->nullable();
            $table->integer('pallets')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_product');
        Schema::dropIfExists('products');
    }
};