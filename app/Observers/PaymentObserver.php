<?php

namespace App\Observers;

use App\Models\Payment;
use Illuminate\Support\Facades\Cache;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        $this->clearCache($payment);
    }

    public function updated(Payment $payment): void
    {
        if ($payment->isDirty(['payment_status', 'amount', 'paid_at'])) {
            $this->clearCache($payment);
        }
    }

    public function deleted(Payment $payment): void
    {
        $this->clearCache($payment);
    }

    protected function clearCache(Payment $payment): void
    {
        Cache::tags(['revenue'])->flush();
        Cache::tags(['payments', 'charts'])->flush();
        Cache::tags(['filament', 'badge'])->flush();
    }
}
