<?php

namespace App\Http\Controllers\User;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Notifications\DoctorNewAppointmentNotification;
use App\Services\StripeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class AppointmentController extends Controller
{
    public function __construct(protected StripeService $stripeService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $cacheKey = 'user:' . $user->id . ':appointments';

        $appointments = Cache::remember($cacheKey, 3600, function () use ($user) {
            return $user->appointments()
                ->with(['doctor.user', 'payment'])
                ->latest()
                ->get();
        });

        return view('users.appointments.index', [
            'appointments' => $appointments
        ]);
    }

    public function create(Doctor $doctor)
    {
        Gate::authorize('create', Appointment::class);

        $dates = [];
        $today = now();

        for ($i = 0; $i < 30; $i++) {
            $date = $today->copy()->addDays($i);
            $dates[] = [
                'value' => $date->format('Y-m-d'),
                'display' => $date->format('j/n/Y') . ' (' . $date->format('l') . ')',
                'day_name' => $date->format('l')
            ];
        }

        $workFrom = $doctor->work_from;
        $workTo = $doctor->work_to;

        if ($workTo->lessThan($workFrom)) {
            $workTo->addDay();
        }

        $timeSlots = [];
        $currentTime = $workFrom->copy();

        while ($currentTime->lessThan($workTo)) {
            $timeSlots[] = [
                'value' => $currentTime->format('H:i:s'),
                'display' => $currentTime->format('g:i A')
            ];
            $currentTime->addMinutes(30);
        }

        $bookedAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereBetween('date', [
                $today->format('Y-m-d'),
                $today->copy()->addDays(29)->format('Y-m-d')
            ])
            ->get()
            ->groupBy(function ($appointment) {
                return $appointment->date->format('Y-m-d');
            })
            ->map(function ($appointments) {
                return $appointments->pluck('time')->map(function ($time) {
                    return $time->format('H:i:s');
                })->toArray();
            })
            ->toArray();

        return view('users.appointments.create', compact('doctor', 'dates', 'timeSlots', 'bookedAppointments'));
    }

    public function store(StoreAppointmentRequest $request, Doctor $doctor)
    {
        Gate::authorize('create', Appointment::class);

        $validated = $request->validated();

        // checking that the appointment is not already booked
        $exist_appointment = Appointment::where('doctor_id', $doctor->id)
            ->where('date', $validated['date'])
            ->where('time', $validated['time'])
            ->first();

        if ($exist_appointment) {
            return back()->with('error', 'This Appointment is already booked');
        }

        $appointment = $doctor->appointments()->create([
            'user_id' => $request->user()->id,
            'date' => $validated['date'],
            'time' => $validated['time'],
            'status' => 'pending',
        ]);

        $payment = $appointment->payment()->create([
            'amount' => $doctor->ticket_price,
            'currency' => 'EGP',
            'payment_method' => $validated['payment_method'],
            'payment_status' => PaymentStatusEnum::Pending->value,
        ]);


        if ($validated['payment_method'] === PaymentMethodEnum::Online->value) {
            try {
                $session = $this->stripeService->createCheckoutSession($appointment);
                return redirect($session->url);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Something wrong with the payment try again.');
            }
        }

        $payment->update([
            'paid_at' => now()
        ]);

        $appointment->doctor->user->notify(
            new DoctorNewAppointmentNotification($appointment)
        );

        return redirect()->route('appointments.index')->with('success', 'The Appointment has been booked successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        Gate::authorize('delete', $appointment);

        $appointment->delete();

        return redirect()->back()->with('success', 'Appointment deleted successfully.');
    }
}
