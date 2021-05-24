<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSchedule extends Model
{
    use HasFactory;

    protected $table = 'event_schedules';

    public $timestamps = false;

    protected $fillable = ['event_id', 'allowed', 'days', 'start_time', 'end_time'];

    protected $casts = [
        'days' => 'array',
    ];

    public function event() {
        return $this->belongsTo(Event::class);
    }
}
