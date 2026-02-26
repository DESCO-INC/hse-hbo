<x-layout>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 py-4">
            <h2 class="text-lg font-medium text-gray-800">HBO Lists</h2>
            <!-- Button Row (Right) -->
            <div class="flex gap-2 mt-4 sm:mt-0">
                <a href="{{ url('/hbo/create') }}"
                    class="bg-green-500 text-white text-xs px-3 py-2 rounded hover:bg-green-600">
                    Add Item
                </a>
                <button id="btn_export" type="button"
                    class="bg-green-500 text-white text-xs px-3 py-2 rounded hover:bg-green-600"
                    onclick="$('#modal_export').removeClass('hidden')">
                    Export
                </button>
                <button class="bg-green-500 text-white text-xs px-3 py-2 rounded hover:bg-green-600" id="btn_import"
                    onclick="$('#modal_import').removeClass('hidden')">
                    Import
                </button>
                <a href="{{ asset('templates/hbo_template_new.xlsx') }}"
                    class="bg-green-500 text-white text-xs px-3 py-2 rounded hover:bg-green-600">
                    Download Template
                </a>
                <a href="javascript:void(0);" onclick="window.history.back();"
                    class="bg-blue-500 text-white text-xs px-3 py-2 rounded hover:bg-blue-600">
                    Back
                </a>
                <a href="{{ route('hbo.index') }}"
                    class="bg-blue-500 text-white text-xs px-3 py-2 rounded hover:bg-blue-600">
                    Home
                </a>
            </div>
        </div>
    </div>

    <form id="hbo-filter-form" action="{{ url()->current() }}" method="GET">
        <input type="hidden" name="form_type" value="filter">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-3 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 px-6 py-3">
                <h1 class="text-lg font-semibold text-gray-800 whitespace-nowrap self-center">Filter</h1>

                <div class="flex flex-wrap items-end gap-4">

                    <!-- Status -->
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" id="status"
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]">
                            <option value="">Loading..</option>
                        </select>
                    </div>

                    <!-- Business Unit -->
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-500 mb-1">Business Unit</label>
                        <select name="business_unit" id="business_unit"
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]>
                            <option value="">Loading..</option>
                        </select>
                    </div>

                    <!-- Company -->
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-500 mb-1">Group</label>
                        <select name="company" id="company"
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]">
                            <option value="">All Groups</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-500 mb-1">Date From</label>
                        <input name="date_from" id="date_from" type="date"
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]"
                            value="{{ request('date_from', now()->startOfYear()->format('Y-m-d')) }}" />
                    </div>

                    <!-- Date To -->
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-500 mb-1">Date To</label>
                        <input name="date_to" id="date_to" type="date"
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]"
                            value="{{ request('date_to', now()->format('Y-m-d')) }}" />
                    </div>

                    <!-- Filter Button -->
                    <div class="flex flex-col justify-end">
                        <button type="submit" id="filter-btn" name="filter-btn"
                            class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2 rounded-md shadow-sm transition whitespace-nowrap">
                            Apply Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- HBO Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-5 overflow-x-auto">
            <!-- Search -->
            <form method="GET" class="mb-4 flex items-center gap-2">
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
                        onclick="$('#modal_import').addClass('hidden')">
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
                    onclick="$('#modal_export').addClass('hidden')">
                    Cancel
                </button>

                <button id="confirmExport" type="button"
                    class="px-4 py-2 text-sm rounded bg-blue-600 text-white hover:bg-blue-700">
                    <span id="confirmText">Confirm Export</span>

                    <!-- Small spinner (hidden by default) -->
                    <svg id="confirmSpinner" class="hidden animate-spin h-4 w-4 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button>

            </div>
        </div>
    </div>

    <!-- Export Loading Overlay -->
    <div id="exportLoading"
        class="fixed inset-0 z-[999] hidden items-center justify-center bg-white/80 backdrop-blur-sm">

        <div class="flex flex-col items-center gap-4">
            <!-- Spinner -->
            <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                    stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>

            <p class="text-sm font-medium text-gray-700">
                Exporting data, please wait…
            </p>

            <p class="text-xs text-gray-500">
                Do not refresh or close this page
            </p>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const $status = $('#status');
            const $selectBU = $('#business_unit');
            const $selectCompany = $('#company');
            const $datefrom = $('#date_from');
            const $dateto = $('#date_to');

            const $btnFilter = $('#filter-btn');

            const savedStatus = localStorage.getItem('filterStatus');
            const savedBU = localStorage.getItem('filterBU');
            const savedCompany = localStorage.getItem('filterCompany');
            const savedDatefrom = localStorage.getItem('filterDatefrom');
            const savedDateto = localStorage.getItem('filterDateto');

            savedDatefrom ? ($datefrom.val(savedDatefrom)) : null;
            savedDateto ? ($dateto.val(savedDateto)) : null;

            fetch_Statuses(savedStatus);
            fetch_BusinessUnit(savedBU, savedCompany);

            // When BU changes normally (user interaction)
            $selectBU.on('change', function() {
                const selectedBU = $(this).val();
                fetchCompanies(selectedBU); // no restore value here
            });

            // Save on filter click
            $btnFilter.on('click', function() {
                localStorage.setItem('filterStatus', $status.val());
                localStorage.setItem('filterBU', $selectBU.val());
                localStorage.setItem('filterCompany', $selectCompany.val());
                localStorage.setItem('filterDatefrom', $datefrom.val());
                localStorage.setItem('filterDateto', $dateto.val());
            });

            $('#btn_export').on('click', function() {
                const $list = $('#exportFilters');
                $list.empty();

                // Array of label + value pairs
                const filters = [{
                        label: 'Status',
                        value: $status.val()
                    },
                    {
                        label: 'Business Unit',
                        value: $selectBU.val()
                    },
                    {
                        label: 'Group',
                        value: $selectCompany.val()
                    },
                    {
                        label: 'Date from',
                        value: $datefrom.val()
                    },
                    {
                        label: 'Date to',
                        value: $dateto.val()
                    }
                ];

                // Loop through and append only if value is not empty/null
                filters.forEach(f => {
                    if (f.value) { // skips null, undefined, or empty string
                        $list.append($('<li>').append($('<strong>').text(f.label + ': '), f.value));
                    }
                });
            });

            $('#confirmExport').on('click', function() {
                const $btn = $(this);
                const $spinner = $('#confirmSpinner');
                const $confirmText = $('#confirmText');

                // 1️⃣ Grab filter values
                const filters = {
                    status: $status.val(),
                    business_unit: $selectBU.val(),
                    company: $selectCompany.val(),
                    date_from: $datefrom.val(),
                    date_to: $dateto.val()
                };

                // 2️⃣ Show spinner & disable button
                $btn.prop('disabled', true);
                $spinner.removeClass('hidden');
                $confirmText.text('Exporting...');

                // 3️⃣ Build URL with query parameters
                const query = $.param(filters); // automatically skips null/empty values
                const url = '{{ route('hbo.export') }}' + (query ? '?' + query : '');

                // 4️⃣ Trigger file download
                window.location.href = url;

                // 5️⃣ Optional: hide modal and reset button
                $('#modal_export').addClass('hidden');
                $btn.prop('disabled', false);
                $spinner.addClass('hidden');
                $confirmText.text('Confirm Export');
            });
        });

        // ------------------- Fetch Functions -------------------

        function fetch_Statuses(restoreValue = null) {
            $.ajax({
                url: '{{ route('hbo.statuses') }}',
                method: 'GET',
                dataType: 'json',
                success: function(data) {

                    const $select = $('#status');
                    $select.empty();
                    $select.append($('<option>', {
                        value: '',
                        text: 'All Status'
                    }));

                    data.forEach(unit =>
                        $select.append($('<option>', {
                            value: unit,
                            text: unit
                        }))
                    );

                    if (restoreValue) {
                        $select.val(restoreValue);
                    }
                }
            });
        }

        function fetch_BusinessUnit(restoreBU = null, restoreCompany = null) {
            $.ajax({
                url: '{{ route('hbo.business_unit') }}',
                method: 'GET',
                dataType: 'json',
                success: function(data) {

                    const $select = $('#business_unit');
                    $select.empty();
                    $select.append($('<option>', {
                        value: '',
                        text: 'All Business Unit'
                    }));

                    data.forEach(unit =>
                        $select.append($('<option>', {
                            value: unit,
                            text: unit
                        }))
                    );

                    if (restoreBU) {
                        $select.val(restoreBU);
                        fetchCompanies(restoreBU, restoreCompany);
                    }
                }
            });
        }

        function fetchCompanies(selectedBU, restoreValue = null) {
            if (!selectedBU) return;

            $.ajax({
                url: '{{ route('hbo.companies', ['business_unit' => 'BU']) }}'
                    .replace('BU', encodeURIComponent(selectedBU)),
                method: 'GET',
                dataType: 'json',
                success: function(data) {

                    const $select = $('#company');
                    $select.empty();
                    $select.append($('<option>', {
                        value: '',
                        text: 'All Groups'
                    }));

                    data.forEach(unit =>
                        $select.append($('<option>', {
                            value: unit,
                            text: unit
                        }))
                    );

                    if (restoreValue) {
                        $select.val(restoreValue);
                    }
                }
            });
        }
    </script>
</x-layout>
