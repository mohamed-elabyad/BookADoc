<?php

namespace App\Console\Commands;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteUnpaidAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:delete-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete online appointments that are not confirmed and older than 3 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $three_days_ago = Carbon::now()->subDays(3);

        $appointments = Appointment::whereHas('payment', function ($query) use ($three_days_ago) {
            $query->where('payment_method', PaymentMethodEnum::Online->value)
                ->where('payment_status', '!=', PaymentStatusEnum::Confirmed->value);
        })
            ->where('created_at', '<', $three_days_ago)
            ->get();

        $deleted_count = 0;

        foreach ($appointments as $appointment) {
            $appointment?->payment()->delete();

            $appointment->delete();

            $deleted_count++;
        }

        $this->info("Deleted {$deleted_count} unpaid online appointments (older than 3 days).");

        return Command::SUCCESS;
    }
}
