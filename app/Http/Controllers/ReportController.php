<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/revenue",
     *     summary="Get total revenue",
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="Start date for the revenue calculation"
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="End date for the revenue calculation"
     *     ),
     *     @OA\Parameter(
     *         name="eventID",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Event ID to filter the revenue"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Total revenue",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="revenue", type="number", format="float")
     *         )
     *     )
     * )
     */
    public function revenue(Request $request)
    {
        $query = Ticket::where('isSold', true);

        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->input('start_date'));
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->has('eventID')) {
            $query->where('eventID', $request->input('eventID'));
        }

        $revenue = $query->sum('price');
        return response()->json(['revenue' => $revenue], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/tickets-sold",
     *     summary="Get total tickets sold",
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="Start date for the tickets sold calculation"
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="End date for the tickets sold calculation"
     *     ),
     *     @OA\Parameter(
     *         name="eventID",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Event ID to filter the tickets sold"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Total tickets sold",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="tickets_sold", type="integer")
     *         )
     *     )
     * )
     */
    public function ticketsSold(Request $request)
    {
        $query = Ticket::where('isSold', true);

        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->input('start_date'));
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->has('eventID')) {
            $query->where('eventID', $request->input('eventID'));
        }

        $ticketsSold = $query->count();
        return response()->json(['tickets_sold' => $ticketsSold], 200);
    }
}
