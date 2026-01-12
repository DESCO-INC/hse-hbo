<x-layout>
    <h1 class="text-xl font-semibold text-gray-800 mb-5">MAINTENANCE</h1>

    {{-- ====================== USER LIST ====================== --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center">
            <h1 class="text-xl font-semibold text-gray-800">User List</h1>
            <div class="flex-1"></div>
            <a href="{{ url('/register') }}"
                class="inline-block px-4 py-2 bg-green-500 text-white rounded-md shadow hover:bg-green-600 transition font-medium ml-2">
                Add User
            </a>
        </div>

        <div class="px-6 py-5 overflow-x-auto">
            <div class="overflow-x-auto bg-white shadow rounded-lg border border-gray-200">
                <table class="w-full min-w-max border-collapse mb-5">
                    <thead class="bg-green-600">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">#</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">Name</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">Credential</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-white uppercase tracking-wide">Actions</th>
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
                                    <button type="button"
                                        data-id="{{ $user->id }}"
                                        data-type="user"
                                        class="deleteBtn px-3 py-1.5 text-xs bg-red-500 text-white rounded hover:bg-red-600 transition">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- ====================== DELETE CONFIRM MODAL ====================== --}}
    <div id="delete-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Confirm Delete</h2>
            <p class="mt-2 text-sm text-gray-600">Are you sure you want to delete this? This action cannot be undone.</p>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" id="cancelDelete"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">Cancel</button>
                <button type="button" id="confirmDelete"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">Delete</button>
            </div>
        </div>
    </div>

    {{-- ====================== ADD BUSINESS UNIT MODAL ====================== --}}
    <div id="add-bu-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Add Business Unit</h2>
            <p class="mt-2 text-sm text-gray-600">Enter the name of the new business unit.</p>

            <form id="add-bu-form" class="mt-4 space-y-4" method="POST" action="{{ route('maintenance.bu.store') }}">
                @csrf
                <div>
                    <label for="business_unit" class="block text-sm font-medium text-gray-700 mb-1">
                        Business Unit Name
                    </label>
                    <input type="text" id="business_unit" name="business_unit"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="e.g. Finance Department" required>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" id="cancelAddBU"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">Add</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ====================== ADD COMPANY MODAL ====================== --}}
    <div id="add-company-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Add Company</h2>
            <p class="mt-2 text-sm text-gray-600">Enter the name of the new company.</p>

            <form id="add-company-form" class="mt-4 space-y-4" method="POST" action="{{ route('maintenance.company.store') }}">
                @csrf
                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-1">
                        Company Name
                    </label>
                    <input type="text" id="company" name="company"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="e.g. ABC Corporation" required>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" id="cancelAddCompany"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">Add</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ====================== SCRIPTS ====================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const deleteModal = document.getElementById('delete-modal');
            const cancelBtn = document.getElementById('cancelDelete');
            const confirmBtn = document.getElementById('confirmDelete');
            let selectedId = null;
            let selectedType = null;

            document.body.addEventListener('click', function(e) {
                if (e.target.classList.contains('deleteBtn')) {
                    selectedId = e.target.dataset.id;
                    selectedType = e.target.dataset.type;

                    const modalText = deleteModal.querySelector('p');
                    modalText.textContent = `Are you sure you want to delete this ${selectedType}? This action cannot be undone.`;
                    deleteModal.classList.remove('hidden');
                }
            });

            cancelBtn.addEventListener('click', () => {
                deleteModal.classList.add('hidden');
                selectedId = null;
                selectedType = null;
            });

            confirmBtn.addEventListener('click', () => {
                if (!selectedId || !selectedType) return;

                let endpoint = '';
                if (selectedType === 'user') endpoint = `/maintenance/user/${selectedId}`;
                else if (selectedType === 'bu') endpoint = `/maintenance/bu/${selectedId}`;
                else if (selectedType === 'company') endpoint = `/maintenance/company/${selectedId}`;

                fetch(endpoint, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                }).then(() => location.reload());
            });
        });

        // Handle add modals
        document.addEventListener('DOMContentLoaded', () => {
            const addBUModal = document.getElementById('add-bu-modal');
            const addCompanyModal = document.getElementById('add-company-modal');

            const openAddBU = document.getElementById('openAddBU');
            const cancelAddBU = document.getElementById('cancelAddBU');
            const openAddCompany = document.getElementById('openAddCompany');
            const cancelAddCompany = document.getElementById('cancelAddCompany');

            openAddBU.addEventListener('click', () => addBUModal.classList.remove('hidden'));
            cancelAddBU.addEventListener('click', () => addBUModal.classList.add('hidden'));

            openAddCompany.addEventListener('click', () => addCompanyModal.classList.remove('hidden'));
            cancelAddCompany.addEventListener('click', () => addCompanyModal.classList.add('hidden'));
        });
    </script>
</x-layout>
