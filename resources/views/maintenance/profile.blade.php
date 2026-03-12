<x-layout>
    <x-card class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-800">PROFILE</h2>

            <div class="flex gap-2 mt-4 sm:mt-0">
                <x-button size="sm" href="{{ route('hbo.index') }}" variant="info">
                    Home
                </x-button>
            </div>
        </div>
    </x-card>

    @php
        $editing = request()->get('edit') == 1;
    @endphp

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
        <form id="profileForm" method="POST" action="{{ route('maintenance.profile_update') }}"
            class="px-6 py-8 grid grid-cols-4 gap-5">
            @csrf
            @method('PUT')

            <!-- Avatar Placeholder with Initials -->
            <div class="col-span-1 flex flex-col items-center mb-5">
                <div
                    class="h-40 w-40 rounded-full bg-gray-200 flex items-center justify-center text-4xl font-semibold text-gray-700 border border-gray-300">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name)[1] ?? '', 0, 1)) }}
                </div>

                <h1 class="text-2xl font-semibold text-gray-800 mt-4">{{ auth()->user()->name }}</h1>

                <!-- Edit / Cancel Button -->
                @if (!$editing)
                    <a href="{{ route('maintenance.profile') }}?edit=1"
                        class="mt-4 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition w-full text-center">
                        Edit Profile
                    </a>
                @else
                    <a href="{{ route('maintenance.profile') }}"
                        class="mt-4 px-6 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg font-medium transition w-full text-center">
                        Cancel
                    </a>
                @endif
            </div>

            <!-- User Info -->
            <div class="col-span-3 space-y-4">
                <div>
                    <x-input label="Name" size="lg" width="full" name="name"
                        value="{{ auth()->user()->name }}" readonly="{{ !$editing }}" required />
                </div>

                <div>
                    <x-input label="Email" size="lg" width="full" type="email" name="email"
                        value="{{ auth()->user()->email }}" readonly="{{ !$editing }}" required />
                </div>

                <div class="{{ Auth::user()->credentials === 'SUPER_ADMIN' ? '' : 'hidden' }}">
                    <x-select label="Credentials" name="credentials" size="lg" width="full"
                        :options="[
                            '' => 'Select Credentials',
                            'SUPER_ADMIN' => 'SUPER ADMIN',
                            'ADMIN' => 'ADMIN',
                            'STAFF' => 'STAFF',
                        ]" :selected="old('credentials', Auth::user()->credentials)" :value="Auth::user()->credentials" readonly="{{ !$editing }}" />
                </div>

                <!-- Password Fields (only show when editing) -->
                @if ($editing)
                    <div class="space-y-2">
                        <div>
                            <x-input label="New Password" size="lg" width="full" type="password" name="password" placeholder="Leave blank to keep current password" />
                        </div>
                        <div>
                            <x-input label="Confirm Password" size="lg" width="full" type="password" name="password_confirmation" placeholder="Leave blank to keep current password" />
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div>
                        <button type="button" id="saveBtn"
                            class="mt-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition w-full">
                            Save Changes
                        </button>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Save Changes Modal (reuse your logout modal style) -->
    <div id="save-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800">Confirm Update</h2>
            <p class="mt-2 text-sm text-gray-600">Updating your profile will log you out. Do you want to proceed?</p>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                    onclick="document.getElementById('save-modal').classList.add('hidden')">
                    Cancel
                </button>

                <button type="button" id="confirmSaveBtn"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    Proceed
                </button>
            </div>
        </div>
    </div>

    <!-- JS to open modal -->
    <script>
        document.getElementById('saveBtn')?.addEventListener('click', function() {
            document.getElementById('save-modal').classList.remove('hidden');
        });

        document.getElementById('confirmSaveBtn')?.addEventListener('click', function() {
            document.getElementById('profileForm').submit();
        });
    </script>
</x-layout>
