<x-layout>

    <h1 class="my-16 text-center text-2xl md:text-4xl font-medium text-white">
        Applay as a Doctor
    </h1>

    <x-card class="mb-8 text-sm grid grid-cols-1 gap-4 text-start">
        <form action="{{ route('doctor.register.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-8">
                <x-label for="name" :required="true">Name</x-label>
                <x-input name="name" id="name" placeholder="Name..." :required="true" :value="old('name')" />
            </div>

            <div class="mb-8">
                <x-label for="email" :required="true">Email</x-label>
                <x-input name="email" id="email" placeholder="email..." :required="true" :value="old('email')" />
            </div>
            <div class="mb-8">
                <x-label for="password" :required="true">Password</x-label>
                <x-input name="password" id="password" type="password" placeholder="Password..." :required="true" :value="old('password')" />
            </div>
            <div class="mb-8">
                <x-label for="password_confirmation" :required="true">Password Confirmation</x-label>
                <x-input name="password_confirmation" type="password" id="password_confirmation" placeholder="Password Confirmation..."
                    :required="true" :value="old('password_confirmation')" />
            </div>

            <div class="mb-8">
                <x-select name="specialty" :options="App\Enums\SpecialtyEnum::values()" :required="true" :value="old('specialty')" />
            </div>


            <div class="mb-8">
                <x-label for="address" :required="true">
                    Address
                </x-label>
                <x-input name="address" id="address" placeholder='Address...' :required="true" :value="old('address')" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-15 mb-8">
                <div>
                    <x-label for="phone" :required="true">
                        Phone
                    </x-label>
                    <x-input name="phone" id="phone" placeholder='Phone...' :required="true"
                        :value="old('phone')" />
                </div>

                <div>
                    <x-label for="ticket_price" :required="true">Ticket Price</x-label>
                    <x-input name="ticket_price" id="ticket_price" placeholder="Ticket Price..." type='number'
                        :required="true" :value="old('ticket_price')" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-15 mb-8">
                <div>
                    <x-label for="work_from" :required="true">Work From</x-label>
                    <select name="work_from" id="work_from" class="w-full rounded-lg border-gray-300" required>
                        <option value="" disabled hidden @selected(!old('work_from'))>Select</option>
                        @foreach (range(0, 23) as $hour)
                            @php
                                $display = date('g:i A', strtotime("$hour:00"));
                                $value = date('H:i:s', strtotime("$hour:00"));
                            @endphp
                            <option value="{{ $value }}" @selected(old('work_from') === $value)>
                                {{ $display }}</option>
                        @endforeach
                    </select>
                    <div>
                        @error('work_from')
                            <div class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div>
                    <x-label for="work_to" :required="true">Work To</x-label>
                    <select name="work_to" id="work_to" class=" w-full rounded-lg border-gray-300" required>
                        <option value="" disabled hidden @selected(!request('work_to'))>Select</option>
                        @foreach (range(0, 23) as $hour)
                            @php
                                $display = date('g:i A', strtotime("$hour:00"));
                                $value = date('H:i:s', strtotime("$hour:00"));
                            @endphp
                            <option value="{{ $value }}" @selected(old('work_from') === $value)>
                                {{ $display }}</option>
                        @endforeach
                    </select>
                    <div>
                        @error('work_to')
                            <div class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <x-label for="bio">Bio</x-label>
                <textarea name="bio" id="bio" class="w-full rounded-lg border-gray-300" rows="4" :value="old('bio')" placeholder="write about your career..."></textarea>
            </div>

            <div class="mb-8">
                <x-label for="image" :required="true">Image</x-label>
                <x-input type="file" name="image" id="image" :required="true" />
            </div>

            <div class="mb-8">
                <x-label for="license" :required="true">License</x-label>
                <x-input type="file" name="license" id="license" :required="true" />
            </div>

            <div class="mb-8">
                <x-label for="degree" :required="true">Degree</x-label>
                <x-input type="file" name="degree" id="degree" :required="true" />
            </div>

            <x-button class="w-full">Apply</x-button>
        </form>
    </x-card>
</x-layout>
