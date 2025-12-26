<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appointments = Appointment::whereIn('status', ['confirmed', 'completed'])
            ->with(['doctor.user', 'user'])
            ->get();

        if ($appointments->isEmpty()) {
            $this->command->warn('Ensure you have Appointments first!');
            return;
        }

        $conversations_created = 0;
        $messages_created = 0;

        foreach ($appointments as $appointment) {
            $conversation = Conversation::firstOrCreate(
                [
                    'doctor_id' => $appointment->doctor_id,
                    'user_id' => $appointment->user_id,
                ],
                [
                    'appointment_id' => $appointment->id,
                    'last_message_at' => now()->subDays(fake()->numberBetween(1, 30)),
                ]
            );

            if (!$conversation->wasRecentlyCreated && $conversation->messages()->count() > 0) {
                continue;
            }

            if ($conversation->wasRecentlyCreated) {
                $conversations_created++;
            }

            // نخلق رسالتين فقط في المحادثة
            $baseTime = now()->subDays(fake()->numberBetween(1, 30));

            // رسالة أولى من المستخدم
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $appointment->user_id,
                'message' => fake()->sentence(fake()->numberBetween(5, 15)),
                'is_read' => fake()->boolean(70),
                'created_at' => $baseTime->copy(),
            ]);
            $messages_created++;

            // رسالة ثانية من الدكتور
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $appointment->doctor->user_id,
                'message' => fake()->sentence(fake()->numberBetween(5, 15)),
                'is_read' => fake()->boolean(70),
                'created_at' => $baseTime->copy()->addMinutes(fake()->numberBetween(10, 60)),
            ]);
            $messages_created++;

            // نحدث last_message_at
            $last_message = $conversation->messages()->latest()->first();
            if ($last_message) {
                $conversation->update([
                    'last_message_at' => $last_message->created_at
                ]);
            }
        }
    }
}
