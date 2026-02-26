<x-layout>
    <div
        class="flex justify-between items-center mb-5 bg-white rounded-2xl shadow-sm border border-gray-200 px-6 py-4 transition-all duration-300">
        <h2 class="text-lg font-medium text-gray-800">View / Manage HBO</h2>

        <div>
            <a href="javascript:void(0);" onclick="window.history.back();"
                class="bg-blue-500 text-white text-xs px-3 py-2 rounded hover:bg-blue-600">
                Back
            </a>

            <a href="{{ route('hbo.index') }}"
                class="bg-blue-500 text-white text-xs px-3 py-2 rounded hover:bg-blue-600 mx-1">
                Home
            </a>
        </div>
    </div>

    <div id="mainContainer">

        <form id="formHazard_update" method="POST" action="{{ route('hbo.update', $hbo->id) }}"
            class="grid grid-cols-1 md:grid-cols-3 md:grid-rows-2 gap-6 transition-all duration-300">
            @csrf
            @method('PUT')
            {{-- HBO FORM --}}
            <div
                class="col-span-2 row-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 p-6 transition-all duration-300">
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
                    <div class="relative col-span-2">
                        <x-select name="business_unit" :options="[]" :value="old('business_unit', $hbo->business_unit ?? '')"
                            addedClass="{{ Auth::user()->credentials != 'SUPER_ADMIN' ? 'bg-gray-300 pointer-events-none' : '' }}">
                            Business Unit
                        </x-select>
                    </div>

                    <!-- Company -->
                    <div class="relative col-span-2">
                        <x-select name="company" :options="['' => 'please select a Business unit first']">
                            Company
                        </x-select>
                    </div>

                    <!-- Type -->
                    <div class="relative col-span-2">
                        <x-select name="type" :options="['' => 'Select Type'] + array_combine($types, $types)" :value="old('type', $hbo->type ?? '')">
                            Type
                        </x-select>
                    </div>

                    <!-- Category -->
                    <div class="relative col-span-2">
                        <x-select name="category" id="category" :options="['' => 'Select Category'] +
                            collect($categories)->keys()->mapWithKeys(fn($k) => [$k => $k])->toArray()" :value="old('category', $hbo->category ?? '')">
                            Category
                        </x-select>
                    </div>

                    <!-- Sub Category -->
                    <div class="relative col-span-2">
                        <x-select name="sub_category" id="sub_category" :options="['' => 'Select a Category first']" :value="old('sub_category', $hbo->sub_category ?? '')">
                            Sub Category
                        </x-select>
                    </div>

                    <!-- Dates -->
                    <div>
                        <x-input name="date_raised" type="date" :value="$hbo->date_raised">
                            Date Raised
                        </x-input>
                    </div>

                    <div>
                        <x-input name="date_due" type="date" :value="$hbo->date_due">
                            Due Date
                        </x-input>
                    </div>

                    <!-- SWA -->
                    <div class="relative col-span-2">
                        <x-select name="SWA" id="SWA" :options="['' => 'Select SWA'] + array_combine($swa_sro['SWA'], $swa_sro['SWA'])" :value="old('SWA', $hbo->SWA ?? '')">
                            SWA
                        </x-select>
                    </div>

                    <!-- SRO -->
                    <div class="relative col-span-2">
                        <x-select name="SRO" id="SRO" :options="['' => 'Select SRO'] + array_combine($swa_sro['SRO'], $swa_sro['SRO'])" :value="old('SRO', $hbo->SRO ?? '')">
                            SRO
                        </x-select>
                    </div>

                    <!-- Reporter Info -->
                    <div class="col-span-2">
                        <x-input name="reported_by" :value="$hbo->reported_by">
                            Reported By
                        </x-input>
                    </div>

                    <div class="col-span-2">
                        <x-input name="reported_by" :value="$hbo->reported_to">
                            Reported To
                        </x-input>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <x-form-label for="hazard_description">Hazard Description</x-form-label>
                        <textarea id="hazard_description" name="hazard_description" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">{{ ucfirst(strtolower($hbo->hazard_description)) }}</textarea>
                        <x-form-error name="hazard_description" />
                    </div>

                    <div class="md:col-span-2">
                        <x-form-label for="recommendation">Recommendation</x-form-label>
                        <textarea id="recommendation" name="recommendation" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">{{ ucfirst(strtolower($hbo->recommendation)) }}</textarea>
                        <x-form-error name="recommendation" />
                    </div>

                    <div class="md:col-span-4">
                        <x-form-label for="hbo_photo">
                            Picture (Paste the link of the Photo use , comma symbol as seperator for multiple photo)
                        </x-form-label>
                        @php
                            $photos = $hbo->hbo_photo;

                            if (is_string($photos)) {
                                $decoded = json_decode($photos, true);
                                $photos = is_array($decoded) ? $decoded : $photos;
                            }

                            $photoValue = is_array($photos) ? implode(', ', $photos) : $photos;
                        @endphp

                        <x-form-input id="hbo_photo" name="hbo_photo" value="{{ $photoValue ?? '' }}" />
                        <x-form-error name='hbo_photo' />
                    </div>
                </fieldset>
                <!-- Buttons -->
                <div class="md:col-span-4 mt-6 flex flex-wrap gap-2">
                    <!-- Edit Button -->
                    <button type="button" id="editBtn"
                        class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition">
                        Edit Information
                    </button>

                    <!-- Save Button -->
                    <button type="submit" id="saveBtn"
                        class="px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700 transition hidden">
                        Save Changes
                    </button>

                    <!-- Delete Button -->
                    <button type="button" id="deleteBtn"
                        class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition {{ Auth::user()->credentials == 'user' ? 'hidden' : '' }}">
                        Delete
                    </button>

                    @if ($hbo->status === 'ONGOING')
                        <button type="button" id="takeActionBtn"
                            class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition  {{ Auth::user()->credentials == 'user' ? 'hidden' : '' }}">
                            Take Action
                        </button>
                    @elseif ($hbo->status === 'FOR VERIFICATION')
                        <button type="button" id="takeVerifyBtn"
                            class="px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700 transition  {{ Auth::user()->credentials == 'user' ? 'hidden' : '' }}">
                            Verify
                        </button>
                    @endif

                    <button type="button" id="cancelBtn"
                        class="px-4 py-2 rounded bg-gray-300 text-gray-800 hover:bg-gray-400 transition hidden">
                        Cancel
                    </button>
                </div>
            </div>

            <div id="actionCard"
                class="col-start-3 row-start-1 bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Action Taken</h2>
                <fieldset id="actionForm" class="" disabled>
                    <div>
                        <x-input name="action_date" type="date" :value="$hbo->action_date">
                            Action Date
                        </x-input>
                    </div>
                    <div>
                        <x-input name="action_by" :value="$hbo->action_by">
                            Action By
                        </x-input>
                    </div>
                    <div class="">
                        <x-form-label for="action_remarks">Action Taken</x-form-label>
                        <textarea id="action_remarks" name="action_remarks" class="w-full border border-gray-300 rounded px-3 py-2">{{ $hbo->action_remarks ?? '' }}</textarea>
                    </div>
                </fieldset>
            </div>

            <div id="verifyCard"
                class="col-start-3 row-start-2 bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Verification</h2>
                <fieldset id="verifyForm" class="" disabled>
                    <div>
                        <x-input name="verified_date" type="date" :value="$hbo->verified_date">
                            Verified Date
                        </x-input>
                    </div>
                    <div>
                        <x-input name="verified_by" :value="$hbo->verified_by">
                            Verified By
                        </x-input>
                    </div>
                    <div class="">
                        <x-form-label for="verified_remarks">Verification Remarks</x-form-label>
                        <textarea id="verified_remarks" name="verified_remarks" class="w-full border border-gray-300 rounded px-3 py-2">{{ $hbo->verified_remarks ?? '' }}</textarea>
                    </div>
                </fieldset>
            </div>
        </form>
    </div>

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
                        <x-form-label for="action_remarks">Action Taken</x-form-label>
                        <textarea id="action_remarks" name="action_remarks" class="w-full border border-gray-300 rounded px-3 py-2 text-sm"></textarea>
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-6">

                    <button type="button" id="cancelAction" onclick="$('#modal_action').addClass('hidden');"
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
                        <x-form-label for="verified_remarks">Verification Remarks</x-form-label>
                        <textarea id="verified_remarks" name="verified_remarks"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm"></textarea>
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
        const userCredentials = @json(old('credentials', Auth::user()->credentials ?? null));
        const categories = @json($categories); // Pass full categories/subcategories array
        const existingSubCategory = @json(old('sub_category', $hbo->sub_category ?? null));
        const existingactiontaken = @json(old('action_remarks', $hbo->action_remarks ?? null));
        const existingverification = @json(old('verified_remarks', $hbo->verified_remarks ?? null));
    </script>

    <script>
        $(document).ready(function() {
            const $form1 = $('#formHazard_update');
            const $fieldset = $('#hazardForm');
            const $fieldset2 = $('#actionForm');
            const $fieldset3 = $('#verifyForm');
            const $btn_edit = $('#editBtn');
            const $btn_save = $('#saveBtn');
            const $btn_delete = $('#deleteBtn');
            const $btn_cancel = $('#cancelBtn');

            // Edit button click: enable editing
            $btn_edit.on('click', function() {
                $fieldset.prop('disabled', false); // Enable all fields
                if (existingactiontaken) {
                    $fieldset2.prop('disabled', false);
                }
                if (existingverification) {
                    $fieldset3.prop('disabled', false);
                }
                $btn_save.show(); // Show Save button
                $btn_cancel.show(); // Show Cancel button
                $btn_edit.hide(); // Hide Edit button while editing
            });

            // Cancel button click: revert editing
            $btn_cancel.on('click', function() {
                $fieldset.prop('disabled', true); // Disable fields
                $btn_save.hide(); // Hide Save button
                $btn_cancel.hide(); // Hide Cancel button
                $btn_edit.show(); // Show Edit button again
            });

            // Cancel button click: revert editing
            $btn_delete.on('click', function() {
                $('#delete-modal').removeClass('hidden');
            });

            $('#takeActionBtn').on('click', function() {
                $('#modal_action').removeClass('hidden');

            });

            $('#takeVerifyBtn').on('click', function() {
                $('#modal_verify').removeClass('hidden');
            });
        });

        $(document).ready(function() {
            const $selectBU = $('#business_unit');
            const $selectCategory = $('#category');

            fetch_BusinessUnit();
            $selectBU.on('change', function() {
                const selectedBU = $(this).val();
                fetchCompanies(selectedBU); // no restore value here
            });

            $selectCategory.on('change', function() {
                fetchSubCategory(this.value);
            });

            // Trigger initially if editing existing data
            if ($('#category').val()) {
                fetchSubCategory($('#category').val());
            }
        });

        function fetch_BusinessUnit() {
            const existingBU = @json(old('business_unit', $hbo->business_unit ?? null));

            $.ajax({
                url: '{{ route('hbo.business_unit') }}',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    const $select = $('#business_unit');
                    $select.empty();

                    // Placeholder option
                    $select.append($('<option>', {
                        value: '',
                        text: 'All Business Unit'
                    }));

                    data.forEach(unit => {
                        $select.append($('<option>', {
                            value: unit,
                            text: unit,
                            selected: unit === existingBU // <-- set the existing value
                        }));
                    });

                    // Trigger change if you want to load the company list immediately
                    if (existingBU) {
                        fetchCompanies(existingBU);
                    }
                }
            });
        }

        function fetchCompanies(selectedBU) {
            if (!selectedBU) return;

            const existingCompany = @json(old('company', $hbo->company ?? null));

            $.ajax({
                url: '{{ route('hbo.companies', ['business_unit' => 'BU']) }}'
                    .replace('BU', encodeURIComponent(selectedBU)),
                method: 'GET',
                dataType: 'json',
                success: function(data) {

                    const $select = $('#company');
                    $select.empty();

                    // Placeholder option
                    $select.append($('<option>', {
                        value: '',
                        text: 'All Groups'
                    }));

                    data.forEach(unit =>
                        $select.append($('<option>', {
                            value: unit,
                            text: unit,
                            selected: unit === existingCompany // <-- select existing value
                        }))
                    );
                }
            });
        }

        function fetchSubCategory(category) {
            const $selectSubCategory = $('#sub_category');
            $selectSubCategory.empty();

            // Placeholder
            $selectSubCategory.append($('<option>', {
                value: '',
                text: 'Select Sub Category'
            }));

            if (!category || !categories[category]) return;

            const subs = categories[category].subcategories || [];

            Object.keys(subs).forEach(sub => {
                $selectSubCategory.append($('<option>', {
                    value: sub,
                    text: sub,
                    selected: sub === existingSubCategory
                }));
            });
        }
    </script>
</x-layout>
