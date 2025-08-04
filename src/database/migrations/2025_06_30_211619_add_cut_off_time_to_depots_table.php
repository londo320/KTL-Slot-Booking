<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('depots', function (Blueprint $table) {
            $table->time('cut_off_time')->default('16:00');
        });
    }

    public function down(): void {
        Schema::table('depots', function (Blueprint $table) {
            $table->dropColumn('cut_off_time');
        });
    }
};
