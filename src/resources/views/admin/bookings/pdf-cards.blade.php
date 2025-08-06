<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Details #{{ $booking->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            font-size: 11px;
            line-height: 1.3;
            padding: 10px;
            margin: 0;
        }
        .header { 
            text-align: center; 
            margin-bottom: 12px; 
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 4px;
            color: #333;
        }
        .header p {
            font-size: 10px;
            color: #666;
        }
        .status-banner {
            background: #f0f9ff;
            border: 2px solid #0ea5e9;
            padding: 8px;
            margin-bottom: 12px;
            text-align: center;
            font-size: 12px;
            border-radius: 6px;
            font-weight: bold;
        }
        .status-banner.arrived {
            background: #f0fdf4;
            border-color: #22c55e;
            color: #16a34a;
        }
        .status-banner.locked {
            background: #fffbeb;
            border-color: #f59e0b;
            color: #d97706;
        }
        .status-banner.active {
            color: #0369a1;
        }
        
        /* Card Grid Layout */
        .card-grid {
            width: 100%;
        }
        .card-row {
            width: 100%;
            display: block;
            margin-bottom: 8px;
        }
        .card-row:after {
            content: "";
            display: table;
            clear: both;
        }
        .card {
            float: left;
            width: 48%;
            margin-right: 4%;
            vertical-align: top;
            box-sizing: border-box;
        }
        .card:nth-child(2n) {
            margin-right: 0;
        }
        .card-inner {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            background: #fafafa;
            min-height: 140px;
            position: relative;
            margin-bottom: 8px;
        }
        .card-header {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #d1d5db;
        }
        .icon {
            background: #3b82f6;
            color: white;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-block;
            text-align: center;
            line-height: 16px;
            font-size: 8px;
            font-weight: bold;
            margin-right: 4px;
            vertical-align: middle;
        }
        .icon.info { background: #6366f1; }
        .icon.location { background: #10b981; }
        .icon.load { background: #f59e0b; }
        .icon.transport { background: #8b5cf6; }
        .icon.notes { background: #06b6d4; }
        .icon.arrival { background: #22c55e; }
        
        .info-item {
            margin-bottom: 4px;
            display: table;
            width: 100%;
        }
        .label {
            display: table-cell;
            font-weight: bold;
            color: #6b7280;
            width: 35%;
            font-size: 8px;
            vertical-align: top;
            padding-right: 3px;
        }
        .value {
            display: table-cell;
            font-size: 8px;
            color: #111827;
            vertical-align: top;
        }
        
        /* Load comparison styling */
        .load-comparison {
            margin-bottom: 6px;
        }
        .load-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }
        .load-expected {
            display: table-cell;
            width: 40%;
            font-size: 10px;
        }
        .load-arrow {
            display: table-cell;
            width: 15%;
            text-align: center;
            color: #6b7280;
            font-weight: bold;
            font-size: 12px;
        }
        .load-actual {
            display: table-cell;
            width: 45%;
            font-size: 10px;
        }
        .variance {
            font-size: 9px;
            font-weight: bold;
            margin-left: 4px;
        }
        .variance.positive { color: #059669; }
        .variance.negative { color: #dc2626; }
        
        /* Transportation grid */
        .transport-grid {
            display: table;
            width: 100%;
        }
        .transport-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 4px;
        }
        .transport-col:last-child {
            padding-right: 0;
            padding-left: 4px;
        }
        
        /* Special styles */
        .hazmat {
            color: #dc2626;
            font-weight: bold;
        }
        .onsite {
            color: #2563eb;
            font-weight: bold;
        }
        
        /* Full width section for notes */
        .full-width {
            width: 100%;
            margin-top: 8px;
        }
        .notes-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
            background: #f9fafb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Booking Details #{{ $booking->id }}</h1>
        <p>Admin Report | Generated: {{ now()->format('d M Y \a\t H:i') }}</p>
    </div>

    @php
        $isLocked = $booking->slot->locked_at && $booking->slot->locked_at->isPast();
        $hasArrived = $booking->arrived_at;
    @endphp

    @if($hasArrived)
        <div class="status-banner arrived">
            <span class="icon arrival">✓</span> Vehicle Arrived - {{ $booking->arrived_at->format('d M Y, H:i') }}
            @if($booking->departed_at)
                | Departed: {{ $booking->departed_at->format('H:i') }}
            @else
                | Currently On-Site
            @endif
        </div>
    @elseif($isLocked)
        <div class="status-banner locked">
            <span class="icon" style="background: #f59e0b;">!</span> Booking Locked - Cut-off: {{ $booking->slot->locked_at->format('d M Y, H:i') }}
        </div>
    @else
        <div class="status-banner active">
            <span class="icon" style="background: #0ea5e9;">●</span> Booking Active - Can be edited
        </div>
    @endif

    <div class="card-grid">
        <!-- Row 1: Booking Info & Location -->
        <div class="card-row">
            <div class="card">
                <div class="card-inner">
                    <div class="card-header">
                        <span class="icon info">i</span>Booking Information
                    </div>
                    <div class="info-item">
                        <div class="label">Booking ID:</div>
                        <div class="value">#{{ $booking->id }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Customer:</div>
                        <div class="value">{{ $booking->customer->name ?? 'Not assigned' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Created By:</div>
                        <div class="value">{{ $booking->user->name ?? 'Unknown' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Created:</div>
                        <div class="value">{{ $booking->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    @if($booking->reference)
                        <div class="info-item">
                            <div class="label">Reference:</div>
                            <div class="value">{{ $booking->reference }}</div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-inner">
                    <div class="card-header">
                        <span class="icon location">L</span>Slot & Location
                    </div>
                    <div class="info-item">
                        <div class="label">Depot:</div>
                        <div class="value">{{ $booking->slot->depot->name }}</div>
                    </div>
                    @if($booking->slot->depot->location)
                        <div class="info-item">
                            <div class="label">Location:</div>
                            <div class="value">{{ $booking->slot->depot->location }}</div>
                        </div>
                    @endif
                    <div class="info-item">
                        <div class="label">Date:</div>
                        <div class="value">{{ $booking->slot->start_at->format('D, d M Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Time:</div>
                        <div class="value">{{ $booking->slot->start_at->format('H:i') }} - {{ $booking->slot->end_at->format('H:i') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Type:</div>
                        <div class="value">{{ $booking->bookingType->name ?? 'Not specified' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Load Details & Transportation -->
        <div class="card-row">
            <div class="card">
                <div class="card-inner">
                    <div class="card-header">
                        <span class="icon load">B</span>Load Details
                    </div>
                    
                    @if($booking->expected_cases || $booking->actual_cases)
                        <div class="load-comparison">
                            <div class="load-row">
                                <div class="load-expected">
                                    <strong>Cases:</strong><br>{{ number_format($booking->expected_cases ?? 0) }}
                                </div>
                                @if($booking->actual_cases)
                                    <div class="load-arrow">→</div>
                                    <div class="load-actual">
                                        <strong>Actual:</strong><br>{{ number_format($booking->actual_cases) }}
                                        @php $caseDiff = $booking->actual_cases - ($booking->expected_cases ?? 0); @endphp
                                        @if($caseDiff != 0)
                                            <span class="variance {{ $caseDiff > 0 ? 'positive' : 'negative' }}">
                                                ({{ $caseDiff > 0 ? '+' : '' }}{{ number_format($caseDiff) }})
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if($booking->expected_pallets || $booking->actual_pallets)
                        <div class="load-comparison">
                            <div class="load-row">
                                <div class="load-expected">
                                    <strong>Pallets:</strong><br>{{ number_format($booking->expected_pallets ?? 0) }}
                                </div>
                                @if($booking->actual_pallets)
                                    <div class="load-arrow">→</div>
                                    <div class="load-actual">
                                        <strong>Actual:</strong><br>{{ number_format($booking->actual_pallets) }}
                                        @php $palletDiff = $booking->actual_pallets - ($booking->expected_pallets ?? 0); @endphp
                                        @if($palletDiff != 0)
                                            <span class="variance {{ $palletDiff > 0 ? 'positive' : 'negative' }}">
                                                ({{ $palletDiff > 0 ? '+' : '' }}{{ number_format($palletDiff) }})
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if($booking->container_size)
                        <div class="info-item">
                            <div class="label">Container:</div>
                            <div class="value">{{ number_format($booking->container_size) }} kg</div>
                        </div>
                    @endif
                    
                    @if($booking->load_type)
                        <div class="info-item">
                            <div class="label">Load Type:</div>
                            <div class="value">{{ $booking->load_type }}</div>
                        </div>
                    @endif
                    
                    @if($booking->hazmat)
                        <div class="info-item">
                            <div class="label">Special:</div>
                            <div class="value hazmat">⚠ HAZMAT</div>
                        </div>
                    @endif
                    
                    @if($booking->temperature_requirements)
                        <div class="info-item">
                            <div class="label">Temperature:</div>
                            <div class="value">{{ $booking->temperature_requirements }}</div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-inner">
                    <div class="card-header">
                        <span class="icon transport">T</span>Transportation
                    </div>
                    
                    <div class="transport-grid">
                        <div class="transport-col">
                            @if($booking->vehicle_registration)
                                <div class="info-item">
                                    <div class="label">Vehicle:</div>
                                    <div class="value">{{ $booking->vehicle_registration }}</div>
                                </div>
                            @endif
                            
                            @if($booking->container_number)
                                <div class="info-item">
                                    <div class="label">Container:</div>
                                    <div class="value">{{ $booking->container_number }}</div>
                                </div>
                            @endif
                            
                            @if($booking->driver_name)
                                <div class="info-item">
                                    <div class="label">Driver:</div>
                                    <div class="value">{{ $booking->driver_name }}</div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="transport-col">
                            @if($booking->driver_phone)
                                <div class="info-item">
                                    <div class="label">Phone:</div>
                                    <div class="value">{{ $booking->driver_phone }}</div>
                                </div>
                            @endif
                            
                            @if($booking->carrier_company)
                                <div class="info-item">
                                    <div class="label">Carrier:</div>
                                    <div class="value">{{ $booking->carrier_company }}</div>
                                </div>
                            @endif
                            
                            @if($booking->estimated_arrival)
                                <div class="info-item">
                                    <div class="label">ETA:</div>
                                    <div class="value">{{ $booking->estimated_arrival->format('d M, H:i') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Arrival Information (if applicable) -->
        @if($hasArrived)
            <div class="card-row">
                <div class="card">
                    <div class="card-inner">
                        <div class="card-header">
                            <span class="icon arrival">A</span>Arrival Information
                        </div>
                        <div class="info-item">
                            <div class="label">Arrived:</div>
                            <div class="value">{{ $booking->arrived_at->format('d M Y, H:i') }}</div>
                        </div>
                        @if($booking->departed_at)
                            <div class="info-item">
                                <div class="label">Departed:</div>
                                <div class="value">{{ $booking->departed_at->format('d M Y, H:i') }}</div>
                            </div>
                            <div class="info-item">
                                <div class="label">Duration:</div>
                                <div class="value">{{ $booking->arrived_at->diffForHumans($booking->departed_at, true) }}</div>
                            </div>
                        @else
                            <div class="info-item">
                                <div class="label">Status:</div>
                                <div class="value onsite">🚛 Currently On-Site</div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-inner">
                        <!-- Empty card for layout balance -->
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Notes Section (Full Width) -->
    @if($booking->special_instructions || $booking->notes)
        <div class="full-width">
            <div class="notes-card">
                <div class="card-header">
                    <span class="icon notes">N</span>Additional Information
                </div>
                @if($booking->special_instructions)
                    <div style="margin-bottom: 6px;">
                        <strong>Special Instructions:</strong> {{ $booking->special_instructions }}
                    </div>
                @endif
                @if($booking->notes)
                    <div>
                        <strong>Notes:</strong> {{ $booking->notes }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</body>
</html>