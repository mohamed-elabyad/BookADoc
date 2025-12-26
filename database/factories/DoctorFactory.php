<?php

namespace Database\Factories;

use App\Enums\SpecialtyEnum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $work_from = Carbon::createFromTime(fake()->numberBetween(9, 15), 0);
        $work_to   = (clone $work_from)->addHours(fake()->numberBetween(4, 8));

        return [
            'specialty' => fake()->randomElement(SpecialtyEnum::values()),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'work_from' => $work_from->format('H:i:s'),
            'work_to'   => $work_to->format('H:i:s'),
            'active' => fake()->boolean(80),
            'image' => 'https://randomuser.me/api/portraits/men/' . fake()->numberBetween(1, 90) . '.jpg',
            'user_id'   => User::factory()->state(['role' => 'doctor'])->create()->id,
            'license' => null,
            'degree' => null,
            'ticket_price' => fake()->randomElement(range(50, 1000, 50)),
            'bio' => fake()->paragraph(2),
        ];
    }
}
