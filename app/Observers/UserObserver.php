<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    public function created(User $user): void
    {
        $this->clearCache();
    }

    public function updated(User $user): void
    {
        $this->clearCache();
    }

    public function deleted(User $user): void
    {
        $this->clearCache();
    }

    protected function clearCache(): void
    {
        Cache::tags(['stats', 'user'])->flush();
        Cache::tags(['filament', 'badge'])->flush();
    }
}
