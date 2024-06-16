<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    
    public function index()
    {
        return Ticket::all();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'seat' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'eventID' => 'required|exists:events,eventID',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $existingTicket = Ticket::where('location', $request->location)
                                ->where('area', $request->area)
                                ->where('seat', $request->seat)
                                ->where('eventID', $request->eventID)
                                ->first();

        if ($existingTicket && $existingTicket->isSold) {
            return response()->json(['error' => 'Ticket is already sold.'], 409);
        }

        $ticket = Ticket::updateOrCreate(
            [
                'location' => $request->location,
                'area' => $request->area,
                'seat' => $request->seat,
                'eventID' => $request->eventID,
            ],
            [
                'price' => $request->price,
                'isSold' => true,
            ]
        );
        return response()->json($ticket, 201);
    }

    public function show($eventID)
    {
        $tickets = Ticket::where('eventID', $eventID)->get();

        if ($tickets->isEmpty()) {
            return response()->json(['message' => 'No tickets found for this event'], 404);
        }
        return response()->json($tickets, 200);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'seat' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'isSold' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $existingTicket = Ticket::where('eventID', $ticket->eventID)
                                ->where('location', $request->location)
                                ->where('area', $request->area)
                                ->where('seat', $request->seat)
                                ->where('ticketID', '!=', $ticket->ticketID)
                                ->first();

        if ($existingTicket && $existingTicket->isSold) {
            return response()->json(['error' => 'The seat is already sold for this event.'], 409);
        }

        $ticket->update($request->all());
        return response()->json($ticket, 200);
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->isSold) {
            return response()->json(['error' => 'Cannot delete a sold ticket.'], 403);
        }

        $ticket->delete();
        return response()->json(null, 204);
    }

    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'seat' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'eventID' => 'required|exists:events,eventID',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $existingTicket = Ticket::where('eventID', $request->eventID)
                                ->where('location', $request->location)
                                ->where('area', $request->area)
                                ->where('seat', $request->seat)
                                ->first();

        if ($existingTicket && $existingTicket->isSold) {
            return response()->json(['error' => 'This ticket is already sold.'], 409);
        }

        $ticket = Ticket::create([
            'location' => $request->location,
            'area' => $request->area,
            'seat' => $request->seat,
            'price' => $request->price,
            'isSold' => true,
            'eventID' => $request->eventID,
        ]);
        return response()->json($ticket, 201);
    }
    
    public function cancel(Ticket $ticket)
    {
        if (!$ticket->isSold) {
            return response()->json(['error' => 'Ticket is not sold yet.'], 400);
        }

        $ticket->update(['isSold' => false]);
        return response()->json(['message' => 'Ticket cancelled successfully'], 200);
    }

}
