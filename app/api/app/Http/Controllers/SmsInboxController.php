<?php

namespace App\Http\Controllers;

use App\Models\SmsMessage;
use App\Services\SmsSyncService;
use Illuminate\Http\Request;

class SmsInboxController extends Controller
{
    protected $syncService;

    public function __construct(SmsSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function index(Request $request)
    {
        $query = SmsMessage::query()->orderBy('received_at', 'desc');

        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('phone_number', 'like', "%{$search}%")
                  ->orWhere('message_text', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $messages = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function sync()
    {
        $result = $this->syncService->syncInbox();

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    public function markAsRead($id)
    {
        $message = SmsMessage::findOrFail($id);
        $message->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read',
        ]);
    }

    public function markAllAsRead()
    {
        SmsMessage::where('is_read', false)->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All messages marked as read',
        ]);
    }

    public function show($id)
    {
        $message = SmsMessage::findOrFail($id);
        
        // Mark as read when viewing
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return response()->json([
            'success' => true,
            'data' => $message,
        ]);
    }
}
