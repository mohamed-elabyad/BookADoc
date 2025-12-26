<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = Doctor::where('active', 1)->get();
        $users = User::where('role', 'user')->get();

        if ($doctors->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Ensure you have active doctors and users first!');
            return;
        }

        Appointment::factory(2000)
            ->recycle($doctors)
            ->recycle($users)
            ->create();

    }
}
