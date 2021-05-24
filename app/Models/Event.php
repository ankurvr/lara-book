<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = ['name', 'description', 'slot_duration', 'running_days', 'preparation_time'];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s'
    ];

    public function eventSchedules()
    {
        return $this->hasMany(EventSchedule::class, 'event_id');
    }

    public function eventBookings()
    {
        return $this->hasMany(EventBooking::class, 'event_id');
    }
}
