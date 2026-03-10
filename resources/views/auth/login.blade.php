<x-layout>
    <div class="space-y-4 mb-6">
        <div class="flex justify-center py-10">
            <div class="relative flex flex-col my-6 bg-white shadow-xl border border-slate-200 rounded-lg w-96">
                <div class="p-4">
                    <div class="flex flex-col items-center">
                        <p class="text-3xl font-bold text-green-500">DESCO</p>
                        <p class="text-lg font-bold text-green-500 mb-5">Hazard & Behaviour Observation (HBO)</p>
                    </div>
                    <form method="POST" action="{{ url('/login') }}">
                        @csrf
                        <div class="mb-2">
                            <x-input label="Email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="Enter your email" class="w-full" />
                        </div>

                        <div class="mb-2">
                            <x-input label="Password" type="password" name="password" placeholder="Enter your password"
                                class="w-full" />
                        </div>

                </div>
                <div class="px-4 pb-4 pt-0">

                    <x-button type="submit" class="w-full">
                        Login
                    </x-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
