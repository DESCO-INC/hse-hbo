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

    <!-- MAIN GRID CONTAINER -->
    <div id="mainContainer" class="grid grid-cols-1 md:grid-cols-3 gap-6 transition-all duration-300">
        <!-- MAIN FORM CARD -->
        <div id="hazardForm"
            class="md:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-200 p-6 transition-all duration-300">

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
            <form id="formFields" method="POST" action="{{ route('hbo.update', $hbo->id) }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-y-4 gap-x-6">
                @csrf
                @method('PUT')

                <!-- Business Unit -->
                <div class="relative col-span-2">
                    <x-form-label for="business_unit">Business Unit</x-form-label>

                    <x-form-select name="business_unit" id="business_unit" required
                        class="{{ Auth::user()->credentials != 'SUPER_ADMIN' ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}"
                        disabled>
                        <option value="">Select Business Unit</option>
                        <!-- Options populated by JS -->
                    </x-form-select>

                    <x-form-error name="business_unit" />
                </div>

                <!-- Company -->
                <div class="col-span-2">
                    <x-form-label for="company">Group</x-form-label>
                    <x-form-select name="company" id="company" disabled required>
                        <option value="">Select Company</option>
                        <!-- Options populated by JS -->
                    </x-form-select>
                    <x-form-error name="company" />
                </div>

                <!-- Type -->
                <div class="col-span-2">
                    <x-form-label for="type">Type</x-form-label>
                    <x-form-select name="type" id="type" disabled>
                        <option value="">Select Type</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}"
                                {{ old('type', $hbo->type ?? '') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </x-form-select>
                    <x-form-error name="type" />
                </div>

                <!-- Category -->
                <div class="col-span-2">
                    <x-form-label for="category">Category</x-form-label>
                    <x-form-select name="category" id="category" disabled>
                        <option value="">Select Category</option>
                        @foreach ($categories as $category => $data)
                            <option value="{{ $category }}"
                                {{ old('category', $hbo->category ?? '') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </x-form-select>
                    <x-form-error name="category" />
                </div>

                <!-- Sub Category -->
                <div class="col-span-2">
                    <x-form-label for="sub_category">Sub Category</x-form-label>
                    <x-form-select name="sub_category" id="sub_category" disabled>
                        <option value="">Select Sub Category</option>
                        @if (isset($hbo->category) && isset($categories[$hbo->category]['subcategories']))
                            @foreach ($categories[$hbo->category]['subcategories'] as $sub => $color)
                                <option value="{{ $sub }}"
                                    {{ old('sub_category', $hbo->sub_category ?? '') == $sub ? 'selected' : '' }}>
                                    {{ $sub }}
                                </option>
                            @endforeach
                        @endif
                    </x-form-select>
                    <x-form-error name="sub_category" />
                </div>

                <!-- Dates -->
                <div>
                    <x-form-label for="date_raised">Date Raised</x-form-label>
                    <x-form-input type="date" id="date_raised" name="date_raised" value="{{ $hbo->date_raised }}"
                        required disabled />
                    <x-form-error name='date_raised' />
                </div>

                <div>
                    <x-form-label for="date_due">Due Date</x-form-label>
                    <x-form-input type="date" id="date_due" name="date_due" value="{{ $hbo->date_due }}" required
                        disabled />
                    <x-form-error name='date_due' />
                </div>

                <!-- SWA -->
                <div class="col-span-2">
                    <x-form-label for="SWA">SWA</x-form-label>
                    <x-form-select name="SWA" id="SWA" disabled>
                        <option value="">Select SWA</option>
                        @foreach ($swa_sro['SWA'] as $item)
                            <option value="{{ $item }}"
                                {{ old('type', $hbo->SWA ?? '') == $item ? 'selected' : '' }}>
                                {{ $item }}
                            </option>
                        @endforeach
                    </x-form-select>
                    <x-form-error name="SWA" />
                </div>

                <!-- SRO -->
                <div class="col-span-2">
                    <x-form-label for="SRO">SRO</x-form-label>
                    <x-form-select name="SRO" id="SRO" disabled>
                        <option value="">Select SRO</option>
                        @foreach ($swa_sro['SRO'] as $item)
                            <option value="{{ $item }}"
                                {{ old('type', $hbo->SRO ?? '') == $item ? 'selected' : '' }}>
                                {{ $item }}
                            </option>
                        @endforeach
                    </x-form-select>
                    <x-form-error name="SRO" />
                </div>

                <!-- Reporter Info -->
                <div class="col-span-2">
                    <x-form-label for="reported_by">Reported By</x-form-label>
                    <x-form-input id="reported_by" name="reported_by" value="{{ $hbo->reported_by }}" disabled />
                    <x-form-error name='reported_by' />
                </div>

                <div class="col-span-2">
                    <x-form-label for="reported_to">Reported To</x-form-label>
                    <x-form-input id="reported_to" name="reported_to" value="{{ $hbo->reported_to }}" disabled />
                    <x-form-error name='reported_to' />
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <x-form-label for="hazard_description">Hazard Description</x-form-label>
                    <textarea id="hazard_description" name="hazard_description" rows="4" disabled
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">{{ ucfirst(strtolower($hbo->hazard_description)) }}</textarea>
                    <x-form-error name="hazard_description" />
                </div>

                <div class="md:col-span-2">
                    <x-form-label for="recommendation">Recommendation</x-form-label>
                    <textarea id="recommendation" name="recommendation" rows="4" disabled
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

                    <x-form-input id="hbo_photo" name="hbo_photo" value="{{ $photoValue ?? '' }}" required
                        disabled />
                    <x-form-error name='hbo_photo' />

                    <!-- Preview Container -->
                    <div id="hbo_photo_preview" class="mt-3 flex flex-wrap gap-3"></div>
                </div>

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
                        <button type="button" id="verifyBtn"
                            class="px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700 transition  {{ Auth::user()->credentials == 'user' ? 'hidden' : '' }}">
                            Verify
                        </button>
                    @endif

                    <button type="button" id="cancelBtn"
                        class="px-4 py-2 rounded bg-gray-300 text-gray-800 hover:bg-gray-400 transition hidden">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT SIDE CARDS -->
        <div class="flex flex-col gap-6 md:col-span-1 transition-all duration-300">
            <!-- Take Action Card -->
            <div id="actionCard"
                class="{{ $hbo->status === 'ONGOING' ? 'hidden' : '' }} bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Take Action</h2>
                <form method="POST" action="{{ route('hbo.takeAction', $hbo->id) }}"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <x-form-label for="action_by">Action By</x-form-label>
                        <x-form-input id="action_by" name="action_by"
                            value="{{ $hbo->action_by ?? Auth::user()->name }}" />
                        <x-form-error name='action_by' />
                    </div>
                    <div>
                        <x-form-label for="action_date">Action Date</x-form-label>
                        <x-form-input type="date" id="action_date" name="action_date"
                            value="{{ $hbo->action_date }}" />
                    </div>
                    <div class="md:col-span-2">
                        <x-form-label for="action_remarks">Action Taken</x-form-label>
                        <textarea id="action_remarks" name="action_remarks" class="w-full border border-gray-300 rounded px-3 py-2">{{ old('action_remarks', $hbo->action_remarks) }}</textarea>
                    </div>
                    @if (empty($hbo->action_remarks))
                        <div class="md:col-span-2 mt-3 flex justify-between">
                            <button type="button" id="takeActionCancelBtn"
                                class="px-4 py-2 rounded bg-gray-300 text-gray-800 hover:bg-gray-400 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                Submit Action
                            </button>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Verify Card -->
            <div id="verifyCard"
                class="{{ $hbo->status === 'CLOSE' ? '' : 'hidden' }} bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Verify Action</h2>
                <form method="POST" action="{{ route('hbo.verification', $hbo->id) }}"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <x-form-label for="verified_by">Verified By</x-form-label>
                        <x-form-input id="verified_by" name="verified_by"
                            value="{{ $hbo->verified_by ?? Auth::user()->name }}" />
                        <x-form-error name='verified_by' />
                    </div>
                    <div>
                        <x-form-label for="verified_date">Verified Date</x-form-label>
                        <x-form-input type="date" id="verified_date" name="verified_date"
                            value="{{ $hbo->verified_date }}" />
                    </div>
                    <div class="md:col-span-2">
                        <x-form-label for="verified_remarks">Verification Remarks</x-form-label>
                        <textarea id="verified_remarks" name="verified_remarks" class="w-full border border-gray-300 rounded px-3 py-2">{{ old('verified_remarks', $hbo->verified_remarks) }}</textarea>
                    </div>
                    @if (empty($hbo->verified_remarks))
                        <div class="md:col-span-2 mt-3 flex justify-between">
                            <button type="button" id="verifyCancelBtn"
                                class="px-4 py-2 rounded bg-gray-300 text-gray-800 hover:bg-gray-400 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                                Submit Verification
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Confirm Delete</h2>
            <p class="mt-2 text-sm text-gray-600">Are you sure you want to delete this HBO record? This action cannot
                be undone.</p>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                    onclick="document.getElementById('delete-modal').classList.add('hidden')">
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


    <!-- JAVASCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const businessUnitSelect = document.getElementById('business_unit');
            const companySelect = document.getElementById('company');

            const currentBU = "{{ $hbo->business_unit }}";
            const currentCompany = "{{ $hbo->company }}";

            // Generate the route URL in Blade with a placeholder
            const companiesRouteTemplate = "{{ route('org.business_unit.companies', ':bu') }}";

            // Load business units
            fetch('{{ route('org.business_unit') }}')
                .then(res => res.json())
                .then(data => {
                    businessUnitSelect.innerHTML = '';
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
                });

            businessUnitSelect.addEventListener('change', function() {
                loadCompanies(this.value);
            });

            function loadCompanies(businessUnit) {
                companySelect.innerHTML = '<option value="">Select Company</option>';
                if (!businessUnit) return;

                // ✅ Use the Blade-generated route with placeholder replaced
                const companiesUrl = companiesRouteTemplate.replace(':bu', encodeURIComponent(businessUnit));

                fetch(companiesUrl)
                    .then(res => res.json())
                    .then(data => {
                        companySelect.innerHTML = '<option value="">Select Company</option>';
                        if (!data.length) {
                            const noOption = document.createElement('option');
                            noOption.value = '';
                            noOption.textContent = 'No companies';
                            noOption.disabled = true;
                            companySelect.appendChild(noOption);
                            return;
                        }

                        data.forEach(company => {
                            const name = company.company_name; // object property
                            const option = document.createElement('option');
                            option.value = name;
                            option.textContent = name;

                            // Pre-select current company
                            if (name === currentCompany) option.selected = true;

                            companySelect.appendChild(option);
                        });
                    })
                    .catch(err => console.error('Error fetching companies:', err));
            }
        });
    </script>



    <script>
        const hazardForm = document.getElementById('hazardForm');
        const actionCard = document.getElementById('actionCard');
        const verifyCard = document.getElementById('verifyCard');
        const takeActionBtn = document.getElementById('takeActionBtn');
        const takeActionCancelBtn = document.getElementById('takeActionCancelBtn');
        const verifyBtn = document.getElementById('verifyBtn');
        const verifyCancelBtn = document.getElementById('verifyCancelBtn');
        const editBtn = document.getElementById('editBtn');
        const saveBtn = document.getElementById('saveBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const formFields = document.getElementById('formFields');
        const inputs = formFields.querySelectorAll('input, textarea, select');

        let originalValues = [];

        // Edit Mode
        editBtn.addEventListener('click', () => {
            originalValues = Array.from(inputs).map(i => i.value);

            inputs.forEach(i => {
                // If user is NOT SUPER_ADMIN and field is business_unit, keep it disabled
                if (i.id === 'business_unit' && "{{ Auth::user()->credentials }}" !== 'SUPER_ADMIN') {
                    i.disabled = true;
                } else {
                    i.disabled = false;
                }
            });

            editBtn.classList.add('hidden');
            saveBtn.classList.remove('hidden');
            cancelBtn.classList.remove('hidden');
        });


        cancelBtn.addEventListener('click', () => {
            inputs.forEach((i, idx) => {
                i.value = originalValues[idx];
                i.disabled = true;
            });
            editBtn.classList.remove('hidden');
            saveBtn.classList.add('hidden');
            cancelBtn.classList.add('hidden');
        });

        // Category -> Subcategory update
        const categorySelect = document.getElementById('category');
        const subCategorySelect = document.getElementById('sub_category');
        const allCategories = @json($categories);

        categorySelect.addEventListener('change', function() {
            const selected = this.value;
            subCategorySelect.innerHTML = '<option value="">Select Sub Category</option>';

            if (allCategories[selected] && allCategories[selected].subcategories) {
                const subcategories = allCategories[selected].subcategories;
                for (const sub in subcategories) {
                    if (subcategories.hasOwnProperty(sub)) {
                        const option = document.createElement('option');
                        option.value = sub;
                        option.textContent = sub;
                        subCategorySelect.appendChild(option);
                    }
                }
            }
        });

        // Layout toggle
        function updateLayout() {
            const anyVisible = !actionCard.classList.contains('hidden') || !verifyCard.classList.contains('hidden');
            hazardForm.classList.toggle('md:col-span-2', anyVisible);
            hazardForm.classList.toggle('md:col-span-3', !anyVisible);
        }

        if (takeActionBtn) takeActionBtn.addEventListener('click', () => {
            actionCard.classList.toggle('hidden');
            updateLayout();
        });

        if (takeActionCancelBtn) takeActionCancelBtn.addEventListener('click', () => {
            actionCard.classList.add('hidden');
            updateLayout();
        });

        if (verifyBtn) verifyBtn.addEventListener('click', () => {
            verifyCard.classList.toggle('hidden');
            updateLayout();
        });

        if (verifyCancelBtn) verifyCancelBtn.addEventListener('click', () => {
            verifyCard.classList.add('hidden');
            updateLayout();
        });

        updateLayout();
    </script>

    <script>
        const deleteBtn = document.getElementById('deleteBtn');
        const deleteModal = document.getElementById('delete-modal');

        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                deleteModal.classList.remove('hidden');
            });
        }
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const input = document.getElementById("hbo_photo");
            const previewContainer = document.getElementById("hbo_photo_preview");

            let timeout = null;

            function normalizeImgur(link) {
                link = link.trim();

                // If it's imgur and no extension, add .jpg automatically
                if (link.includes("i.imgur.com") && !link.match(/\.(jpg|jpeg|png|webp|gif)$/i)) {
                    return link + ".jpg";
                }

                return link;
            }

            function renderImages() {
                previewContainer.innerHTML = "";

                if (!input.value.trim()) return;

                const links = input.value.split(",");

                links.forEach(link => {
                    let finalLink = normalizeImgur(link);

                    if (!finalLink) return;

                    const wrapper = document.createElement("div");
                    wrapper.className = "flex flex-col items-center";

                    const img = document.createElement("img");
                    img.src = finalLink;
                    img.className = "w-32 h-32 object-cover rounded border shadow";

                    const errorText = document.createElement("span");
                    errorText.className = "text-xs text-red-500 mt-1 hidden";
                    errorText.textContent = "Invalid image link";

                    img.onerror = function() {
                        img.remove();
                        errorText.classList.remove("hidden");
                    };

                    wrapper.appendChild(img);
                    wrapper.appendChild(errorText);
                    previewContainer.appendChild(wrapper);
                });
            }

            input.addEventListener("input", function() {
                clearTimeout(timeout);
                timeout = setTimeout(renderImages, 400);
            });

            // 🔥 IMPORTANT: render immediately on page load (for edit mode)
            if (input.value.trim() !== "") {
                renderImages();
            }

        });
    </script>


</x-layout>
