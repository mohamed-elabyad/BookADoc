<?php

namespace Database\Factories;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $conversation = Conversation::inRandomOrder()->first();

        $conversation->load(['doctor.user', 'user']);

        $sender_id = fake()->randomElement([
            $conversation->user_id,
            $conversation->doctor->user_id
        ]);

        return [
            'conversation_id' => $conversation->id,
            'sender_id' => $sender_id,
            'message' => fake()->sentence(fake()->numberBetween(5, 20)),
            'is_read' => fake()->boolean(70),
            'created_at' => now()->subMinutes(fake()->numberBetween(1, 1440)),
        ];
    }
}
