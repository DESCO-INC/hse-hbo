<x-layout>
    <x-card class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-800">HBO Lists</h2>
        </div>
    </x-card>

    <x-card class="relative mb-2"> <!-- add relative here -->
        <form id="hbo-filter-form">
            <input type="hidden" name="form_type" value="filter">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <h1 class="text-lg font-semibold text-gray-800 whitespace-nowrap self-center">Filter</h1>

                <div class="flex flex-wrap items-end gap-4">
                    @php
                        $superAdmin = Auth::user()->credentials == 'SUPER_ADMIN';
                    @endphp
                    <x-select label="Business Unit" name="business_unit" size="sm" :value="$superAdmin ? '' : Auth::user()->business_unit"
                        :readonly="!$superAdmin" :options="['' => 'All Business Unit'] +
                            $business_unit->mapWithKeys(fn($bu) => [$bu => $bu])->toArray()" />

                    <x-select label="Group" name="company" size="sm" :options="['' => 'All Group']" />

                    <x-input label="Date From" size="sm" width="30" type="date" name="date_from"
                        value="{{ request('date_from', now()->startOfYear()->format('Y-m-d')) }}" />

                    <x-input label="Date To" size="sm" width="30" type="date" name="date_to"
                        value="{{ request('date_to', now()->format('Y-m-d')) }}" />

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

    <div class="h-[2300px] grid grid-cols-4 grid-rows-18 gap-2 mb-4">
        <x-card class="flex flex-col">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                Total HBO submitted
            </p>
            <p class="text-5xl font-extrabold text-gray-900 self-center" id="totalCount">0</p>
        </x-card>

        <x-card class="flex flex-col">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                Ongoing HBO Count
            </p>
            <p class="text-5xl font-extrabold text-gray-900 self-center" id="ongoingCount">0</p>
        </x-card>

        <x-card class="flex flex-col">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                For Verification HBO Count
            </p>
            <p class="text-5xl font-extrabold text-gray-900 self-center" id="forVerificationCount">0</p>
        </x-card>

        <x-card class="flex flex-col">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                Closed HBO Count
            </p>
            <p class="text-5xl font-extrabold text-gray-900 self-center" id="closeCount">0</p>
        </x-card>

        <x-card class="col-span-4 row-span-3">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        HBO submitted by Date
                    </p>
                    <div id="hbo-by-date" class="w-full h-full"></div>
                </div>
            </div>
        </x-card>

        <x-card class="col-span-2 row-span-3 row-start-5">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">HBO Reported by Category
                    </p>
                    <div id="hbo-by-category" class="w-full h-full"></div>
                </div>
            </div>
        </x-card>

        <x-card class="row-span-3 row-start-5 col-span-2">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">HBO Submission by Group</p>
                    <div id="hbo-by-group" class="w-full h-full"></div>
                </div>
            </div>
        </x-card>

        <x-card class="col-span-2 row-span-3 row-start-8">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">HBO Submission by Type</p>
                    <div id="hbo-by-type" class="w-full h-full"></div>
                </div>
            </div>
        </x-card>

        <x-card class="col-span-2 row-span-3 row-start-8">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">HBO Reported by
                        Sub-Category</p>
                    <div id="hbo-by-subcategory" class="w-full h-full"></div>
                </div>
            </div>
        </x-card>

        <x-card class="col-span-3 row-span-3 row-start-11">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">HBO submitted by Date
                        (WEEKLY)</p>
                    <div id="hbo-by-week" class="w-full h-full"></div>
                </div>
            </div>
        </x-card>

        <x-card class="row-span-2 row-start-11">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Top 5 Reporters for
                        <span id="ranking-date-range"></span>
                    </p>
                    <div id="hbo-by-reporter" class="w-full h-full"></div>
                </div>
            </div>
        </x-card>

        <x-card class="row-span-1">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Number of Reporters
                    </p>
                    <div id="hbo-by-reportertotal" class="w-full h-full flex justify-center items-center"></div>
                </div>
            </div>
        </x-card>

        <x-card class="col-span-4 row-span-2 row-start-14">
            <div class="flex h-full items-center justify-between">
                <div class="flex-1 h-full">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Weekly Summary Chart</p>
                    <div id="weekly-summary" class="w-full h-full flex justify-center items-center"></div>
                </div>
            </div>
        </x-card>

        <x-card class="col-span-4 row-span-2 row-start-16">
            <div class="flex flex-col h-full">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-5">Weekly Summary</p>
                <div class="overflow-x-auto">
                    <table id="weekly-summary-table" class="min-w-full text-xs text-gray-700 border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-2 py-1 border text-left">Week & Range</th>
                                <th class="px-2 py-1 border text-center">Mon</th>
                                <th class="px-2 py-1 border text-center">Tue</th>
                                <th class="px-2 py-1 border text-center">Wed</th>
                                <th class="px-2 py-1 border text-center">Thu</th>
                                <th class="px-2 py-1 border text-center">Fri</th>
                                <th class="px-2 py-1 border text-center">Sat</th>
                                <th class="px-2 py-1 border text-center">Sun</th>
                                <th class="px-2 py-1 border text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody id="weekly-summary-tbody">
                            <!-- populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </x-card>
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

            let filters = {
                business_unit: $('#business_unit').val(),
                company: $('#company').val(),
                date_from: $('#date_from').val(),
                date_to: $('#date_to').val()
            };
            fetchHboCount(filters);
            fetchHboCountByDate(filters);
            fetchHboByCategory(filters);
            fetchHboByGroup(filters);
            fetchHboByType(filters);
            fetchHboBySubCategory(filters);
            fetchHboByWeek(filters);
            fetchHboByReporter(filters);
            fetchHboWeeklySummary(filters);
        });

        $('#business_unit').on('change', function() {
            loadGroups($(this).val());
        });

        // Apply Filter button
        $('#btn_filter').on('click', function() {
            const formData = {};
            $('#hbo-filter-form').serializeArray().forEach(field => {
                formData[field.name] = field.value;
            });
            localStorage.setItem(FILTER_STORAGE_KEY, JSON.stringify(formData));
            fetchHboCount(formData);
            fetchHboCountByDate(formData);
            fetchHboByCategory(formData);
            fetchHboByGroup(formData);
            fetchHboByType(formData);
            fetchHboBySubCategory(formData);
            fetchHboByWeek(formData);
            fetchHboByReporter(formData);
            fetchHboWeeklySummary(formData);
        });
    </script>

    <script>
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

        function fetchHboCount(filters = {}) {
            const $loading = $('#filterLoading');
            $loading.removeClass('hidden');

            return $.ajax({
                url: "{{ route('hbo.getcount') }}",
                method: 'GET',
                data: filters,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#totalCount').text(response.count);
                        $('#ongoingCount').text(response.ongoing);
                        $('#forVerificationCount').text(response.forVerification);
                        $('#closeCount').text(response.close);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $loading.addClass('hidden'); // hide overlay
                }
            });
        }

        function fetchHboCountByDate(filters = {}) {
            const $loading = $('#filterLoading');
            $loading.removeClass('hidden');
            $.ajax({
                url: "{{ route('hbo.getcountbyDate') }}",
                method: 'GET',
                data: filters,
                dataType: 'json',
                success: function(response) {
                    if (Array.isArray(response)) {
                        renderHboByDateChart(response);
                    } else {
                        console.warn('Unexpected response:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $loading.addClass('h    idden');
                }
            });
        }

        function fetchHboByCategory(filters = {}) {
            const $loading = $('#filterLoading');
            $loading.removeClass('hidden');

            $.ajax({
                url: "{{ route('hbo.getcountbyCategory') }}",
                method: 'GET',
                data: filters,
                dataType: 'json',
                success: function(response) {
                    if (Array.isArray(response)) {
                        renderHboByCategoryChart(response);
                    } else {
                        console.warn('Unexpected response:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $loading.addClass('hidden');
                }
            });
        }

        function fetchHboByGroup(filters = {}) {
            const $loading = $('#filterLoading');
            $loading.removeClass('hidden');

            $.ajax({
                url: "{{ route('hbo.getcountbyGroup') }}",
                method: 'GET',
                data: filters,
                dataType: 'json',
                success: function(response) {
                    if (Array.isArray(response)) {
                        renderHboByGroupChart(response);
                    } else {
                        console.warn('Unexpected response:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $loading.addClass('hidden');
                }
            });
        }

        function fetchHboByType(filters = {}) {
            const $loading = $('#filterLoading');
            $loading.removeClass('hidden');

            $.ajax({
                url: "{{ route('hbo.getcountbyType') }}",
                method: 'GET',
                data: filters,
                dataType: 'json',
                success: function(response) {
                    if (Array.isArray(response)) {
                        renderHboByTypeChart(response);
                    } else {
                        console.warn('Unexpected response:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $loading.addClass('hidden');
                }
            });
        }

        function fetchHboBySubCategory(filters = {}) {
            const $loading = $('#filterLoading');
            $loading.removeClass('hidden');

            $.ajax({
                url: "{{ route('hbo.getcountbySubCategory') }}",
                method: 'GET',
                data: filters,
                dataType: 'json',
                success: function(response) {
                    if (Array.isArray(response)) {
                        renderHboBySubCategoryChart(response);
                    } else {
                        console.warn('Unexpected response:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $loading.addClass('hidden');
                }
            });
        }

        function fetchHboByWeek(filters = {}) {
            const $loading = $('#filterLoading');
            $loading.removeClass('hidden');

            $.ajax({
                url: "{{ route('hbo.getcountbyWeek') }}",
                method: 'GET',
                data: filters,
                dataType: 'json',
                success: function(response) {
                    if (Array.isArray(response)) {
                        renderHboByWeekChart(response);
                    } else {
                        console.warn('Unexpected response:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $loading.addClass('hidden');
                }
            });
        }

        function fetchHboByReporter(filters = {}) {
            const $loading = $('#filterLoading');
            $loading.removeClass('hidden');

            $.ajax({
                url: "{{ route('hbo.getcountbyReporter') }}",
                method: 'GET',
                data: filters,
                dataType: 'json',
                success: function(response) {
                    if (response && response.top_reporters) {
                        const data = response.top_reporters;
                        let html = '';

                        data.forEach((item, index) => {
                            const crown = index === 0 ? ' 🜲' : '';
                            html += `<p class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-2">
                        ${index + 1}. ${item.reported_by} [ ${item.total} ] <span style="color:#efbf04;">${crown}</span>
                    </p>`;
                        });

                        // Optionally show total number of distinct reporters
                        if (response.total_reporters !== undefined) {
                            totalreporter =
                                `<p class="text-5xl font-bold text-gray-500 mb-1">${response.total_reporters}</p>`;
                        }

                        date_range =
                            `<p class="text-xs font-semibold text-gray-500 mb-1">${filters.date_from} to ${filters.date_to}</p>`

                        $("#ranking-date-range").html(date_range);
                        $("#hbo-by-reporter").html(html);
                        $("#hbo-by-reportertotal").html(totalreporter);
                    } else {
                        console.warn('Unexpected response:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $loading.addClass('hidden');
                }
            });
        }

        function fetchHboWeeklySummary(filters = {}) {
            const $loading = $('#filterLoading');
            $loading.removeClass('hidden');

            $.ajax({
                url: "{{ route('hbo.getWeeklyData') }}", // backend method
                method: 'GET',
                data: filters,
                dataType: 'json',
                success: function(response) {
                    if (Array.isArray(response)) {
                        // Render the chart
                        window.renderWeeklySummaryChart(response);

                        // Populate the table
                        const $tbody = $('#weekly-summary-tbody');
                        $tbody.empty();

                        response.forEach(week => {
                            const days = week.days.map(d =>
                                `<td class="px-2 py-1 border text-center">${d}</td>`).join('');

                            const row = `
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-1 border font-medium">
                                <div class="flex justify-between">
                                    <span>${week.week_label}</span>
                                    <span class="text-gray-500 text-xs">${week.range}</span>
                                </div>
                            </td>
                            ${days}
                            <td class="px-2 py-1 border font-semibold text-center">${week.total}</td>
                        </tr>
                    `;
                            $tbody.append(row);
                        });
                    } else {
                        console.warn('Unexpected response:', response);
                    }
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
