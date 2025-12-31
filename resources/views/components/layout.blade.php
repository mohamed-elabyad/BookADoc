<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>BookADoc</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="from-10% via-30% to-60% mx-auto max-w-3xl bg-gradient-to-r from-pink-300 to-violet-600 text-slate-700 px-2 md:px-0">

    <nav x-data="{ mobileMenuOpen: false }"
        class="relative pt-2 top-0 mb-12 mx-auto bg-white shadow-2xl rounded-b-2xl px-6 py-4 flex justify-between items-center">
        
        <!-- Mobile Home Button -->
        <a href="{{ route('doctors.index') }}" 
            class="md:hidden px-4 py-2 rounded-xl bg-purple-500 text-white font-medium hover:bg-purple-600 active:scale-95 transition duration-200">
            Home
        </a>

        <!-- Mobile Menu Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen"
            class="md:hidden px-4 py-2 rounded-xl bg-purple-500 text-white font-medium hover:bg-purple-600 active:scale-95 transition duration-200 z-50">
            <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Desktop Navigation -->
        <ul class="hidden md:flex space-x-2">
            <li>
                <x-link-button class="pt-20" href="{{ route('doctors.index') }}">Home</x-link-button>
            </li>
        </ul>

        @auth
            @if (auth()->user()->role->value === 'user')
                <ul class="hidden md:flex space-x-27 mr-17">
                    <li>
                        <x-link-button class="pt-10" href="{{ route('appointments.index') }}">Appointments</x-link-button>
                    </li>
                    <li>
                        <x-link-button class="pt-10" href="{{ route('chat.index') }}">Chats</x-link-button>
                    </li>
                </ul>
            @elseif (auth()->user()->role->value === 'doctor')
                <ul class="hidden md:block">
                    <li>
                        <x-link-button class="pt-10" href="{{ route('profile.show') }}">Profile</x-link-button>
                    </li>
                </ul>
                <ul class="hidden md:flex space-x-12 mr-17">
                    <li>
                        <x-link-button class="pt-10"
                            href="{{ route('doctor.appointments.index') }}">Appointments</x-link-button>
                    </li>
                    <li>
                        <x-link-button class="pt-10" href="{{ route('chat.index') }}">Chats</x-link-button>
                    </li>
                </ul>
            @endif
            <ul class="hidden md:flex space-x-4">
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            class="px-5 absolute -top-8 right-5 py-1.5 pt-10 rounded-xl bg-purple-500 text-white font-medium hover:bg-purple-600 active:scale-95 transition duration-200">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        @else
            <div class="hidden md:flex justify-between gap-3">
                <ul class="flex space-x-4">
                    <li class="relative" x-data="{ open: false }">
                        <x-link-button @click="open = !open" class="pt-10">
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

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="mobileMenuOpen = false"
            class="md:hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="md:hidden fixed top-0 left-0 h-full w-64 bg-white shadow-2xl z-50 overflow-y-auto">
            
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-purple-600">Menu</h2>
                    <button @click="mobileMenuOpen = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <ul class="space-y-4">

                    @auth
                        @if (auth()->user()->role->value === 'user')
                            <li>
                                <a href="{{ route('appointments.index') }}" 
                                    class="block px-4 py-2 rounded-lg bg-purple-500 text-white font-medium hover:bg-purple-600 transition">
                                    Appointments
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('chat.index') }}" 
                                    class="block px-4 py-2 rounded-lg bg-purple-500 text-white font-medium hover:bg-purple-600 transition">
                                    Chats
                                </a>
                            </li>
                        @elseif (auth()->user()->role->value === 'doctor')
                            <li>
                                <a href="{{ route('profile.show') }}" 
                                    class="block px-4 py-2 rounded-lg bg-purple-500 text-white font-medium hover:bg-purple-600 transition">
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('doctor.appointments.index') }}" 
                                    class="block px-4 py-2 rounded-lg bg-purple-500 text-white font-medium hover:bg-purple-600 transition">
                                    Appointments
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('chat.index') }}" 
                                    class="block px-4 py-2 rounded-lg bg-purple-500 text-white font-medium hover:bg-purple-600 transition">
                                    Chats
                                </a>
                            </li>
                        @endif
                        
                        <li class="pt-4 border-t border-gray-200">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 rounded-lg bg-purple-500 text-white font-medium hover:bg-purple-600 transition">
                                    Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li x-data="{ open: false }">
                            <button @click="open = !open" 
                                class="w-full text-left px-4 py-2 rounded-lg bg-purple-500 text-white font-medium hover:bg-purple-600 transition">
                                Register
                            </button>
                            <div x-show="open" class="mt-2 ml-4 space-y-2">
                                <a href="{{ route('register') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-100 rounded-lg transition">
                                    Register as User
                                </a>
                                <a href="{{ route('doctor.register') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-100 rounded-lg transition">
                                    Register as Doctor
                                </a>
                            </div>
                        </li>

                        <li x-data="{ open: false }">
                            <button @click="open = !open" 
                                class="w-full text-left px-4 py-2 rounded-lg bg-purple-500 text-white font-medium hover:bg-purple-600 transition">
                                Login
                            </button>
                            <div x-show="open" class="mt-2 ml-4 space-y-2">
                                <a href="{{ route('login') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-100 rounded-lg transition">
                                    Login as User
                                </a>
                                <a href="{{ route('doctor.login') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-100 rounded-lg transition">
                                    Login as Doctor
                                </a>
                            </div>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
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
