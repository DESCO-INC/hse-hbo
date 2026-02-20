<x-layout>
    <div
        class="flex justify-between items-center mb-5 bg-white rounded-2xl shadow-sm border border-gray-200 px-6 py-4 transition-all duration-300">
        <h2 class="text-lg font-medium text-gray-800">Create HBO</h2>

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

            <!-- FORM -->
            <form id="formFields" method="POST" action="{{ route('hbo.store') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-y-4 gap-x-6">
                @csrf

                <!-- Business Unit -->
                <div class="relative col-span-2">
                    <x-form-label for="business_unit">Business Unit</x-form-label>

                    <x-form-select name="business_unit" id="business_unit" required
                        class="{{ Auth::user()->credentials != 'SUPER_ADMIN' ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                        <option value="">Select Business Unit</option>
                    </x-form-select>

                    {{-- Overlay to lock the dropdown for normal users --}}
                    @if (Auth::user()->credentials != 'SUPER_ADMIN')
                        <div class="absolute inset-0 bg-transparent cursor-not-allowed"></div>
                        {{-- Hidden field so the locked value still submits --}}
                        <input type="hidden" name="business_unit" value="{{ Auth::user()->business_unit }}">
                    @endif

                    <x-form-error name="business_unit" />
                </div>

                <!-- Company -->
                <div class="col-span-2">
                    <x-form-label for="company">Group</x-form-label>
                    <x-form-select name="company" id="company" required>
                        <option value="">Select Company</option>
                    </x-form-select>
                    <x-form-error name="company" />
                </div>

                <!-- Type -->
                <div class="col-span-2">
                    <x-form-label for="type">Type</x-form-label>
                    <x-form-select name="type" id="type" required>
                        <option value="">Select Type</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </x-form-select>
                    <x-form-error name="type" />
                </div>

                <!-- Category -->
                <div class="col-span-2">
                    <x-form-label for="category">Category</x-form-label>
                    <x-form-select name="category" id="category" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $category => $subcategories)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </x-form-select>
                    <x-form-error name="category" />
                </div>

                <!-- Sub Category -->
                <div class="col-span-2">
                    <x-form-label for="sub_category">Sub Category</x-form-label>
                    <x-form-select name="sub_category" id="sub_category" required>
                        <option value="">Select Sub Category</option>
                    </x-form-select>
                    <x-form-error name="sub_category" />
                </div>

                <!-- Dates -->
                <div>
                    <x-form-label for="date_raised">Date Raised</x-form-label>
                    <x-form-input type="date" id="date_raised" name="date_raised" required />
                    <x-form-error name='date_raised' />
                </div>

                <div>
                    <x-form-label for="date_due">Due Date</x-form-label>
                    <x-form-input type="date" id="date_due" name="date_due" required />
                    <x-form-error name='date_due' />
                </div>

                <!-- SWA -->
                <div class="col-span-2">
                    <x-form-label for="SWA">SWA</x-form-label>
                    <x-form-select name="SWA" id="SWA" required>
                        <option value="">Select SWA</option>
                        @foreach ($swa_sro['SWA'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </x-form-select>
                    <x-form-error name="SWA" />
                </div>

                <!-- SRO -->
                <div class="col-span-2">
                    <x-form-label for="SRO">SRO</x-form-label>
                    <x-form-select name="SRO" id="SRO" required>
                        <option value="">Select SRO</option>
                        @foreach ($swa_sro['SRO'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </x-form-select>
                    <x-form-error name="SRO" />
                </div>


                <!-- Reporter Info -->
                <div class="col-span-2">
                    <x-form-label for="reported_by">Reported By</x-form-label>
                    <x-form-input id="reported_by" name="reported_by" value="{{ Auth::user()->name ?? '' }}"
                        required />
                    <x-form-error name='reported_by' />
                </div>

                <div class="col-span-2">
                    <x-form-label for="reported_to">Reported To</x-form-label>
                    <x-form-select name="reported_to" id="reported_to" required>
                        <option value="">Select User</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
                    </x-form-select>
                    <x-form-error name="reported_to" />
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <x-form-label for="hazard_description">Hazard Description</x-form-label>
                    <x-form-input id="hazard_description" name="hazard_description" required />
                    <x-form-error name='hazard_description' />
                </div>

                <div class="md:col-span-2">
                    <x-form-label for="recommendation">Recommendation</x-form-label>
                    <x-form-input id="recommendation" name="recommendation" required />
                    <x-form-error name='recommendation' />
                </div>

                <div class="md:col-span-4">
                    <x-form-label for="hbo_photo">
                        Picture (Paste the link of the Photo use , comma symbol as seperator for multiple photo)
                    </x-form-label>
                    <x-form-input id="hbo_photo" name="hbo_photo" required />
                    <x-form-error name='hbo_photo' />

                    <!-- Preview Container -->
                    <div id="hbo_photo_preview" class="mt-3 flex flex-wrap gap-3"></div>
                </div>

                <!-- Buttons -->
                <div class="md:col-span-4 mt-6 flex flex-wrap gap-2">
                    <button type="submit" id="saveBtn"
                        class="px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700 transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DYNAMIC CATEGORY HANDLER -->
    <script>
        const categories = @json($categories);
        const categorySelect = document.getElementById('category');
        const subCategorySelect = document.getElementById('sub_category');

        categorySelect.addEventListener('change', function() {
            const selectedCategory = this.value;
            subCategorySelect.innerHTML = '<option value="">Select Sub Category</option>';

            if (categories[selectedCategory] && categories[selectedCategory].subcategories) {
                const subcategories = categories[selectedCategory].subcategories;
                for (const sub in subcategories) {
                    if (subcategories.hasOwnProperty(sub)) {
                        const opt = document.createElement('option');
                        opt.value = sub;
                        opt.textContent = sub;
                        subCategorySelect.appendChild(opt);
                    }
                }
            }
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const businessUnitSelect = document.getElementById('business_unit');
            const companySelect = document.getElementById('company');

            // Add default placeholder
            businessUnitSelect.innerHTML = '<option value="">Select Business Unit</option>';
            companySelect.innerHTML = '<option value="">Select Company</option>';

            // Load business units
            fetch('{{ route('org.business_unit') }}')
                .then(res => res.json())
                .then(data => {
                    data.forEach(bu => {
                        const option = document.createElement('option');
                        option.value = bu;
                        option.textContent = bu;
                        businessUnitSelect.appendChild(option);
                    });
                })
                .catch(err => console.error('Error fetching business units:', err));

            // Load companies when a business unit is selected
            businessUnitSelect.addEventListener('change', function() {
                const businessUnit = this.value;

                companySelect.innerHTML = '<option value="">Select Company</option>'; // reset

                if (!businessUnit) return;

                const companiesUrl = "{{ route('org.business_unit.companies', ':bu') }}".replace(':bu',
                    encodeURIComponent(businessUnit));

                fetch(companiesUrl)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(company => {
                            const option = document.createElement('option');
                            option.value = company.company_name;
                            option.textContent = company.company_name;
                            companySelect.appendChild(option);
                        });
                    })
                    .catch(err => console.error('Error fetching companies:', err));
            });
        });
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

                    if (finalLink !== "") {

                        const img = document.createElement("img");
                        img.src = finalLink;
                        img.className = "w-32 h-32 object-cover rounded border shadow";

                        img.onerror = function() {
                            this.classList.add("opacity-40");
                        };

                        previewContainer.appendChild(img);
                    }
                });
            }

            input.addEventListener("input", function() {
                clearTimeout(timeout);
                timeout = setTimeout(renderImages, 400);
            });

        });
    </script>
</x-layout>
