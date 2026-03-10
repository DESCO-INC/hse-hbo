<x-layout>
    <x-card class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-800">Create POB</h2>

            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button size="sm" href="{{ route('pob.list') }}" variant="info">
                    Back
                </x-button>

                <x-button size="sm" href="{{ route('hbo.index') }}" variant="info">
                    Home
                </x-button>
            </div>
        </div>
    </x-card>

    <x-card class="mb-2">
        <!-- FORM -->
        <form id="formFields" method="POST" action="{{ route('pob.store') }}"
            class="grid grid-cols-1 md:grid-cols-4 gap-y-4 gap-x-6">
            @csrf

            <!-- Business Unit -->
            <div class="relative col-span-3">
                @php
                    $superAdmin = Auth::user()->credentials == 'SUPER_ADMIN';
                @endphp
                <x-select label="Business Unit" name="business_unit" size="lg" width="full"
                    :value="$superAdmin ? '' : Auth::user()->business_unit" :readonly="!$superAdmin" :options="['' => 'All Business Unit'] +
                        $business_unit->mapWithKeys(fn($bu) => [$bu => $bu])->toArray()" />
            </div>

            <!-- Dates -->
            <div>
                <x-input label="Date" size="sm" size="lg" width="full" type="date"
                    name="date" value="{{ request('date', now()->format('Y-m-d')) }}" required />
            </div>

            <div id="companyContainer" class="relative col-span-4 grid grid-cols-10 gap-4">
                <!-- Company inputs will be injected here dynamically -->
            </div>

            <!-- Buttons -->
            <div class="md:col-span-4 mt-6 flex flex-wrap gap-2">
                <button type="submit" id="saveBtn"
                    class="px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700 transition">
                    Save
                </button>
            </div>
        </form>
    </x-card>

    <script>
        $(document).ready(function() {
            if ($('#business_unit').val()) {
                loadGroups($('#business_unit').val());
            }
        });

        $('#business_unit').on('change', function() {
            loadGroups($(this).val());
        });
    </script>

    <script>
        const organizationData = @json($organization);

        function loadGroups(selectedBU) {
            $('#companyContainer').empty();
            if (!selectedBU) return;

            const companyNames = organizationData.filter(org => org.business_unit === selectedBU).map(org => org
                .company_name);

            const uniqueCompanyNames = [...new Set(companyNames)];

            if (!uniqueCompanyNames.length) {
                $('#companyContainer').html(
                    '<p class="text-gray-500 col-span-4">No companies found for this Business Unit.</p>'
                );
                return;
            }

            uniqueCompanyNames.forEach(function(name) {
                const $div = $('<div>', {
                    class: 'flex flex-col text-center mb-2'
                });

                $div.html(`
                    <label class="text-[10px] font-medium text-gray-600 mb-1">${name}</label>
                    <input type="hidden" name="company[]" value="${name}">
                    <input type="number" name="attendance[]" class="appearance-none border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center" placeholder="0" min="0" max="999" oninput="this.value=this.value.slice(0,3)">
                `);

                $('#companyContainer').append($div);
            });
        }
    </script>
</x-layout>
