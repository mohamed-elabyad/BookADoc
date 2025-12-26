<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test',
            'email' => 'test@example.com',
            'role' => 'admin'
        ]);




        $this->call([
            UserSeeder::class,
            DoctorSeeder::class,
            AppointmentSeeder::class,
            PaymentSeeder::class,
            ChatSeeder::class
        ]);
    }
}
