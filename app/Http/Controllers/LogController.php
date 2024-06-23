<?php

namespace App\Http\Controllers;

use App\Services\RabbitMQService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LogController extends Controller
{
    protected $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService)
    {
        $this->rabbitMQService = $rabbitMQService;
    }

    public function logMessage(Request $request)
    {
        $typeLogging = $request->input('type-logging');
        $timestamp = Carbon::now()->toIso8601String();
        $logger = $request->input('logger');
        $level = $request->input('level');
        $path = $request->input('path');
        $content = $request->input('content');

        $this->rabbitMQService->sendLog($typeLogging, $timestamp, $logger, $level, $path, $content);

        return response()->json(['message' => 'Log message sent to RabbitMQ']);
    }
}
