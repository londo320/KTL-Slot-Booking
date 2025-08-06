<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Details #{{ $booking->id }}</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', 'Arial Unicode MS', Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            font-size: 12px;
            line-height: 1.4;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .section { 
            margin-bottom: 15px; 
            page-break-inside: avoid;
        }
        .section-title { 
            font-size: 14px; 
            font-weight: bold; 
            margin-bottom: 8px; 
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }
        .info-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 10px; 
        }
        .info-item { 
            margin-bottom: 5px; 
        }
        .label { 
            font-weight: bold; 
            color: #555; 
        }
        .value { 
            margin-left: 10px; 
        }
        .status-banner {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
        .arrived .status-banner {
            background: #f0fdf4;
            border-color: #22c55e;
        }
        .locked .status-banner {
            background: #fffbeb;
            border-color: #f59e0b;
        }
        .load-comparison {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        .load-comparison .arrow {
            color: #666;
            font-weight: bold;
        }
        .variance {
            font-size: 11px;
            font-weight: bold;
        }
        .variance.positive { color: #059669; }
        .variance.negative { color: #dc2626; }
        
        /* Emoji styling for PDFs */
        .emoji {
            font-family: 'Apple Color Emoji', 'Segoe UI Emoji', 'Noto Color Emoji', 'Segoe UI Symbol', sans-serif;
            font-size: 14px;
            margin-right: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Booking Details #{{ $booking->id }}</h1>
        <p>Generated on {{ now()->format('d M Y, H:i') }}</p>
    </div>

    @php
        $isLocked = $booking->slot->locked_at && $booking->slot->locked_at->isPast();
        $hasArrived = $booking->arrived_at;
    @endphp

    @if($hasArrived)
        <div class="status-banner arrived">
            <strong><span class="emoji">✅</span>Vehicle Arrived</strong><br>
            Arrived: {{ $booking->arrived_at->format('d M Y, H:i') }}
            @if($booking->departed_at)
                | Departed: {{ $booking->departed_at->format('d M Y, H:i') }}
            @else
                | Currently on-site
            @endif
        </div>
    @elseif($isLocked)
        <div class="status-banner locked">
            <strong><span class="emoji">🔒</span>Booking Locked</strong><br>
            Cut-off time: {{ $booking->slot->locked_at->format('d M Y, H:i') }}
        </div>
    @else
        <div class="status-banner">
            <strong><span class="emoji">📅</span>Booking Active</strong><br>
            This booking is active and can be edited.
        </div>
    @endif

    <div class="info-grid">
        <div class="section">
            <div class="section-title"><span class="emoji">📋</span>Booking Information</div>
            <div class="info-item">
                <span class="label">Booking ID:</span>
                <span class="value">#{{ $booking->id }}</span>
            </div>
            <div class="info-item">
                <span class="label">Customer:</span>
                <span class="value">{{ $booking->customer->name ?? 'Not assigned' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Created By:</span>
                <span class="value">{{ $booking->user->name ?? 'Unknown' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Created At:</span>
                <span class="value">{{ $booking->created_at->format('d M Y, H:i') }}</span>
            </div>
            @if($booking->reference)
                <div class="info-item">
                    <span class="label">Reference:</span>
                    <span class="value">{{ $booking->reference }}</span>
                </div>
            @endif
        </div>

        <div class="section">
            <div class="section-title"><span class="emoji">📍</span>Slot & Location</div>
            <div class="info-item">
                <span class="label">Depot:</span>
                <span class="value">{{ $booking->slot->depot->name }}</span>
            </div>
            @if($booking->slot->depot->location)
                <div class="info-item">
                    <span class="label">Location:</span>
                    <span class="value">{{ $booking->slot->depot->location }}</span>
                </div>
            @endif
            <div class="info-item">
                <span class="label">Date:</span>
                <span class="value">{{ $booking->slot->start_at->format('l, d F Y') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Time:</span>
                <span class="value">{{ $booking->slot->start_at->format('H:i') }} - {{ $booking->slot->end_at->format('H:i') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Booking Type:</span>
                <span class="value">{{ $booking->bookingType->name ?? 'Not specified' }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title"><span class="emoji">📦</span>Load Details</div>
        
        @if($booking->expected_cases || $booking->actual_cases)
            <div class="load-comparison">
                <div>
                    <span class="label">Cases Expected:</span>
                    <span class="value">{{ number_format($booking->expected_cases ?? 0) }}</span>
                </div>
                @if($booking->actual_cases)
                    <span class="arrow">→</span>
                    <div>
                        <span class="label">Actual:</span>
                        <span class="value">{{ number_format($booking->actual_cases) }}</span>
                        @php
                            $caseDiff = $booking->actual_cases - ($booking->expected_cases ?? 0);
                        @endphp
                        @if($caseDiff != 0)
                            <span class="variance {{ $caseDiff > 0 ? 'positive' : 'negative' }}">
                                ({{ $caseDiff > 0 ? '+' : '' }}{{ number_format($caseDiff) }})
                            </span>
                        @endif
                    </div>
                @elseif($hasArrived)
                    <span class="arrow">→</span>
                    <div>
                        <span class="label">Actual:</span>
                        <span class="value" style="color: #666;">Not recorded</span>
                    </div>
                @endif
            </div>
        @endif
        
        @if($booking->expected_pallets || $booking->actual_pallets)
            <div class="load-comparison">
                <div>
                    <span class="label">Pallets Expected:</span>
                    <span class="value">{{ number_format($booking->expected_pallets ?? 0) }}</span>
                </div>
                @if($booking->actual_pallets)
                    <span class="arrow">→</span>
                    <div>
                        <span class="label">Actual:</span>
                        <span class="value">{{ number_format($booking->actual_pallets) }}</span>
                        @php
                            $palletDiff = $booking->actual_pallets - ($booking->expected_pallets ?? 0);
                        @endphp
                        @if($palletDiff != 0)
                            <span class="variance {{ $palletDiff > 0 ? 'positive' : 'negative' }}">
                                ({{ $palletDiff > 0 ? '+' : '' }}{{ number_format($palletDiff) }})
                            </span>
                        @endif
                    </div>
                @elseif($hasArrived)
                    <span class="arrow">→</span>
                    <div>
                        <span class="label">Actual:</span>
                        <span class="value" style="color: #666;">Not recorded</span>
                    </div>
                @endif
            </div>
        @endif
        
        @if($booking->container_size)
            <div class="info-item">
                <span class="label">Container Size:</span>
                <span class="value">{{ number_format($booking->container_size) }} kg</span>
            </div>
        @endif
        
        @if($booking->load_type)
            <div class="info-item">
                <span class="label">Load Type:</span>
                <span class="value">{{ $booking->load_type }}</span>
            </div>
        @endif
        
        @if($booking->hazmat)
            <div class="info-item">
                <span class="label">Special Requirements:</span>
                <span class="value" style="color: #dc2626; font-weight: bold;"><span class="emoji">⚠️</span>Hazardous Materials (HAZMAT)</span>
            </div>
        @endif
        
        @if($booking->temperature_requirements)
            <div class="info-item">
                <span class="label">Temperature:</span>
                <span class="value">{{ $booking->temperature_requirements }}</span>
            </div>
        @endif
    </div>

    @if($booking->vehicle_registration || $booking->driver_name || $booking->carrier_company)
        <div class="section">
            <div class="section-title"><span class="emoji">🚛</span>Transportation</div>
            
            @if($booking->vehicle_registration)
                <div class="info-item">
                    <span class="label">Vehicle Registration:</span>
                    <span class="value">{{ $booking->vehicle_registration }}</span>
                </div>
            @endif
            
            @if($booking->container_number)
                <div class="info-item">
                    <span class="label">Container Number:</span>
                    <span class="value">{{ $booking->container_number }}</span>
                </div>
            @endif
            
            @if($booking->driver_name)
                <div class="info-item">
                    <span class="label">Driver Name:</span>
                    <span class="value">{{ $booking->driver_name }}</span>
                </div>
            @endif
            
            @if($booking->driver_phone)
                <div class="info-item">
                    <span class="label">Driver Phone:</span>
                    <span class="value">{{ $booking->driver_phone }}</span>
                </div>
            @endif
            
            @if($booking->carrier_company)
                <div class="info-item">
                    <span class="label">Carrier Company:</span>
                    <span class="value">{{ $booking->carrier_company }}</span>
                </div>
            @endif
            
            @if($booking->estimated_arrival)
                <div class="info-item">
                    <span class="label">Estimated Arrival:</span>
                    <span class="value">{{ $booking->estimated_arrival->format('d M Y, H:i') }}</span>
                </div>
            @endif
        </div>
    @endif

    @if($booking->special_instructions || $booking->notes)
        <div class="section">
            <div class="section-title"><span class="emoji">📝</span>Additional Information</div>
            
            @if($booking->special_instructions)
                <div class="info-item">
                    <span class="label">Special Instructions:</span>
                    <div class="value">{{ $booking->special_instructions }}</div>
                </div>
            @endif
            
            @if($booking->notes)
                <div class="info-item">
                    <span class="label">Notes:</span>
                    <div class="value">{{ $booking->notes }}</div>
                </div>
            @endif
        </div>
    @endif

    @if($hasArrived)
        <div class="section">
            <div class="section-title"><span class="emoji">✅</span>Arrival Information</div>
            
            <div class="info-item">
                <span class="label">Arrived At:</span>
                <span class="value">{{ $booking->arrived_at->format('l, d F Y - H:i') }}</span>
            </div>
            
            @if($booking->departed_at)
                <div class="info-item">
                    <span class="label">Departed At:</span>
                    <span class="value">{{ $booking->departed_at->format('l, d F Y - H:i') }}</span>
                </div>
                
                <div class="info-item">
                    <span class="label">Time On-Site:</span>
                    <span class="value">{{ $booking->arrived_at->diffForHumans($booking->departed_at, true) }}</span>
                </div>
            @else
                <div class="info-item">
                    <span class="label">Status:</span>
                    <span class="value" style="color: #2563eb; font-weight: bold;"><span class="emoji">🚛</span>Currently on-site</span>
                </div>
            @endif
        </div>
    @endif
</body>
</html>