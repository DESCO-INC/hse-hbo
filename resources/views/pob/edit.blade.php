<x-layout>
    <div id="mainContainer" class="grid grid-cols-1 md:grid-cols-3 gap-6 transition-all duration-300">

        <div id="hazardForm"
            class="md:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-200 p-6 transition-all duration-300">
            <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-4">
                <h1 class="text-xl font-semibold text-gray-800">Edit</h1>
            </div>

            <form id="formFields" class="grid grid-cols-1 md:grid-cols-4 gap-y-4 gap-x-6" method="POST"
                action="{{ route('pob.update', $pob->id) }}">
                @csrf
                @method('PUT')

                <!-- Business Unit -->
                <div class="relative col-span-2">
                    <x-form-label for="business_unit">Business Unit</x-form-label>
                    <x-form-select name="business_unit" id="business_unit" required readonly
                        class="{{ Auth::user()->credentials != 'SUPER_ADMIN' ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                        <option value="">Select Business Unit</option>
                        <!-- Options populated by JS -->
                    </x-form-select>
                    <x-form-error name="business_unit" />
                </div>

                <!-- Date -->
                <div>
                    <x-form-label for="date">Date</x-form-label>
                    <x-form-input type="date" id="date" name="date" value="{{ $pob->date->format('Y-m-d') }}"
                        required />
                    <x-form-error name="date" />
                </div>

                <!-- Attendance fields -->
                <div id="companyContainer" class="relative col-span-4 grid grid-flow-col auto-cols-fr gap-4">
                    <!-- Company inputs will be injected here dynamically -->
                </div>

                <!-- Buttons -->
                <div class="md:col-span-4 mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('pob.index') }}"
                        class="px-4 py-2 rounded bg-gray-300 text-gray-800 hover:bg-gray-400 transition">Cancel</a>
                    <button type="submit" id="saveBtn"
                        class="px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700 transition">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const businessUnitSelect = document.getElementById('business_unit');
            const companyContainer = document.getElementById('companyContainer');

            // Existing data from controller
            const currentBU = "{{ $pob->business_unit }}";
            const attendanceData = @json($pob->attendance_data ?? []);

            // Add placeholder option
            businessUnitSelect.innerHTML = '<option value="">Select Business Unit</option>';

            // Load business units
            fetch('{{ route('org.business_unit') }}')
                .then(res => res.json())
                .then(data => {
                    data.forEach(bu => {
                        const option = document.createElement('option');
                        option.value = bu;
                        option.textContent = bu;

                        // Pre-select current BU
                        if (bu === currentBU) option.selected = true;

                        @if (Auth::user()->credentials != 'SUPER_ADMIN')
                            if (bu === "{{ Auth::user()->business_unit }}") option.selected = true;
                        @endif

                        businessUnitSelect.appendChild(option);
                    });

                    // Load companies for selected BU
                    if (businessUnitSelect.value) loadCompanies(businessUnitSelect.value);
                })
                .catch(err => console.error('Error fetching business units:', err));

            // Function to load companies and pre-fill attendance
            const loadCompanies = (businessUnit) => {
                companyContainer.innerHTML = ''; // Clear previous

                if (!businessUnit) return;

                // Use named route for companies to avoid fetch errors
                const companiesUrl = "{{ route('org.business_unit.companies', ':bu') }}".replace(':bu',
                    encodeURIComponent(businessUnit));

                fetch(companiesUrl)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.length) {
                            companyContainer.innerHTML =
                                '<p class="text-gray-500 col-span-4">No companies found for this Business Unit.</p>';
                            return;
                        }

                        data.forEach(co => {
                            const companyName = co.company_name;
                            const div = document.createElement('div');
                            div.classList.add('flex', 'flex-col', 'text-center');

                            const attendanceValue = attendanceData[companyName] ?? 0;

                            div.innerHTML = `
                        <label class="text-[10px] font-medium text-gray-600 mb-1">${companyName}</label>
                        <input type="hidden" name="company[]" value="${companyName}">
                        <input type="number" name="attendance[]"
                            class="appearance-none border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center"
                            placeholder="0" min="0" max="999"
                            value="${attendanceValue}"
                            oninput="this.value=this.value.slice(0,3)">
                    `;
                            companyContainer.appendChild(div);
                        });
                    })
                    .catch(err => console.error('Error fetching companies:', err));
            };

            // Trigger on BU change
            businessUnitSelect.addEventListener('change', function() {
                loadCompanies(this.value);
            });
        });
    </script>

</x-layout>
