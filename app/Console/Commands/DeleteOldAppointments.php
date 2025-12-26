<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteOldAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete appointments older than a year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year_ago = Carbon::now()->subYear();

        $appointments = Appointment::where('date', '<', $year_ago);

        $deleted_count = 0;

        foreach($appointments as $appointment){
            $appointment?->payment->delete();

            $appointment->delete();

            $deleted_count ++;
        }
        $this->info("Deleted {$deleted_count} old appointments (older than a year).");

        return Command::SUCCESS;
    }
}
