<?php

namespace App\Http\Controllers\Doctor;

use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Notifications\AppointmentCancelledNotification;
use App\Notifications\AppointmentConfirmedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctor = Auth::user()->doctor;

        $cache_key = 'doctor:' . $doctor->id . ':appointments';

        $appointments = Cache::remember($cache_key, 3600, function () use ($doctor) {
            return $doctor->appointments()
                ->with(['user', 'payment'])
                ->latest()
                ->get();
        });

        return view('doctors.appointments.index', [
            'appointments' => $appointments
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Appointment $appointment, Request $request)
    {
        try {
            Gate::authorize('update', $appointment);

            $valid_statuses = StatusEnum::values();
            $new_status = $request->input('status');

            if (!in_array($new_status, $valid_statuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status'
                ], 400);
            }

            if ($appointment->status === StatusEnum::Completed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot change completed appointment status'
                ], 400);
            }

            $appointment->status = $new_status;
            $appointment->save();

            if ($appointment->status === StatusEnum::Confirmed) {
                $appointment->user->notify(
                    new AppointmentConfirmedNotification($appointment)
                );
            }

            return response()->json([
                'success' => true,
                'status' => $appointment->status,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    public function confirmCashPayment(Appointment $appointment)
    {

        // التأكد من أن الدفع نقدي
        if (!$appointment->payment || $appointment->payment->payment_method->value !== 'cash') {
            return response()->json([
                'success' => false,
                'message' => 'This is not a cash payment'
            ], 400);
        }

        $appointment->payment->update([
            'payment_status' => PaymentStatusEnum::Confirmed
        ]);

        return response()->json([
            'success' => true,
            'payment_status' => 'confirmed',
            'message' => 'Cash payment confirmed successfully'
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        Gate::authorize('delete', $appointment);

        $appointment->user->notify(
            new AppointmentCancelledNotification($appointment)
        );

        $appointment->delete();

        return redirect()->back()->with('success', 'Appointment deleted successfully.');
    }
}
