<?php

namespace Modules\Teams\app\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Teams\app\Entities\Team;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\Bookings\app\Entities\Booking;

class TeamController extends Controller
{
    public function index()
    {
        // Return all teams for current tenant
        $teams = Team::all();
        return response()->json($teams);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $team = Team::create($data); // tenant_id auto-set by trait

        return response()->json($team, 201);
    }

    public function show($id)
    {
        $team = Team::findOrFail($id);
        return response()->json($team);
    }
    public function generateSlots(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);

        $from = Carbon::parse($request->query('from'));
        $to   = Carbon::parse($request->query('to'));

        $slots = [];

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday

            $availabilities = $team->availability()->where('day_of_week', $dayOfWeek)->get();

            foreach ($availabilities as $a) {
                $slotStart = Carbon::parse($a->start_time);
                $slotEndLimit = Carbon::parse($a->end_time);

                while ($slotStart->lt($slotEndLimit)) {
                    $slotEnd = $slotStart->copy()->addHour();

                    // Ensure we don't go past availability end time
                    if ($slotEnd->gt($slotEndLimit)) {
                        $slotEnd = $slotEndLimit->copy();
                    }

                    // Check if slot overlaps any booking
                    $exists = Booking::where('team_id', $team->id)
                        ->where('date', $date->format('Y-m-d'))
                        ->where(function ($q) use ($slotStart, $slotEnd) {
                            $q->where(function ($query) use ($slotStart, $slotEnd) {
                                $query->where('start_time', '<', $slotEnd->format('H:i'))
                                    ->where('end_time', '>', $slotStart->format('H:i'));
                            });
                        })->exists();

                    if (!$exists) {
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

        return response()->json($slots);
    }
}
