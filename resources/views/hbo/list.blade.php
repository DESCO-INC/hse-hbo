<x-layout>
    <x-card class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-800">HBO Lists</h2>

            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button size="sm" size="sm" href="{{ url('/hbo/create') }}">
                    Add Item
                </x-button>

                <x-button size="sm" id="btn_export">
                    Export
                </x-button>

                <x-button size="sm" id="btn_import" onclick="toggleModal('modal_import')">
                    Import
                </x-button>

                <x-button size="sm" href="{{ asset('templates/hbo_template_new.xlsx') }}">
                    Download Template
                </x-button>

                <x-button size="sm" onclick="window.history.back();" variant="info">
                    Back
                </x-button>

                <x-button size="sm" href="{{ route('hbo.index') }}" variant="info">
                    Home
                </x-button>
            </div>
        </div>
    </x-card>

    <x-card class="mb-2">
        <form id="hbo-filter-form" action="{{ url()->current() }}" method="GET">
            <input type="hidden" name="form_type" value="filter">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <h1 class="text-lg font-semibold text-gray-800 whitespace-nowrap self-center">Filter</h1>

                <div class="flex flex-wrap items-end gap-4">
                    <!-- Status -->
                    <x-select label="Status" name="status" size="sm" width="30" :options="['' => 'All Status'] +
                        $status->mapWithKeys(fn($status) => [$status => $status])->toArray()" />

                    @php
                        $superAdmin = Auth::user()->credentials == 'SUPER_ADMIN';
                    @endphp
                    <x-select label="Business Unit" name="business_unit" size="sm" :value="$superAdmin ? '' : Auth::user()->business_unit"
                        :readonly="!$superAdmin" :options="['' => 'All Business Unit'] +
                            $business_unit->mapWithKeys(fn($bu) => [$bu => $bu])->toArray()" />

                    <!-- Company -->
                    <x-select label="Group" name="company" size="sm" :options="['' => 'All Group']" />

                    <!-- Date From -->
                    <x-input label="Date From" size="sm" width="30" type="date" name="date_from"
                        value="{{ request('date_from', now()->startOfYear()->format('Y-m-d')) }}" />

                    <!-- Date To -->
                    <x-input label="Date To" size="sm" width="30" type="date" name="date_to"
                        value="{{ request('date_to', now()->format('Y-m-d')) }}" />

                    <!-- Filter Button -->
                    <div class="flex flex-col justify-end">
                        <x-button id="btn_filter">
                            Apply Filter
                        </x-button>
                    </div>
                </div>
            </div>
        </form>
    </x-card>


    <!-- HBO Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-5 overflow-x-auto">
            <!-- Search -->
            <form id="search-form" method="GET" action="{{ url()->current() }}" method="GET"
                class="mb-4 flex items-center gap-2">
                <input type="hidden" name="form_type" value="search">

                <input name="search" id="search" type="text"
                    class="border border-gray-300 rounded-md px-3 py-1 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]"
                    placeholder="Search By ID" value="{{ request('search') }}" />

                <button class="bg-green-500 text-white text-xs px-2 py-1 rounded hover:bg-green-600" type="submit">
                    Search
                </button>
            </form>

            <div class="overflow-x-auto border border-gray-200 rounded">
                <table class="min-w-full divide-y divide-gray-200 text-sm table-auto">
                    <thead class="bg-green-500 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium w-10">#</th>
                            <th class="px-4 py-3 text-left text-sm font-medium max-w-[250px]">Hazard Description</th>
                            <th class="px-4 py-3 text-left text-sm font-medium max-w-[250px]">Recommendation</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-32">Business Unit</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-32">Group</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-36">Date Raised</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-28">Status</th>
                            <th class="px-4 py-3 text-center text-sm font-medium w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($hboList as $index => $hbo)
                            <tr>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $hbo->id }}</td>

                                <!-- Wrapping columns with sentence case -->
                                <td class="px-4 py-3 text-xs text-gray-800 max-w-[250px] break-words"
                                    title="{{ $hbo->hazard_description }}">
                                    {{ ucfirst(strtolower($hbo->hazard_description)) }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-800 max-w-[250px] break-words"
                                    title="{{ $hbo->recommendation }}">
                                    {{ ucfirst(strtolower($hbo->recommendation)) }}
                                </td>

                                <!-- Other columns keep full width -->
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $hbo->business_unit }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $hbo->company }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">
                                    {{ \Carbon\Carbon::parse($hbo->date_raised)->format('Y-m-d') }}</td>
                                @php
                                    $statusColors = [
                                        'CLOSE' => ['text' => '#166534', 'bg' => '#bbf7d0'],
                                        'ONGOING' => ['text' => '#991b1b', 'bg' => '#fecaca'],
                                        'FOR VERIFICATION' => ['text' => '#78350f', 'bg' => '#fef3c7'],
                                    ];
                                    $colors = $statusColors[$hbo->status] ?? ['text' => '#1f2937', 'bg' => '#e5e7eb'];
                                @endphp
                                <td class="px-4 py-3 text-xs text-gray-800">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full"
                                        style="color: {{ $colors['text'] }}; background-color: {{ $colors['bg'] }};">
                                        {{ $hbo->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <a href="{{ url('/hbo/' . $hbo->id . '/edit') }}"
                                        class="bg-blue-500 text-white text-xs px-2 py-1 rounded hover:bg-blue-600">
                                        Manage
                                    </a>
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
            <div class="mt-8">
                {{ $hboList->links() }}
            </div>
        </div>
    </div>

    <!-- Excel Upload Modal -->
    <div id="modal_import"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Upload Excel File</h2>
            <p class="mt-2 text-sm text-gray-600">Select an Excel file (.xlsx or .xls) to upload.</p>

            <form id="uploadForm" method="POST" action="{{ route('hbo.upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="excel_file" accept=".xlsx,.xls"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 file:border-0 file:bg-gray-100 file:px-3 file:py-1 file:rounded-md file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200"
                    required>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                        onclick="toggleModal('modal_import')">
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

    <!-- Export Confirmation Modal -->
    <div id="modal_export"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">

        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">
                Confirm Export
            </h2>

            <p class="text-sm text-gray-600 mb-3">
                You are about to export HBO data with the following filters:
            </p>

            <ul id="exportFilters" class="text-sm text-gray-700 space-y-1 mb-5">
                <!-- Filled by JS -->
            </ul>

            <div class="flex justify-end gap-3">
                <button id="cancelExport" class="px-4 py-2 text-sm rounded border border-gray-300 hover:bg-gray-100"
                    onclick="toggleModal('modal_export')">
                    Cancel
                </button>

                <button id="confirmExport" type="button"
                    class="px-4 py-2 text-sm rounded bg-blue-600 text-white hover:bg-blue-700 flex justify-between items-center">
                    <span id="confirmText">Confirm Export </span>

                    <!-- Small spinner (hidden by default) -->
                    <x-heroicon-o-arrow-path id="confirmSpinner"
                        class="hidden animate-spin h-4 w-4 text-white ml-2" /></path>
                    </svg>
                </button>

            </div>
        </div>
    </div>

    <script>
        const $organizationData = @json($organization);
        const FILTER_STORAGE_KEY = 'hbo_filter_data';

        $(document).ready(function() {

            const savedFilters = localStorage.getItem(FILTER_STORAGE_KEY);
            if (savedFilters) {
                const filters = JSON.parse(savedFilters);

                Object.keys(filters).forEach(name => {
                    $(`[name="${name}"]`).val(filters[name]);
                });
            }

            const selectedBU = $('#business_unit').val();
            if (selectedBU) {
                loadGroups(selectedBU);

                // restore company AFTER groups load
                if (savedFilters) {
                    const filters = JSON.parse(savedFilters);
                    if (filters.company) {
                        $('select[name="company"]').val(filters.company);
                    }
                }
            }
        });

        $('#business_unit').on('change', function() {
            loadGroups($(this).val());
        });

        $('#btn_export').on('click', function() {
            const formData = {};
            $('#hbo-filter-form').serializeArray().forEach(field => {
                formData[field.name] = field.value;
            });
            const $exportFilters = $('#exportFilters');
            $exportFilters.empty();
            Object.entries(formData).forEach(([key, value]) => {
                if (value) {
                    // Nice label formatting
                    const label = key.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                    $exportFilters.append(`<li><strong>${label}:</strong> ${value}</li>`);
                }
            });
            toggleModal('modal_export');
        });

        $('#confirmExport').on('click', function() {
            const formData = {};
            $('#hbo-filter-form').serializeArray().forEach(field => {
                formData[field.name] = field.value;
            });
            const queryString = $.param(formData);
            $('#confirmSpinner').removeClass('hidden');
            $('#confirmText').text('Exporting...');
            window.location.href = "{{ route('hbo.export') }}" + "?" + queryString;
            setTimeout(() => {
                toggleModal('modal_export');
                $('#confirmSpinner').addClass('hidden');
                $('#confirmText').text('Confirm Export');
            }, 1500);
        });

        // Apply Filter button
        $('#btn_filter').on('click', function() {
            const formData = {};
            $('#hbo-filter-form').serializeArray().forEach(field => {
                if (field.name !== 'form_type') { // <-- exclude form_type
                    formData[field.name] = field.value;
                }
            });
            localStorage.setItem(FILTER_STORAGE_KEY, JSON.stringify(formData));
            $('#hbo-filter-form').submit();
        });

        function toggleModal(modalId) {
            $('#' + modalId).toggleClass('hidden');
        }

        function loadGroups(selectedBU) {
            const companyNames = $organizationData
                .filter(org => org.business_unit === selectedBU)
                .map(org => org.company_name);

            const uniqueCompanyNames = [...new Set(companyNames)];

            const $companySelect = $('select[name="company"]');
            $companySelect.empty();
            $companySelect.append('<option value="">All Group</option>');

            uniqueCompanyNames.forEach(name => {
                $companySelect.append(`<option value="${name}">${name}</option>`);
            });
        }
    </script>
</x-layout>
