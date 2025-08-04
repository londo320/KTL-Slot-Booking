<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slot_release_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->tinyInteger('release_day')->comment('ISO day of week: 1=Mon … 7=Sun');
            $table->time('release_time');
            $table->integer('lock_cutoff_days')->default(1);
            $table->time('lock_cutoff_time')->default('16:00:00');
            $table->unsignedInteger('priority')->default(0);
            $table->timestamps();

            $table->unique(['depot_id','customer_id','release_day','release_time'], 'slot_release_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slot_release_rules');
    }
};
