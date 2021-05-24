<?php

namespace Database\Factories;

use App\Models\EventSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventScheduleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventSchedule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'event_id' => null,
            'allowed' => 1,
            'days' => json_encode([1, 2, 3, 4, 5, 6]),
            'start_time' => '08:00:00',
            'end_time' => '20:00:00'
        ];
    }

    public function breakTimeDefinition() {
        return [
            'event_id' => null,
            'allowed' => 0,
            'days' => json_encode([1, 2, 3, 4, 5, 6]),
            'start_time' => '13:00:00',
            'end_time' => '14:00:00'
        ];
    }

    public function holidayDefinition() {
        return [
            'event_id' => null,
            'allowed' => 0,
            'days' => json_encode([0, 7]),
            'start_time' => '00:00:00',
            'end_time' => '23:59:59'
        ];
    }
}
