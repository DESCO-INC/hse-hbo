<x-layout >
    <div class="space-y-4 mb-6">
        <div class="flex justify-center py-10">
            <div class="relative flex flex-col my-6 bg-white shadow-xl border border-slate-200 rounded-lg w-96">
                <div class="p-4">
                <h6 class="mb-5 text-slate-500 text-xl font-semibold">
                    HSE Condition & Behavior Recording (CBR)
                </h6>
                <form method="POST" action="{{ url('/login') }}">
                @csrf
                <div class="mb-2">
                    <x-form-label>Email</x-form-label>
                    <div class="mt-2">
                        <x-form-input id="email" name="email" placeholder="admin@desco.ph" required />
                        <x-form-error name='email'/>
                    </div>
                </div>

                <div class="mb-2">
                    <x-form-label>Password</x-form-label>
                    <div class="mt-2">
                        <x-form-input type="password" id="password" name="password" required/>
                        <x-form-error name='password'/>
                    </div>
                </div>

                </div>
                <div class="px-4 pb-4 pt-0">
                <button
                    class="w-full rounded-md bg-green-500 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                    type="submit"
                >
                    Login
                </button>
                </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>