<?php
/**
 * Created by PhpStorm.
 * User: ankurr
 * Date: 23/5/21
 * Time: 6:58 PM
 */

/**
 * @param array $returnArray
 * @param string $startTime
 * @param string $endTime
 * @param int $interval
 * @param string|null $skipTime
 */
function createTimeSlots(array &$returnArray, string $startTime, string $endTime, int $interval, string $skipTime = null)
{
    $startTime = strtotime(date('Y-m-d ') . $startTime);
    $endTime = strtotime(date('Y-m-d ') . $endTime);
    $skipTime = strtotime(date('Y-m-d ') . $skipTime);

    $addMinutes = $interval * 60;

    while ($startTime <= $endTime) {
        $slotStartTime = date("G:i", $startTime);
        $slotEndTime = date("G:i", $startTime + $addMinutes);
        $startTime += $addMinutes;

        if ($startTime > $endTime) {
            break;
        }

        if ($skipTime && ($startTime < $skipTime || ($startTime - $skipTime) <= $addMinutes)) {
            continue;
        }
        $returnArray[$slotStartTime] = [
            'start' => $slotStartTime,
            'end' => $slotEndTime
        ];
    }
}

/**
 * @param $availableSlots
 * @param $unavailableSlots
 * @return array
 */
function removeUnavailableSlots(array $availableSlots, array $unavailableSlots)
{
    $avlStartTime = strtotime(date('Y-m-d ') . $availableSlots['start']);
    $avlEndTime = strtotime(date('Y-m-d ') . $availableSlots['end']);

    $unavlStartTime = strtotime(date('Y-m-d ') . $unavailableSlots['start']);
    $unavlEndTime = strtotime(date('Y-m-d ') . $unavailableSlots['end']);

    $slots = [];

    if ($avlStartTime < $unavlStartTime) {
        $slots[] = [
            'start' => $availableSlots['start'],
            'end' => $unavailableSlots['start']
        ];
        if ($avlEndTime > $unavlEndTime) {
            $slots[] = [
                'start' => $unavailableSlots['end'],
                'end' => $availableSlots['end']
            ];
        }
    }
    return $slots;
}

