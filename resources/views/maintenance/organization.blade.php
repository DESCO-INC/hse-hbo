<x-layout>
    <h1 class="text-xl font-semibold text-gray-800 mb-5">MAINTENANCE</h1>

    {{-- ====================== ORG LIST ====================== --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-5" x-data="{
        addOrgOpen: false,
        updateOrgOpen: false,
        deleteModalOpen: false,
        deleteFormAction: '',
        updateOrgData: { id: '', business_unit: '', company_name: '' }
    }">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-200 flex items-center">
            <h1 class="text-xl font-semibold text-gray-800">Business Unit and Group List</h1>
            <div class="flex-1"></div>
            <button @click="addOrgOpen = true"
                class="inline-block px-4 py-2 bg-green-500 text-white rounded-md shadow hover:bg-green-600 transition font-medium ml-2">
                Add Items
            </button>
        </div>

        {{-- Table --}}
        <div class="px-6 py-5 overflow-x-auto">

            <form method="GET" action="{{ route('maintenance.organization') }}"
                class="mb-4 flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search Business Unit or Group..."
                    class="border border-gray-300 rounded-md px-3 py-1 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]">
                <button type="submit" class="bg-green-500 text-white text-xs px-2 py-1 rounded hover:bg-green-600">
                    Search
                </button>
            </form>

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
                                <td class="px-4 py-3 text-center space-x-1">
                                    <button
                                        @click="updateOrgOpen = true; updateOrgData = { id: '{{ $org->id }}', business_unit: '{{ $org->business_unit }}', company_name: '{{ $org->company_name }}' }"
                                        class="px-3 py-1.5 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                        Edit
                                    </button>
                                    <button
                                        @click="deleteModalOpen = true; deleteFormAction = '{{ route('maintenance.destroy_org', $org->id) }}'"
                                        class="px-3 py-1.5 text-xs bg-red-500 text-white rounded hover:bg-red-600 transition">
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

        {{-- ====================== ADD ORG MODAL ====================== --}}
        <div x-show="addOrgOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Add Business Unit / Group</h2>

                <form x-ref="addOrgForm" method="POST" action="{{ route('maintenance.store_org') }}">
                    @csrf

                    <div class="mb-2">
                        <x-form-label>Business Unit</x-form-label>
                        <x-form-input name="org_business_unit" placeholder="Enter Business Unit" required />
                        @error('org_business_unit')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-2">
                        <x-form-label>Group / Company Name</x-form-label>
                        <x-form-input name="org_company_name" placeholder="Enter Group Name" required />
                        @error('org_company_name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="addOrgOpen = false; $refs.addOrgForm.reset()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">Add
                            Org</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================== UPDATE ORG MODAL ====================== --}}
        <div x-show="updateOrgOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Update Organization</h2>

                <form method="POST"
                    x-bind:action="`{{ route('maintenance.update_org', ['org' => ':id']) }}`.replace(':id', updateOrgData.id)">
                    @csrf
                    @method('PUT')

                    <div class="mb-2">
                        <x-form-label>Business Unit</x-form-label>
                        <x-form-input name="org_business_unit" x-model="updateOrgData.business_unit" required />
                    </div>

                    <div class="mb-2">
                        <x-form-label>Group / Company Name</x-form-label>
                        <x-form-input name="org_company_name" x-model="updateOrgData.company_name" required />
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="updateOrgOpen = false"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Update
                            Org</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================== DELETE ORG MODAL ====================== --}}
        <div x-show="deleteModalOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h2 class="text-xl font-semibold text-gray-800">Confirm Delete</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Are you sure you want to delete this? This action cannot be undone.
                </p>

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
