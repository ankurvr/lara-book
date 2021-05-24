<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventGuests extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'first_name', 'last_name', 'email'];

    public function booking() {
        return $this->belongsTo(EventBooking::class);
    }
}
