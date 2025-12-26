<x-layout>

    <h1 class="my-16 text-center text-4xl font-medium text-white">
        Update your Profile
    </h1>

    <x-card class="mb-8 text-sm grid grid-cols-1 gap-4 text-start">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="mb-8">
                <x-label for="name">Name</x-label>
                <x-input name="name" id="name" :value="old('name', $doctor->name)" :required="true" />
            </div>

            <div class="mb-8">
                <x-label for="address">
                    Address
                </x-label>
                <x-input name="address" id="address" :value="old('address', $doctor->address)" :required="true" />
            </div>

            <div class="grid grid-cols-2 gap-15 mb-8">
                <div>
                    <x-label for="phone">
                        Phone
                    </x-label>
                    <x-input name="phone" id="phone" :value="old('phone', $doctor->phone)" :required="true" />
                </div>

                <div>
                    <x-label for="ticket_price">Ticket Price</x-label>
                    <x-input name="ticket_price" id="ticket_price" :value="old('ticket_price', $doctor->ticket_price)" type='number' :required="true" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-15 mb-8">
                <div>
                    <x-label for='work_from'>Work From</x-label>
                    <select name="work_from" id="work_from" class="w-full rounded-lg border-gray-300">
                        <option value="">Select</option>
                        @foreach (range(0, 22) as $hour)
                            @php
                                $display = date('g:i A', strtotime("$hour:00"));
                                $value = date('H:i:s', strtotime("$hour:00"));
                                $currentValue = old('work_from', optional($doctor->work_from)->format('H:i:s'));
                            @endphp
                            <option value="{{ $value }}" @selected($currentValue === $value)>
                                {{ $display }}
                            </option>
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
                    <x-label for='work_to'>Work To</x-label>
                    <select name="work_to" id="work_to" class="w-full rounded-lg border-gray-300">
                        <option value="">Select</option>
                        @foreach (range(1, 23) as $hour)
                            @php
                                $display = date('g:i A', strtotime("$hour:00"));
                                $value = date('H:i:s', strtotime("$hour:00"));
                                $currentValue = old('work_to', optional($doctor->work_to)->format('H:i:s'));
                            @endphp
                            <option value="{{ $value }}" @selected($currentValue === $value)>
                                {{ $display }}
                            </option>
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
                <textarea name="bio" id="bio" cols="85" :value="old('bio', $doctor->bio)"
                    placeholder="write about your career..."></textarea>
                <div>
                    @error('bio')
                        <div class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="mb-8">
                <x-label for="image">Image</x-label>
                <x-input type="file" name="image" id="image" />
                <div>
                    @error('image')
                        <div class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <x-button class="w-full">update</x-button>
        </form>
    </x-card>
</x-layout>
