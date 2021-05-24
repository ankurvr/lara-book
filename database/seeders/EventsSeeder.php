<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventSchedule;
use Illuminate\Database\Seeder;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $events = Event::factory()->count(5)->create();
        $events->each(function($event) {
            // Regular Day
            EventSchedule::factory()->create([
                'event_id' => $event['id'],
                'allowed' => 1,
                'days' => [1, 2, 3, 4, 5, 6],
                'start_time' => '08:00:00',
                'end_time' => '20:00:00'
            ]);
            // Break Time
            EventSchedule::factory()->create([
                'event_id' => $event['id'],
                'allowed' => 0,
                'days' => [1, 2, 3, 4, 5, 6],
                'start_time' => '13:00:00',
                'end_time' => '14:00:00'
            ]);
            // Holiday
            EventSchedule::factory()->create([
                'event_id' => $event['id'],
                'allowed' => 0,
                'days' => [0, 7],
                'start_time' => '00:00:00',
                'end_time' => '23:59:59'
            ]);
        });
    }
}
