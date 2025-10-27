<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\Communication\Message;
use App\Models\Operation\Application;
use App\Services\FakeSmsService;
use App\Services\TextBeeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class MessageController extends Controller
{
    public function sendMessage(Request $request, TextBeeService $textBeeService, FakeSmsService $fakeSmsService)
    {
        try {
            $request->validate([
                'application_id' => 'required|string',
                'message_text' => 'required|string|max:1000',
                'msg_tmp_id' => 'required|string',
            ]);

            $application = Application::with(['applicant.client.contacts' => function ($query) {
                $query->where('contact_type', 'Application');
            }])->findOrFail($request->input('application_id'));

            $contact = $application->applicant->client->contacts->first();

            if (! $contact || ! $contact->phone_number) {
                return response()->json(['error' => 'Applicant phone number not found.'], 404);
            }

            $targetNumber = $contact->phone_number;
            $contactId = $contact->contact_id;

            $messageTextForSms = Str::replace('<br>', "\n", $request->input('message_text'));
            $messageTextForDb = $request->input('message_text');
            $msgTmpId = $request->input('msg_tmp_id');

            $response = null;

            try {
                $response = $textBeeService->sendSms([$targetNumber], $messageTextForSms);
            } catch (Throwable $e) {
                $fakeSmsService->sendSms([$targetNumber], $messageTextForSms);
                Log::warning('TextBee API call failed, falling back to FakeSmsService.', ['error' => $e->getMessage()]);
                $response = null;
            }

            if ($response && $response->successful()) {
                $lastMessage = Message::orderBy('message_id', 'desc')->first();
                $nextId = $lastMessage ? (int) Str::afterLast($lastMessage->message_id, '-') + 1 : 1;
                $messageId = 'MESSAGE-'.Carbon::now()->year.'-'.Str::padLeft($nextId, 9, '0');

                Message::create([
                    'message_id' => $messageId,
                    'msg_tmp_id' => $msgTmpId,
                    'staff_id' => Auth::user()->staff->staff_id,
                    'contact_id' => $contactId,
                    'message_text' => $messageTextForDb,
                    'sent_at' => Carbon::now(),
                ]);

                return $messageId;
            } elseif ($response) {
                Log::error('SMS sending failed via TextBee.', ['status' => $response->status(), 'body' => $response->body()]);
                throw new Exception('SMS sending failed. Check API logs for TextBee response.');
            } else {
                throw new Exception('SMS sending failed. Check network or TextBee API connection.');
            }
        } catch (Exception $e) {
            Log::error('sendMessage error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }
}
