<x-layout >
    <div class="space-y-4 mb-6">
        <div class="flex justify-center">
          <div class="relative flex flex-col my-6 bg-white shadow-xl border border-slate-200 rounded-lg w-96">
            <div class="p-4">
              <h6 class="mb-5 text-slate-800 text-xl font-semibold">
                Add Business unit
              </h6>
              <form method="POST" action="">
              @csrf
              
              <div class="mb-2">
                  <x-form-label>Name</x-form-label>
                  <div class="mt-2">
                      <x-form-input id="name" name="name" placeholder="Jane Smith" required />
                      <x-form-error name='name'/>
                  </div>
              </div>

              <div class="mb-2">
                  <x-form-label>Email</x-form-label>
                  <div class="mt-2">
                      <x-form-input id="email" name="email" placeholder="JaneSmith@gmail.com" required />
                      <x-form-error name='email'/>
                  </div>
              </div>

              <!-- Business Unit -->
              <div class="relative mb-2">
                <x-form-label for="business_unit">Business Unit</x-form-label>
                  <x-form-select name="business_unit" id="business_unit" required class="text-gray-500">
                    <option value="">Select Business Unit</option>
                    @foreach ($business_unit as $bu)
                      <option value="{{ $bu->business_unit }}">
                        {{ $bu->business_unit }}
                      </option>
                    @endforeach
                  </x-form-select>
                <x-form-error name="business_unit" />
              </div>

              <!-- Credentials -->
              <div class="relative mb-2">
                <x-form-label for="credentials">Credentials</x-form-label>
                  <x-form-select name="credentials" id="credentials" required class="text-gray-500">
                    <option value="">Select Credentials</option>
                    <option value="USER">User</option>
                    <option value="ADMIN">Admin</option>
                    <option value="SUPER_ADMIN">super Admin</option>
                  </x-form-select>
                <x-form-error name="credentials" />
              </div>

              <div class="mb-2">
                  <x-form-label>Password</x-form-label>
                  <div class="mt-2">
                      <x-form-input type="password" id="password" name="password" required/>
                      <x-form-error name='password'/>
                  </div>
              </div>

              <div class="mb-2">
                  <x-form-label>Confirm Password</x-form-label>
                  <div class="mt-2">
                      <x-form-input type="password" id="password_confirmation" name="password_confirmation" required/>
                      <x-form-error name='password_confirmation'/>
                  </div>
              </div>
            </div>
            <div class="px-4 pb-4 pt-0 mt-2">
              <button
                class="w-full rounded-md bg-green-500 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                type="submit"
              >
                Sign up
              </button>
              </form>
            </div>
          </div>
        </div>

</div>

    </div>
</x-layout>