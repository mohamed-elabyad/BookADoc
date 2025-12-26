<x-layout>

    <x-breadcrumbs class="mb-4"
    :links="['Doctors' => route('doctors.index')  , $doctor->user->name => '#' ]"/>

    <x-card class="mb-8">
        <x-img  :doctor="$doctor" :profile="true" />

            <div class="flex flex-col text-xl gap-3">

                <p class="font-bold text-slate-800">Dr. {{ucFirst($doctor->user->name)}}</p>
                <p class="font-semibold text-slate-600">{{ucFirst($doctor->specialty->value)}}</p>
                <p class="text-lg">{{$doctor->bio}}</p>
            </div>

            <div>
                <p class="text-lg text-slate-500 font-bold mt-6 mb-4">More Info:</p>
                <div class="flex w-full px-6 mb-6 justify-between">
                    <p class="flex-shrink-0">{{$doctor->phone}}</p>
                    <p class="w-1/2">
                        <x-address :address="$doctor->address" />
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-10 mb-6">
                <p class="col-span-2 text-lg text-slate-500 font-bold">Working Hours</p>
                <p>Works From: {{$doctor->work_from->format('g:i A')}}</p>
                <p>To: {{$doctor->work_to->format('g:i A')}}</p>
            </div>

            <div>
                <p class="text-lg text-slate-500 font-bold mt-6 mb-4">Ticket Price</p>
                <div class="mb-6">
                    <p class="m-auto">
                        ${{$doctor->ticket_price}} EGP
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-between p-3 pt-6 border-t w-full border-gray-200">
                <x-link-button :href="route('doctors.index')">
                    Home
                </x-link-button>

                @if (auth()->check() && $doctor->user_id === auth()->user()->id)
                    <x-link-button :href="route('profile.edit')"
                    >Edit Profile</x-link-button>
                @endif

                @if (auth()->check() && auth()->user()->role->value === 'user')
                    <x-link-button :href="route('doctors.appointments.create', $doctor)">
                        Book Appointment
                    </x-link-button>
                @endif

            </div>
    </x-card>
</x-layout>
