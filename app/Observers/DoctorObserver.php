<?php

namespace App\Observers;

use App\Models\Doctor;
use Illuminate\Support\Facades\Cache;
use PhpParser\Comment\Doc;

class DoctorObserver
{
    public function created(Doctor $doctor): void
    {
        $this->clearCache($doctor);
    }

    public function updated(Doctor $doctor): void
    {
        $this->clearCache($doctor);
    }

    public function deleted(Doctor $doctor): void
    {
        $this->clearCache($doctor);

        $this->clearCache($doctor);
        $this->clearCache($doctor);
    }

    public function clearCache(Doctor $doctor){
Cache::tags(['doctors'])->flush();

        Cache::tags(['stats', 'doctor'])->flush();
        Cache::tags(['filament', 'badge'])->flush();
    }
}
