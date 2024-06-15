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
            'isSold' => 'required|boolean',
            'eventID' => 'required|exists:events,eventID',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ticket = Ticket::create($request->all());
        return response()->json($ticket, 201);
    }

    public function show($id)
    {
        return Ticket::findOrFail($id);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'seat' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'isSold' => 'required|boolean',
            'eventID' => 'required|exists:events,eventID',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ticket->update($request->all());
        return response()->json($ticket, 200);
    }


    public function destroy($id)
    {
        Ticket::findOrFail($id)->delete();
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
        $ticket->update(['isSold' => false]);
        return response()->json(['message' => 'Ticket cancelled successfully'], 200);
    }

}
