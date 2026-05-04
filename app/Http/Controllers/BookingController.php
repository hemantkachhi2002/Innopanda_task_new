<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBookingRequest;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Booking::with(['user', 'resource']);

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $bookings = $query->paginate(15);
        return response()->json($bookings);
    }

    public function store(StoreBookingRequest $request)
    {
        $user = $request->user();
        
        $start = $request->start_time;
        $end = $request->end_time;
        
        // Conflict prevention logic: 
        // new_start < existing_end AND new_end > existing_start
        $conflict = Booking::where('resource_id', $request->resource_id)
            ->where('date', $request->date)
            ->where(function($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                      ->where('end_time', '>', $start);
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Time slot already booked'
            ], 409); // 409 Conflict
        }

        $booking = Booking::create([
            'user_id' => $user->id,
            'resource_id' => $request->resource_id,
            'date' => $request->date,
            'start_time' => $start,
            'end_time' => $end,
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    public function cancel($id, Request $request)
    {
        // Explicitly find the booking to prevent HTML 404 errors in Postman
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found! You must create Booking ID ' . $id . ' first before you can delete it.'
            ], 404);
        }

        $user = $request->user();
        
        if (!$user->isAdmin() && $booking->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $booking->delete();
        return response()->json(['message' => 'Booking cancelled successfully']);
    }
}
