<x-layout>
    <h1 class="text-xl font-semibold text-gray-800 mb-5">MAINTENANCE</h1>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-5" x-data="{
        addUserOpen: @js($errors->any()),
        updateUserOpen: false,
        updateUserData: { id: '', name: '', email: '', business_unit: '', credentials: '' },
        deleteModalOpen: false,
        deleteFormAction: ''
    }" x-cloak>
        {{-- USER LIST HEADER --}}
        <div class="px-6 py-5 border-b border-gray-200 flex items-center">
            <h1 class="text-xl font-semibold text-gray-800">User List</h1>
            <div class="flex-1"></div>
            <button @click="addUserOpen = true"
                class="inline-block px-4 py-2 bg-green-500 text-white rounded-md shadow hover:bg-green-600 transition font-medium ml-2">
                Add User
            </button>
        </div>

        {{-- USER TABLE --}}
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
                                Business Unit</th>
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
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->business_unit }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->credentials ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if (Auth::user()->id != $user->id)
                                        {{-- EDIT BUTTON --}}
                                        <button
                                            @click="updateUserOpen = true; updateUserData = {
                                            id: '{{ $user->id }}',
                                            name: '{{ $user->name }}',
                                            email: '{{ $user->email }}',
                                            business_unit: '{{ $user->business_unit }}',
                                            credentials: '{{ $user->credentials }}'
                                        }"
                                            class="px-3 py-1.5 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                            Edit
                                        </button>

                                        {{-- DELETE BUTTON --}}
                                        <button
                                            @click="deleteModalOpen = true; deleteFormAction = '{{ route('maintenance.destroy_user', $user->id) }}'"
                                            class="px-3 py-1.5 text-xs bg-red-500 text-white rounded hover:bg-red-600 transition">
                                            Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="m-2">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        {{-- ADD USER MODAL --}}
        <div x-show="addUserOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Add User</h2>

                <form x-ref="addUserForm" method="POST" action="{{ route('maintenance.store_user') }}">
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
                        <x-form-input type="password" id="password_confirmation" name="password_confirmation"
                            required />
                        @error('password_confirmation')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="addUserOpen = false; $refs.addUserForm.reset()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
                            Add User
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- UPDATE USER MODAL --}}
        <div x-show="updateUserOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Update User</h2>

                <form method="POST"
                    x-bind:action="`{{ route('maintenance.update_user', ['user' => ':id']) }}`.replace(':id', updateUserData.id)">
                    @csrf
                    @method('PUT')

                    <div class="mb-2">
                        <x-form-label>Name</x-form-label>
                        <x-form-input name="name" x-model="updateUserData.name" required />
                    </div>

                    <div class="mb-2">
                        <x-form-label>Email</x-form-label>
                        <x-form-input name="email" x-model="updateUserData.email" required />
                    </div>

                    <div class="mb-2">
                        <x-form-label>Business Unit</x-form-label>
                        <x-form-select name="business_unit" x-model="updateUserData.business_unit" required>
                            <option value="">Select Business Unit</option>
                            @foreach ($business_unit as $bu)
                                <option value="{{ $bu }}">{{ $bu }}</option>
                            @endforeach
                        </x-form-select>
                    </div>

                    <div class="mb-2">
                        <x-form-label>Credentials</x-form-label>
                        <x-form-select name="credentials" x-model="updateUserData.credentials" required>
                            <option value="">Select Credentials</option>
                            <option value="STAFF">Staff</option>
                            <option value="ADMIN">Admin</option>
                            <option value="SUPER_ADMIN">Super Admin</option>
                        </x-form-select>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="updateUserOpen = false"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Update
                            User</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- DELETE MODAL --}}
        <div x-show="deleteModalOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h2 class="text-xl font-semibold text-gray-800">Confirm Delete</h2>
                <p class="mt-2 text-sm text-gray-600">Are you sure you want to delete this? This action cannot be
                    undone.</p>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="deleteModalOpen = false"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">Cancel</button>
                    <form :action="deleteFormAction" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
