<x-layout>
    <h1 class="text-xl font-semibold text-gray-800 mb-5">HBO LISTS</h1>

    <form action="{{ url()->current() }}" method="GET">
        <input type="hidden" name="form_type" value="filter">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-5 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 px-6 py-3">
                <h1 class="text-lg font-semibold text-gray-800 whitespace-nowrap self-center">Filter</h1>

                <div class="flex flex-wrap items-end gap-4">

                    <!-- Status -->
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" id="status"
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]">
                            <option value="">All Status</option>
                        </select>
                    </div>

                    <!-- Business Unit -->
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-500 mb-1">Business Unit</label>
                        <select name="business_unit" id="business_unit"
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]">
                            <option value="">All Business Units</option>
                        </select>
                    </div>

                    <!-- Company -->
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-500 mb-1">Group</label>
                        <select name="company" id="company"
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]">
                            <option value="">All Group</option>
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
            <h2 class="text-lg font-medium text-gray-800">HBO Lists</h2>
            <!-- Button Row (Right) -->
            <div class="flex gap-2 mt-4 sm:mt-0">
                <a href="{{ url('/hbo/create') }}"
                    class="bg-green-500 text-white text-xs px-3 py-2 rounded hover:bg-green-600">
                    Add Item
                </a>
                <button id="exportBtn" type="button"
                    class="bg-blue-500 text-white text-xs px-3 py-2 rounded hover:bg-blue-600">
                    Export
                </button>
                <button class="bg-blue-500 text-white text-xs px-3 py-2 rounded hover:bg-blue-600" id="upload-trigger">
                    Import
                </button>
                <a href="{{ asset('templates/hbo_template_new.xlsx') }}"
                    class="bg-blue-500 text-white text-xs px-3 py-2 rounded hover:bg-blue-600">
                    Download Template
                </a>
            </div>
        </div>
    </div>
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
    <div id="upload-modal"
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

    <!-- Export Confirmation Modal -->
    <div id="exportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

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
                <button id="cancelExport" class="px-4 py-2 text-sm rounded border border-gray-300 hover:bg-gray-100">
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

    <!-- Excel Upload Modal -->
    <div id="upload-modal"
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

    @push('scripts')
        <script>
            document.getElementById('upload-trigger').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('upload-modal').classList.remove('hidden');
            });

            document.addEventListener('DOMContentLoaded', function() {
                // ===============================
                // 1️⃣ Elements
                // ===============================
                const businessUnitSelect = document.getElementById('business_unit');
                const companySelect = document.getElementById('company');
                const dateFromInput = document.getElementById('date_from');
                const dateToInput = document.getElementById('date_to');
                const statusSelect = document.getElementById('status'); // list view only, might be null

                const exportBtn = document.getElementById('exportBtn');
                const modal = document.getElementById('exportModal');
                const loadingOverlay = document.getElementById('exportLoading');
                const confirmBtn = document.getElementById('confirmExport');
                const confirmText = document.getElementById('confirmText');
                const confirmSpinner = document.getElementById('confirmSpinner');

                const uploadForm = document.getElementById('uploadForm');
                const uploadBtn = document.getElementById('uploadBtn');
                const spinner = document.getElementById('spinner');
                const uploadBtnText = document.getElementById('uploadBtnText');

                const today = new Date();
                const startOfYear = new Date(today.getFullYear(), 0, 1);

                const formatDate = (date) => {
                    const yyyy = date.getFullYear();
                    const mm = String(date.getMonth() + 1).padStart(2, '0');
                    const dd = String(date.getDate()).padStart(2, '0');
                    return `${yyyy}-${mm}-${dd}`;
                };

                // ===============================
                // 2️⃣ Load filters from localStorage or defaults
                // ===============================
                let filters = JSON.parse(localStorage.getItem('hboFilters') || '{}');
                if (!filters.date_from) filters.date_from = formatDate(startOfYear);
                if (!filters.date_to) filters.date_to = formatDate(today);
                if (!filters.business_unit) filters.business_unit = '';
                if (!filters.company) filters.company = '';
                if (!filters.status) filters.status = '';

                // Set inputs
                if (businessUnitSelect) businessUnitSelect.value = filters.business_unit;
                if (companySelect) companySelect.value = filters.company;
                if (dateFromInput) dateFromInput.value = filters.date_from;
                if (dateToInput) dateToInput.value = filters.date_to;
                if (statusSelect) statusSelect.value = filters.status;

                // ===============================
                // 3️⃣ Load business units
                // ===============================
                if (businessUnitSelect) {
                    fetch('{{ route('hbo.business_unit') }}')
                        .then(res => res.json())
                        .then(data => {
                            data.forEach(bu => {
                                const option = document.createElement('option');
                                option.value = bu;
                                option.textContent = bu;
                                if (bu === filters.business_unit) option.selected = true;
                                @if (Auth::user()->credentials != 'superadmin')
                                    if (bu === "{{ Auth::user()->business_unit }}") option.selected = true;
                                @endif
                                businessUnitSelect.appendChild(option);
                            });

                            if (filters.business_unit) loadCompanies(filters.business_unit, filters.company);
                        });

                    businessUnitSelect.addEventListener('change', function() {
                        loadCompanies(this.value);
                    });
                }

                function loadCompanies(businessUnit, selectedCompany = '') {
                    if (!companySelect) return;
                    companySelect.innerHTML = '<option value="">All Companies</option>';
                    if (!businessUnit) return;

                    const url = "{{ route('hbo.companies', ':bu') }}".replace(':bu', encodeURIComponent(businessUnit));
                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            data.forEach(c => {
                                const option = document.createElement('option');
                                option.value = c;
                                option.textContent = c;
                                if (c === selectedCompany) option.selected = true;
                                companySelect.appendChild(option);
                            });
                        })
                        .catch(err => console.error('Error loading companies:', err));
                }

                // ===============================
                // 4️⃣ Load statuses (list view)
                // ===============================
                if (statusSelect) {
                    fetch('{{ route('hbo.statuses') }}')
                        .then(res => res.json())
                        .then(data => {
                            if (!Array.isArray(data) || !data.length) {
                                const option = document.createElement('option');
                                option.value = '';
                                option.textContent = 'No Status Found';
                                statusSelect.appendChild(option);
                                return;
                            }

                            data.forEach(s => {
                                const option = document.createElement('option');
                                option.value = s;
                                option.textContent = s;
                                if (s === filters.status) option.selected = true;
                                statusSelect.appendChild(option);
                            });
                        })
                        .catch(err => console.error('Error loading statuses:', err));
                }

                // ===============================
                // 5️⃣ Get filters from inputs
                // ===============================
                function getFilters() {
                    return {
                        business_unit: businessUnitSelect ? businessUnitSelect.value : '',
                        company: companySelect ? companySelect.value : '',
                        date_from: dateFromInput ? dateFromInput.value : '',
                        date_to: dateToInput ? dateToInput.value : '',
                        status: statusSelect ? statusSelect.value : ''
                    };
                }

                // ===============================
                // 6️⃣ Apply filters button
                // ===============================
                const filterBtn = document.querySelector('#filter-btn') || document.querySelector(
                    'button[type="submit"]');
                if (filterBtn) {
                    filterBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        filters = getFilters();
                        localStorage.setItem('hboFilters', JSON.stringify(filters));

                        // Reload dashboard/chart/weekly if functions exist
                        if (window.loadDashboardCount) loadDashboardCount(filters);
                        if (window.loadChartData) loadChartData(filters);
                        if (window.loadWeeklySummary) loadWeeklySummary(filters);
                    });
                }

                // ===============================
                // 7️⃣ Initial load
                // ===============================
                if (window.loadDashboardCount) loadDashboardCount(filters);
                if (window.loadChartData) loadChartData(filters);
                if (window.loadWeeklySummary) loadWeeklySummary(filters);

                // ===============================
                // 8️⃣ Export modal
                // ===============================
                if (exportBtn) {
                    exportBtn.addEventListener('click', function() {
                        const filtersForExport = getFilters();
                        const list = document.getElementById('exportFilters');
                        if (list) {
                            list.innerHTML = '';
                            Object.entries(filtersForExport).forEach(([k, v]) => {
                                if (v) list.innerHTML +=
                                    `<li><strong>${k.replace('_',' ').toUpperCase()}:</strong> ${v}</li>`;
                            });
                            if (!list.innerHTML) list.innerHTML =
                                '<li><em>No filters applied (All records)</em></li>';
                        }

                        modal.classList.remove('hidden');
                        modal.classList.add('flex');

                        confirmBtn.onclick = function() {
                            confirmBtn.disabled = true;
                            confirmBtn.classList.add('opacity-70', 'cursor-not-allowed');
                            confirmText.textContent = 'Exporting...';
                            confirmSpinner.classList.remove('hidden');

                            closeExportModal();
                            lockPage();

                            const query = new URLSearchParams(filtersForExport).toString();
                            window.location.href = "{{ route('hbo.export') }}?" + query;
                        };
                    });

                    document.getElementById('cancelExport')?.addEventListener('click', closeExportModal);
                    modal?.addEventListener('click', function(e) {
                        if (e.target.id === 'exportModal') closeExportModal();
                    });

                    function closeExportModal() {
                        modal?.classList.add('hidden');
                        modal?.classList.remove('flex');
                    }

                    function lockPage() {
                        if (loadingOverlay) {
                            loadingOverlay.classList.remove('hidden');
                            loadingOverlay.classList.add('flex');
                        }
                        document.body.style.overflow = 'hidden';
                        document.body.style.pointerEvents = 'none';
                        if (loadingOverlay) loadingOverlay.style.pointerEvents = 'all';
                    }

                    function unlockPage() {
                        if (loadingOverlay) {
                            loadingOverlay.classList.add('hidden');
                            loadingOverlay.classList.remove('flex');
                        }
                        document.body.style.overflow = '';
                        document.body.style.pointerEvents = '';
                        confirmBtn.disabled = false;
                        confirmBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                        if (confirmText) confirmText.textContent = 'Confirm Export';
                        if (confirmSpinner) confirmSpinner.classList.add('hidden');
                    }

                    window.addEventListener('focus', unlockPage);
                }

                // ===============================
                // 9️⃣ Upload spinner (list view)
                // ===============================
                if (uploadForm) {
                    uploadForm.addEventListener('submit', function() {
                        if (spinner) spinner.classList.remove('hidden');
                        if (uploadBtn) uploadBtn.disabled = true;
                        if (uploadBtnText) uploadBtnText.textContent = 'Uploading...';
                    });
                }
            });
        </script>
    @endpush
</x-layout>
