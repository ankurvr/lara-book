<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => "Concert by " . $this->faker->name(),
            'description' => $this->faker->text(),
            'slot_duration' => $this->faker->randomElement([10, 20, 30, 40]),
            'slot_qty' => $this->faker->randomElement([2, 4, 5]),
            'running_days' => $this->faker->randomElement([5, 10, 15, 20, 30]),
            'preparation_time' => $this->faker->randomElement([0, 20, 30, 60, 90, 120])
        ];
    }
}
