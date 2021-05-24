<?php

namespace Tests\Feature;

use Tests\TestCase;

class EventSlotsTest extends TestCase
{
    /**
     * An available events test
     *
     * @return void
     */
    public function testEventsList()
    {
        $response = $this->get('/events');

        $response->assertStatus(200);
    }
}
