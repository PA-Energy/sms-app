<?php

namespace App\Core;

class Controller
{
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        echo json_encode($data);
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
