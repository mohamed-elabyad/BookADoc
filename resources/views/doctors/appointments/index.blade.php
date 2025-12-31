<x-layout>
    <div>
        @forelse ($appointments as $appointment)
            <x-appoint-card class="grid-cols-1 place-items-center sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5"
            x-data="{
                status: '{{ $appointment->status }}',
                paymentStatus: '{{ $appointment->payment?->payment_status?->value ?? 'pending' }}',
                updating: false,
                async updateStatus() {
                    if (this.status === 'completed') return;

                    this.updating = true;
                    try {
                        const response = await fetch(`{{ route('doctor.appointments.update', $appointment) }}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                status: this.status === 'pending' ? 'confirmed' : 'completed'
                            })
                        });

                        if (response.ok) {
                            const data = await response.json();
                            this.status = data.status;
                            console.log(data.message);
                        } else {
                            alert(data.message || 'Error while updating status');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error while updating the status');
                    } finally {
                        this.updating = false;
                    }
                },
                async confirmCashPayment() {
                    this.updating = true;
                    try {
                        const response = await fetch(`{{ route('doctor.appointments.confirm-cash-payment', $appointment) }}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                payment_status: 'confirmed'
                            })
                        });

                        if (response.ok) {
                            const data = await response.json();
                            this.paymentStatus = data.payment_status;
                            console.log(data.message);
                        } else {
                            const data = await response.json();
                            alert(data.message || 'Error while confirming payment');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error while confirming the payment');
                    } finally {
                        this.updating = false;
                    }
                }
            }">
                <div class="flex flex-col gap-2">
                    <p class="text-slate-500 font-medium">Patient:</p>
                    <p class="text-xl text-slate-600 font-bold mb-2">
                        {{ucFirst($appointment->user->name)}}
                    </p>
                </div>
                <div class="min-w-[10rem] text-center">
                    <p class="text-slate-500 font-medium">Appointment:</p>
                    <p class="mt-2 font-semibold text-slate-800">{{ $appointment->date->format('j/n/Y') }}</p>
                    <p class="font-semibold text-slate-800">{{ $appointment->time->format('g:i A') }}</p>
                </div>

                @if($appointment->payment)
                    <div class="min-w-[10rem] text-center">
                        <p class="text-slate-500 font-medium">Payment:</p>
                        <p class="mt-2 font-semibold text-slate-800 capitalize">{{ $appointment->payment->payment_method->value }}</p>
                        <span x-show="'{{ $appointment->payment->payment_method->value }}' === 'cash'"
                              x-text="paymentStatus"
                              :class="{
                                  'text-yellow-600 bg-yellow-100': paymentStatus === 'pending',
                                  'text-green-600 bg-green-100': paymentStatus === 'confirmed'
                              }"
                              class="mt-2 px-2 py-1 rounded-full text-xs font-semibold uppercase">
                        </span>
                        @if($appointment->payment->payment_method->value === 'online')
                            <span class="mt-2 px-2 py-1 rounded-full text-xs font-semibold uppercase
                                {{ $appointment->payment->payment_status->value === 'pending' ? 'text-yellow-600 bg-yellow-100' : '' }}
                                {{ $appointment->payment->payment_status->value === 'confirmed' ? 'text-green-600 bg-green-100' : '' }}
                                {{ $appointment->payment->payment_status->value === 'failed' ? 'text-red-600 bg-red-100' : '' }}
                                {{ $appointment->payment->payment_status->value === 'cancelled' ? 'text-gray-600 bg-gray-100' : '' }}">
                                {{ $appointment->payment->payment_status->value }}
                            </span>
                        @endif

                        <div x-show="'{{ $appointment->payment->payment_method->value }}' === 'cash' && paymentStatus === 'pending'">
                            <button @click="confirmCashPayment()"
                                    :disabled="updating"
                                    class="mt-2 bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-xs font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!updating">Confirm Payment</span>
                                <span x-show="updating">Confirming...</span>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="min-w-[10rem] text-center">
                        <p class="text-slate-500 font-medium">Payment:</p>
                        <p class="mt-2 font-semibold text-slate-600">No Payment Info</p>
                    </div>
                @endif

                <div class="flex flex-col items-center gap-2">
                    <div class="flex items-center gap-2">
                        <span x-text="status"
                            :class="{
                                'text-yellow-600 bg-yellow-100': status === 'pending',
                                'text-blue-600 bg-blue-100': status === 'confirmed',
                                'text-green-600 bg-green-100': status === 'completed'
                            }"
                            class="px-2 py-1 rounded-full text-xs font-semibold uppercase">
                        </span>
                    </div>

                    @php
                        $canUpdate = true;
                        if ($appointment->payment && $appointment->payment->payment_method->value === 'online') {
                            $canUpdate = in_array($appointment->payment->payment_status->value, ['confirmed', 'pending']);
                        }
                    @endphp

                    @if($canUpdate)
                        <button @click="updateStatus()"
                                x-show="status !== 'completed'"
                                :disabled="updating"
                                :class="{
                                    'bg-yellow-500 hover:bg-yellow-600': status === 'pending',
                                    'bg-blue-500 hover:bg-blue-600': status === 'confirmed'
                                }"
                                class="text-white px-3 py-1 rounded-lg text-sm font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!updating">
                                <span x-show="status === 'pending'">Confirm</span>
                                <span x-show="status === 'confirmed'">Complete</span>
                            </span>
                            <span x-show="updating">Updating...</span>
                        </button>
                    @else
                        <p class="text-xs text-red-600 text-center mt-2">Payment required</p>
                    @endif
                </div>

                <div class="ml-auto flex flex-col gap-2 w-full md:w-auto">
                    @if(in_array($appointment->status->value, ['confirmed', 'completed']))
                        <button
                            onclick="startChat({{ $appointment->id }})"
                            class="bg-purple-500 text-white px-4 py-2 rounded-lg shadow hover:bg-purple-600 focus:ring-purple-500 transition-all text-sm font-medium">
                            Chat
                        </button>
                    @endif
                    <form action="{{ route('doctor.appointments.destroy', $appointment) }}" method="POST">
                        @csrf
                        @method("DELETE")
                        <x-button class="w-full bg-red-400 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 focus:ring-red-500 transition-all">
                            Cancel
                        </x-button>
                    </form>
                </div>
            </x-appoint-card>

        @empty
            <div class="rounded-md border border-dashed border-slate-300 p-8">
                <div class="text-center font-bold text-white">
                    No Appointments Yet
                </div>
                <div class="text-center">
                    <a class="text-blue-200 hover:underline font-medium "
                    href="{{route('doctors.index')}}">Home</a>
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
