<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $appointment = Appointment::whereIn('status', ['confirmed', 'completed'])
            ->inRandomOrder()
            ->first();

        return [
            'doctor_id' => $appointment->doctor_id,
            'user_id' => $appointment->user_id,
            'appointment_id' => $appointment->id,
            'last_message_at' => now()->subMinutes(fake()->numberBetween(1, 1440)),
        ];
    }
}
