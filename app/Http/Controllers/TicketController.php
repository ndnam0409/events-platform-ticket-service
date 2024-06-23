<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="Events Platform Tickets API",
 *     version="1.0.0",
 *     description="API documentation for the Events Platform Tickets service"
 * )
 */

/**
 * @OA\Schema(
 *     schema="Ticket",
 *     type="object",
 *     title="Ticket",
 *     properties={
 *         @OA\Property(property="id", type="integer", description="ID of the ticket"),
 *         @OA\Property(property="location", type="string", description="Location of the ticket"),
 *         @OA\Property(property="area", type="string", description="Area of the ticket"),
 *         @OA\Property(property="seat", type="string", description="Seat of the ticket"),
 *         @OA\Property(property="price", type="number", format="float", description="Price of the ticket"),
 *         @OA\Property(property="eventID", type="integer", description="ID of the event"),
 *         @OA\Property(property="isSold", type="boolean", description="Sold status of the ticket")
 *     }
 * )
 */


class TicketController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tickets",
     *     summary="Get all tickets",
     *     @OA\Response(
     *         response=200,
     *         description="A list of tickets",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Ticket")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $tickets = Ticket::all();
        return response()->json($tickets);
    }

    /**
     * @OA\Post(
     *     path="/api/tickets",
     *     summary="Create a new ticket",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Ticket")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ticket created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Ticket")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Ticket is already sold",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/tickets/{eventID}",
     *     summary="Get tickets for an event",
     *     @OA\Parameter(
     *         name="eventID",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of tickets for the event",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Ticket")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No tickets found for this event",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     */
    public function show($eventID)
    {
        $tickets = Ticket::where('eventID', $eventID)->get();

        if ($tickets->isEmpty()) {
            return response()->json(['message' => 'No tickets found for this event'], 404);
        }
        return response()->json($tickets, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/tickets/{ticket}",
     *     summary="Update a ticket",
     *     @OA\Parameter(
     *         name="ticket",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Ticket")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Ticket")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="The seat is already sold for this event",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/tickets/{id}",
     *     summary="Delete a ticket",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Ticket deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Cannot delete a sold ticket",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->isSold) {
            return response()->json(['error' => 'Cannot delete a sold ticket.'], 403);
        }

        $ticket->delete();
        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     *     path="/api/tickets/purchase",
     *     summary="Purchase a ticket",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Ticket")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ticket purchased successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Ticket")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="This ticket is already sold",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/tickets/{ticket}/cancel",
     *     summary="Cancel a ticket",
     *     @OA\Parameter(
     *         name="ticket",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket cancelled successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ticket is not sold yet",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     */
    public function cancel(Ticket $ticket)
    {
        if (!$ticket->isSold) {
            return response()->json(['error' => 'Ticket is not sold yet.'], 400);
        }

        $ticket->update(['isSold' => false]);
        return response()->json(['message' => 'Ticket cancelled successfully'], 200);
    }
}
