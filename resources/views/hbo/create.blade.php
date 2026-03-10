<x-layout>
    <x-card class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-800">Create HBO</h2>
            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button size="sm" href="{{ route('hbo.list') }}" variant="info">
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
        <form id="formFields" method="POST" action="{{ route('hbo.store') }}"
            class="grid grid-cols-1 md:grid-cols-4 gap-y-4 gap-x-6 ">
            @csrf

            <!-- Business Unit -->
            @php
                $superAdmin = Auth::user()->credentials == 'SUPER_ADMIN';
            @endphp
            <div class="relative col-span-2">
                <x-select label="Business Unit" name="business_unit" size="lg" width="full"
                    :value="$superAdmin ? '' : Auth::user()->business_unit" :readonly="!$superAdmin" :options="['' => 'Select Business Unit'] +
                        array_combine($data['Business_unit'], $data['Business_unit'])" />
            </div>

            <!-- Company -->
            <div class="relative col-span-2">
                <x-select label="Group" name="company" size="lg" width="full" :options="['' => 'Select Group']" />
            </div>

            <!-- Type -->
            <div class="col-span-2">
                <x-select label="Type" name="type" size="lg" width="full" :options="['' => 'Select Type'] + array_combine($data['Types'], $data['Types'])" />
            </div>

            <!-- Category -->
            <div class="col-span-2">
                <x-select label="Category" name="category" size="lg" width="full" :options="['' => 'Select Category'] + array_combine($data['Categories'], $data['Categories'])" />
            </div>

            <!-- Sub Category -->
            <div class="col-span-2">
                <x-select label="Sub Category" name="sub_category" size="lg" width="full"
                    :options="['' => 'Select Sub Category']" />
            </div>

            <!-- Dates -->
            <div>
                <x-input label="Date Raised" size="lg" width="30" type="date" name="date_raised"
                    value="{{ request('date_to', now()->format('Y-m-d')) }}" required />
            </div>

            <div>
                <x-input label="Due Date" size="lg" width="30" type="date" name="date_due"
                    required />
            </div>

            <!-- SWA -->
            <div class="col-span-2">
                <x-select label="SWA" name="SWA" size="lg" width="full" :options="['' => 'Select SWA'] + array_combine($data['SWA'], $data['SWA'])" />
            </div>

            <!-- SRO -->
            <div class="col-span-2">
                <x-select label="SRO" name="SRO" size="lg" width="full" :options="['' => 'Select SRO'] + array_combine($data['SRO'], $data['SRO'])" />
            </div>

            <!-- Reporter Info -->
            <div class="col-span-2">
                <x-input label="Reported By" size="lg" width="full" name="reported_by"
                    value="{{ Auth::user()->name ?? '' }}" />
            </div>

            <div class="col-span-2">
                <x-select label="Reported To" name="reported_to" size="lg" width="full"
                    :options="['' => 'Select User'] + array_combine($data['Users'], $data['Users'])" />
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <x-textarea label="Hazard Description" name="hazard_description" />
            </div>

            <div class="md:col-span-2">
                <x-textarea label="Recommendation" name="recommendation" />
            </div>

            <div class="md:col-span-4">
                <x-input label="Picture (Paste the link of the Photo use , comma symbol as seperator for multiple photo)" size="lg" width="full" name="hbo_photo"/>
            </div>

            <!-- Buttons -->
            <div class="md:col-span-4 mt-3 flex flex-wrap gap-2">
                <x-button type="submit" size="lg ">Save</x-button>
            </div>
        </form>
    </x-card>

    <script>
        $(document).ready(function() {
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
        });

        $('#business_unit').on('change', function() {
            loadGroups($(this).val());
        });

        $('#category').on('change', function() {
            loadSubcategories($(this).val());
            console.log($(this).val());
        });
    </script>

    <script>
        const $organization = @json($organization);

        function loadGroups(selectedBU) {
            const companyNames = $organization
                .filter(org => org.business_unit === selectedBU)
                .map(org => org.company_name);

            const uniqueCompanyNames = [...new Set(companyNames)];

            const $companySelect = $('select[name="company"]');
            $companySelect.empty();
            $companySelect.append('<option value="">Select Group</option>');

            uniqueCompanyNames.forEach(name => {
                $companySelect.append(`<option value="${name}">${name}</option>`);
            });
        }

        const $categories = @json($categoriesRaw);

        function loadSubcategories(selectedCategory) {
            const categoryData = $categories[selectedCategory];

            const $subcategorySelect = $('#sub_category');
            $subcategorySelect.empty();
            $subcategorySelect.append('<option value="">Select Sub Category</option>');
            if (categoryData && categoryData.subcategories) {
                const subcategoryNames = Object.keys(categoryData.subcategories);
                subcategoryNames.forEach(name => {
                    $subcategorySelect.append(`<option value="${name}">${name}</option>`);
                });
            }
        }
    </script>
</x-layout>
