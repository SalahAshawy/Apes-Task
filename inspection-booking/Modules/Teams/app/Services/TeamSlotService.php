<?php

namespace Modules\Teams\app\Services;

use Modules\Teams\app\Entities\Team;
use Modules\Bookings\app\Entities\Booking;
use Carbon\Carbon;

class TeamSlotService
{
    protected Team $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    /**
     * Generate 1-hour slots for a date range, excluding booked slots
     */
    public function generateSlots(string $from, string $to): array
    {
        $from = Carbon::parse($from);
        $to   = Carbon::parse($to);

        $slots = [];

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeek;

            $availabilities = $this->team->availability()->where('day_of_week', $dayOfWeek)->get();

            foreach ($availabilities as $a) {
                $slotStart = Carbon::parse($a->start_time);
                $slotEndLimit = Carbon::parse($a->end_time);

                while ($slotStart->lt($slotEndLimit)) {
                    $slotEnd = $slotStart->copy()->addHour();
                    if ($slotEnd->gt($slotEndLimit)) {
                        $slotEnd = $slotEndLimit->copy();
                    }

                    $exists = Booking::where('team_id', $this->team->id)
                        ->where('date', $date->format('Y-m-d'))
                        ->where(function ($q) use ($slotStart, $slotEnd) {
                            $q->where('start_time', '<', $slotEnd->format('H:i'))
                              ->where('end_time', '>', $slotStart->format('H:i'));
                        })->exists();

                    if (! $exists) {
                        $slots[] = [
                            'date'       => $date->format('Y-m-d'),
                            'start_time' => $slotStart->format('H:i'),
                            'end_time'   => $slotEnd->format('H:i'),
                        ];
                    }

                    $slotStart->addHour();
                }
            }
        }

        return $slots;
    }
}
