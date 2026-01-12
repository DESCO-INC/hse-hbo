<x-layout>
    <h1 class="text-xl font-semibold text-gray-800 mb-5">POB RECORDS</h1>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-5 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 px-6 py-3">
            <h1 class="text-lg font-semibold text-gray-800 whitespace-nowrap self-center">Filter</h1>

            <div class="flex flex-wrap items-end gap-4">

                <!-- Business Unit -->
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-500 mb-1">Business Unit</label>
                    <select name="business_unit" id="business_unit"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]"
                        {{ Auth::user()->credentials != 'superadmin' ? 'disabled' : '' }}>
                        <!-- Options will be dynamically inserted -->
                    </select>
                    @if (Auth::user()->credentials != 'superadmin')
                        <input type="hidden" name="business_unit" value="{{ Auth::user()->business_unit }}">
                    @endif
                </div>

                <!-- Year -->
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-500 mb-1">Year</label>
                    <select name="year" id="year"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[120px]">
                        <!-- Options will be dynamically inserted -->
                    </select>
                </div>

                <!-- Work Week -->
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-500 mb-1">Week</label>
                    <select name="week" id="week"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[100px]">
                        <!-- Options will be dynamically inserted -->
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="flex flex-col justify-end">
                    <button id="filter-btn" name="filter-btn"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2 rounded-md shadow-sm transition whitespace-nowrap">
                        Apply Filter
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="col-span-3 row-span-3 mb-5">
        <div
            class="h-full col-span-1 bg-white rounded-lg shadow-md border p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <div id="hbo-vs-pob-chart" class="w-full min-h-[500px]"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-3 row-span-3 mb-5">
        <div
            class="h-full col-span-1 bg-white rounded-lg shadow-md border p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <div id="hbo-vs-pob-weekly-chart" class="w-full min-h-[500px]"></div>
                </div>
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


    @vite('resources/js/chart.js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const businessUnitSelect = document.getElementById('business_unit');
            const yearSelect = document.getElementById('year');
            const weekSelect = document.getElementById('week');

            // ✅ Get filter values
            function getFilters() {
                return {
                    business_unit: businessUnitSelect.value,
                    year: yearSelect.value,
                    week: weekSelect.value
                };
            }

            // ✅ Load main chart
            function loadChartData(filters) {
                const params = new URLSearchParams({
                    business_unit: filters.business_unit,
                    year: filters.year,
                    week: filters.week
                });

                fetch(`{{ route('pob.chart-data') }}?${params.toString()}`)
                    .then(res => res.json())
                    .then(data => {
                        if (window.updateHboVsPobChart) {
                            window.updateHboVsPobChart(data);
                        }
                    })
                    .catch(err => console.error('Error fetching chart data:', err));
            }

            // ✅ Load secondary chart
            function loadChartData2(filters) {
                const params = new URLSearchParams({
                    business_unit: filters.business_unit,
                    year: filters.year,
                    week: filters.week
                });

                fetch(`{{ route('pob.chart-data2') }}?${params.toString()}`)
                    .then(res => res.json())
                    .then(data => {
                        if (window.updateHboVsPobWeeklyChart) {
                            window.updateHboVsPobWeeklyChart(data);
                        }
                    })
                    .catch(err => console.error('Error fetching weekly chart data:', err));
            }

            // ✅ Populate business units
            function loadBusinessUnits(callback) {
                fetch('{{ route('pob.business_unit') }}')
                    .then(res => res.json())
                    .then(data => {
                        businessUnitSelect.innerHTML = '';
                        if (!Array.isArray(data) || data.length === 0) {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No data';
                            option.selected = true;
                            option.disabled = true;
                            businessUnitSelect.appendChild(option);
                        } else {
                            data.forEach((bu, index) => {
                                const option = document.createElement('option');
                                option.value = bu;
                                option.textContent = bu;

                                // Preselect logic
                                if ("{{ request('business_unit') }}" && bu ===
                                    "{{ request('business_unit') }}") {
                                    option.selected = true;
                                } else if ("{{ Auth::user()->credentials }}" !== 'superadmin' && bu ===
                                    "{{ Auth::user()->business_unit }}") {
                                    option.selected = true;
                                } else if (index === 0 && !businessUnitSelect.value) {
                                    option.selected = true;
                                }

                                businessUnitSelect.appendChild(option);
                            });
                        }

                        if (callback) callback(); // call next step after loading BU
                    })
                    .catch(err => console.error('Error fetching business units:', err));
            }

            // ✅ Populate years and weeks
            function loadYearsAndWeeks(callback) {
                fetch('{{ route('pob.getYearWeek') }}')
                    .then(res => res.json())
                    .then(data => {
                        // Years
                        yearSelect.innerHTML = '';
                        if (data.years && data.years.length > 0) {
                            data.years.forEach(y => {
                                const option = document.createElement('option');
                                option.value = y;
                                option.textContent = y;
                                if ("{{ request('year', now()->year) }}" == y) option.selected = true;
                                yearSelect.appendChild(option);
                            });
                        }

                        // Weeks
                        weekSelect.innerHTML = '';
                        if (data.weeks && data.weeks.length > 0) {
                            data.weeks.forEach(w => {
                                const option = document.createElement('option');
                                option.value = w;
                                option.textContent = w;
                                if ("{{ request('week', now()->format('W')) }}" == w) option.selected =
                                    true;
                                weekSelect.appendChild(option);
                            });
                        }

                        if (callback) callback(); // call next step after loading year/week
                    })
                    .catch(err => console.error('Error fetching years and weeks:', err));
            }

            // ✅ Load all filters and charts on page load
            function initialize() {
                loadBusinessUnits(() => {
                    loadYearsAndWeeks(() => {
                        const filters = getFilters();
                        loadChartData(filters);
                        loadChartData2(filters);
                    });
                });
            }

            // ✅ Filter button
            document.getElementById('filter-btn').addEventListener('click', (e) => {
                e.preventDefault();
                const filters = getFilters();
                loadChartData(filters);
                loadChartData2(filters);
            });

            // ✅ Initialize page
            initialize();
        });
    </script>

</x-layout>
