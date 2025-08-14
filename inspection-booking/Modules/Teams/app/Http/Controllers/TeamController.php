<?php

namespace Modules\Teams\app\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Teams\app\Entities\Team;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\Bookings\app\Entities\Booking;
use Modules\Teams\app\Services\TeamSlotService;

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

        $service = new TeamSlotService($team);
        $slots = $service->generateSlots($request->query('from'), $request->query('to'));

        return response()->json($slots);
    }
}
