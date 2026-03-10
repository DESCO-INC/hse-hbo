<x-layout>
    <x-card class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-800">USER MAINTENANCE</h2>

            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button size="sm" onclick="toggleModal('addUser_modal')">
                    Add User
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
                            <th class="px-4 py-3 text-left text-sm font-medium w-32">Name</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-36">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-36">Business Unit</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-28">Credentials</th>
                            <th class="px-4 py-3 text-center text-sm font-medium w-24">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $user->id }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $user->business_unit }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $user->credentials }}</td>
                                <td class="px-4 py-2 text-center flex justify-center gap-1">
                                    <!-- View/Edit Button -->
                                    <x-button size="xs" variant="info" type="button" class="editUserBtn"
                                        data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}" data-business_unit="{{ $user->business_unit }}"
                                        data-credentials="{{ $user->credentials }}">
                                        Edit
                                    </x-button>

                                    <!-- Delete Button -->
                                    <x-button size="xs" variant="error"
                                        onclick="openDeleteModal({{ $user->id }})">
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
                {{ $users->links() }}
            </div>
        </div>
    </x-card>

    <!-- Add User Modal -->
    <div id="addUser_modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Add User</h2>
            <p class="mt-1 mb-3 text-sm text-gray-600">Fill out the form.</p>

            <form id="addForm" method="POST" action="{{ route('maintenance.store_user') }}">
                @csrf
                <div class="mt-3">
                    <x-input label="Name" size="lg" width="full" name="name" :value="old('name')" />
                </div>
                <div class="mt-3">
                    <x-input label="Email" type="email" size="lg" width="full" name="email"
                        :value="old('email')" />
                </div>
                <div class="mt-3">
                    <x-select label="Business Unit" name="business_unit" size="lg" width="full"
                        :options="['' => 'Select Business Unit'] +
                            $business_unit->mapWithKeys(fn($bu) => [$bu => $bu])->toArray()" :selected="old('business_unit')" />
                </div>
                <div class="mt-3">
                    <x-select label="Credentials" name="credentials" size="lg" width="full"
                        :options="[
                            '' => 'Select Credentials',
                            'SUPER_ADMIN' => 'SUPER ADMIN',
                            'ADMIN' => 'ADMIN',
                            'STAFF' => 'STAFF',
                        ]" :selected="old('credentials')" />
                </div>
                <div class="mt-3">
                    <x-input label="Password" type="password" size="lg" width="full" name="password" />
                </div>
                <div class="mt-3">
                    <x-input label="Confirm Password" type="password" size="lg" width="full"
                        name="password_confirmation" />
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                        onclick="document.getElementById('addUser_modal').classList.add('hidden')">
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
    <div id="updateUser_modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Edit User</h2>
            <p class="mt-1 mb-3 text-sm text-gray-600">Fill out the form.</p>

            <form id="updateForm" method="POST" action="{{ route('maintenance.update_user', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="mt-3">
                    <x-input label="Name" size="lg" width="full" name="name" :value="old('name', $user->name)" />
                </div>

                <div class="mt-3">
                    <x-input label="Email" type="email" size="lg" width="full" name="email"
                        :value="old('email', $user->email)" />
                </div>

                <div class="mt-3">
                    <x-select label="Business Unit" name="business_unit" size="lg" width="full"
                        :options="['' => 'Select Business Unit'] +
                            $business_unit->mapWithKeys(fn($bu) => [$bu => $bu])->toArray()" :selected="old('business_unit', $user->business_unit)" />
                </div>

                <div class="mt-3">
                    <x-select label="Credentials" name="credentials" size="lg" width="full"
                        :options="[
                            '' => 'Select Credentials',
                            'SUPER_ADMIN' => 'SUPER ADMIN',
                            'ADMIN' => 'ADMIN',
                            'STAFF' => 'STAFF',
                        ]" :selected="old('credentials', $user->credentials)" />
                </div>

                <div class="mt-3">
                    <x-input label="Password (leave blank to retain)" type="password" size="lg"
                        width="full" name="password" />
                </div>

                <div class="mt-3">
                    <x-input label="Confirm Password" type="password" size="lg" width="full"
                        name="password_confirmation" />
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                        onclick="$('#updateUser_modal').addClass('hidden')">
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
    <div id="deleteUser_modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Confirm Delete</h2>
            <p class="mt-2 text-sm text-gray-600">
                Are you sure you want to delete this User? This action cannot be undone.
            </p>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="toggleModal('deleteUser_modal')"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                    Cancel
                </button>

                <!-- Form: action will be set dynamically -->
                <form id="deleteUserForm" method="POST" action="">
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
                $('#addUser_modal').removeClass('hidden');
                $('#addUser_modal')[0].scrollIntoView({
                    behavior: 'smooth'
                });
            @endif

            // Open Update User modal on validation error
            @if ($errors->any() && session('modal') === 'update')
                $('#updateUser_modal').removeClass('hidden');
                $('#updateUser_modal')[0].scrollIntoView({
                    behavior: 'smooth'
                });
            @endif
        });

        $('.editUserBtn').on('click', function() {
            const userId = $(this).data('id');
            const name = $(this).data('name');
            const email = $(this).data('email');
            const businessUnit = $(this).data('business_unit');
            const credentials = $(this).data('credentials');

            const form = $('#updateForm');
            form.attr('action', "{{ route('maintenance.update_user', ':id') }}".replace(':id', userId));
            form.find('input[name="name"]').val(name);
            form.find('input[name="email"]').val(email);
            form.find('select[name="business_unit"]').val(businessUnit);
            form.find('select[name="credentials"]').val(credentials);

            form.find('input[name="password"]').val('');
            form.find('input[name="password_confirmation"]').val('');

            $('#updateUser_modal').removeClass('hidden');
            $('#updateUser_modal')[0].scrollIntoView({
                behavior: 'smooth'
            });
        });
    </script>

    <script>
        function toggleModal(modalId) {
            $('#' + modalId).toggleClass('hidden');
        }

        function openDeleteModal(userId) {
            const form = $('#deleteUserForm');
            form.attr('action', "{{ route('maintenance.destroy_user', ':id') }}".replace(':id', userId));
            toggleModal('deleteUser_modal');
        }
    </script>
</x-layout>
