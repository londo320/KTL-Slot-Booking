<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Unique booking reference system
            $table->string('booking_reference', 20)->nullable()->unique()->after('id');
            
            // Vehicle/Transportation information
            $table->string('vehicle_registration', 50)->nullable()->after('notes');
            $table->string('trailer_number', 50)->nullable()->after('vehicle_registration');
            $table->string('driver_name', 100)->nullable()->after('trailer_number');
            $table->string('driver_phone', 20)->nullable()->after('driver_name');
            
            // Carrier information
            $table->string('carrier_company', 100)->nullable()->after('driver_phone');
            $table->string('carrier_contact', 100)->nullable()->after('carrier_company');
            
            // Gate/Dock assignment
            $table->string('gate_number', 10)->nullable()->after('carrier_contact');
            $table->string('bay_number', 10)->nullable()->after('gate_number');
            
            // Load/Manifest information
            $table->string('manifest_number', 50)->nullable()->after('bay_number');
            $table->string('load_type', 50)->nullable()->after('manifest_number');
            $table->boolean('hazmat')->default(false)->after('load_type');
            $table->string('temperature_requirements', 50)->nullable()->after('hazmat');
            
            // Estimated vs actual timing
            $table->timestamp('estimated_arrival')->nullable()->after('temperature_requirements');
            $table->text('special_instructions')->nullable()->after('estimated_arrival');
            
            // Index for performance
            $table->index('booking_reference');
            $table->index(['vehicle_registration', 'trailer_number']);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['booking_reference']);
            $table->dropIndex(['vehicle_registration', 'trailer_number']);
            
            $table->dropColumn([
                'booking_reference',
                'vehicle_registration',
                'trailer_number', 
                'driver_name',
                'driver_phone',
                'carrier_company',
                'carrier_contact',
                'gate_number',
                'bay_number',
                'manifest_number',
                'load_type',
                'hazmat',
                'temperature_requirements',
                'estimated_arrival',
                'special_instructions'
            ]);
        });
    }
};