<x-layout>
    <x-card class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-800">AUDIT TRAIL</h2>

            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button size="sm" href="{{ route('hbo.index') }}" variant="info">
                    Home
                </x-button>
            </div>
        </div>
    </x-card>

    <x-card class="mb-2">
        <div class="overflow-x-auto">
            {{-- <form method="GET" class="mb-4 flex items-center gap-2">
                <input name="search" id="search" type="text"
                    class="border border-gray-300 rounded-md px-3 py-1 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[150px]"
                    placeholder="Search Here" value="{{ request('search') }}" />

                <button class="bg-green-500 text-white text-xs px-2 py-1 rounded hover:bg-green-600" type="submit">
                    Search
                </button>
            </form> --}}
            <div class="overflow-x-auto border border-gray-200 rounded">
                <table class="min-w-full divide-y divide-gray-200 text-sm table-auto">
                    <thead class="bg-green-500 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium w-10">User</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-32">Action</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-36">DB Table</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-36">Date</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-28">Changes</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($trails as $trail)
                            <tr>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $trail->user }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $trail->action }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $trail->model }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800">{{ $trail->created_at }}</td>
                                <td class="px-4 py-3 text-xs text-gray-800 max-w-xs truncate">
                                    {{ $trail->changes }}
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
                {{ $trails->links() }}
            </div>
        </div>
    </x-card>
</x-layout>
