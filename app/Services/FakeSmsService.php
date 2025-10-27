<?php

namespace App\Services;

use App\Models\Communication\Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class FakeSmsService
{
    public function sendSms(array $targetNumbers, string $messageText)
    {
        $targetNumber = $targetNumbers[0] ?? '';

        try {
            $lastMessage = Message::orderBy('message_id', 'desc')->first();
            $nextId = $lastMessage ? (int) Str::afterLast($lastMessage->message_id, '-') + 1 : 1;
            $idSuffix = Str::padLeft($nextId, 9, '0');
        } catch (\Exception $e) {
            $idSuffix = Str::upper(Str::random(9));
        }

        $fakeMessageId = 'MESSAGE-'.Carbon::now()->year.'-'.$idSuffix;

        return [
            [
                'message_id' => $fakeMessageId,
                'target_number' => $targetNumber,
                'message_text' => $messageText,
                'timestamp' => Carbon::now()->toDateTimeString(),
            ],
        ];
    }
}
