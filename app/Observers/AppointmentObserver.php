<?php

namespace App\Observers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Cache;

class AppointmentObserver
{

    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $this->clearCache($appointment);
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        $this->clearCache($appointment);
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        $this->clearCache($appointment);
    }

    /**
     * Clear cache for both user and doctor
     */
    protected function clearCache(Appointment $appointment): void
    {
        Cache::forget('doctor:' . $appointment->doctor_id . ':appointments');

        Cache::forget('user:' . $appointment->user_id . ':appointments');

        Cache::tags(['stats', 'appointment'])->flush();
        Cache::tags(['appointments', 'charts'])->flush();
        Cache::tags(['appointments'])->flush();
        Cache::tags(['filament', 'badge'])->flush();
    }
}
