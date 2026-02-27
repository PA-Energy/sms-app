<?php

namespace App\Core;

class Controller
{
    protected function json($data, $statusCode = 200)
    {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        // Set proper headers
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        
        // Encode and output JSON
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        if ($json === false) {
            error_log("JSON encode error: " . json_last_error_msg());
            $json = json_encode([
                'success' => false,
                'error' => 'JSON encoding failed',
                'message' => json_last_error_msg()
            ]);
        }
        
        echo $json;
        exit;
    }

    protected function getRequestData()
    {
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);
        
        // If JSON decode failed, try $_POST
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $_POST;
        }
        
        return $data ?? $_POST;
    }

    protected function validate($data, $rules)
    {
        $errors = [];
        foreach ($rules as $field => $rule) {
            $required = strpos($rule, 'required') !== false;
            if ($required && empty($data[$field])) {
                $errors[$field] = "The {$field} field is required.";
            }
        }
        return $errors;
    }
}
