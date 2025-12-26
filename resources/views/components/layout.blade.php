<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>BookADoc</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="from-10% via-30% to-60% mx-auto max-w-3xl bg-gradient-to-r from-pink-300 to-violet-600 text-slate-700">

    <nav
        class="relative pt-2 top-0 mb-12 mx-auto bg-white shadow-2xl rounded-b-2xl px-6 py-4  flex justify-between items-center">
        <ul class="flex space-x-2">
            <li>
                <x-link-button class="pt-20" href="{{ route('doctors.index') }}">Home</x-link-button>
            </li>
        </ul>

        @auth
            @if (auth()->user()->role->value === 'user')
                <ul class="flex space-x-27 mr-17">
                    <li>
                        <x-link-button class="pt-10" href="{{ route('appointments.index') }}">Appointments</x-link-button>
                    </li>
                    <li>
                        <x-link-button class="pt-10" href="{{ route('chat.index') }}">Chats</x-link-button>
                    </li>
                </ul>
            @elseif (auth()->user()->role->value === 'doctor')
                <ul>
                    <li>
                        <x-link-button class="pt-10" href="{{ route('profile.show') }}">Profile</x-link-button>
                    </li>
                </ul>
                <ul class="flex space-x-12 mr-17">
                    <li>
                        <x-link-button class="pt-10"
                            href="{{ route('doctor.appointments.index') }}">Appointments</x-link-button>
                    </li>
                    <li>
                        <x-link-button class="pt-10" href="{{ route('chat.index') }}">Chats</x-link-button>
                    </li>
                </ul>
            @endif
            <ul class="flex space-x-4">
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            class=" px-5 absolute -top-8 right-5 py-1.5 pt-10 rounded-xl bg-purple-500 text-white font-medium hover:bg-purple-600 active:scale-95 transition duration-200">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        @else
            <div class="flex justify-between gap-3">
                <ul class="flex space-x-4">
                    <li class="relative" x-data="{ open: false }">
                        <x-link-button @click="open = !open" class=" pt-10">
                            Register
                        </x-link-button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                            <a href="{{ route('register') }}"
                                class="block px-4 py-2 text-gray-800 hover:bg-purple-100 transition">
                                Register as User
                            </a>
                            <a href="{{ route('doctor.register') }}"
                                class="block px-4 py-2 text-gray-800 hover:bg-purple-100 transition">
                                Register as Doctor
                            </a>
                        </div>
                    </li>
                </ul>

                <!-- Login Dropdown -->
                <ul class="flex space-x-4">
                    <li class="relative" x-data="{ open: false }">
                        <x-link-button @click="open = !open" class="pt-10">
                            Login
                        </x-link-button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                            <a href="{{ route('login') }}"
                                class="block px-4 py-2 text-gray-800 hover:bg-purple-100 transition">
                                Login as User
                            </a>
                            <a href="{{ route('doctor.login') }}"
                                class="block px-4 py-2 text-gray-800 hover:bg-purple-100 transition">
                                Login as Doctor
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        @endauth
    </nav>

    @if (session('success'))
        <div role="alert"
            class="my-8 rounded-md border-l-4 border-green-400 bg-green-200 p-4 text-green-600 opacity-75">
            <p class="font-bold">Success!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('message'))
        <div role="alert"
            class="my-8 rounded-md border-l-4 border-green-400 bg-green-200 p-4 text-green-600 opacity-75">
            <p class="font-bold">Success!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div role="alert" class="my-8 rounded-md border-l-4 border-red-300 bg-red-100 p-4 text-red-700 opacity-75">
            <p class="font-bold">Error!</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{ $slot }}
    @stack('scripts')

</body>

</html>
