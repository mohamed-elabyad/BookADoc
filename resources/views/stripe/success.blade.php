<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Success Payment' => '#']" />

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="text-green-500 mb-4">
                    <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-gray-800 mb-2">Payment Successful!</h1>
                <p class="text-gray-600 mb-6">Your appointment has been confirmed.</p>

                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                    <h3 class="font-semibold text-gray-800 mb-2">Appointment Details:</h3>
                    <p><strong>Doctor: </strong> Dr. {{ $appointment->doctor->name }}</p>
                    <p><strong>Date: </strong> {{ $appointment->date->format('j/n/Y') }}</p>
                    <p><strong>Time: </strong> {{ $appointment->time->format('g:i A') }}</p>
                    <p><strong>Amount: </strong> ${{ $appointment->payment->amount }}</p>
                </div>

                <div class="flex gap-4 justify-center">

                    <x-link-button :href="route('appointments.index')">
                        My Appointments
                    </x-link-button>
                </div>
            </div>
        </div>
    </div>
</x-layout>
