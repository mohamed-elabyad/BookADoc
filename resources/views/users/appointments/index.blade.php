<x-layout>

    <x-breadcrumbs class="mb-4" :links="['Doctors' => route('doctors.index'), 'Appointments' => '#']" />

    <div class="grid grid-cols-1 {{ count($appointments) > 1 ? 'md:grid-cols-2' : '' }} gap-6 max-w-7xl mx-auto px-4 mb-8">
        @forelse ($appointments as $appointment)
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200 flex flex-col h-[500px]{{ count($appointments) === 1 ? 'max-w-2xl mx-auto' : '' }}">
                <div class="grid grid-cols-2 gap-6 flex-grow">

                    <div class="flex flex-col items-center">
                        <x-img :doctor="$appointment->doctor" class="w-24 h-24 rounded-full object-cover border shadow mb-3" />
                        <a href="{{ route('doctors.show', $appointment->doctor) }}" class="text-lg text-blue-600 font-bold text-center">
                            Dr. {{ ucfirst($appointment->doctor->name) }}
                        </a>
                        <p class="text-slate-600 text-sm font-semibold mt-1">
                            {{ $appointment->doctor->specialty }}
                        </p>
                    </div>

                    <div class="flex flex-col items-center">
                        <p class="text-slate-500 font-medium text-sm mb-2">Address</p>
                        <x-address class="text-slate-700 font-semibold text-center" :address="$appointment->doctor->address" />
                    </div>

                    <div class="flex flex-col items-center">
                        <p class="text-slate-500 font-medium text-sm mb-2">Appointment</p>
                        <p class="font-semibold text-slate-800">{{ $appointment->date->format('j/n/Y') }}</p>
                        <p class="font-semibold text-slate-800">{{ $appointment->time->format('g:i A') }}</p>
                        <span class="mt-2 px-3 py-1 rounded-full text-xs font-semibold uppercase
                            {{ $appointment->status->value === 'pending' ? 'text-yellow-600 bg-yellow-100' : '' }}
                            {{ $appointment->status->value === 'confirmed' ? 'text-blue-600 bg-blue-100' : '' }}
                            {{ $appointment->status->value === 'completed' ? 'text-green-600 bg-green-100' : '' }}">
                            {{ $appointment->status->value }}
                        </span>
                    </div>

                    @if($appointment->payment)
                        <div class="flex flex-col items-center">
                            <p class="text-slate-500 font-medium text-sm mb-2">Payment</p>
                            <p class="font-semibold text-slate-800 capitalize">{{ $appointment->payment->payment_method->value }}</p>
                            @if($appointment->payment->payment_method->value === 'online')
                                <span class="mt-2 px-3 py-1 rounded-full text-xs font-semibold uppercase
                                    {{ $appointment->payment->payment_status->value === 'pending' ? 'text-yellow-600 bg-yellow-100' : '' }}
                                    {{ $appointment->payment->payment_status->value === 'confirmed' ? 'text-green-600 bg-green-100' : '' }}
                                    {{ $appointment->payment->payment_status->value === 'failed' ? 'text-red-600 bg-red-100' : '' }}
                                    {{ $appointment->payment->payment_status->value === 'cancelled' ? 'text-gray-600 bg-gray-100' : '' }}">
                                    {{ $appointment->payment->payment_status->value }}
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="flex flex-col items-center">
                            <p class="text-slate-500 font-medium text-sm mb-2">Payment</p>
                            <p class="font-semibold text-slate-600">Cash</p>
                        </div>
                    @endif

                </div>

                <div class="flex gap-2 mt-auto pt-4 border-t border-slate-200">
                    @if ($appointment->payment && $appointment->payment->payment_method->value === 'online' && $appointment->payment->payment_status->value !== 'confirmed')
                        <form action="{{ route('stripe.checkout', $appointment) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit"
                                class="w-full bg-indigo-500 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-600 focus:ring-indigo-500 transition-all text-sm font-medium">
                                Pay Now
                            </button>
                        </form>
                    @endif

                    @if (in_array($appointment->status->value, ['confirmed', 'completed']))
                        <button onclick="startChat({{ $appointment->id }})"
                            class="flex-1 bg-purple-500 text-white px-4 py-2 rounded-lg shadow hover:bg-purple-600 focus:ring-purple-500 transition-all text-sm font-medium">
                            Chat
                        </button>
                    @endif

                    <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-red-400 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 focus:ring-red-500 transition-all text-sm font-medium">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-md border border-dashed border-slate-300 p-8">
                <div class="text-center font-bold text-white">
                    No Appointments Yet
                </div>
                <div class="text-center">
                    <a class="text-blue-200 hover:underline font-medium " href="{{ route('doctors.index') }}">Home</a>
                </div>
            </div>
        @endforelse
    </div>

    @push('scripts')
        <script>
            async function startChat(appointmentId) {
                try {
                    const response = await fetch(`/chat`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            appointment_id: appointmentId
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.location.href = `/chat/${data.conversation_id}`;
                    } else {
                        alert(data.message || 'Failed to start chat');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to start chat');
                }
            }
        </script>
    @endpush
</x-layout>
