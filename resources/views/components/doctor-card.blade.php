<x-card class="w-[348px] h-[350px]">


    <x-img :doctor="$doctor" />


    <h2 class="text-xl font-semibold text-slate-800">Dr. {{ucfirst($doctor->user->name)}}</h2>

    <p class="text-slate-600 text-lg font-semibold mb-2" >{{$doctor->specialty}}</p>

    <x-address :address="$doctor->address"/>

    <x-link-button href="{{route('doctors.show', $doctor)}}">
        Book
    </x-link-button>
</x-card>
