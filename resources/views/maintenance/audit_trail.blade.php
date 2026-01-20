<x-layout>
    <h1 class="text-xl font-semibold text-gray-800 mb-5">AUDIT TRAIL</h1>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">

        <div class="px-6 py-5 overflow-x-auto">
            <div class="overflow-x-auto bg-white shadow rounded-lg border border-gray-200">
                <table class="w-full min-w-max border-collapse mb-5">
                    <thead class="bg-green-600">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">
                                User</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">
                                Action</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white uppercase tracking-wide">
                                DB Table</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-white uppercase tracking-wide">
                                Date</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-white uppercase tracking-wide">
                                changes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($trails as $trail)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $trail->user }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $trail->action }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $trail->model }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $trail->created_at }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 truncate max-w-xs">
                                    {{ $trail->changes }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $trails->links() }}
            </div>
        </div>
    </div>
</x-layout>
