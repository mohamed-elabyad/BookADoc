<x-layout>

    <x-breadcrumbs :links="[
        'Dotcors' => route('doctors.index'),
        $doctor->user->name => route('doctors.show', $doctor),
        'Booking' => '#',
    ]" />

    <div class="container mx-auto px-4 py-8 mb-8">
        <div class="max-w-4xl mx-auto">

            <x-card class="p-6">
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Book an Appointment with</h1>
                    <p class="text-xl text-gray-600">Dr. {{ $doctor->user->name }}</p>
                    <p class="text-md text-gray-500">{{ $doctor->specialty }}</p>
                </div>

                <div x-data="appointmentBooking()" x-init="init()">
                    <form action="{{ route('doctors.appointments.store', $doctor) }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- choseeing date  --}}
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-3">
                                Select the Date
                            </label>
                            <select name="date" id="date" x-model="selectedDate"
                                @change="updateAvailableSlots()"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required>
                                <option value="">Select</option>
                                @foreach ($dates as $date)
                                    <option value="{{ $date['value'] }}">
                                        {{ $date['display'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- available appointments --}}
                        <div x-show="selectedDate && availableSlots.length > 0" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Available Appointments
                            </label>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                <template x-for="slot in availableSlots" :key="slot.value">
                                    <div>
                                        <input type="radio" :id="'time_' + slot.value" name="time"
                                            :value="slot.value" x-model="selectedTime" class="sr-only" required>
                                        <label :for="'time_' + slot.value"
                                            class="flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg border-2 cursor-pointer transition-all duration-200"
                                            :class="selectedTime === slot.value ?
                                                'bg-indigo-700 text-white border-indigo-700 shadow-md transform scale-105' :
                                                'bg-indigo-500 text-white border-indigo-200 hover:bg-indigo-100 hover:border-indigo-300'">
                                            <span x-text="slot.display"></span>
                                        </label>
                                    </div>
                                </template>
                            </div>

                            @error('time')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- message no appointments available --}}
                        <div x-show="selectedDate && availableSlots.length === 0" x-transition>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            No Appointments Available in that date.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div x-show="selectedDate && selectedTime" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Select Payment Method
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Cash Payment -->
                                <div>
                                    <input type="radio" id="payment_cash" name="payment_method" value="cash"
                                        x-model="paymentMethod" class="sr-only" required>
                                    <label for="payment_cash"
                                        class="flex flex-col items-center justify-center p-6 rounded-lg border-2 cursor-pointer transition-all duration-200"
                                        :class="paymentMethod === 'cash'
                                            ?
                                            'bg-green-50 border-green-500 shadow-md' :
                                            'bg-white border-gray-300 hover:border-green-300 hover:bg-green-50'">
                                        <!-- أيقونة فلوس كاش -->
                                        <svg class="w-12 h-12 mb-3"
                                            :class="paymentMethod === 'cash' ? 'text-green-600' : 'text-gray-400'"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold mb-1"
                                            :class="paymentMethod === 'cash' ? 'text-green-700' : 'text-gray-700'">
                                            Cash Payment
                                        </h3>
                                        <p class="text-sm text-center"
                                            :class="paymentMethod === 'cash' ? 'text-green-600' : 'text-gray-500'">
                                            Pay at the clinic
                                        </p>
                                        <span class="mt-3 px-3 py-1 text-xs font-medium rounded-full"
                                            :class="paymentMethod === 'cash' ? 'bg-green-100 text-green-700' :
                                                'bg-gray-100 text-gray-600'">
                                            Pending Confirmation
                                        </span>
                                    </label>
                                </div>

                                <!-- Online Payment -->
                                <div>
                                    <input type="radio" id="payment_online" name="payment_method" value="online"
                                        x-model="paymentMethod" class="sr-only" required>
                                    <label for="payment_online"
                                        class="flex flex-col items-center justify-center p-6 rounded-lg border-2 cursor-pointer transition-all duration-200"
                                        :class="paymentMethod === 'online'
                                            ?
                                            'bg-indigo-50 border-indigo-500 shadow-md' :
                                            'bg-white border-gray-300 hover:border-indigo-300 hover:bg-indigo-50'">
                                        <!-- أيقونة كارت -->
                                        <svg class="w-12 h-12 mb-3"
                                            :class="paymentMethod === 'online' ? 'text-indigo-600' : 'text-gray-400'"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                            <path fill-rule="evenodd"
                                                d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold mb-1"
                                            :class="paymentMethod === 'online' ? 'text-indigo-700' : 'text-gray-700'">
                                            Online Payment
                                        </h3>
                                        <p class="text-sm text-center"
                                            :class="paymentMethod === 'online' ? 'text-indigo-600' : 'text-gray-500'">
                                            Pay now with card
                                        </p>
                                        <span class="mt-3 px-3 py-1 text-xs font-medium rounded-full"
                                            :class="paymentMethod === 'online' ? 'bg-indigo-100 text-indigo-700' :
                                                'bg-gray-100 text-gray-600'">
                                            Instant Confirmation
                                        </span>
                                    </label>
                                </div>
                            </div>

                            @error('payment_method')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        @error('payment_method')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror

                        <div
                            class="flex flex-col md:flex-row items-center justify-between w-full pt-6 mt-6 border-t border-gray-200 gap-4">
                            <x-link-button :href="route('doctors.index')">
                                Home
                            </x-link-button>

                            <button type="submit" :disabled="!selectedDate || !selectedTime || !paymentMethod"
                                class="w-full md:w-auto px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors duration-200">
                                Book Appointment
                            </button>
                        </div>
                </div>


                </form>
        </div>
        </x-card>
    </div>
    </div>

    <script>
        function appointmentBooking() {
            return {
                selectedDate: '',
                selectedTime: '',
                paymentMethod: '',
                availableSlots: [],
                allTimeSlots: @json($timeSlots),
                bookedAppointments: @json($bookedAppointments),

                init() {
                    // يمكن إضافة أي تهيئة مطلوبة هنا
                },

                updateAvailableSlots() {
                    if (!this.selectedDate) {
                        this.availableSlots = [];
                        return;
                    }

                    // الحصول على المواعيد المحجوزة لهذا التاريخ
                    const bookedTimes = this.bookedAppointments[this.selectedDate] || [];

                    // تصفية المواعيد المتاحة (إزالة المحجوزة)
                    this.availableSlots = this.allTimeSlots.filter(slot => {
                        return !bookedTimes.includes(slot.value);
                    });

                    // إعادة تعيين الوقت المحدد إذا لم يعد متاحاً
                    if (this.selectedTime && !this.availableSlots.find(slot => slot.value === this.selectedTime)) {
                        this.selectedTime = '';
                    }
                }
            }
        }
    </script>
</x-layout>
