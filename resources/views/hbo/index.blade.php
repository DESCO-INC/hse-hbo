<x-layout>
    <h1 class="text-xl font-semibold text-gray-800 mb-5">DASHBOARD </h1>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-5 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 px-6 py-3">
            <h1 class="text-lg font-semibold text-gray-800 whitespace-nowrap self-center">Filter</h1>

            <div class="flex flex-wrap items-end gap-4">

                <!-- Business Unit -->
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-500 mb-1">Business Unit</label>
                    <select name="business_unit" id="business_unit"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]"
                        {{ Auth::user()->credentials == 'superadmin' ? '' : 'disabled' }}>
                        <option value="" selected>All Business Units</option>
                    </select>

                    @if (Auth::user()->credentials != 'superadmin')
                        <input type="hidden" name="business_unit" value="{{ Auth::user()->business_unit }}">
                    @endif
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
                    <label class="text-xs font-medium text-gray-500 mb-1">Date From (dd/mm/yyyy)</label>
                    <input name="date_from" id="date_from" type="date"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]" />
                </div>

                <!-- Date To -->
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-500 mb-1">Date To (dd/mm/yyyy)</label>
                    <input name="date_to" id="date_to" type="date"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]" />
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



    <div class="h-[1800px] grid grid-cols-4 grid-rows-15 gap-2 mb-4">

        <div class="col-span-1 row-span-1">
            <div
                class="bg-white rounded-lg shadow-sm border p-5 hover:shadow-md transition-shadow duration-200 flex flex-col justify-center">
                <!-- Title -->
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                    Total HBO submitted
                </p>
                <!-- Count -->
                <p class="text-5xl font-extrabold text-gray-900 self-center" id="total-count">0</p>
            </div>
        </div>

        <div class="col-span-1 row-span-1">
            <div
                class="bg-white rounded-lg shadow-sm border p-5 hover:shadow-md transition-shadow duration-200 flex flex-col justify-center">
                <!-- Title -->
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                    Ongoing HBO Count
                </p>
                <!-- Count -->
                <p class="text-5xl font-extrabold text-gray-900 self-center" id="ongoing-count">0</p>
            </div>
        </div>

        <div class="col-span-1 row-span-1">
            <div
                class="bg-white rounded-lg shadow-sm border p-5 hover:shadow-md transition-shadow duration-200 flex flex-col justify-center">
                <!-- Title -->
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                    For Verification HBO Count
                </p>
                <!-- Count -->
                <p class="text-5xl font-extrabold text-gray-900 self-center" id="for-verification-count">0</p>
            </div>
        </div>

        <div class="col-span-1 row-span-1">
            <div
                class="bg-white rounded-lg shadow-sm border p-5 hover:shadow-md transition-shadow duration-200 flex flex-col justify-center">
                <!-- Title -->
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                    Closed HBO Count
                </p>
                <!-- Count -->
                <p class="text-5xl font-extrabold text-gray-900 self-center" id="closed-count">0</p>
            </div>
        </div>


        <div class="col-span-4 row-span-3">
            <div
                class="h-full col-span-1 bg-white rounded-lg shadow-md border p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex h-full items-center justify-between">
                    <div class="flex-1 h-full">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">HBO submitted by Date</p>
                        <div id="hbo-by-date-chart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-2 row-span-3 row-start-5">
            <div
                class="h-full col-span-1 bg-white rounded-lg shadow-md border p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex h-full items-center justify-between">
                    <div class="flex-1 h-full">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">HBO Reported by Category
                        </p>
                        <div id="hbo-by-category-chart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-span-3 row-start-5 col-span-2">
            <div
                class="bg-white rounded-lg shadow-md border p-6 hover:shadow-lg transition-shadow duration-200 flex flex-col h-full">
                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-4">HBO Submission by Group</p>
                <!-- Chart container -->
                <div class="flex-1 relative">
                    <div id="hbo-submitted-by-company-chart" class="absolute inset-0"></div>
                </div>
            </div>
        </div>

        <!-- Must be a direct child of your grid container -->
        <div class="col-span-2 row-span-3 row-start-8">
            <div
                class="bg-white rounded-lg shadow-md border p-6 hover:shadow-lg transition-shadow duration-200 flex flex-col h-full">
                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-4">HBO Submission by Type</p>
                <!-- Chart container -->
                <div class="flex-1 relative">
                    <div id="hbo-submitted-by-type-chart" class="absolute inset-0"></div>
                </div>
            </div>
        </div>

        <div class="col-span-2 row-span-3 row-start-8">
            <div
                class="h-full col-span-1 bg-white rounded-lg shadow-md border p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex h-full items-center justify-between">
                    <div class="flex-1 h-full">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">HBO Reported by
                            Sub-Category</p>
                        <div id="hbo-by-subcategory-chart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-3 row-span-3 row-start-11">
            <div
                class="h-full col-span-1 bg-white rounded-lg shadow-md border p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex h-full items-center justify-between">
                    <div class="flex-1 h-full">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">HBO submitted by Date
                            (WEEKLY)</p>
                        <div id="hbo-weekly-chart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Must be a direct child of your grid container -->
        <div class="row-span-2 row-start-11">
            <div
                class="bg-white rounded-lg shadow-md border p-6 hover:shadow-lg transition-shadow duration-200 flex flex-col h-full">
                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                    Top 5 Reporters for <br> <span id="ranking-date-range"></span>
                </p>
                <div class="flex-1 relative">
                    <div id="ranking_reportedby" class="absolute inset-0 p-2 overflow-auto"></div>
                </div>
            </div>
        </div>

        <!-- Must be a direct child of your grid container -->
        <div class="row-span-1">
            <div
                class="bg-white rounded-lg shadow-md border p-6 hover:shadow-lg transition-shadow duration-200 flex flex-col h-full">

                <p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-4">
                    Total Number of Reporters
                </p>

                <div class="flex-1 flex items-center justify-center">
                    <h1 class="text-3xl font-bold" id="reportees_count"></h1>
                </div>

            </div>
        </div>


        <div class="col-span-4 row-span-2 row-start-14">
            <div
                class="bg-white rounded-lg shadow-sm border p-5 hover:shadow-md transition-shadow duration-200 flex flex-col justify-center">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Weekly Summary</h2>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 text-xs text-center">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="py-1 px-2 border text-left">Week</th>
                                <th class="py-1 px-2 border">Mon</th>
                                <th class="py-1 px-2 border">Tue</th>
                                <th class="py-1 px-2 border">Wed</th>
                                <th class="py-1 px-2 border">Thu</th>
                                <th class="py-1 px-2 border">Fri</th>
                                <th class="py-1 px-2 border">Sat</th>
                                <th class="py-1 px-2 border">Sun</th>
                                <th class="py-1 px-2 border font-semibold bg-gray-50">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-gray-50">
                                <td id="twoHeader" class="py-1 px-2 border text-left font-medium">Two Weeks Ago</td>
                                <td class="py-1 px-2 border"><span id="twoMon">0</span></td>
                                <td class="py-1 px-2 border"><span id="twoTue">0</span></td>
                                <td class="py-1 px-2 border"><span id="twoWed">0</span></td>
                                <td class="py-1 px-2 border"><span id="twoThu">0</span></td>
                                <td class="py-1 px-2 border"><span id="twoFri">0</span></td>
                                <td class="py-1 px-2 border"><span id="twoSat">0</span></td>
                                <td class="py-1 px-2 border"><span id="twoSun">0</span></td>
                                <td class="py-1 px-2 border font-medium text-gray-800 bg-gray-50"><span
                                        id="twoTotal">0</span></td>
                            </tr>

                            <tr class="hover:bg-gray-50">
                                <td id="lastHeader" class="py-1 px-2 border text-left font-medium">Last Week</td>
                                <td class="py-1 px-2 border"><span id="lastMon">0</span></td>
                                <td class="py-1 px-2 border"><span id="lastTue">0</span></td>
                                <td class="py-1 px-2 border"><span id="lastWed">0</span></td>
                                <td class="py-1 px-2 border"><span id="lastThu">0</span></td>
                                <td class="py-1 px-2 border"><span id="lastFri">0</span></td>
                                <td class="py-1 px-2 border"><span id="lastSat">0</span></td>
                                <td class="py-1 px-2 border"><span id="lastSun">0</span></td>
                                <td class="py-1 px-2 border font-medium text-gray-800 bg-gray-50"><span
                                        id="lastTotal">0</span></td>
                            </tr>

                            <tr class="hover:bg-gray-50">
                                <td id="thisHeader" class="py-1 px-2 border text-left font-medium">This Week</td>
                                <td class="py-1 px-2 border"><span id="thisMon">0</span></td>
                                <td class="py-1 px-2 border"><span id="thisTue">0</span></td>
                                <td class="py-1 px-2 border"><span id="thisWed">0</span></td>
                                <td class="py-1 px-2 border"><span id="thisThu">0</span></td>
                                <td class="py-1 px-2 border"><span id="thisFri">0</span></td>
                                <td class="py-1 px-2 border"><span id="thisSat">0</span></td>
                                <td class="py-1 px-2 border"><span id="thisSun">0</span></td>
                                <td class="py-1 px-2 border font-medium text-gray-800 bg-gray-50"><span
                                        id="thisTotal">0</span></td>
                            </tr>

                            <tr class="font-semibold bg-gray-100">
                                <td class="py-1 px-2 border text-left">Grand Total</td>
                                <td class="py-1 px-2 border" colspan="7"></td>
                                <td class="py-1 px-2 border text-gray-900"><span id="grandTotal">0</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>


    @vite('resources/js/chart.js')
    @vite('resources/js/datatable.js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const businessUnitSelect = document.getElementById('business_unit');
            const companySelect = document.getElementById('company');
            const dateFromInput = document.getElementById('date_from');
            const dateToInput = document.getElementById('date_to');

            const today = new Date();
            const startOfYear = new Date(today.getFullYear(), 0, 1);

            const formatDate = (date) => {
                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, '0');
                const dd = String(date.getDate()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}`;
            };

            // ===============================
            // 1️⃣ Load filters from localStorage or set defaults
            // ===============================
            let filters = JSON.parse(localStorage.getItem('hboFilters') || '{}');
            if (!filters.date_from) filters.date_from = formatDate(startOfYear);
            if (!filters.date_to) filters.date_to = formatDate(today);
            if (!filters.business_unit) filters.business_unit = '';
            if (!filters.company) filters.company = '';

            // Set input values from filters
            businessUnitSelect.value = filters.business_unit;
            dateFromInput.value = filters.date_from;
            dateToInput.value = filters.date_to;

            // ===============================
            // 2️⃣ Load business units
            // ===============================
            fetch('{{ route('hbo.business_unit') }}')
                .then(res => res.json())
                .then(data => {
                    data.forEach(bu => {
                        const option = document.createElement('option');
                        option.value = bu;
                        option.textContent = bu;

                        // Preselect saved filter
                        if (bu === filters.business_unit) option.selected = true;

                        // Superadmin restriction
                        @if (Auth::user()->credentials != 'superadmin')
                            if (bu === "{{ Auth::user()->business_unit }}") option.selected = true;
                        @endif

                        businessUnitSelect.appendChild(option);
                    });

                    // Load companies if a business unit is already selected
                    if (filters.business_unit) {
                        loadCompanies(filters.business_unit, filters.company);
                    }
                });

            // Change business unit → load companies
            businessUnitSelect.addEventListener('change', function() {
                loadCompanies(this.value);
            });

            function loadCompanies(businessUnit, selectedCompany = '') {
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
            // 3️⃣ Get filters from inputs
            // ===============================
            function getFilters() {
                return {
                    business_unit: businessUnitSelect.value,
                    company: companySelect.value,
                    date_from: dateFromInput.value,
                    date_to: dateToInput.value
                };
            }

            // ===============================
            // 4️⃣ Apply Filter button
            // ===============================
            $("#filter-btn").on("click", function(e) {
                e.preventDefault();
                filters = getFilters();

                // Save filters to localStorage
                localStorage.setItem('hboFilters', JSON.stringify(filters));

                // Reload dashboard/chart/weekly summary
                loadDashboardCount(filters);
                loadChartData(filters);
                loadWeeklySummary(filters);
            });

            // ===============================
            // 5️⃣ Initial load based on filters
            // ===============================
            loadDashboardCount(filters);
            loadChartData(filters);
            loadWeeklySummary(filters);

            // ===============================
            // 6️⃣ Load Dashboard Counts
            // ===============================
            function loadDashboardCount(filters = {}, url = "{{ route('hbo.count') }}") {
                $.ajax({
                    url: url,
                    type: "GET",
                    data: filters,
                    success: function(response) {
                        $("#total-count").text(Number(response.total).toLocaleString());
                        $("#ongoing-count").text(Number(response.ongoing).toLocaleString());
                        $("#for-verification-count").text(Number(response.for_verification)
                            .toLocaleString());
                        $("#closed-count").text(Number(response.closed).toLocaleString());
                    },
                    error: function(xhr) {
                        $("#total-count,#ongoing-count,#for-verification-count,#closed-count").text(
                            'Err');
                        console.error("❌ AJAX Error:", xhr.responseText);
                    }
                });
            }

            // ===============================
            // 7️⃣ Load Chart Data
            // ===============================
            function loadChartData(filters = {}, url = "{{ route('hbo.chartData') }}") {
                $.ajax({
                    url: url,
                    type: "GET",
                    data: filters,
                    success: function(response) {
                        if (response.byDate && window.updateHboByDateChart) window.updateHboByDateChart(
                            response.byDate);
                        if (response.byCategory && window.updateHboByCategoryChart) window
                            .updateHboByCategoryChart(response.byCategory);
                        if (response.byCompany && window.updateHboByCompanyChart) window
                            .updateHboByCompanyChart(response.byCompany);
                        if (response.byType && window.updateHboByTypeChart) window.updateHboByTypeChart(
                            response.byType);
                        if (response.bySubcategory && window.updateHboBySubcategoryChart) window
                            .updateHboBySubcategoryChart(response.bySubcategory);
                        if (response.byWeekly && window.updateHboByWeekChart) window
                            .updateHboByWeekChart(response.byWeekly);

                        if (response.report_ranking) {
                            const data = response.report_ranking.ranking;
                            const dateRange = response.report_ranking.date_filter;
                            $("#ranking-date-range").text(
                                `[${new Date(dateRange.from).toLocaleDateString()} to ${new Date(dateRange.to).toLocaleDateString()}]`
                                );
                            let html = '';
                            data.forEach((item, index) => {
                                const crown = index === 0 ? ' 🜲' : '';
                                html += `<p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-2">
                            ${index+1}. ${item.reported_by} [ ${item.total} ] <span style="color:#efbf04;">${crown}</span>
                        </p>`;
                            });
                            $("#ranking_reportedby").html(html);
                        }

                        if (typeof response.reportees_count !== 'undefined') {
                            $("#reportees_count").text(`${response.reportees_count}`);
                        }
                    },
                    error: function(xhr) {
                        console.error("❌ AJAX Error:", xhr.responseText);
                    }
                });
            }

            // ===============================
            // 8️⃣ Load Weekly Summary
            // ===============================
            function loadWeeklySummary(filters = {}, url = "{{ route('hbo.filter') }}") {
                function formatDateDisplay(dateStr) {
                    if (!dateStr) return '';
                    const date = new Date(dateStr);
                    return isNaN(date) ? dateStr : date.toLocaleDateString('en-US', {
                        month: 'short',
                        day: '2-digit',
                        year: 'numeric'
                    });
                }

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: filters,
                    success: function(response) {
                        if (!response.weekly_summary || !response.weekly_summary.length) return;

                        let grandTotal = 0;
                        response.weekly_summary.forEach(item => {
                            let prefix = '';
                            switch (item.week_label) {
                                case 'two_weeks':
                                    prefix = 'two';
                                    break;
                                case 'last_week':
                                    prefix = 'last';
                                    break;
                                case 'this_week':
                                    prefix = 'this';
                                    break;
                                default:
                                    return;
                            }

                            const from = formatDateDisplay(item.date_from ?? '');
                            const to = formatDateDisplay(item.date_to ?? '');

                            $(`#${prefix}Header`).html(`<div class="flex justify-between">
                        <span>${formatWeekLabel(prefix)}</span>
                        <span>[${from} - ${to}]</span>
                    </div>`);

                            $(`#${prefix}Mon`).text(item.mon ?? 0);
                            $(`#${prefix}Tue`).text(item.tue ?? 0);
                            $(`#${prefix}Wed`).text(item.wed ?? 0);
                            $(`#${prefix}Thu`).text(item.thu ?? 0);
                            $(`#${prefix}Fri`).text(item.fri ?? 0);
                            $(`#${prefix}Sat`).text(item.sat ?? 0);
                            $(`#${prefix}Sun`).text(item.sun ?? 0);

                            const total = item.total ?? ((item.mon || 0) + (item.tue || 0) + (
                                item.wed || 0) + (item.thu || 0) + (item.fri || 0) + (
                                item.sat || 0) + (item.sun || 0));
                            $(`#${prefix}Total`).text(total);
                            grandTotal += total;
                        });

                        $('#grandTotal').text(grandTotal);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading weekly summary:", error);
                    }
                });
            }

            function formatWeekLabel(prefix) {
                switch (prefix) {
                    case 'two':
                        return 'Two Weeks Ago';
                    case 'last':
                        return 'Last Week';
                    case 'this':
                        return 'This Week';
                    default:
                        return '';
                }
            }
        });
    </script>
</x-layout>
