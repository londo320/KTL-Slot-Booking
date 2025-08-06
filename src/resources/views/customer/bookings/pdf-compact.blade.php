<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Booking Details #{{ $booking->id }}</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', 'Segoe UI Emoji', Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            font-size: 10px;
            line-height: 1.3;
        }
        .container {
            padding: 8px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 12px; 
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0 0 4px 0;
        }
        .header p {
            margin: 2px 0;
            font-size: 9px;
            color: #666;
        }
        .status-banner {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            padding: 6px 8px;
            margin-bottom: 10px;
            text-align: center;
            font-size: 11px;
            border-radius: 3px;
        }
        .status-banner.arrived {
            background: #f0fdf4;
            border-color: #22c55e;
        }
        .status-banner.locked {
            background: #fffbeb;
            border-color: #f59e0b;
        }
        .two-column {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 8px;
        }
        .column:last-child {
            padding-right: 0;
            padding-left: 8px;
        }
        .section { 
            margin-bottom: 10px;
            break-inside: avoid;
        }
        .section-title { 
            font-size: 11px; 
            font-weight: bold; 
            margin-bottom: 4px; 
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 1px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 2px;
        }
        .label {
            display: table-cell;
            font-weight: bold;
            color: #555;
            width: 35%;
            font-size: 9px;
            vertical-align: top;
            padding-right: 4px;
        }
        .value {
            display: table-cell;
            font-size: 9px;
            vertical-align: top;
        }
        .load-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }
        .load-expected, .load-actual {
            display: table-cell;
            width: 45%;
            font-size: 9px;
        }
        .load-arrow {
            display: table-cell;
            width: 10%;
            text-align: center;
            color: #666;
            font-weight: bold;
        }
        .variance {
            font-size: 8px;
            font-weight: bold;
            margin-left: 4px;
        }
        .variance.positive { color: #059669; }
        .variance.negative { color: #dc2626; }
        .full-width {
            width: 100%;
            margin-bottom: 8px;
        }
        .emoji {
            font-family: 'Segoe UI Emoji', 'Apple Color Emoji', 'Noto Color Emoji', sans-serif;
            font-size: 12px;
        }
        .transportation-grid {
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Booking Details #{{ $booking->id }}</h1>
            <p>{{ $booking->customer->name ?? 'Not assigned' }} | Generated: {{ now()->format('d M Y, H:i') }}</p>
        </div>

        @php
            $isLocked = $booking->slot->locked_at && $booking->slot->locked_at->isPast();
            $hasArrived = $booking->arrived_at;
        @endphp

        @if($hasArrived)
            <div class="status-banner arrived">
                <strong><span class="emoji">✅</span> Vehicle Arrived</strong> - 
                {{ $booking->arrived_at->format('d M Y, H:i') }}
                @if($booking->departed_at)
                    | Departed: {{ $booking->departed_at->format('H:i') }}
                @else
                    | On-site
                @endif
            </div>
        @elseif($isLocked)
            <div class="status-banner locked">
                <strong><span class="emoji">🔒</span> Booking Locked</strong> - 
                Cut-off: {{ $booking->slot->locked_at->format('d M Y, H:i') }}
            </div>
        @else
            <div class="status-banner">
                <strong><span class="emoji">📅</span> Booking Confirmed</strong> - 
                Can edit until cut-off time
            </div>
        @endif

        <div class="two-column">
            <div class="column">
                <!-- Slot Information -->
                <div class="section">
                    <div class="section-title"><span class="emoji">📍</span> Slot & Location</div>
                    <div class="info-row">
                        <div class="label">Depot:</div>
                        <div class="value">{{ $booking->slot->depot->name }}</div>
                    </div>
                    @if($booking->slot->depot->location)
                        <div class="info-row">
                            <div class="label">Location:</div>
                            <div class="value">{{ $booking->slot->depot->location }}</div>
                        </div>
                    @endif
                    <div class="info-row">
                        <div class="label">Date:</div>
                        <div class="value">{{ $booking->slot->start_at->format('D, d M Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="label">Time:</div>
                        <div class="value">{{ $booking->slot->start_at->format('H:i') }} - {{ $booking->slot->end_at->format('H:i') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="label">Type:</div>
                        <div class="value">{{ $booking->bookingType->name ?? 'Not specified' }}</div>
                    </div>
                    @if($booking->reference)
                        <div class="info-row">
                            <div class="label">Reference:</div>
                            <div class="value">{{ $booking->reference }}</div>
                        </div>
                    @endif
                </div>

                <!-- Load Details -->
                <div class="section">
                    <div class="section-title"><span class="emoji">📦</span> Load Details</div>
                    
                    @if($booking->expected_cases || $booking->actual_cases)
                        <div class="load-row">
                            <div class="load-expected">
                                <strong>Cases:</strong> {{ number_format($booking->expected_cases ?? 0) }}
                            </div>
                            @if($booking->actual_cases)
                                <div class="load-arrow">→</div>
                                <div class="load-actual">
                                    <strong>{{ number_format($booking->actual_cases) }}</strong>
                                    @php $caseDiff = $booking->actual_cases - ($booking->expected_cases ?? 0); @endphp
                                    @if($caseDiff != 0)
                                        <span class="variance {{ $caseDiff > 0 ? 'positive' : 'negative' }}">
                                            ({{ $caseDiff > 0 ? '+' : '' }}{{ number_format($caseDiff) }})
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    @if($booking->expected_pallets || $booking->actual_pallets)
                        <div class="load-row">
                            <div class="load-expected">
                                <strong>Pallets:</strong> {{ number_format($booking->expected_pallets ?? 0) }}
                            </div>
                            @if($booking->actual_pallets)
                                <div class="load-arrow">→</div>
                                <div class="load-actual">
                                    <strong>{{ number_format($booking->actual_pallets) }}</strong>
                                    @php $palletDiff = $booking->actual_pallets - ($booking->expected_pallets ?? 0); @endphp
                                    @if($palletDiff != 0)
                                        <span class="variance {{ $palletDiff > 0 ? 'positive' : 'negative' }}">
                                            ({{ $palletDiff > 0 ? '+' : '' }}{{ number_format($palletDiff) }})
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    @if($booking->container_size)
                        <div class="info-row">
                            <div class="label">Container:</div>
                            <div class="value">{{ number_format($booking->container_size) }} kg</div>
                        </div>
                    @endif
                    
                    @if($booking->load_type)
                        <div class="info-row">
                            <div class="label">Load Type:</div>
                            <div class="value">{{ $booking->load_type }}</div>
                        </div>
                    @endif
                    
                    @if($booking->hazmat)
                        <div class="info-row">
                            <div class="label">Special:</div>
                            <div class="value" style="color: #dc2626; font-weight: bold;">
                                <span class="emoji">⚠️</span> HAZMAT
                            </div>
                        </div>
                    @endif
                    
                    @if($booking->temperature_requirements)
                        <div class="info-row">
                            <div class="label">Temperature:</div>
                            <div class="value">{{ $booking->temperature_requirements }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="column">
                <!-- Transportation -->
                @if($booking->vehicle_registration || $booking->driver_name || $booking->carrier_company)
                    <div class="section">
                        <div class="section-title"><span class="emoji">🚛</span> Transportation</div>
                        
                        <div class="transportation-grid">
                            <div class="transport-col">
                                @if($booking->vehicle_registration)
                                    <div class="info-row">
                                        <div class="label">Vehicle:</div>
                                        <div class="value">{{ $booking->vehicle_registration }}</div>
                                    </div>
                                @endif
                                
                                @if($booking->container_number)
                                    <div class="info-row">
                                        <div class="label">Container:</div>
                                        <div class="value">{{ $booking->container_number }}</div>
                                    </div>
                                @endif
                                
                                @if($booking->driver_name)
                                    <div class="info-row">
                                        <div class="label">Driver:</div>
                                        <div class="value">{{ $booking->driver_name }}</div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="transport-col">
                                @if($booking->driver_phone)
                                    <div class="info-row">
                                        <div class="label">Phone:</div>
                                        <div class="value">{{ $booking->driver_phone }}</div>
                                    </div>
                                @endif
                                
                                @if($booking->carrier_company)
                                    <div class="info-row">
                                        <div class="label">Carrier:</div>
                                        <div class="value">{{ $booking->carrier_company }}</div>
                                    </div>
                                @endif
                                
                                @if($booking->estimated_arrival)
                                    <div class="info-row">
                                        <div class="label">ETA:</div>
                                        <div class="value">{{ $booking->estimated_arrival->format('d M, H:i') }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Booking Information -->
                <div class="section">
                    <div class="section-title"><span class="emoji">📋</span> Booking Info</div>
                    <div class="info-row">
                        <div class="label">ID:</div>
                        <div class="value">#{{ $booking->id }}</div>
                    </div>
                    <div class="info-row">
                        <div class="label">Created:</div>
                        <div class="value">{{ $booking->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>

                <!-- Arrival Information -->
                @if($hasArrived)
                    <div class="section">
                        <div class="section-title"><span class="emoji">✅</span> Arrival</div>
                        <div class="info-row">
                            <div class="label">Arrived:</div>
                            <div class="value">{{ $booking->arrived_at->format('d M Y, H:i') }}</div>
                        </div>
                        @if($booking->departed_at)
                            <div class="info-row">
                                <div class="label">Departed:</div>
                                <div class="value">{{ $booking->departed_at->format('d M Y, H:i') }}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Duration:</div>
                                <div class="value">{{ $booking->arrived_at->diffForHumans($booking->departed_at, true) }}</div>
                            </div>
                        @else
                            <div class="info-row">
                                <div class="label">Status:</div>
                                <div class="value" style="color: #2563eb; font-weight: bold;">
                                    <span class="emoji">🚛</span> On-site
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Notes (Full Width) -->
        @if($booking->special_instructions || $booking->notes)
            <div class="full-width">
                <div class="section">
                    <div class="section-title"><span class="emoji">📝</span> Additional Information</div>
                    @if($booking->special_instructions)
                        <div style="margin-bottom: 4px;">
                            <strong>Instructions:</strong> {{ $booking->special_instructions }}
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
    </div>
</body>
</html>