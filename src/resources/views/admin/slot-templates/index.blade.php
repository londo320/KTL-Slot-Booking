<x-app-layout>
    @include('layouts.admin-nav')

    <div class="py-6 max-w-4xl mx-auto space-y-6">

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded">{{ session('success') }}</div>
        @endif

        {{-- FORM TO ADD NEW TEMPLATE --}}
        <form method="POST" action="{{ route('admin.slot-templates.store') }}"
              class="bg-white p-6 rounded shadow grid grid-cols-2 gap-4">
            @csrf

            <div>
                <label class="block font-medium">Depot</label>
                <select name="depot_id" class="border p-2 w-full">
                    <option value="">— select depot —</option>
                    @foreach($depots as $d)
                        <option value="{{ $d->id }}" @selected(old('depot_id') == $d->id)>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
                @error('depot_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-medium">Day of Week</label>
                <select name="day_of_week" class="border p-2 w-full">
                    <option value="">— choose day —</option>
                    @foreach([
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        0 => 'Sunday'
                    ] as $num => $label)
                        <option value="{{ $num }}" @selected(old('day_of_week') == $num)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('day_of_week')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            @php
                $times = [];
                for ($hour = 0; $hour < 24; $hour++) {
                    foreach ([0, 30] as $minute) {
                        $times[] = sprintf('%02d:%02d', $hour, $minute);
                    }
                }
            @endphp

            <div>
                <label class="block font-medium">Start Time</label>
                <select name="start_time" class="border p-2 w-full">
                    <option value="">— select time —</option>
                    @foreach($times as $time)
                        <option value="{{ $time }}" @selected(old('start_time') === $time)>
                            {{ $time }}
                        </option>
                    @endforeach
                </select>
                @error('start_time')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-medium">End Time</label>
                <select name="end_time" class="border p-2 w-full">
                    <option value="">— select time —</option>
                    @foreach($times as $time)
                        <option value="{{ $time }}" @selected(old('end_time') === $time)>
                            {{ $time }}
                        </option>
                    @endforeach
                </select>
                @error('end_time')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            <div class="col-span-2 text-right">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    ➕ Add Template
                </button>
            </div>
        </form>

        {{-- LIST OF TEMPLATES --}}
   <div class="bg-white shadow rounded p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Existing Templates</h2>
        <button id="bulkCopyBtn" onclick="openBulkCopyModal()" disabled 
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
            📋 Copy Selected Templates
        </button>
    </div>

    @if($templates->isEmpty())
        <p class="text-gray-600">No templates yet.</p>
    @else
        @foreach($templates as $depotName => $group)
            <h3 class="text-lg font-semibold mt-6 mb-2 border-b pb-1">{{ $depotName }}</h3>
            <table class="min-w-full text-sm mb-4">
                <thead>
                    <tr class="text-left">
                        <th class="px-3 py-1">
                            <input type="checkbox" id="selectAll-{{ $loop->index }}" onchange="toggleGroupSelection(this, {{ $loop->index }})" class="mr-2">
                            Select
                        </th>
                        <th class="px-3 py-1">Day</th>
                        <th class="px-3 py-1">Start</th>
                        <th class="px-3 py-1">End</th>
                        <th class="px-3 py-1">Duration</th>
                        <th class="px-3 py-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group as $tpl)
                        <tr class="border-t">
                            <td class="px-3 py-1">
                                @php
                                    $dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                                    $templateData = [
                                        'id' => $tpl->id,
                                        'depot' => $tpl->depot->name,
                                        'day' => $dayNames[$tpl->day_of_week],
                                        'start' => \Carbon\Carbon::parse($tpl->start_time)->format('H:i'),
                                        'end' => \Carbon\Carbon::parse($tpl->end_time)->format('H:i')
                                    ];
                                @endphp
                                <input type="checkbox" class="template-checkbox group-{{ $loop->parent->index }}" 
                                       value="{{ $tpl->id }}" onchange="updateBulkCopyButton()" 
                                       data-template='@json($templateData)'>
                            </td>
                            <td class="px-3 py-1">
                                {{ ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$tpl->day_of_week] }}
                            </td>
                            <td class="px-3 py-1">{{ \Carbon\Carbon::parse($tpl->start_time)->format('H:i') }}</td>
                            <td class="px-3 py-1">{{ \Carbon\Carbon::parse($tpl->end_time)->format('H:i') }}</td>
                            <td class="px-3 py-1">{{ abs($tpl->duration_minutes) }} min</td>
                            <td class="px-3 py-1 text-sm">
                                <a href="{{ route('admin.slot-templates.edit', $tpl) }}" class="text-blue-600 hover:underline">Edit</a>
                                <button onclick="openDuplicateModal({{ $tpl->id }}, '{{ $tpl->depot->name }}', '{{ ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$tpl->day_of_week] }}', '{{ \Carbon\Carbon::parse($tpl->start_time)->format('H:i') }}', '{{ \Carbon\Carbon::parse($tpl->end_time)->format('H:i') }}')" 
                                        class="text-green-600 hover:underline ml-2">Copy</button>
                                <form action="{{ route('admin.slot-templates.destroy', $tpl) }}" method="POST" class="inline ml-2"
                                      onsubmit="return confirm('Delete this template?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif
</div>
    </div>

    {{-- DUPLICATE MODAL --}}
    <div id="duplicateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold mb-4">Copy Template to Other Depots</h3>
            <div id="templateInfo" class="mb-4 p-3 bg-gray-100 rounded text-sm"></div>
            
            <form id="duplicateForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block font-medium mb-2">Select Target Depots:</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($depots as $depot)
                            <label class="flex items-center">
                                <input type="checkbox" name="depot_ids[]" value="{{ $depot->id }}" class="mr-2">
                                {{ $depot->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeDuplicateModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Copy Template</button>
                </div>
            </form>
        </div>
    </div>

    {{-- BULK COPY MODAL --}}
    <div id="bulkCopyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <h3 class="text-lg font-bold mb-4">Copy Selected Templates</h3>
            
            <div id="selectedTemplatesInfo" class="mb-4 p-3 bg-gray-100 rounded text-sm max-h-32 overflow-y-auto"></div>
            
            <form id="bulkCopyForm" method="POST" action="{{ route('admin.slot-templates.bulk-duplicate') }}">
                @csrf
                <div id="bulkTemplateIds"></div>
                
                <!-- Copy Type Selection -->
                <div class="mb-4">
                    <label class="block font-medium mb-2">What do you want to copy?</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="copy_type" value="depots" checked class="mr-2" onchange="toggleCopyOptions()">
                            Copy templates to other depots
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="copy_type" value="days" class="mr-2" onchange="toggleCopyOptions()">
                            Copy templates to other days of the week
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="copy_type" value="both" class="mr-2" onchange="toggleCopyOptions()">
                            Copy to both other depots AND other days
                        </label>
                    </div>
                </div>

                <!-- Depot Selection -->
                <div id="depotSelection" class="mb-4">
                    <label class="block font-medium mb-2">Select Target Depots:</label>
                    <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto">
                        @foreach($depots as $depot)
                            <label class="flex items-center">
                                <input type="checkbox" name="depot_ids[]" value="{{ $depot->id }}" class="mr-2">
                                {{ $depot->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Day Selection -->
                <div id="daySelection" class="mb-4 hidden">
                    <label class="block font-medium mb-2">Select Target Days:</label>
                    <div class="grid grid-cols-2 gap-2">
                        @php
                            $days = [
                                0 => 'Sunday',
                                1 => 'Monday', 
                                2 => 'Tuesday',
                                3 => 'Wednesday',
                                4 => 'Thursday',
                                5 => 'Friday',
                                6 => 'Saturday'
                            ];
                        @endphp
                        @foreach($days as $value => $label)
                            <label class="flex items-center">
                                <input type="checkbox" name="day_of_week[]" value="{{ $value }}" class="mr-2">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeBulkCopyModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Copy Templates</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action*="slot-templates"]');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        const startTime = form.querySelector('select[name="start_time"]').value;
        const endTime = form.querySelector('select[name="end_time"]').value;

        if (!startTime || !endTime) return; // Laravel handles empty

        const [startH, startM] = startTime.split(':').map(Number);
        const [endH, endM] = endTime.split(':').map(Number);

        const start = new Date();
        const end = new Date();

        start.setHours(startH, startM, 0);
        end.setHours(endH, endM, 0);

        let duration = (end - start) / 60000; // in minutes

        if (duration <= 0) {
            duration += 1440; // handle overnight (e.g., 23:00–01:00)
        }

        if (duration > 720) {
            e.preventDefault();
            alert("⛔ Duration must not exceed 12 hours.");
            return;
        }

        if (duration % 15 !== 0) {
            e.preventDefault();
            alert("⚠️ Duration must be in 15-minute intervals.");
            return;
        }
    });
});

function openDuplicateModal(templateId, depotName, dayOfWeek, startTime, endTime) {
    const modal = document.getElementById('duplicateModal');
    const templateInfo = document.getElementById('templateInfo');
    const form = document.getElementById('duplicateForm');
    
    templateInfo.innerHTML = `
        <strong>Copying Template:</strong><br>
        Depot: ${depotName}<br>
        Day: ${dayOfWeek}<br>
        Time: ${startTime} - ${endTime}
    `;
    
    form.action = `/admin/slot-templates/${templateId}/duplicate`;
    
    // Uncheck all checkboxes
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = false);
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDuplicateModal() {
    const modal = document.getElementById('duplicateModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('duplicateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDuplicateModal();
    }
});

// Bulk Copy Functions
function updateBulkCopyButton() {
    const selectedCheckboxes = document.querySelectorAll('.template-checkbox:checked');
    const bulkBtn = document.getElementById('bulkCopyBtn');
    
    if (selectedCheckboxes.length > 0) {
        bulkBtn.disabled = false;
        bulkBtn.textContent = `📋 Copy ${selectedCheckboxes.length} Selected Template(s)`;
    } else {
        bulkBtn.disabled = true;
        bulkBtn.textContent = '📋 Copy Selected Templates';
    }
}

function toggleGroupSelection(selectAllCheckbox, groupIndex) {
    const groupCheckboxes = document.querySelectorAll(`.group-${groupIndex}`);
    groupCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
    updateBulkCopyButton();
}

function openBulkCopyModal() {
    const selectedCheckboxes = document.querySelectorAll('.template-checkbox:checked');
    if (selectedCheckboxes.length === 0) return;
    
    const modal = document.getElementById('bulkCopyModal');
    const templateInfo = document.getElementById('selectedTemplatesInfo');
    const templateIds = document.getElementById('bulkTemplateIds');
    
    let infoHtml = '<strong>Selected Templates:</strong><br>';
    let ids = [];
    
    selectedCheckboxes.forEach(cb => {
        const template = JSON.parse(cb.dataset.template);
        ids.push(template.id);
        infoHtml += `• ${template.depot} - ${template.day} ${template.start}-${template.end}<br>`;
    });
    
    templateInfo.innerHTML = infoHtml;
    
    // Clear previous template IDs and add new ones
    templateIds.innerHTML = '';
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'template_ids[]';
        input.value = id;
        templateIds.appendChild(input);
    });
    
    // Clear all selections
    const depotCheckboxes = modal.querySelectorAll('input[name="depot_ids[]"]');
    depotCheckboxes.forEach(cb => cb.checked = false);
    
    const dayCheckboxes = modal.querySelectorAll('input[name="day_of_week[]"]');
    dayCheckboxes.forEach(cb => cb.checked = false);
    
    // Reset to depot copy mode
    document.querySelector('input[name="copy_type"][value="depots"]').checked = true;
    toggleCopyOptions();
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeBulkCopyModal() {
    const modal = document.getElementById('bulkCopyModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close bulk modal when clicking outside
document.getElementById('bulkCopyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBulkCopyModal();
    }
});

// Toggle copy options based on radio selection
function toggleCopyOptions() {
    const copyType = document.querySelector('input[name="copy_type"]:checked').value;
    const depotSelection = document.getElementById('depotSelection');
    const daySelection = document.getElementById('daySelection');
    
    if (copyType === 'depots') {
        depotSelection.classList.remove('hidden');
        daySelection.classList.add('hidden');
    } else if (copyType === 'days') {
        depotSelection.classList.add('hidden');
        daySelection.classList.remove('hidden');
    } else if (copyType === 'both') {
        depotSelection.classList.remove('hidden');
        daySelection.classList.remove('hidden');
    }
}
</script>
