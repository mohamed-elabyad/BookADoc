<?php

namespace Database\Factories;

use App\Enums\PaymentMethodEnum;
use App\Enums\StatusEnum;
use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'amount' => fake()->randomElement(range(50, 1000, 50)),
            'currency' => 'EGP',
            'payment_method' => fake()->randomElement(['cash', 'online']),
            'payment_status' => 'pending',
            'stripe_session_id' => null,
            'stripe_payment_intent_id' => null,
            'paid_at' => null,
            'notes' => null,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Payment $payment) {
            $appointment = $payment->appointment;
            $method = $payment->payment_method;

            if ($appointment->status === StatusEnum::Completed) {
                $status = 'confirmed';
            } elseif ($appointment->status === StatusEnum::Confirmed && $method === PaymentMethodEnum::Online) {
                $status = 'confirmed';
            } elseif ($method === PaymentMethodEnum::Cash) {
                $status = 'pending';
            } else {
                $status = fake()->randomElement(['pending', 'failed', 'cancelled']);
            }

            $updateData['payment_status'] = $status;

            if ($status === 'confirmed') {
                $updateData['paid_at'] = fake()->dateTimeBetween('-30 days', 'now');

                if ($method === PaymentMethodEnum::Online) {
                    $updateData['stripe_session_id'] = 'cs_test_' . fake()->uuid();
                    $updateData['stripe_payment_intent_id'] = 'pi_' . fake()->uuid();
                }
            }

            $payment->update($updateData);
        });
    }
}
