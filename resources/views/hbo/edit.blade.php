<x-layout>
    <x-card class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-800">View/Manage HBO</h2>
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
    <form id="formHazard_update" method="POST" action="{{ route('hbo.update', $hbo->id) }}"
        class="grid grid-cols-1 md:grid-cols-3 md:grid-rows-2 gap-6 transition-all duration-300">
        @csrf
        @method('PUT')
        {{-- HBO FORM --}}
        <x-card class="col-span-2 row-span-2 mb-2">
            <!-- HEADER -->
            <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-4">
                <h1 class="text-xl font-semibold text-gray-800">HBO No. : <span
                        class="font-bold">{{ $hbo->id }}</span></h1>
                <span
                    class="px-4 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 border border-gray-300">
                    {{ $hbo->status }}
                </span>
            </div>

            <!-- FORM -->
            <fieldset id="hazardForm" class="grid grid-cols-1 md:grid-cols-4 gap-y-4 gap-x-6" disabled>

                <!-- Business Unit -->
                @php
                    $superAdmin = Auth::user()->credentials == 'SUPER_ADMIN';
                @endphp
                <div class="relative col-span-2">
                    <x-select label="Business Unit" name="business_unit" size="lg" width="full" :value="$superAdmin ? $hbo->business_unit : Auth::user()->business_unit"
                        :readonly="!$superAdmin" :options="['' => 'Select Business Unit'] +
                            array_combine($data['Business_unit'], $data['Business_unit'])" required/>
                </div>

                <!-- Company -->
                <div class="relative col-span-2">
                    <x-select label="Group" name="company" size="lg" width="full" :options="['' => 'Select Group']" required/>
                </div>

                <!-- Type -->
                <div class="col-span-2">
                    <x-select label="Type" name="type" size="lg" width="full" :options="['' => 'Select Type'] + array_combine($data['Types'], $data['Types'])"
                        value="{{ $hbo->type }}" required/>
                </div>

                <!-- Category -->
                <div class="col-span-2">
                    <x-select label="Category" name="category" size="lg" width="full" :options="['' => 'Select Category'] + array_combine($data['Categories'], $data['Categories'])"
                        :value="$hbo->category" required/>
                </div>

                <!-- Sub Category -->
                <div class="col-span-2">
                    <x-select label="Sub Category" name="sub_category" size="lg" width="full"
                        :options="['' => 'Select Sub Category']" required/>
                </div>

                <!-- Dates -->
                <div>
                    <x-input label="Date Raised" size="lg" width="30" type="date" name="date_raised"
                        value="{{ request('date_to', now()->format('Y-m-d')) }}" value="{{ $hbo->date_raised }}"
                        required />
                </div>

                <div>
                    <x-input label="Due Date" size="lg" width="30" type="date" name="date_due"
                        value="{{ $hbo->date_due }}" required />
                </div>

                <!-- SWA -->
                <div class="col-span-2">
                    <x-select label="SWA" name="SWA" size="lg" width="full" :options="['' => 'Select SWA'] + array_combine($data['SWA'], $data['SWA'])"
                        value="{{ $hbo->SWA }}" required/>
                </div>

                <!-- SRO -->
                <div class="col-span-2">
                    <x-select label="SRO" name="SRO" size="lg" width="full" :options="['' => 'Select SRO'] + array_combine($data['SRO'], $data['SRO'])"
                        value="{{ $hbo->SRO }}" required/>
                </div>

                <!-- Reporter Info -->
                <div class="col-span-2">
                    <x-input label="Reported By" size="lg" width="full" name="reported_by"
                        value="{{ $hbo->reported_by }}" required/>
                </div>

                <div class="col-span-2">
                    <x-select label="Reported To" name="reported_to" size="lg" width="full" :options="['' => 'Select User'] + array_combine($data['Users'], $data['Users'])"
                        value="{!! $hbo->reported_to !!}" required/>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <x-textarea label="Hazard Description" name="hazard_description" value="{!! $hbo->hazard_description !!}" required/>
                </div>

                <div class="md:col-span-2">
                    <x-textarea label="Recommendation" name="recommendation" value="{!! $hbo->recommendation !!}" required/>
                </div>

                <div class="md:col-span-4 relative">
                    @php
                        $photos = $hbo->hbo_photo;

                        if (is_string($photos)) {
                            $decoded = json_decode($photos, true);
                            $photos = is_array($decoded) ? $decoded : $photos;
                        }

                        $photoValue = is_array($photos) ? implode(', ', $photos) : $photos;
                    @endphp

                    <!-- Input with label -->
                    <x-input label="Picture (Paste the link of the Photo, use comma as separator for multiple)"
                        size="lg" width="full" name="hbo_photo" id="hbo_photo_input"
                        value="{!! $photoValue ?? '' !!}" />
                </div>
            </fieldset>
            <!-- Buttons -->
            <div class="md:col-span-4
                        mt-6 flex flex-wrap gap-2">
                <!-- Edit Button -->
                <button type="button" id="editBtn" onclick="toggleEditInformation()"
                    class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition">
                    Edit Information
                </button>

                <button type="button" id="cancelBtn" onclick="toggleEditInformation()"
                    class="px-4 py-2 rounded bg-gray-300 text-gray-800 hover:bg-gray-400 transition hidden">
                    Cancel
                </button>

                <!-- Save Button -->
                <button type="submit" id="saveBtn"
                    class="px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700 transition hidden">
                    Save Changes
                </button>

                <!-- Delete Button -->
                @if (Auth::user()->credentials == 'SUPER_ADMIN')
                    <x-button size="lg" variant="error" id="deleteBtn" onclick="toggleModal('delete-modal')">
                        Delete
                    </x-button>
                @endif

                @if ($hbo->status === 'ONGOING')
                    <x-button size="lg" variant="info" id="takeActionBtn"
                        onclick="toggleModal('modal_action')">
                        Take Action
                    </x-button>
                @elseif ($hbo->status === 'FOR VERIFICATION' && Auth::user()->credentials != 'STAFF')
                    <x-button size="lg" variant="purple" id="takeVerifyBtn"
                        onclick="toggleModal('modal_verify')">
                        Verify
                    </x-button>
                @endif
            </div>
        </x-card>

        <x-card class="col-start-3 row-start-1 mb-2">

            <h2 class="text-lg font-semibold text-gray-800 mb-3">Action Taken</h2>
            <fieldset id="actionForm" class="" disabled>
                <div class="mb-2">
                    <x-input label="Action Date" name="action_date" type="date" :value="$hbo->action_date" />
                </div>
                <div class="mb-2">
                    <x-input label="Action By" name="action_by" :value="$hbo->action_by" />
                </div>
                <div class="mb-2">
                    <x-textarea label="Action Taken" name="action_remarks" value="{{ $hbo->action_remarks }}" />
                </div>
            </fieldset>
        </x-card>

        <x-card class="col-start-3 row-start-2 mb-2">

            <h2 class="text-lg font-semibold text-gray-800 mb-3">Verification</h2>
            <fieldset id="verifyForm" class="" disabled>
                <div class="mb-2">
                    <x-input label="Verified Date" type="date" name="verified_date" :value="$hbo->verified_date" />
                </div>
                <div class="mb-2">
                    <x-input label="Verified By" name="verified_by" :value="$hbo->verified_by" />
                </div>
                <div class="mb-2">
                    <x-textarea label="Verification Remarks" name="verified_remarks"
                        value="{{ $hbo->verified_remarks }}" />
                </div>
            </fieldset>
        </x-card>

    </form>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Confirm Delete</h2>
            <p class="mt-2 text-sm text-gray-600">Are you sure you want to delete this HBO record? This action cannot
                be undone.</p>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="$('#delete-modal').addClass('hidden');"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                    Cancel
                </button>

                <form method="POST" action="{{ route('hbo.destroy', $hbo->id) }}">
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

    <!-- Action Taken Modal -->
    <div id="modal_action"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">

        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                Action Taken
            </h2>

            <form method="POST" action="{{ route('hbo.takeAction', $hbo->id) }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <x-input name="action_date" type="date" :value="now()->toDateString()">
                            Action Date
                        </x-input>
                    </div>
                    <div>
                        <x-input name="action_by" :value="Auth::user()->name">
                            Action By
                        </x-input>
                    </div>
                    <div>
                        <x-textarea label="Action Taken" name="action_remarks" value="{{ $hbo->action_remarks }}" />
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-6">

                    <button type="button" id="cancelAction" onclick="toggleModal('modal_action')"
                        class="px-4 py-2 text-sm rounded border border-gray-300 hover:bg-gray-100">
                        Cancel
                    </button>

                    <button type="submit" class="px-4 py-2 text-sm rounded bg-blue-600 text-white hover:bg-blue-700">
                        Save Action
                    </button>

                </div>
            </form>
        </div>
    </div>

    <!-- Verify Action Modal -->
    <div id="modal_verify"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                Verify Action
            </h2>
            <form method="POST" action="{{ route('hbo.verification', $hbo->id) }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <x-input name="verified_date" type="date" :value="old('verified_date', $hbo->verified_date ?? now()->toDateString())">
                            Verified Date
                        </x-input>
                    </div>
                    <div>
                        <x-input name="verified_by" :value="Auth::user()->name">
                            Verified By
                        </x-input>
                    </div>
                    <div>
                        <x-textarea label="Verification Remarks" name="verified_remarks"
                            value="{{ $hbo->verified_remarks }}" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" id="verifyCancelBtn" onclick="$('#modal_verify').addClass('hidden');"
                        class="px-4 py-2 text-sm rounded border border-gray-300 hover:bg-gray-100">
                        Cancel
                    </button>

                    <button type="submit"
                        class="px-4 py-2 text-sm rounded bg-purple-600 text-white hover:bg-purple-700">
                        Submit Verification
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const DataCompany = "{{ $hbo->company ?? '' }}";
        const DataSubcategory = @json($hbo->sub_category ?? '');
        $(document).ready(function() {
            const selectedBU = $('#business_unit').val();
            if (selectedBU) {
                loadGroups(selectedBU, DataCompany);
            }

            const selectedCat = $('#category').val();
            console.log(selectedCat);
            if (selectedCat) {
                loadSubcategories(selectedCat, DataSubcategory);
            }
        });

        $('#business_unit').on('change', function() {
            const DataCompany = "{{ $hbo->company ?? '' }}";
            loadGroups($(this).val(), DataCompany);
        });

        $('#category').on('change', function() {
            loadSubcategories($(this).val(), DataSubcategory);
            console.log($(this).val());
        });
    </script>

    <script>
        function toggleModal(modalId) {
            $('#' + modalId).toggleClass('hidden');
        }

        function toggleEditInformation() {
            $('#editBtn').toggleClass('hidden');
            $('#saveBtn').toggleClass('hidden');
            $('#cancelBtn').toggleClass('hidden');
            $('#hazardForm').prop('disabled', !$('#hazardForm').prop('disabled'));
            $('#actionForm').prop('disabled', !$('#actionForm').prop('disabled'));
            $('#verifyForm').prop('disabled', !$('#verifyForm').prop('disabled'));
        }

        const $organization = @json($organization);

        function loadGroups(selectedBU, selectedCompany = null) {
            const companyNames = $organization
                .filter(org => org.business_unit === selectedBU)
                .map(org => org.company_name);

            const uniqueCompanyNames = [...new Set(companyNames)];

            const $companySelect = $('select[name="company"]');
            $companySelect.empty();
            $companySelect.append('<option value="">Select Group</option>');

            uniqueCompanyNames.forEach(name => {
                const isSelected = selectedCompany && selectedCompany === name ? 'selected' : '';
                $companySelect.append(`<option value="${name}" ${isSelected}>${name}</option>`);
            });
        }

        const $categories = @json($categoriesRaw);

        function loadSubcategories(selectedCategory, selectedSubCategory = null) {
            const categoryData = $categories[selectedCategory];

            const $subcategorySelect = $('#sub_category');
            $subcategorySelect.empty();
            $subcategorySelect.append('<option value="">Select Sub Category</option>');
            if (categoryData && categoryData.subcategories) {
                const subcategoryNames = Object.keys(categoryData.subcategories);
                subcategoryNames.forEach(name => {
                    const isSelected = selectedSubCategory && selectedSubCategory === name ? 'selected' : '';
                    $subcategorySelect.append(`<option value="${name}" ${isSelected}>${name}</option>`);
                });
            }
        }
    </script>
</x-layout>
