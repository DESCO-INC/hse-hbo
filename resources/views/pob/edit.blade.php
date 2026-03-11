<x-layout>
    <x-card class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-800">Manage POB</h2>

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
        <form id="formFields" class="grid grid-cols-1 md:grid-cols-4 gap-y-4 gap-x-6" method="POST"
            action="{{ route('pob.update', $pob->id) }}">
            @csrf
            @method('PUT')

            <!-- Business Unit -->
            <div class="relative col-span-3">
                @php
                    $superAdmin = Auth::user()->credentials == 'SUPER_ADMIN';
                @endphp
                <x-select label="Business Unit" name="business_unit" size="lg" width="full"
                    :value="$pob->business_unit" readonly="true" :options="['' => 'All Business Unit'] +
                        $business_unit->mapWithKeys(fn($bu) => [$bu => $bu])->toArray()" />
            </div>

            <!-- Date -->
            <div>
                <x-input label="Date" size="sm" size="lg" width="full" type="date"
                    name="date" :value="optional($pob->date)->format('Y-m-d')" readonly="true" />
            </div>

            <fieldset id="companyContainer" class="relative col-span-4 grid grid-cols-10 gap-4" disabled>
                <!-- Company inputs will be injected here dynamically -->
            </fieldset>

            <!-- Buttons -->
            <div class="md:col-span-4 mt-6 flex flex-wrap gap-2">
                <!-- Cancel Button -->
                <button type="button" id="btnCancel" onclick="toggleEditPob()"
                    class="hidden px-4 py-2 rounded bg-gray-600 text-white hover:bg-gray-700 transition">
                    Cancel
                </button>

                <!-- Save Button -->
                <button id="btnSave" type="submit"
                    class="hidden px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition">
                    Save
                </button>

                <!-- Edit Button -->
                <button type="button" id="btnEdit" onclick="toggleEditPob()"
                    class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition">
                    Edit POB
                </button>

                <!-- Delete Button -->
                <button type="button" id="btnDelete" onclick="toggleModal('delete-modal')"
                    class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition">
                    Delete POB
                </button>
            </div>
        </form>
    </x-card>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Confirm Delete</h2>
            <p class="mt-2 text-sm text-gray-600">Are you sure you want to delete this POB record? This action cannot be
                undone.</p>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="toggleModal('delete-modal')"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                    Cancel
                </button>

                <form method="POST" action="{{ route('pob.destroy', $pob->id) }}">
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
        const attendanceData = @json($pob->attendance_data ?? []);

        function loadGroups(selectedBU) {
            $('#companyContainer').empty();
            if (!selectedBU) return;

            const companyNames = organizationData
                .filter(org => org.business_unit === selectedBU)
                .map(org => org.company_name);

            const uniqueCompanyNames = [...new Set(companyNames)];

            if (!uniqueCompanyNames.length) {
                $('#companyContainer').html(
                    '<p class="text-gray-500 col-span-4">No companies found for this Business Unit.</p>'
                );
                return;
            }

            uniqueCompanyNames.forEach(function(name) {
                const value = attendanceData[name] ?? 0; // <-- fill with existing attendance if available

                const $div = $('<div>', {
                    class: 'flex flex-col text-center mb-2'
                });

                $div.html(`
            <label class="text-[10px] font-medium text-gray-600 mb-1">${name}</label>
            <input type="hidden" name="company[]" value="${name}">
            <input type="number" name="attendance[]" value="${value}" class="appearance-none border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center" placeholder="0" min="0" max="999" oninput="this.value=this.value.slice(0,3)">
        `);

                $('#companyContainer').append($div);
            });
        }

        function toggleModal(modalId) {
            $('#' + modalId).toggleClass('hidden');
        }

        function toggleEditPob() {
            $('#btnEdit').toggleClass('hidden');
            $('#btnDelete').toggleClass('hidden');
            $('#btnSave').toggleClass('hidden');
            $('#btnCancel').toggleClass('hidden');
            $('#companyContainer').prop('disabled', !$('#companyContainer').prop('disabled'));
        }
    </script>

</x-layout>
