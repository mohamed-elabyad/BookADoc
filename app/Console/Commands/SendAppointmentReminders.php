<?php

namespace App\Console\Commands;

use App\Enums\StatusEnum;
use App\Models\Appointment;
use App\Notifications\AppointmentReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications for appointments within 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        $appointments = Appointment::where('date', $tomorrow)
            ->where('status', StatusEnum::Confirmed->value)
            ->with(['user', 'doctor'])
            ->get();


        $sent_count = 0;

        foreach ($appointments as $appointment) {
            $appointment->user->notify(
                new AppointmentReminderNotification($appointment)
            );

            $sent_count++;
        }


        $this->info("Sent {$sent_count} appointment reminders for tomorrow.");

        return Command::SUCCESS;
    }
}
