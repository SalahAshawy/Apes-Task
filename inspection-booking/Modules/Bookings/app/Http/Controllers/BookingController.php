<?php

namespace Modules\Bookings\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Modules\Bookings\app\Entities\Booking;
use Modules\Teams\app\Entities\Team;
use Carbon\Carbon;

class BookingController extends Controller
{
    // List bookings for authenticated user
    public function index(Request $request)
    {
        $user = $request->user();
        $bookings = Booking::where('user_id', $user->id)->get();
        return response()->json($bookings);
    }

    // Create a booking
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'team_id'    => 'required|exists:teams,id',
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ]);

        // Check for conflicts
        $exists = Booking::where('team_id', $data['team_id'])
            ->where('date', $data['date'])
            ->where(function ($q) use ($data) {
                $q->where('start_time', '<', $data['end_time']) 
                    ->where('end_time', '>', $data['start_time']); 
            })->exists();


        if ($exists) {
            return response()->json(['message' => 'Time slot already booked'], 409);
        }

        $booking = Booking::create([
            'user_id'    => $user->id,
            'team_id'    => $data['team_id'],
            'tenant_id'  => $user->tenant_id,
            'date'       => $data['date'],
            'start_time' => $data['start_time'],
            'end_time'   => $data['end_time'],
        ]);

        return response()->json($booking, 201);
    }

    // Cancel booking
    public function destroy($id, Request $request)
    {
        $user = $request->user();
        $booking = Booking::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $booking->delete();
        return response()->json(['message' => 'Booking canceled']);
    }
}
