<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventBooking extends Model
{
    use HasFactory;

    protected $table = 'event_bookings';

    public function guests() {
        return $this->hasMany(EventGuests::class, 'booking_id');
    }

    public function event() {
        return $this->belongsTo(Event::class);
    }
}
