<?php

namespace App\Services;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Appointment;
use App\Models\Payment;
use App\Notifications\DoctorNewAppointmentNotification;
use App\Notifications\PaymentFailedNotification;
use App\Notifications\PaymentSucceededNotification;
use Illuminate\Support\Facades\Gate;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Appointment $appointment)
    {
        Gate::authorize('checkout', $appointment);

        // Calculate price in USD
        $price_in_EGP = $appointment->payment->amount;
        $exchange_rate = 47;
        $price_in_USD = round($price_in_EGP / $exchange_rate, 2);

        $session = Session::create([
            'payment_method_types' => config('services.stripe.payment_methods', ['card']),
            'line_items' => [[
                'price_data' => [
                    'currency' => config('services.stripe.currency', 'usd'),
                    'product_data' => [
                        'name' => 'Booking appointment with Dr. ' . $appointment->doctor->name,
                        'description' => 'Date: ' . $appointment->date->format('j/n/Y') . ' at ' . $appointment->time->format('g:i A'),
                    ],
                    'unit_amount' => $price_in_USD * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',

            'success_url' => route('stripe.success') . '?appointment_id=' . $appointment->id,
            'cancel_url' => route('stripe.cancel') . '?appointment_id=' . $appointment->id,

            'metadata' => [
                'appointment_id' => $appointment->id,
                'user_id' => $appointment->user_id,
                'payment_id' => $appointment->payment->id,
            ],
        ]);

        $appointment->payment->update([
            'stripe_session_id' => $session->id,
            'payment_status' => PaymentStatusEnum::Pending
        ]);

        return $session;
    }

    public function confirmPayment(object $event)
    {
        $session = $event->data->object;
        $session_id = $session->id;
        $stripe_payment_intent_id = $session->payment_intent;

        $payment = Payment::where('stripe_session_id', $session_id)->first();

        if (!$payment) {
            return null;
        }

        if ($payment->isConfirmed()) {
            return $payment;
        }

        $payment->update([
            'payment_status' =>  PaymentStatusEnum::Confirmed,
            'stripe_payment_intent_id' => $stripe_payment_intent_id,
            'paid_at' => now(),
        ]);

        $payment->appointment->update([
            'status' => 'confirmed'
        ]);

        Log::info('Payment confirmed', [
            'payment_id' => $payment->id,
            'appointment_id' => $payment->appointment_id
        ]);

        $appointment = $payment->appointment;

        $payment->appointment->user->notify(
            new PaymentSucceededNotification($payment)
        );

        $payment->appointment->doctor->user->notify(
            new DoctorNewAppointmentNotification($appointment)
        );

        return $payment;
    }

    public function expiredPayment(object $event)
    {
        $session = $event->data->object;
        $session_id = $session->id;

        $payment = Payment::where('stripe_session_id', $session_id)->first();

        if (!$payment) {
            return null;
        }

        $payment->update([
            'payment_status' => PaymentStatusEnum::Failed,
        ]);

        Log::warning('Payment expired', [
            'payment_id' => $payment->id,
            'appointment_id' => $payment->appointment_id
        ]);

        $payment->appointment->user->notify(
            new PaymentFailedNotification($payment)
        );

        return $payment;
    }

    public function failedPayment(object $event)
    {
        $payment_intent = $event->data->object;

        $appointment_id = $payment_intent->metadata->appointment_id ?? null;

        if (!$appointment_id) {
            Log::error('Appointment id not found in payment intent metadata');
            return null;
        }

        $payment = Payment::whereHas('appointment', function ($query) use ($appointment_id) {
            $query->where('id', $appointment_id);
        })->first();

        if (!$payment) {
            return null;
        }

        $payment->update([
            'payment_status' =>  PaymentStatusEnum::Failed,
            'notes' => $payment_intent->last_payment_error->message ?? 'Payment failed',
        ]);

        Log::error('Payment failed', [
            'payment_id' => $payment->id,
            'appointment_id' => $payment->appointment_id,
            'error' => $payment_intent->last_payment_error->message ?? 'Unknown'
        ]);

        $payment->appointment->user->notify(
            new PaymentFailedNotification($payment)
        );

        return $payment;
    }
}
