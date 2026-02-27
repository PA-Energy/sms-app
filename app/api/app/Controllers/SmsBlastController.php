<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\SmsBatch;
use App\Services\SmsBlastService;

class SmsBlastController extends Controller
{
    public function index()
    {
        $user = Auth::requireAuth();
        
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 20;

        $result = SmsBatch::getAll($user['id'], $page, $perPage);
        
        return $this->json(['success' => true, 'data' => $result]);
    }

    public function create()
    {
        $user = Auth::requireAuth();
        $data = $this->getRequestData();
        
        $errors = $this->validate($data, [
            'name' => 'required',
            'message' => 'required',
            'phone_numbers' => 'required',
        ]);

        if (!empty($errors)) {
            return $this->json(['success' => false, 'errors' => $errors], 422);
        }

        if (!is_array($data['phone_numbers']) || empty($data['phone_numbers'])) {
            return $this->json(['success' => false, 'message' => 'phone_numbers must be a non-empty array'], 422);
        }

        try {
            $blastService = new SmsBlastService();
            $batch = $blastService->createBatch($user['id'], $data['name'], $data['phone_numbers'], $data['message'], $data['line'] ?? null);
            
            return $this->json([
                'success' => true,
                'message' => 'SMS blast created and queued',
                'data' => $batch,
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to create SMS blast', 'error' => $e->getMessage()], 500);
        }
    }

    public function uploadCsv()
    {
        $user = Auth::requireAuth();
        $data = $this->getRequestData();
        
        $errors = $this->validate($data, [
            'name' => 'required',
            'message' => 'required',
        ]);

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['success' => false, 'message' => 'CSV file is required'], 422);
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $phoneNumbers = [];

        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                if (!empty($row[0])) {
                    $phoneNumbers[] = trim($row[0]);
                }
            }
            fclose($handle);
        }

        if (empty($phoneNumbers)) {
            return $this->json(['success' => false, 'message' => 'No phone numbers found in CSV file'], 422);
        }

        try {
            $blastService = new SmsBlastService();
            $batch = $blastService->createBatch($user['id'], $data['name'], $phoneNumbers, $data['message'], $data['line'] ?? null);
            
            return $this->json([
                'success' => true,
                'message' => 'SMS blast created from CSV and queued',
                'data' => $batch,
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to create SMS blast from CSV', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $user = Auth::requireAuth();
        $batch = SmsBatch::findByIdAndUser($id, $user['id']);
        
        if (!$batch) {
            return $this->json(['success' => false, 'message' => 'Batch not found'], 404);
        }

        return $this->json(['success' => true, 'data' => $batch]);
    }

    public function progress($id)
    {
        $user = Auth::requireAuth();
        $batch = SmsBatch::findByIdAndUser($id, $user['id']);
        
        if (!$batch) {
            return $this->json(['success' => false, 'message' => 'Batch not found'], 404);
        }

        $blastService = new SmsBlastService();
        $progress = $blastService->getBatchProgress($id);

        return $this->json(['success' => true, 'data' => $progress]);
    }
}
