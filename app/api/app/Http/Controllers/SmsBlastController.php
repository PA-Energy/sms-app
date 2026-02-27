<?php

namespace App\Http\Controllers;

use App\Jobs\SendSmsBlastJob;
use App\Models\SmsBatch;
use App\Services\SmsBlastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SmsBlastController extends Controller
{
    protected $blastService;

    public function __construct(SmsBlastService $blastService)
    {
        $this->blastService = $blastService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = SmsBatch::where('user_id', $user->id)->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 20);
        $batches = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $batches,
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:160',
            'phone_numbers' => 'required|array|min:1',
            'phone_numbers.*' => 'required|string',
            'line' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        try {
            $batch = $this->blastService->createBatch(
                $user->id,
                $request->name,
                $request->phone_numbers,
                $request->message
            );

            // Queue jobs for each recipient
            foreach ($batch->recipients as $recipient) {
                SendSmsBlastJob::dispatch($recipient->id, $request->message, $request->line);
            }

            return response()->json([
                'success' => true,
                'message' => 'SMS blast created and queued',
                'data' => $batch->load('recipients'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create SMS blast',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function uploadCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:160',
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'line' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('csv_file');
        $phoneNumbers = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (!empty($data[0])) {
                    $phoneNumbers[] = trim($data[0]);
                }
            }
            fclose($handle);
        }

        if (empty($phoneNumbers)) {
            return response()->json([
                'success' => false,
                'message' => 'No phone numbers found in CSV file',
            ], 422);
        }

        $user = $request->user();

        try {
            $batch = $this->blastService->createBatch(
                $user->id,
                $request->name,
                $phoneNumbers,
                $request->message
            );

            // Queue jobs for each recipient
            foreach ($batch->recipients as $recipient) {
                SendSmsBlastJob::dispatch($recipient->id, $request->message, $request->line);
            }

            return response()->json([
                'success' => true,
                'message' => 'SMS blast created from CSV and queued',
                'data' => $batch->load('recipients'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create SMS blast from CSV',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $user = $request->user();
        $batch = SmsBatch::where('user_id', $user->id)
            ->with('recipients')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $batch,
        ]);
    }

    public function progress($id)
    {
        $user = $request->user();
        $batch = SmsBatch::where('user_id', $user->id)->findOrFail($id);

        $progress = $this->blastService->getBatchProgress($batch->id);

        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }
}
