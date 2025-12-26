<x-layout>

    <form action="{{route('doctors.index')}}" method="GET">
        <x-card class="mb-4 text-sm grid grid-cols-1 gap-4" >
            <div class="w-full mt-2">
                <x-label for="name" class="mb-1 font-semibold">Doctor's name</x-label>
                <x-input name="name" id="name" value="{{request('name')}}" placeholder="search by doc's name..." />
            </div>
            <div class="w-full mt-2">
                <x-label for="address" class="mb-1 font-semibold">Address</x-label>
                <x-input name="address" id="address" value="{{request('address')}}" placeholder="search for doctors by address..." />
            </div>

            <div class="mt-4 mb-6">
                <x-label for="specialty" class="block text-sm font-medium text-gray-700 mb-1">
                    Specialty
                </x-label>
                <x-select  name="specialty" :options="App\Enums\SpecialtyEnum::values()" />
            </div>

            <x-button class="w-full">Filter</x-button>
        </x-card>

    </form>



    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
        @forelse ($doctors as $doctor)
            <x-doctor-card :$doctor />
        @empty
        <div class="text-center text-white font-bold border col-span-2 border-dashed mx-auto p-4 w-64">
            No Doctors Available
        </div>
        @endforelse
    </div>

    <div class="mb-6">
        {{$doctors->appends(request()->query())->links()}}
    </div>
</x-layout>
