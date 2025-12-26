<x-layout>

    <h1 class="my-16 text-center text-4xl font-medium text-white">
        Sign in to your Account
    </h1>

    <x-card class="mb-4 text-sm grid grid-cols-1 gap-4 text-start ">
        <form action="{{route('login.store')}}" method="POST">
            @csrf

            <div class="mb-8">
                <x-label for="email" required="true">E-mail</x-label>
                <x-input name="email" id="email" type='email' placeholder="Email" />
            </div>

            <div class="mb-8">
                <x-label for="password" required="true">
                Password
                </x-label>
                <x-input name="password" id="password" type="password" placeholder='Password'/>
            </div>

                <div class="mb-8 ">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="remember" id="remember"
                    class="rounded-sm border border-slate-400" />
                    <x-label for="remember" class="mb-0">Remember me</x-label>
                </div>
                </div>


            <x-button class="w-full" >Login</x-button>
        </form>
    </x-card>
</x-layout>
