<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appointments = Appointment::doesntHave('payment')->get();

        if ($appointments->isEmpty()) {
            $this->command->warn('Ensure you have Appointments first!');
            return;
        }

        foreach ($appointments as $appointment) {
            Payment::factory()->create([
                'appointment_id' => $appointment->id,
                'amount' => $appointment->doctor->ticket_price
            ]);
        }
    }
}
