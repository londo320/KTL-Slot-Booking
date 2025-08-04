<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepotCaseRangesTable extends Migration
{
    public function up()
    {
        Schema::create('depot_case_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depot_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('min_cases')->nullable();
            $table->unsignedInteger('max_cases')->nullable();
            $table->unsignedInteger('duration_minutes');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('depot_case_ranges');
    }
}