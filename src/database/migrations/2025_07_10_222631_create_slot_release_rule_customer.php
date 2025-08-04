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
     Schema::create('slot_release_rule_customer', function (Blueprint $table) {
    $table->foreignId('slot_release_rule_id')->constrained()->cascadeOnDelete();
    $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
    $table->primary(['slot_release_rule_id','customer_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot_release_rule_customer');
    }
};
