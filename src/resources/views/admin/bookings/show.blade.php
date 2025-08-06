<x-app-layout>
  @include('layouts.admin-nav')

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Booking Details #{{ $booking->id }}</h2>
      <div class="flex space-x-2">
        @php
          $isLocked = $booking->slot->locked_at && $booking->slot->locked_at->isPast();
          $hasArrived = $booking->arrived_at;
        @endphp
        
        {{-- PDF Actions --}}
        <a href="{{ route('admin.bookings.download-pdf', $booking) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          📄 Download PDF
        </a>
        
        <button onclick="emailBookingPDF({{ $booking->id }})"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          📧 Email PDF
        </button>
        
        @if(!$hasArrived)
          <a href="{{ route('admin.bookings.edit', $booking) }}"
             class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Edit Booking
          </a>
        @endif
        
        <a href="{{ route('admin.bookings.index') }}"
           class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
          Back to Bookings
        </a>
      </div>
    </div>
  </x-slot>

  <div class="py-6 max-w-4xl mx-auto">
    
    {{-- Success/Info Messages --}}
    @if(session('success'))
      <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg">
        <p class="text-green-800">{{ session('success') }}</p>
      </div>
    @endif
    
    @if(session('info'))
      <div class="mb-6 p-4 bg-blue-100 border border-blue-300 rounded-lg">
        <p class="text-blue-800">{{ session('info') }}</p>
      </div>
    @endif
    
    {{-- Status Banner --}}
    @if($hasArrived)
      <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg">
        <div class="flex items-center">
          <span class="text-green-600 text-2xl mr-3">✅</span>
          <div>
            <h3 class="text-lg font-semibold text-green-800">Vehicle Arrived</h3>
            <p class="text-green-700">
              Arrived: {{ $booking->arrived_at->format('d M Y, H:i') }}
              @if($booking->departed_at)
                | Departed: {{ $booking->departed_at->format('d M Y, H:i') }}
              @else
                | Currently on-site
              @endif
            </p>
          </div>
        </div>
      </div>
    @elseif($isLocked)
      <div class="mb-6 p-4 bg-orange-100 border border-orange-300 rounded-lg">
        <div class="flex items-center">
          <span class="text-orange-600 text-2xl mr-3">🔒</span>
          <div>
            <h3 class="text-lg font-semibold text-orange-800">Booking Locked</h3>
            <p class="text-orange-700">
              Cut-off time: {{ $booking->slot->locked_at->format('d M Y, H:i') }}
            </p>
          </div>
        </div>
      </div>
    @else
      <div class="mb-6 p-4 bg-blue-100 border border-blue-300 rounded-lg">
        <div class="flex items-center">
          <span class="text-blue-600 text-2xl mr-3">📅</span>
          <div>
            <h3 class="text-lg font-semibold text-blue-800">Booking Active</h3>
            <p class="text-blue-700">This booking is active and can be edited.</p>
          </div>
        </div>
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      {{-- Booking Information --}}
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📋 Booking Information</h3>
        
        <div class="space-y-3">
          <div>
            <label class="text-sm font-medium text-gray-600">Booking ID</label>
            <p class="text-lg font-mono">#{{ $booking->id }}</p>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Customer</label>
            <p class="text-lg">{{ $booking->customer->name ?? 'Not assigned' }}</p>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Created By</label>
            <p class="text-lg">{{ $booking->user->name ?? 'Unknown' }}</p>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Created At</label>
            <p class="text-lg">{{ $booking->created_at->format('d M Y, H:i') }}</p>
          </div>
          
          @if($booking->reference)
            <div>
              <label class="text-sm font-medium text-gray-600">Reference</label>
              <p class="text-lg font-mono">{{ $booking->reference }}</p>
            </div>
          @endif
        </div>
      </div>

      {{-- Slot & Location Details --}}
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📍 Slot & Location</h3>
        
        <div class="space-y-3">
          <div>
            <label class="text-sm font-medium text-gray-600">Depot</label>
            <p class="text-lg">{{ $booking->slot->depot->name }}</p>
            @if($booking->slot->depot->location)
              <p class="text-sm text-gray-500">{{ $booking->slot->depot->location }}</p>
            @endif
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Date & Time</label>
            <p class="text-lg">
              {{ $booking->slot->start_at->format('l, d F Y') }}
            </p>
            <p class="text-lg font-semibold text-blue-600">
              {{ $booking->slot->start_at->format('H:i') }} - {{ $booking->slot->end_at->format('H:i') }}
            </p>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Booking Type</label>
            <p class="text-lg">{{ $booking->bookingType->name ?? 'Not specified' }}</p>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Slot Capacity</label>
            <p class="text-lg">{{ $booking->slot->capacity ?? 'Unlimited' }}</p>
          </div>
        </div>
      </div>

      {{-- Load Details --}}
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📦 Load Details</h3>
        
        <div class="space-y-4">
          {{-- Cases --}}
          @if($booking->expected_cases || $booking->actual_cases)
            <div>
              <label class="text-sm font-medium text-gray-600">Cases</label>
              <div class="flex items-center space-x-4 mt-1">
                <div>
                  <span class="text-sm text-gray-500">Expected:</span>
                  <span class="text-lg font-semibold">{{ number_format($booking->expected_cases ?? 0) }}</span>
                </div>
                @if($booking->actual_cases)
                  <div class="text-gray-400">→</div>
                  <div>
                    <span class="text-sm text-gray-500">Actual:</span>
                    <span class="text-lg font-semibold text-green-600">{{ number_format($booking->actual_cases) }}</span>
                    @php
                      $caseDiff = $booking->actual_cases - ($booking->expected_cases ?? 0);
                    @endphp
                    @if($caseDiff != 0)
                      <span class="text-sm {{ $caseDiff > 0 ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $caseDiff > 0 ? '+' : '' }}{{ number_format($caseDiff) }})
                      </span>
                    @endif
                  </div>
                @elseif($hasArrived)
                  <div class="text-gray-400">→</div>
                  <div>
                    <span class="text-sm text-gray-500">Actual:</span>
                    <span class="text-lg text-gray-400">Not recorded</span>
                  </div>
                @endif
              </div>
            </div>
          @endif
          
          {{-- Pallets --}}
          @if($booking->expected_pallets || $booking->actual_pallets)
            <div>
              <label class="text-sm font-medium text-gray-600">Pallets</label>
              <div class="flex items-center space-x-4 mt-1">
                <div>
                  <span class="text-sm text-gray-500">Expected:</span>
                  <span class="text-lg font-semibold">{{ number_format($booking->expected_pallets ?? 0) }}</span>
                </div>
                @if($booking->actual_pallets)
                  <div class="text-gray-400">→</div>
                  <div>
                    <span class="text-sm text-gray-500">Actual:</span>
                    <span class="text-lg font-semibold text-green-600">{{ number_format($booking->actual_pallets) }}</span>
                    @php
                      $palletDiff = $booking->actual_pallets - ($booking->expected_pallets ?? 0);
                    @endphp
                    @if($palletDiff != 0)
                      <span class="text-sm {{ $palletDiff > 0 ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $palletDiff > 0 ? '+' : '' }}{{ number_format($palletDiff) }})
                      </span>
                    @endif
                  </div>
                @elseif($hasArrived)
                  <div class="text-gray-400">→</div>
                  <div>
                    <span class="text-sm text-gray-500">Actual:</span>
                    <span class="text-lg text-gray-400">Not recorded</span>
                  </div>
                @endif
              </div>
            </div>
          @endif
          
          @if($booking->container_size)
            <div>
              <label class="text-sm font-medium text-gray-600">Container Size</label>
              <p class="text-lg">{{ number_format($booking->container_size) }} kg</p>
            </div>
          @endif
          
          @if($booking->load_type)
            <div>
              <label class="text-sm font-medium text-gray-600">Load Type</label>
              <p class="text-lg">{{ $booking->load_type }}</p>
            </div>
          @endif
          
          @if($booking->hazmat)
            <div>
              <label class="text-sm font-medium text-gray-600">Special Requirements</label>
              <p class="text-lg text-red-600 font-semibold">⚠️ Hazardous Materials (HAZMAT)</p>
            </div>
          @endif
          
          @if($booking->temperature_requirements)
            <div>
              <label class="text-sm font-medium text-gray-600">Temperature Requirements</label>
              <p class="text-lg">{{ $booking->temperature_requirements }}</p>
            </div>
          @endif
        </div>
      </div>

      {{-- Transportation Details --}}
      @if($booking->vehicle_registration || $booking->driver_name || $booking->carrier_company)
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-xl font-semibold mb-4 text-gray-800">🚛 Transportation</h3>
          
          <div class="space-y-3">
            @if($booking->vehicle_registration)
              <div>
                <label class="text-sm font-medium text-gray-600">Vehicle Registration</label>
                <p class="text-lg font-mono">{{ $booking->vehicle_registration }}</p>
              </div>
            @endif
            
            @if($booking->container_number)
              <div>
                <label class="text-sm font-medium text-gray-600">Container Number</label>
                <p class="text-lg font-mono">{{ $booking->container_number }}</p>
              </div>
            @endif
            
            @if($booking->driver_name)
              <div>
                <label class="text-sm font-medium text-gray-600">Driver Name</label>
                <p class="text-lg">{{ $booking->driver_name }}</p>
              </div>
            @endif
            
            @if($booking->driver_phone)
              <div>
                <label class="text-sm font-medium text-gray-600">Driver Phone</label>
                <p class="text-lg">
                  <a href="tel:{{ $booking->driver_phone }}" class="text-blue-600 hover:underline">
                    {{ $booking->driver_phone }}
                  </a>
                </p>
              </div>
            @endif
            
            @if($booking->carrier_company)
              <div>
                <label class="text-sm font-medium text-gray-600">Carrier Company</label>
                <p class="text-lg">{{ $booking->carrier_company }}</p>
              </div>
            @endif
            
            @if($booking->estimated_arrival)
              <div>
                <label class="text-sm font-medium text-gray-600">Estimated Arrival</label>
                <p class="text-lg">{{ $booking->estimated_arrival->format('d M Y, H:i') }}</p>
              </div>
            @endif
          </div>
        </div>
      @endif

      {{-- Additional Information --}}
      @if($booking->special_instructions || $booking->notes)
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-xl font-semibold mb-4 text-gray-800">📝 Additional Information</h3>
          
          <div class="space-y-3">
            @if($booking->special_instructions)
              <div>
                <label class="text-sm font-medium text-gray-600">Special Instructions</label>
                <p class="text-base leading-relaxed">{{ $booking->special_instructions }}</p>
              </div>
            @endif
            
            @if($booking->notes)
              <div>
                <label class="text-sm font-medium text-gray-600">Notes</label>
                <p class="text-base leading-relaxed">{{ $booking->notes }}</p>
              </div>
            @endif
          </div>
        </div>
      @endif

      {{-- Arrival Information (if arrived) --}}
      @if($hasArrived)
        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
          <h3 class="text-xl font-semibold mb-4 text-green-800">✅ Arrival Information</h3>
          
          <div class="space-y-3">
            <div>
              <label class="text-sm font-medium text-gray-600">Arrived At</label>
              <p class="text-lg">{{ $booking->arrived_at->format('l, d F Y - H:i') }}</p>
            </div>
            
            @if($booking->departed_at)
              <div>
                <label class="text-sm font-medium text-gray-600">Departed At</label>
                <p class="text-lg">{{ $booking->departed_at->format('l, d F Y - H:i') }}</p>
              </div>
              
              <div>
                <label class="text-sm font-medium text-gray-600">Time On-Site</label>
                <p class="text-lg">{{ $booking->arrived_at->diffForHumans($booking->departed_at, true) }}</p>
              </div>
            @else
              <div class="p-3 bg-blue-100 rounded border border-blue-300">
                <p class="text-blue-800 font-medium">🚛 Currently on-site</p>
              </div>
            @endif
          </div>
        </div>
      @endif

    </div>
  </div>

  {{-- Email PDF Modal --}}
  <div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold mb-4">Email Booking PDF</h3>
      <form id="emailForm">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
          <input type="email" id="emailAddress" required
                 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                 placeholder="Enter email address">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Message (Optional)</label>
          <textarea id="emailMessage" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Add a personal message..."></textarea>
        </div>
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeEmailModal()"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Send PDF
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function emailBookingPDF(bookingId) {
      document.getElementById('emailModal').classList.remove('hidden');
      document.getElementById('emailModal').classList.add('flex');
    }

    function closeEmailModal() {
      document.getElementById('emailModal').classList.add('hidden');
      document.getElementById('emailModal').classList.remove('flex');
    }

    document.getElementById('emailForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const email = document.getElementById('emailAddress').value;
      const message = document.getElementById('emailMessage').value;
      
      // Send request to email endpoint
      fetch('{{ route("admin.bookings.email-pdf", $booking) }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          email: email,
          message: message
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          closeEmailModal();
          alert('PDF sent successfully!');
        } else {
          alert('Error sending PDF: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error sending PDF');
      });
    });

    // Close modal when clicking outside
    document.getElementById('emailModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeEmailModal();
      }
    });
  </script>
</x-app-layout>