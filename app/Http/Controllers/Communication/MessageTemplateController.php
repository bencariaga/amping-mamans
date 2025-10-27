<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\Communication\MessageTemplate;
use App\Models\Storage\Data;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MessageTemplateController extends Controller
{
    public function create()
    {
        return view('pages.dashboard.templates.text-messages.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'msg_tmp_title' => 'required|string|max:30',
                'msg_tmp_text' => [
                    'required',
                    'string',
                    'max:1000',
                    'regex:/^(?!.*\\\\\\\\[\\\\$[^\\\\\\\\]]*\\\\s[^\\\\\\\\]]*\\\\\\\\]).*$/',
                ],
            ]);

            DB::beginTransaction();

            $data = Data::create([
                'data_status' => 'Unarchived',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            MessageTemplate::create([
                'data_id' => $data->data_id,
                'msg_tmp_title' => $request->input('msg_tmp_title'),
                'msg_tmp_text' => $request->input('msg_tmp_text'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Text message template created successfully.',
                'redirect' => route('message-templates.list'),
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Message template creation failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create new Text message template. Please try again.',
            ], 500);
        }
    }

    public function edit(MessageTemplate $template)
    {
        return view('pages.dashboard.templates.text-messages.edit', ['template' => $template]);
    }

    public function update(Request $request, MessageTemplate $template)
    {
        try {
            $request->validate([
                'msg_tmp_title' => 'required|string|max:30',
                'msg_tmp_text' => [
                    'required',
                    'string',
                    'max:1000',
                    'regex:/^(?!.*\\\\\\\\[\\\\$[^\\\\\\\\]]*\\\\s[^\\\\\\\\]]*\\\\\\\\]).*$/',
                ],
            ]);

            DB::beginTransaction();

            $template->update([
                'msg_tmp_title' => $request->input('msg_tmp_title'),
                'msg_tmp_text' => $request->input('msg_tmp_text'),
            ]);

            $template->data->update([
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Text message template updated successfully.',
                'redirect' => route('message-templates.list'),
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Message template update failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update Text message template. Please try again.',
            ], 500);
        }
    }

    public function destroy(MessageTemplate $template)
    {
        try {
            DB::beginTransaction();

            $data = $template->data;
            $template->delete();

            if ($data) {
                $data->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Text message template deleted successfully.',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Message template hard deletion failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete text message template. Please try again.',
            ], 500);
        }
    }
}
