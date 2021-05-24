<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventBooking;
use App\Models\EventGuests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventsController extends Controller
{
    public function getEvents()
    {

        $events = Event::whereRaw('current_timestamp() <= DATE_ADD(events.created_at, INTERVAL events.running_days DAY)')
            ->with('eventSchedules')
            ->with('eventBookings')
            ->get()->toArray();

        foreach ($events as &$event) {
//            $event['allowedTimeSlots'] = $this->getAllowedTimeSlots($event);
            $event['availableSlots'] = $this->getAvailableTimeSlots($event);

//            unset($event['event_schedules']);
            unset($event['event_bookings']);
        }

        return response()->json($events);
    }

    public function bookEvent(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d|after_or_equal:' . date('Y-m-d'),
            'time' => 'required|date_format:H:i',
            'guests' => 'required|array',
            'guests.*.first_name' => 'required|string',
            'guests.*.last_name' => 'required|string',
            'guests.*.email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $event = Event::WhereRaw('current_timestamp() <= DATE_ADD(events.created_at, INTERVAL events.running_days DAY)')
            ->where('id', $id)
            ->with('eventSchedules')
            ->with(['eventBookings' => function ($query) use ($request) {
                $query->where('date', $request->input('date'));

            }])
            ->first();

        if (!$event) {
            return response()->json(['success' => false], 422);
        }
        $event = $event->toArray();

        $bookingDateTimestamp = strtotime($request->input('date') . '00:00:00');

        $bufferMinutes = 0;
        $skipTillNowFlag = false;
        if (date('Y-m-d', strtotime($event['created_at'])) === $request->input('date')) {
            // Compute event created day today slots
            $bufferMinutes = $event['preparation_time'] * 60;
            $skipTillNowFlag = true;
        } else if(date('Y-m-d') === $request->input('date')) {
            // Compute today slots
            $skipTillNowFlag = true;
        }
        $allowedSlots = $this->getAllowedTimeSlots($event, date('w', $bookingDateTimestamp), $bufferMinutes, $skipTillNowFlag);
        if (!isset($allowedSlots[$request->input('time')])) {
            return response()->json(['success' => false, 'message' => 'Selected slot is invalid.'], 422);
        }

        if (count($event['event_bookings'])) {
            $bookings = array_values($this->getAvailableTimeSlots($event))[0];

            if (isset($bookings[$request->input('time')]) && $bookings[$request->input('time')] === 0) {
                return response()->json(['success' => false, 'message' => 'Selected slot is fully booked.'], 422);
            }
        }

        $booking = new EventBooking;
        $booking->event_id = $id;
        $booking->date = $request->input('date');
        $booking->slot = $request->input('time');
        $booking->save();

        foreach ($request->input('guests') as $guest) {
            EventGuests::insert([
                'booking_id' => $booking->id,
                'first_name' => $guest['first_name'],
                'last_name' => $guest['last_name'],
                'email' => $guest['email']
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * @param $event
     * @param int $dayNumber
     * @param int $bufferMinutes
     * @param bool $skipTillNow
     * @return array|mixed
     */
    private function getAllowedTimeSlots($event, $dayNumber = -1, $bufferMinutes = 0, $skipTillNow = false)
    {
        $finalTimeSlots = [];
        $skipTime = null;

        $todayDayNum = ($dayNumber > -1 ? $dayNumber : 0);
        for (; $todayDayNum < 7; $todayDayNum++) {
            $scheduleUnavailable = [];
            $scheduleAvailable = [];
            foreach ($event['event_schedules'] as $schedule) {
                if (in_array($todayDayNum, $schedule['days'])) {
                    if ($schedule['allowed']) {
                        if($bufferMinutes > 0) {
                            // Adding buffer (Preparation time) in event created time
                            $startTime = strtotime($event['created_at']) + $bufferMinutes;
                        } else {
                            $startTime = strtotime(date('Y-m-d ') . $schedule['start_time']);
                        }
                        $scheduleAvailable = [
                            'start' => date('H:i:s', $startTime),
                            'end' => $schedule['end_time']
                        ];
                    } else {
                        $scheduleUnavailable = [
                            'start' => $schedule['start_time'],
                            'end' => $schedule['end_time']
                        ];
                    }
                }
            }
            if($skipTillNow) {
                // Skip past time for today
                $skipTime = date('H:i:00');
            }
            if (count($scheduleUnavailable) && count($scheduleAvailable)) {
                $timeSlots = removeUnavailableSlots($scheduleAvailable, $scheduleUnavailable);
            } else {
                $timeSlots = $scheduleAvailable;
            }
            $finalTimeSlots['day_' . $todayDayNum] = [];
            foreach ($timeSlots as $slot) {
                createTimeSlots($finalTimeSlots['day_' . $todayDayNum], $slot['start'], $slot['end'], $event['slot_duration'], $skipTime);
            }
            if ($dayNumber > -1) {
                break;
            }
        }

        return $dayNumber > -1 ? $finalTimeSlots['day_' . $dayNumber] : $finalTimeSlots;
    }

    /**
     * @param $event
     * @return array
     */
    private function getAvailableTimeSlots($event)
    {
        $bookings = [];
        foreach ($event['event_bookings'] as $booking) {
            $slotTime = date('H:i', strtotime(date('Y-m-d ') . $booking['slot']));
            if (!isset($bookings[$booking['date']]) || !isset($bookings[$booking['date']][$slotTime])) {
                $bookings[$booking['date']][$slotTime] = $event['slot_qty'];
            }
            $bookings[$booking['date']][$slotTime] -= 1;
        }
        return $bookings;
    }
}
