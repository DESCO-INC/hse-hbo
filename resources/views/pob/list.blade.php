<x-layout>
    <h1 class="text-xl font-semibold text-gray-800 mb-5">POB RECORDS</h1>

    <form action="{{ url()->current() }}" method="GET">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-5 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 px-6 py-3">
                <h1 class="text-lg font-semibold text-gray-800 whitespace-nowrap self-center">Filter</h1>
                <div class="flex flex-wrap items-end gap-4">

                    <!-- Business Unit -->
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-500 mb-1">Business Unit</label>
                        <select name="business_unit" id="business_unit"
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]"
                            {{ Auth::user()->credentials != 'SUPER_ADMIN' ? 'disabled' : '' }}>
                            <option value="">All Business Units</option>
                        </select>
                        @if (Auth::user()->credentials != 'SUPER_ADMIN')
                            <input type="hidden" name="business_unit" value="{{ Auth::user()->business_unit }}">
                        @endif
                    </div>

                    <!-- Date From -->
                    <input name="date_from" id="date_from" type="date"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]"
                        value="{{ request('date_from', now()->startOfYear()->format('Y-m-d')) }}" />

                    <!-- Date To -->
                    <input name="date_to" id="date_to" type="date"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]"
                        value="{{ request('date_to', now()->format('Y-m-d')) }}" />

                    <!-- Filter Button -->
                    <div class="flex flex-col justify-end">
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2 rounded-md shadow-sm transition whitespace-nowrap">
                            Apply Filter
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 py-4">
            <h2 class="text-lg font-medium text-gray-800">POB Lists</h2>
            <!-- Button Row (Right) -->
            <div class="flex gap-2 mt-4 sm:mt-0">
                <a href="{{ url('/pob/create') }}"
                    class="bg-green-500 text-white text-xs px-3 py-2 rounded hover:bg-green-600">
                    Add POB
                </a>
                <button type="button" class="bg-blue-500 text-white text-xs px-3 py-2 rounded hover:bg-blue-600"
                    onclick="document.getElementById('download-template-modal').classList.remove('hidden')">
                    Download Template
                </button>
                <button class="bg-blue-500 text-white text-xs px-3 py-2 rounded hover:bg-blue-600" id="upload-trigger">
                    Import
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden mt-5">
        <div class="px-6 py-5 overflow-x-auto">
            <div class="overflow-x-auto border border-gray-200 rounded">
                <table class="min-w-full divide-y divide-gray-200 text-sm table-auto">
                    <thead class="bg-green-500 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium w-10">id</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-32">Business Unit</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-36">Date</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-28">Total</th>
                            <th class="px-4 py-3 text-center text-sm font-medium w-24">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($poblist as $pob)
                            <tr>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $pob->id }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $pob->business_unit }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">
                                    {{ \Carbon\Carbon::parse($pob->date)->format('Y-m-d') }}
                                </td>
                                <!-- TOTAL -->
                                <td class="px-4 py-3 text-xs text-gray-800">
                                    {{ array_sum($pob->attendance_data ?? []) }}
                                </td>
                                <td class="px-4 py-2 text-center flex justify-center gap-1">
                                    <!-- View/Edit Button -->
                                    <a href="{{ url('/pob/' . $pob->id . '/edit') }}"
                                        class="bg-blue-500 text-white text-xs px-2 py-1 rounded hover:bg-blue-600">
                                        View
                                    </a>

                                    <!-- Delete Button -->
                                    <button type="button"
                                        class="bg-red-500 text-white text-xs px-2 py-1 rounded hover:bg-red-600"
                                        onclick="openDeleteModal({{ $pob->id }})">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-2 text-center text-gray-500">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $poblist->links() }}
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Confirm Delete</h2>
            <p class="mt-2 text-sm text-gray-600">
                Are you sure you want to delete this POB record? This action cannot be undone.
            </p>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                    onclick="document.getElementById('delete-modal').classList.add('hidden')">
                    Cancel
                </button>

                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Download Template Modal -->
    <div id="download-template-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Select Business Unit</h2>
            <p class="mt-2 text-sm text-gray-600">
                Choose a business unit to generate the Excel template.
            </p>

            <form id="downloadTemplateForm" method="GET">
                <div class="mt-4">
                    <x-form-label for="bu_select">Business Unit</x-form-label>
                    <x-form-select name="business_unit" id="bu_select" required>
                        <option value="">Loading business units...</option>
                    </x-form-select>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                        onclick="document.getElementById('download-template-modal').classList.add('hidden')">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Download
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Excel Upload Modal -->
    <div id="upload-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Upload Excel File</h2>
            <p class="mt-2 text-sm text-gray-600">Select an Excel file (.xlsx or .xls) to upload.</p>

            <form id="uploadForm" method="POST" action="{{ route('pob.upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="excel_file" accept=".xlsx,.xls"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 file:border-0 file:bg-gray-100 file:px-3 file:py-1 file:rounded-md file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200"
                    required>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                        onclick="document.getElementById('upload-modal').classList.add('hidden')">
                        Cancel
                    </button>

                    <button type="submit" id="uploadBtn"
                        class="relative px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center justify-center">
                        <svg id="spinner" class="animate-spin h-5 w-5 mr-2 text-white hidden"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                            </path>
                        </svg>
                        <span id="uploadBtnText">Upload</span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    @vite('resources/js/chart.js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buSelect = document.getElementById('bu_select');

            // Load business units via AJAX
            fetch('{{ route('org.business_unit') }}')
                .then(res => res.json())
                .then(data => {
                    buSelect.innerHTML = '<option value="">Select Business Unit</option>';
                    data.forEach(bu => {
                        const option = document.createElement('option');
                        option.value = bu; // depends on your controller JSON structure
                        option.textContent = bu;
                        buSelect.appendChild(option);
                    });
                })
                .catch(err => {
                    console.error('Error loading business units:', err);
                    buSelect.innerHTML = '<option value="">Failed to load business units</option>';
                });
        });
    </script>

    <script>
        document.getElementById('downloadTemplateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const bu = document.getElementById('bu_select').value;
            if (!bu) return alert('Please select a business unit.');

            // Use the correct named route
            const url = "{{ route('pob.downloadTemplate', ':bu') }}".replace(':bu', encodeURIComponent(bu));

            // Redirect to download
            window.location.href = url;
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const businessUnitSelect = document.getElementById('business_unit');
            const companySelect = document.getElementById('company');

            fetch('{{ route('pob.business_unit') }}')
                .then(res => res.json())
                .then(data => {
                    businessUnitSelect.innerHTML = ''; // Clear existing options

                    if (!Array.isArray(data) || data.length === 0) {
                        // Show "No data" if empty
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No data';
                        option.selected = true;
                        option.disabled = true;
                        businessUnitSelect.appendChild(option);
                        return;
                    }

                    // Populate the select with actual business units
                    data.forEach((bu, index) => {
                        const option = document.createElement('option');
                        option.value = bu;
                        option.textContent = bu;

                        // Preselect logic
                        if ("{{ request('business_unit') }}" && bu ===
                            "{{ request('business_unit') }}") {
                            option.selected = true;
                        } else if ("{{ Auth::user()->credentials }}" !== 'SUPER_ADMIN' && bu ===
                            "{{ Auth::user()->business_unit }}") {
                            option.selected = true;
                        } else if (index === 0 && !businessUnitSelect.value) {
                            option.selected = true;
                        }

                        businessUnitSelect.appendChild(option);
                    });
                })
                .catch(err => {
                    console.error('Error fetching business units:', err);
                    // Show fallback "No data" if fetch fails
                    businessUnitSelect.innerHTML = '';
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No data';
                    option.selected = true;
                    option.disabled = true;
                    businessUnitSelect.appendChild(option);
                });
        });
    </script>
    <script>
        function openDeleteModal(pobId) {
            const modal = document.getElementById('delete-modal');
            const form = document.getElementById('deleteForm');

            // Use Laravel named route with placeholder
            const url = "{{ route('pob.destroy', ':id') }}".replace(':id', pobId);
            form.action = url;

            // Show the modal
            modal.classList.remove('hidden');
        }
    </script>
    <script>
        document.getElementById('upload-trigger').addEventListener('click', function() {
            document.getElementById('upload-modal').classList.remove('hidden');
        });

        // Spinner on submit
        document.getElementById('uploadForm').addEventListener('submit', function() {
            document.getElementById('spinner').classList.remove('hidden');
            document.getElementById('uploadBtnText').classList.add('hidden');
            document.getElementById('uploadBtn').setAttribute('disabled', true);
        });
    </script>



</x-layout>
