<x-layout>
    <x-card class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-800">BUSINESS UNIT AND GROUP MAINTENANCE</h2>

            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button size="sm" onclick="toggleModal('addItem_modal')">
                    Add Item
                </x-button>

                <x-button size="sm" href="{{ route('hbo.index') }}" variant="info">
                    Home
                </x-button>
            </div>
        </div>
    </x-card>

    <x-card class="mb-2">
        <div class="overflow-x-auto">
            <form method="GET" class="mb-4 flex items-center gap-2">
                <input name="search" id="search" type="text"
                    class="border border-gray-300 rounded-md px-3 py-1 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]"
                    placeholder="Search Here" value="{{ request('search') }}" />

                <button class="bg-green-500 text-white text-xs px-2 py-1 rounded hover:bg-green-600" type="submit">
                    Search
                </button>
            </form>
            <div class="overflow-x-auto border border-gray-200 rounded">
                <table class="min-w-full divide-y divide-gray-200 text-sm table-auto">
                    <thead class="bg-green-500 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium w-10">ID</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-32">Business Unit</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-36">Group</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-36">Date Added</th>
                            <th class="px-4 py-3 text-center text-sm font-medium w-24">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($orgs as $org)
                            <tr>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $org->id }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $org->business_unit }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $org->company_name }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $org->created_at }}</td>
                                <td class="px-4 py-2 text-center flex justify-center gap-1">
                                    <!-- View/Edit Button -->
                                    <x-button size="xs" variant="info" type="button" class="editItemBtn"
                                        data-id="{{ $org->id }}" data-business_unit="{{ $org->business_unit }}"
                                        data-company_name="{{ $org->company_name }}">
                                        Edit
                                    </x-button>

                                    <!-- Delete Button -->
                                    <x-button size="xs" variant="error"
                                        onclick="openDeleteModal({{ $org->id }})">
                                        Delete
                                    </x-button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-2 text-center text-gray-500">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $orgs->links() }}
            </div>
        </div>
    </x-card>

    <!-- Add User Modal -->
    <div id="addItem_modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Add Item</h2>
            <p class="mt-1 mb-3 text-sm text-gray-600">Fill out the form.</p>

            <form id="addForm" method="POST" action="{{ route('maintenance.store_org') }}">
                @csrf
                <div class="mt-3">
                    <x-input label="Business Unit" size="lg" width="full" name="business_unit" :value="old('business_unit')" />
                </div>
                <div class="mt-3">
                    <x-input label="Group" size="lg" width="full" name="company_name" :value="old('company_name')" />
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                        onclick="document.getElementById('addItem_modal').classList.add('hidden')">
                        Cancel
                    </button>

                    <button type="submit" id="uploadBtn"
                        class="relative px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center justify-center">
                        <svg id="spinner" class="animate-spin h-5 w-5 mr-2 text-white hidden"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                            </path>
                        </svg>
                        <span id="save_btn">Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update User Modal -->
    <div id="updateItem_modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Edit User</h2>
            <p class="mt-1 mb-3 text-sm text-gray-600">Fill out the form.</p>

            <form id="updateForm" method="POST" action="{{ route('maintenance.update_org', $org->id) }}">
                @csrf
                @method('PUT')

                <div class="mt-3">
                    <x-input label="Business Unit" size="lg" width="full" name="business_unit" :value="old('business_unit', $org->business_unit)" />
                </div>

                <div class="mt-3">
                    <x-input label="Group" size="lg" width="full" name="company_name" :value="old('company_name', $org->company_name)" />
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                        onclick="$('#updateItem_modal').addClass('hidden')">
                        Cancel
                    </button>

                    <button type="submit" id="updateBtn"
                        class="relative px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center justify-center">
                        <svg id="spinner" class="animate-spin h-5 w-5 mr-2 text-white hidden"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                            </path>
                        </svg>
                        <span id="update_btn">Update</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteItem_modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Confirm Delete</h2>
            <p class="mt-2 text-sm text-gray-600">
                Are you sure you want to delete this Item? This action cannot be undone.
            </p>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="toggleModal('deleteItem_modal')"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                    Cancel
                </button>

                <!-- Form: action will be set dynamically -->
                <form id="deleteItemForm" method="POST" action="">
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
            // Open Add User modal on validation error
            @if ($errors->any() && session('modal') === 'add')
                $('#addItem_modal').removeClass('hidden');
                $('#addItem_modal')[0].scrollIntoView({
                    behavior: 'smooth'
                });
            @endif

            // Open Update User modal on validation error
            @if ($errors->any() && session('modal') === 'update')
                $('#updateItem_modal').removeClass('hidden');
                $('#updateItem_modal')[0].scrollIntoView({
                    behavior: 'smooth'
                });
            @endif
        });

        $('.editItemBtn').on('click', function() {
            const itemId = $(this).data('id');
            const business_unit = $(this).data('business_unit');
            const company_name = $(this).data('company_name');

            const form = $('#updateForm');
            form.attr('action', "{{ route('maintenance.update_org', ':id') }}".replace(':id', itemId));
            form.find('input[name="business_unit"]').val(business_unit);
            form.find('input[name="company_name"]').val(company_name);

            $('#updateItem_modal').removeClass('hidden');
            $('#updateItem_modal')[0].scrollIntoView({
                behavior: 'smooth'
            });
        });
    </script>

    <script>
        function toggleModal(modalId) {
            $('#' + modalId).toggleClass('hidden');
        }

        function openDeleteModal(orgId) {
            const form = $('#deleteItemForm');
            form.attr('action', "{{ route('maintenance.destroy_org', ':id') }}".replace(':id', orgId));
            toggleModal('deleteItem_modal');
        }
    </script>
</x-layout>
