<?php

namespace App\Http\Controllers\Stripe;

use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CheckoutController extends Controller
{
    public function __construct(protected StripeService $stripeService) {}

    public function create(Appointment $appointment)
    {
        try {
            $session = $this->stripeService->createCheckoutSession($appointment);
            return redirect($session->url);
        } catch (\Exception $e) {
            return redirect()
                ->route('appointments.index', $appointment)
                ->with('error', $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $appointment_id = $request->query('appointment_id');

        if (!$appointment_id) {
            return redirect()->route('appointments.index')
                ->with('error', 'Invalid request');
        }

        $appointment = Appointment::with('payment', 'doctor')->findOrFail($appointment_id);

        Gate::authorize('success', $appointment);

        return view('stripe.success', [
            'appointment' => $appointment
        ]);
    }


    public function cancel(Request $request)
    {
        $appointment_id = $request->query('appointment_id');

        if (!$appointment_id) {
            return redirect()->route('appointments.index')
                ->with('error', 'Invalid request');
        }

        $appointment = Appointment::with('payment', 'doctor')->findOrFail($appointment_id);

        $appointment->payment->update([
            'payment_status' =>  PaymentStatusEnum::Cancelled
        ]);

        return view('stripe.cancel', [
            'appointment' => $appointment
        ]);
    }
}
