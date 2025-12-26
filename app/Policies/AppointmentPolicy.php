<?php

namespace App\Policies;

use App\Enums\PaymentStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->value, ['user', 'doctor', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->role->value === 'user' ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        if ($user->role->value === 'doctor' && $appointment->doctor_id === $user->doctor->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        if ($user->role->value === 'doctor' && $appointment->doctor_id === $user->doctor->id) {
            return true;
        }

        if ($user->role->value === 'user' && $appointment->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can checkout (pay for) the appointment.
     */
    public function checkout(User $user, Appointment $appointment): bool
    {
        return $appointment->user_id === $user->id
            && !$appointment->payment()
                ->where('payment_status', PaymentStatusEnum::Confirmed)
                ->exists();
    }

    /**
     * Determine if the user can view the payment success page.
     */
    public function success(User $user, Appointment $appointment): bool
    {
        return $appointment->user_id === $user->id;
    }

    /**
     * Determine if the user can cancel payment.
     */
    public function cancel(User $user, Appointment $appointment): bool
    {
        return $appointment->user_id === $user->id && $appointment->payment
            && !in_array($appointment->payment->payment_status, [
                PaymentStatusEnum::Confirmed,
                PaymentStatusEnum::Cancelled
            ]);
    }
}
