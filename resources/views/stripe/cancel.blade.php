<x-layout>
        <x-breadcrumbs class="mb-4" :links="['Cancelled Payment' => '#']" />

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="text-red-500 mb-4">
                    <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-gray-800 mb-2">Payment Cancelled</h1>
                <p class="text-gray-600 mb-6">You cancelled the payment process.</p>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <p class="text-yellow-800">Your appointment is still pending. Complete the payment to confirm your
                        booking.</p>
                </div>

                    <div class="flex gap-20 justify-center">
                        <form action="{{ route('stripe.checkout', $appointment) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="mt-auto px-5 py-2 rounded-xl bg-purple-500 text-white font-medium
                                hover:bg-purple-600 active:scale-95 transition duration-200 cursor-pointer">
                                <i class="bi bi-arrow-repeat"></i> Try Again
                            </button>
                        </form>

                        <x-link-button :href="route('appointments.index')">
                            Appointments
                        </x-link-button>

                </div>
            </div>
        </div>
    </div>
</x-layout>
