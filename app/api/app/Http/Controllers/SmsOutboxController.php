<?php

namespace App\Http\Controllers;

use App\Models\SmsOutbox;
use App\Services\GoIpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SmsOutboxController extends Controller
{
    protected $goIpService;

    public function __construct(GoIpService $goIpService)
    {
        $this->goIpService = $goIpService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = SmsOutbox::where('user_id', $user->id)->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $perPage = $request->get('per_page', 20);
        $messages = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'message' => 'required|string|max:160',
            'line' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Create outbox record
        $outbox = SmsOutbox::create([
            'user_id' => $user->id,
            'phone_number' => $request->phone_number,
            'message_text' => $request->message,
            'goip_line' => $request->line ?? 1,
            'status' => 'pending',
        ]);

        try {
            $result = $this->goIpService->sendSms(
                $request->phone_number,
                $request->message,
                $request->line
            );

            if ($result['success']) {
                $outbox->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => $outbox->fresh(),
                ]);
            } else {
                $outbox->update([
                    'status' => 'failed',
                    'error_message' => $result['message'],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send SMS',
                    'error' => $result['message'],
                ], 500);
            }
        } catch (\Exception $e) {
            $outbox->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $outbox = SmsOutbox::where('user_id', $user->id)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $outbox,
        ]);
    }
}
