<x-layout>
    <x-card class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-800">POB Records</h2>
            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button size="sm" href="{{ route('hbo.index') }}" variant="info">
                    Home
                </x-button>
            </div>
        </div>
    </x-card>

    <x-card class="relative mb-2"> <!-- add relative here -->
        <form id="pob-filter-form">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <h1 class="text-lg font-semibold text-gray-800 whitespace-nowrap self-center">Filter</h1>

                <div class="flex flex-wrap items-end gap-4">
                    @php
                        $superAdmin = Auth::user()->credentials == 'SUPER_ADMIN';
                    @endphp
                    <x-select label="Business Unit" name="business_unit" size="sm"
                        value="{{ Auth::user()->business_unit }}" :readonly="!$superAdmin" :options="$business_unit->mapWithKeys(fn($bu) => [$bu => $bu])->toArray()" />

                    <x-select label="Year" name="year" size="sm" :options="$years->mapWithKeys(fn($year) => [$year => $year])->toArray()" />

                    <x-select label="Work Week" name="week" size="sm" :options="$weeks->mapWithKeys(fn($week) => [$week => $week])->toArray()" :value="now()->format('W')" />

                    <div class="flex flex-col justify-end">
                        <x-button id="btn_filter">Apply Filter</x-button>
                    </div>
                </div>
            </div>
        </form>

        <div id="filterLoading" class="absolute inset-0 bg-white/70 flex items-center justify-center z-50 hidden">
            <svg class="animate-spin h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </div>
    </x-card>


    <div class="h-[1200px] grid grid-cols-3 grid-rows-7 gap-2">
        <x-card class="col-span-3 row-span-4">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        POB Ave vs HBO Count for Work Week
                    </p>
                    <div id="hbo-vs-pob" class="w-full h-full"></div>
                </div>
            </div>
        </x-card>

        <x-card class="col-span-3 row-span-3">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                        POB vs HBO Weekly Report
                    </p>
                    <div id="hbo-vs-pob-weekly" class="w-full h-full"></div>
                </div>
            </div>
        </x-card>
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

                <form method="POST">
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

            let filters = {
                business_unit: $('#business_unit').val(),
                year: $('#year').val(),
                week: $('#week').val(),
            };
            fetchAveDataCount(filters);
            fetchPobHboWeeklyData(filters);
        });
    </script>

    <script>
        $('#btn_filter').on('click', function() {
            const formData = {};
            $('#pob-filter-form').serializeArray().forEach(field => {
                formData[field.name] = field.value;
            });
            localStorage.setItem(FILTER_STORAGE_KEY, JSON.stringify(formData));
            fetchAveDataCount(formData);
            fetchPobHboWeeklyData(formData);
        });
    </script>

    <script>
        function fetchAveDataCount(filters = {}) {
            const $loading = $('#filterLoading');
            $loading.removeClass('hidden');
            $.ajax({
                url: "{{ route('pob.getAveDataCount') }}",
                method: 'GET',
                data: filters,
                dataType: 'json',
                success: function(response) {
                    window.renderPobVsHboChart(response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $loading.addClass('hidden');
                }
            });
        }

        function fetchPobHboWeeklyData(filters = {}) {
            const $loading = $('#filterLoading'); // same loading indicator
            $loading.removeClass('hidden');

            $.ajax({
                url: "{{ route('pob.getWeeklyData') }}",
                method: 'GET',
                data: filters,
                dataType: 'json',
                success: function(response) {
                    window.renderPobVsHboWeeklyChart(response); // call the weekly chart renderer
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $loading.addClass('hidden');
                }
            });
        }
    </script>

</x-layout>
