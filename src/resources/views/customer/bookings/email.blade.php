<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Booking Details</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2563eb;">Your Booking Details #{{ $booking->id }}</h2>
        
        @if($message)
            <div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #2563eb; margin-bottom: 20px;">
                <p><strong>Message:</strong></p>
                <p>{{ $message }}</p>
            </div>
        @endif
        
        <p>Please find attached the detailed booking information for your upcoming appointment:</p>
        
        <ul style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <li><strong>Booking ID:</strong> #{{ $booking->id }}</li>
            <li><strong>Customer:</strong> {{ $booking->customer->name ?? 'Not assigned' }}</li>
            <li><strong>Depot:</strong> {{ $booking->slot->depot->name }}</li>
            <li><strong>Date & Time:</strong> {{ $booking->slot->start_at->format('l, d F Y - H:i') }} - {{ $booking->slot->end_at->format('H:i') }}</li>
            <li><strong>Status:</strong> 
                @if($booking->arrived_at)
                    @if($booking->departed_at)
                        Completed
                    @else
                        On-site
                    @endif
                @else
                    @if($booking->slot->locked_at && $booking->slot->locked_at->isPast())
                        Locked
                    @else
                        Confirmed
                    @endif
                @endif
            </li>
        </ul>
        
        <p>The attached PDF contains complete booking details including load information, transportation details, and arrival status.</p>
        
        @if(!$booking->arrived_at && (!$booking->slot->locked_at || $booking->slot->locked_at->isFuture()))
            <p style="background: #dbeafe; padding: 15px; border-radius: 5px; border-left: 4px solid #3b82f6;">
                <strong>📝 Remember:</strong> You can edit your booking details until the cut-off time. 
                @if($booking->slot->locked_at)
                    Cut-off: {{ $booking->slot->locked_at->format('d M Y, H:i') }}
                @endif
            </p>
        @endif
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
        
        <p style="font-size: 12px; color: #666;">
            This email was generated automatically from your booking management system.<br>
            Generated on {{ now()->format('d M Y, H:i') }}
        </p>
    </div>
</body>
</html>