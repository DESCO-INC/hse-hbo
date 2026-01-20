<x-layout>
    <!-- MAIN GRID CONTAINER -->
    <div id="mainContainer" class="grid grid-cols-1 md:grid-cols-3 gap-6 transition-all duration-300">

        <!-- MAIN FORM CARD -->
        <div id="hazardForm"
            class="md:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-200 p-6 transition-all duration-300">

            <!-- HEADER -->
            <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-4">
                <h1 class="text-xl font-semibold text-gray-800">Create</h1>
            </div>

            <!-- FORM -->
            <form id="formFields" method="POST" action="{{ route('pob.store') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-y-4 gap-x-6">
                @csrf

                <!-- Business Unit -->
                <div class="relative col-span-3">
                    <x-form-label for="business_unit">Business Unit</x-form-label>

                    <x-form-select name="business_unit" id="business_unit" required
                        class="{{ Auth::user()->credentials != 'superadmin' ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                        @if (Auth::user()->credentials == 'superadmin')
                            <option value="" selected>Select Business Unit</option>
                            @foreach ($business_unit as $bu)
                                <option value="{{ $bu->business_unit }}">{{ $bu->business_unit }}</option>
                            @endforeach
                        @else
                            @foreach ($business_unit as $bu)
                                <option value="{{ $bu->business_unit }}"
                                    {{ Auth::user()->business_unit == $bu->business_unit ? 'selected' : '' }}>
                                    {{ $bu->business_unit }}
                                </option>
                            @endforeach
                        @endif
                    </x-form-select>

                    {{-- Overlay to lock the dropdown for normal users --}}
                    @if (Auth::user()->credentials != 'superadmin')
                        <div class="absolute inset-0 bg-transparent cursor-not-allowed"></div>
                        {{-- Hidden field so the locked value still submits --}}
                        <input type="hidden" name="business_unit" value="{{ Auth::user()->business_unit }}">
                    @endif

                    <x-form-error name="business_unit" />
                </div>

                <!-- Dates -->
                <div>
                    <x-form-label for="date">Date</x-form-label>
                    <x-form-input type="date" id="date" name="date" value="{{ $dateToday }}" required />
                    <x-form-error name='date' />
                </div>

                <div id="companyContainer" class="relative col-span-4 grid grid-cols-10 gap-4">
                    <!-- Company inputs will be injected here dynamically -->
                </div>


                <!-- Buttons -->
                <div class="md:col-span-4 mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('pob.list') }}"
                        class="px-4 py-2 rounded bg-gray-300 text-gray-800 hover:bg-gray-400 transition">
                        Cancel
                    </a>
                    <button type="submit" id="saveBtn"
                        class="px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700 transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const businessUnitSelect = document.getElementById('business_unit');
            const companyContainer = document.getElementById('companyContainer');

            const loadCompanies = (businessUnit) => {
                if (!businessUnit) {
                    companyContainer.innerHTML = '';
                    return;
                }

                // Use Laravel route helper with a placeholder
                const companiesUrl = "{{ route('org.business_unit.companies', ':bu') }}".replace(':bu',
                    encodeURIComponent(businessUnit));

                fetch(companiesUrl)
                    .then(res => res.json())
                    .then(data => {
                        companyContainer.innerHTML = '';

                        if (!data.length) {
                            companyContainer.innerHTML =
                                '<p class="text-gray-500 col-span-4">No companies found for this Business Unit.</p>';
                            return;
                        }

                        data.forEach(co => {
                            const div = document.createElement('div');
                            div.classList.add('flex', 'flex-col', 'text-center');
                            div.innerHTML = `
                        <label class="text-[10px] font-medium text-gray-600 mb-1">${co.company_name}</label>
                        <input type="hidden" name="company[]" value="${co.company_name}">
                        <input type="number" name="attendance[]" class="appearance-none border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center" placeholder="0" min="0" max="999" oninput="this.value=this.value.slice(0,3)">
                    `;
                            companyContainer.appendChild(div);
                        });
                    })
                    .catch(err => {
                        console.error('Error fetching companies:', err);
                        companyContainer.innerHTML =
                            '<p class="text-red-500 col-span-4">Failed to load companies.</p>';
                    });
            };

            businessUnitSelect.addEventListener('change', function() {
                loadCompanies(this.value);
            });

            if (businessUnitSelect.value) {
                loadCompanies(businessUnitSelect.value);
            }
        });
    </script>




</x-layout>
