<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TextBeeService
{
    protected $base;

    protected $apiKey;

    protected $deviceId;

    public function __construct()
    {
        $this->base = config('textbee.textbee.base_url');
        $this->apiKey = config('textbee.textbee.api_key');
        $this->deviceId = config('textbee.textbee.device_id');
    }

    public function sendSms(array $recipients, string $message): Response
    {
        $url = "{$this->base}/gateway/devices/{$this->deviceId}/send-sms";

        return Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->post($url, [
            'recipients' => $recipients,
            'message' => $message,
        ]);
    }
}
