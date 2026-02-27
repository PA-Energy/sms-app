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
        
        // Clean data to fix UTF-8 encoding issues
        $data = $this->cleanUtf8($data);
        
        // Encode and output JSON
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        if ($json === false) {
            error_log("JSON encode error: " . json_last_error_msg());
            // Try cleaning more aggressively
            $data = $this->cleanUtf8($data, true);
            $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_IGNORE);
            
            if ($json === false) {
                // Last resort: return error
                $json = json_encode([
                    'success' => false,
                    'error' => 'JSON encoding failed',
                    'message' => json_last_error_msg()
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
        
        echo $json;
        exit;
    }
    
    /**
     * Clean UTF-8 data recursively to fix malformed characters
     */
    protected function cleanUtf8($data, $aggressive = false)
    {
        if (is_array($data)) {
            return array_map(function($item) use ($aggressive) {
                return $this->cleanUtf8($item, $aggressive);
            }, $data);
        } elseif (is_string($data)) {
            // Remove or replace invalid UTF-8 characters
            if ($aggressive) {
                // More aggressive cleaning
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
                // Remove any remaining invalid bytes using regex
                $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $data);
                // Remove invalid UTF-8 sequences
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            } else {
                // Standard cleaning - fix encoding issues
                if (!mb_check_encoding($data, 'UTF-8')) {
                    $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
                }
                // Remove null bytes and other problematic characters
                $data = str_replace(["\0", "\x00"], '', $data);
            }
            return $data;
        }
        return $data;
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
