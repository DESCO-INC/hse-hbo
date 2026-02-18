<x-layout>
    <h1 class="text-xl font-semibold text-gray-800 mb-5">MAINTENANCE</h1>

    {{-- ====================== USER LIST ====================== --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center">
            <h1 class="text-xl font-semibold text-gray-800">User List</h1>
            <div class="flex-1"></div>
            <button id="openAddUser"
                class="inline-block px-4 py-2 bg-green-500 text-white rounded-md shadow hover:bg-green-600 transition font-medium ml-2">
                Add User
            </button>

        </div>

        <div class="px-6 py-5 overflow-x-auto">
            <div class="overflow-x-auto bg-white shadow rounded-lg border border-gray-200">
                <table class="w-full min-w-max border-collapse mb-5">
                    <thead class="bg-green-600">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">#
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">
                                Name</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">
                                Email</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">
                                Credential</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-white uppercase tracking-wide">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->credentials ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if (Auth::user()->id != $user->id)
                                        <button type="button" data-id="{{ $user->id }}" data-type="user"
                                            class="deleteBtn px-3 py-1.5 text-xs bg-red-500 text-white rounded hover:bg-red-600 transition">
                                            Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- ====================== ORG LIST ====================== --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center">
            <h1 class="text-xl font-semibold text-gray-800">Business Unit and Group List</h1>
            <div class="flex-1"></div>
            <button id="openAddOrg"
                class="inline-block px-4 py-2 bg-green-500 text-white rounded-md shadow hover:bg-green-600 transition font-medium ml-2">
                Add Items
            </button>
        </div>

        <div class="px-6 py-5 overflow-x-auto">
            <div class="overflow-x-auto bg-white shadow rounded-lg border border-gray-200">
                <table class="w-full min-w-max border-collapse mb-5">
                    <thead class="bg-green-600">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">#
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">
                                Business Unit</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">
                                Group</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">
                                Date Added</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-white uppercase tracking-wide">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($orgs as $org)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $org->id }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $org->business_unit }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $org->company_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $org->created_at }}</td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" data-id="{{ $org->id }}" data-type="org"
                                        class="deleteBtn px-3 py-1.5 text-xs bg-red-500 text-white rounded hover:bg-red-600 transition">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $orgs->links() }}
            </div>
        </div>
    </div>

    {{-- ====================== ADD USER MODAL ====================== --}}
    <div id="add-user-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Add User</h2>

            <form id="addUserForm" method="POST" action="{{ route('maintenance.store_user') }}">
                @csrf

                <div class="mb-2">
                    <x-form-label>Name</x-form-label>
                    <x-form-input id="name" name="name" placeholder="Jane Smith" required />
                    @error('name')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-2">
                    <x-form-label>Email</x-form-label>
                    <x-form-input id="email" name="email" placeholder="JaneSmith@gmail.com" required />
                    @error('email')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-2">
                    <x-form-label>Business Unit</x-form-label>
                    <x-form-select id="business_unit" name="business_unit" required>
                        <option value="">Select Business Unit</option>
                        @foreach ($business_unit as $bu)
                            <option value="{{ $bu }}">{{ $bu }}</option>
                        @endforeach
                    </x-form-select>
                    @error('business_unit')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-2">
                    <x-form-label>Credentials</x-form-label>
                    <x-form-select id="credentials" name="credentials" required>
                        <option value="">Select Credentials</option>
                        <option value="STAFF">Staff</option>
                        <option value="ADMIN">Admin</option>
                        <option value="SUPER_ADMIN">Super Admin</option>
                    </x-form-select>
                    @error('credentials')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-2">
                    <x-form-label>Password</x-form-label>
                    <x-form-input type="password" id="password" name="password" required />
                    @error('password')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-form-label>Confirm Password</x-form-label>
                    <x-form-input type="password" id="password_confirmation" name="password_confirmation" required />
                    @error('password_confirmation')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelAddUser"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">Add
                        User</button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.getElementById('add-user-modal')?.classList.remove('hidden');
        </script>
    @endif


    {{-- DELETE CONFIRM MODAL --}}
    <div id="delete-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Confirm Delete</h2>
            <p class="mt-2 text-sm text-gray-600">
                Are you sure you want to delete this? This action cannot be undone.
            </p>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" id="cancelDelete"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">Cancel</button>
                <form id="deleteForm" method="POST">
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

    {{-- ====================== ADD ORG MODAL ====================== --}}
    <div id="add-org-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Add Business Unit / Group</h2>

            <form id="addOrgForm" method="POST" action="{{ route('maintenance.store_org') }}">
                @csrf

                <div class="mb-2">
                    <x-form-label>Business Unit</x-form-label>
                    <x-form-input id="org_business_unit" name="org_business_unit" placeholder="Enter Business Unit"
                        required />
                    <div class="text-red-500 text-sm mt-1" id="error-org_business_unit"></div>
                </div>

                <div class="mb-2">
                    <x-form-label>Group / Company Name</x-form-label>
                    <x-form-input id="org_company_name" name="org_company_name" placeholder="Enter Group Name"
                        required />
                    <div class="text-red-500 text-sm mt-1" id="error-org_company_name"></div>
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" id="cancelAddOrg"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">Add
                        Org</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addUserModal = document.getElementById('add-user-modal');
            const openAddUserBtn = document.getElementById('openAddUser');
            const cancelAddUserBtn = document.getElementById('cancelAddUser');
            const addUserForm = document.getElementById('addUserForm');

            // Toggle modal with button (open/close)
            openAddUserBtn.addEventListener('click', () => {
                addUserModal.classList.toggle('hidden'); // <- toggle instead of remove
            });

            // Close modal and reset form
            cancelAddUserBtn.addEventListener('click', () => {
                addUserModal.classList.add('hidden');
                addUserForm.reset();
                clearErrors();
            });

            function clearErrors() {
                ['name', 'email', 'business_unit', 'credentials', 'password', 'password_confirmation']
                .forEach(field => {
                    const errorEl = document.getElementById('error-' + field);
                    if (errorEl) errorEl.textContent = '';
                });
            }
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const deleteModal = document.getElementById('delete-modal');
        const cancelDeleteBtn = document.getElementById('cancelDelete');
        const deleteForm = document.getElementById('deleteForm');

        // Base routes generated by Laravel
        const userDeleteRoute = "{{ route('maintenance.destroy_user', ':id') }}";
        const orgDeleteRoute = "{{ route('maintenance.destroy_org', ':id') }}";

        document.querySelectorAll('.deleteBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const type = btn.dataset.type;

                if (type === 'user') {
                    deleteForm.action = userDeleteRoute.replace(':id', id);
                } else if (type === 'org') {
                    deleteForm.action = orgDeleteRoute.replace(':id', id);
                }

                deleteModal.classList.remove('hidden');
            });
        });

        cancelDeleteBtn.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });
    });
</script>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addOrgModal = document.getElementById('add-org-modal');
            const openAddOrgBtn = document.getElementById('openAddOrg');
            const cancelAddOrgBtn = document.getElementById('cancelAddOrg');
            const addOrgForm = document.getElementById('addOrgForm');

            // Open Add Org modal
            openAddOrgBtn.addEventListener('click', () => {
                addOrgModal.classList.remove('hidden');
            });

            // Close Add Org modal
            cancelAddOrgBtn.addEventListener('click', () => {
                addOrgModal.classList.add('hidden');
                addOrgForm.reset();
                clearOrgErrors();
            });

            function clearOrgErrors() {
                ['org_business_unit', 'org_company_name'].forEach(field => {
                    const errorEl = document.getElementById('error-' + field);
                    if (errorEl) errorEl.textContent = '';
                });
            }
        });
    </script>


</x-layout>
