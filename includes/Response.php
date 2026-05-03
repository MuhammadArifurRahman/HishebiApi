<?php

class Response {
    public static function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function success($data, $message = null) {
        $response = ["success" => true];
        if ($message) {
            $response["message"] = $message;
        }
        $response["data"] = $data;
        self::json($response);
    }

    public static function error($message, $statusCode = 400) {
        self::json(["success" => false, "error" => $message], $statusCode);
    }

    public static function notFound($message = "Resource not found") {
        self::error($message, 404);
    }

    public static function badRequest($message = "Bad request") {
        self::error($message, 400);
    }

    public static function unauthorized($message = "Unauthorized") {
        self::error($message, 401);
    }

    public static function serverError($message = "Internal server error") {
        self::error($message, 500);
    }
}