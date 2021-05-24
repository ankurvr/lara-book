<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class EventBookingTest extends TestCase
{
    private $guestData = [
        ['first_name' => 'Alpha', 'last_name' => 'Romeo', 'email' => 'alpha.romeo@gmail.com', 'd' => 1],
        ['first_name' => 'Alpha 2', 'last_name' => 'Romeo', 'email' => 'alpha.romeo2@gmail.com', 'd' => 1],
        ['first_name' => 'Alpha 3', 'last_name' => 'Romeo', 'email' => 'alpha.romeo3@gmail.com', 'd' => 1]
    ];

    private $bookingDate;

    public function __construct(string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $datetime = new \DateTime(date('Y-m-d'));
        $datetime->modify('+1 day');
//        $datetime->modify('+1 day');
        $this->bookingDate = $datetime->format('Y-m-d');
    }

    /**
     * A booking test
     *
     * @return void
     */
    public function testValidBooking()
    {
        $response = $this->post('/events/2', [
            'date' => $this->bookingDate,
            'time' => '19:00',
            'guests' => $this->guestData
        ]);

        Log::debug(__FUNCTION__);
        Log::debug($response->getContent());

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function testUnavailableEventBooking()
    {
        $response = $this->post('/events/1', [
            'date' => $this->bookingDate,
            'time' => '08:40',
            'guests' => $this->guestData
        ]);

        Log::debug(__FUNCTION__);
        Log::debug($response->getContent());

        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        $response = $this->post('/events/100', [
            'date' => $this->bookingDate,
            'time' => '02:40',
            'guests' => $this->guestData
        ]);

        Log::debug(__FUNCTION__);
        Log::debug($response->getContent());

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function testInvalidEventSlotBooking()
    {
        $response = $this->post('/events/2', [
            'date' => $this->bookingDate,
            'time' => '16:30',
            'guests' => $this->guestData
        ]);

        Log::debug(__FUNCTION__);
        Log::debug($response->getContent());

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Selected slot is invalid.');
    }

    public function testInvalidBookingData()
    {
        $response = $this->post('/events/2', [
            'date' => $this->bookingDate,
            'time' => '02:40',
            'guests' => $this->guestData
        ]);

        Log::debug(__FUNCTION__);
        Log::debug($response->getContent());


        $response->assertStatus(422)
            ->assertJsonPath('message', 'Selected slot is invalid.');
    }
}
