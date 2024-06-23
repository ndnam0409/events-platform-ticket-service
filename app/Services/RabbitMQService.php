<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

class RabbitMQService
{
    protected $connection;
    protected $channel;

    public function __construct()
    {
        try {
            $url = parse_url(env('RABBITMQ_URL'));
            Log::info('Connecting to RabbitMQ', ['url' => $url]);

            $this->connection = new AMQPStreamConnection(
                $url['host'],
                5672,
                $url['user'],
                $url['pass'],
                ltrim($url['path'], '/')
            );

            $this->channel = $this->connection->channel();
            $this->channel->queue_declare(env('RABBITMQ_QUEUE'), false, true, false, false);
            Log::info('Connected to RabbitMQ and queue declared');
        } catch (\Exception $e) {
            Log::error('Failed to connect to RabbitMQ', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function sendLog($typeLogging, $timestamp, $logger, $level, $path, $content)
    {
        try {
            $logData = [
                'type-logging' => $typeLogging,
                'timestamp' => $timestamp,
                'logger' => $logger,
                'level' => $level,
                'path' => $path,
                'content' => $content
            ];

            $logMessage = json_encode($logData);
            $msg = new AMQPMessage($logMessage, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

            $this->channel->basic_publish($msg, '', env('RABBITMQ_QUEUE'));
            Log::info('Log message sent to RabbitMQ', ['log' => $logMessage]);
        } catch (\Exception $e) {
            Log::error('Failed to send log message to RabbitMQ', ['error' => $e->getMessage()]);
        }
    }

    public function __destruct()
    {
        if ($this->channel) {
            $this->channel->close();
        }
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
