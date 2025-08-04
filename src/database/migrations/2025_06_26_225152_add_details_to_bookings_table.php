<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void
{
    Schema::table('bookings', function (Blueprint $table) {
        if (!Schema::hasColumn('bookings', 'booking_type_id')) {
            $table->foreignId('booking_type_id')
                  ->after('slot_id')
                  ->constrained()
                  ->onDelete('cascade');
        }

        if (!Schema::hasColumn('bookings', 'case_count')) {
            $table->unsignedInteger('case_count')->default(0)->after('booking_type_id');
        }

        if (!Schema::hasColumn('bookings', 'container_size')) {
            $table->unsignedInteger('container_size')->default(0)->after('case_count');
        }

        if (!Schema::hasColumn('bookings', 'reference')) {
            $table->string('reference')->nullable()->after('container_size');
        }

        if (!Schema::hasColumn('bookings', 'notes')) {
            $table->text('notes')->nullable()->after('reference');
        }
    });
}

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['booking_type_id']);
            $table->dropColumn([
                'booking_type_id',
                'case_count',
                'container_size',
                'reference',
                'notes',
            ]);
        });
    }
};
