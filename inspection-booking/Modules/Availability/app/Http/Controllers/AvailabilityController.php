<?php

namespace Modules\Availability\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Availability\app\Entities\TeamAvailability;
use Modules\Availability\app\Http\Requests\StoreAvailabilityRequest;
use Modules\Teams\app\Entities\Team;

class AvailabilityController extends Controller
{
    // Set recurring weekly availability for a team
    public function store(StoreAvailabilityRequest $request, $teamId)
    {
        $team = Team::findOrFail($teamId);
        $data = $request->validated();

        $availability = TeamAvailability::create([
            'team_id'     => $team->id,
            'day_of_week' => $data['day_of_week'],
            'start_time'  => $data['start_time'],
            'end_time'    => $data['end_time'],
            'tenant_id'   => $team->tenant_id,
        ]);

        return response()->json($availability, 201);
    }
    // List all availabilities for a team
    public function index($teamId)
    {
        $team = Team::findOrFail($teamId);
        $availabilities = $team->availability()->get();

        return response()->json($availabilities);
    }
}
