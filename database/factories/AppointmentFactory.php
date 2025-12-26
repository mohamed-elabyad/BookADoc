<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use App\Filament\Resources\Doctors\Schemas\DoctorForm;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'user_id' => User::factory(),
            'date' => Carbon::now()->addDays(fake()->numberBetween(0, 30))->format('Y-m-d'),
            'time' => Carbon::createFromTime(fake()->numberBetween(9, 15), 0)->format('H:i:s'),
            'status' => fake()->randomElement(StatusEnum::values()),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Appointment $appointment) {
            $doctor = $appointment->doctor;

            if ($doctor) {
                $start = Carbon::parse($doctor->work_from);
                $end = Carbon::parse($doctor->work_to);
                $slots = [];

                while ($start < $end) {
                    $slots[] = $start->format('H:i');
                    $start->addMinutes(30);
                }

                $appointment->update([
                    'time' => fake()->randomElement($slots)
                ]);
            }
        });
    }
}
