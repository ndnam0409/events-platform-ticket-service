<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Carbon\Carbon;

class ReportController extends Controller
{
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
