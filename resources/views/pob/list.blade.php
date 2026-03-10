<x-layout>
    <x-card class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-800">POB Lists</h2>

            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button size="sm" href="{{ route('pob.create') }}">
                    Add POB
                </x-button>

                <x-button size="sm" id="btn_import" onclick="toggleModal('download-template-modal')">
                    Download Template
                </x-button>

                <x-button size="sm" onclick="toggleModal('upload-modal')">
                    Import
                </x-button>

                <x-button size="sm" href="{{ route('hbo.index') }}" variant="info">
                    Home
                </x-button>
            </div>
        </div>
    </x-card>

    <x-card class="mb-2">
        <form id="pob_filter_form" action="{{ url()->current() }}" method="GET">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <h1 class="text-lg font-semibold text-gray-800 whitespace-nowrap self-center">Filter</h1>

                <div class="flex flex-wrap items-end gap-4">
                    @php
                        $superAdmin = Auth::user()->credentials == 'SUPER_ADMIN';
                    @endphp
                    <x-select label="Business Unit" name="business_unit" size="sm" :value="$superAdmin ? '' : Auth::user()->business_unit"
                        :readonly="!$superAdmin" :options="['' => 'All Business Unit'] +
                            $business_unit->mapWithKeys(fn($bu) => [$bu => $bu])->toArray()" />

                    <!-- Date From -->
                    <x-input label="Date From" size="sm" width="30" type="date" name="date_from"
                        value="{{ request('date_from', now()->startOfYear()->format('Y-m-d')) }}" />

                    <!-- Date To -->
                    <x-input label="Date To" size="sm" width="30" type="date" name="date_to"
                        value="{{ request('date_to', now()->format('Y-m-d')) }}" />

                    <!-- Filter Button -->
                    <div class="flex flex-col justify-end">
                        <x-button type="submit" id="btn_filter">
                            Apply Filter
                        </x-button>
                    </div>
                </div>
            </div>
        </form>
    </x-card>

    <x-card class="mb-2">
        <div class="overflow-x-auto">
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
                                    <x-button size="xs" variant="info"
                                        href="{{ route('pob.edit', $pob->id) }}">
                                        View / Manage
                                    </x-button>
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
    </x-card>

    <!-- Download Template Modal -->
    <div id="download-template-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Select Business Unit</h2>
            <p class="mt-2 text-sm text-gray-600">
                Choose a business unit to generate the Excel template.
            </p>

            <form id="downloadTemplateForm" method="GET" action="{{ route('pob.downloadTemplate') }}">
                <div class="mt-4">
                    <x-select label="Business Unit" name="business_unit" size="md" width="full"
                        :readonly="!$superAdmin" :value="$superAdmin ? '' : Auth::user()->business_unit" :options="['' => 'All Business Unit'] +
                            $business_unit->mapWithKeys(fn($bu) => [$bu => $bu])->toArray()" />
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

    <script>
        const FILTER_STORAGE_KEY = 'pob_filter_data';

        $(document).ready(function() {
            const savedFilters = localStorage.getItem(FILTER_STORAGE_KEY);
            if (savedFilters) {
                const filters = JSON.parse(savedFilters);

                Object.keys(filters).forEach(name => {
                    $(`[name="${name}"]`).val(filters[name]);
                });
            }
        });

        // Apply Filter button
        $('#btn_filter').on('click', function() {
            const formData = {};
            $('#pob_filter_form').serializeArray().forEach(field => {
                formData[field.name] = field.value;
            });
            localStorage.setItem(FILTER_STORAGE_KEY, JSON.stringify(formData));
            $('#pob_filter_form').submit();
        });
    </script>

    <script>
        function toggleModal(modalId) {
            $('#' + modalId).toggleClass('hidden');
        }
    </script>

</x-layout>
