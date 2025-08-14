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

        for ($date = $from; $date->lte($to); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeek; // 0 = Sunday

            $availabilities = $team->availability()->where('day_of_week', $dayOfWeek)->get();

            foreach ($availabilities as $a) {
                $start = Carbon::parse($a->start_time);
                $end   = Carbon::parse($a->end_time);

                while ($start->lt($end)) {
                    $slotEnd = $start->copy()->addHour();

                    // Check if slot overlaps any booking
                    $exists = Booking::where('team_id', $team->id)
                        ->where('date', $date->format('Y-m-d'))
                        ->where(function ($q) use ($start, $slotEnd) {
                            $q->whereBetween('start_time', [$start->format('H:i'), $slotEnd->format('H:i')])
                                ->orWhereBetween('end_time', [$start->format('H:i'), $slotEnd->format('H:i')]);
                        })->exists();

                    if (!$exists) {
                        $slots[] = [
                            'date' => $date->format('Y-m-d'),
                            'start_time' => $start->format('H:i'),
                            'end_time' => $slotEnd->format('H:i'),
                        ];
                    }

                    $start->addHour();
                }
            }
        }

        return response()->json($slots);
    }
}
