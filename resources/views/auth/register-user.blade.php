<x-layout>

    <h1 class="my-16 text-center text-2xl md:text-4xl font-medium text-white">
        Register
    </h1>

    <x-card class="mb-8 text-sm grid grid-cols-1 gap-4 text-start">
        <form action="{{route('register.store')}}" method="POST">
            @csrf

            <div class="mb-8">
                <x-label for="name" :required="true">Name</x-label>
                <x-input name="name" id="name" placeholder="Name..." :required="true" :value="old('name')"/>
            </div>

            <div class="mb-8">
                <x-label for="email" :required="true">Email</x-label>
                <x-input name="email" id="email" placeholder="email..." :required="true" :value="old('email')"/>
            </div>
            <div class="mb-8">
                <x-label for="password" :required="true">Password</x-label>
                <x-input name="password" id="password" type="password" placeholder="Password..." :required="true" :value="old('password')"/>
            </div>
            <div class="mb-8">
                <x-label for="password_confirmation" :required="true">Password Confirmation</x-label>
                <x-input name="password_confirmation" type="password"  id="password_confirmation" placeholder="Password Confirmation..." :required="true" :value="old('password_confirmation')"/>
            </div>


            <x-button class="w-full" >Register</x-button>
        </form>
    </x-card>
</x-layout>

