<?php

require_once dirname(__DIR__) . '/includes/Database.php';
require_once dirname(__DIR__) . '/includes/Response.php';

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$path = parse_url($requestUri, PHP_URL_PATH);
$path = str_replace('/api', '', $path);
$path = trim($path, '/');

if (empty($path) || $path === '') {
    require_once dirname(__DIR__) . '/index.php';
    exit;
}

$parts = explode('/', $path);
$resource = $parts[0] ?? '';
$id = $parts[1] ?? null;

try {
    switch ($resource) {
        case 'transactions':
            require_once __DIR__ . '/transactions.php';
            $api = new TransactionAPI();
            
            switch ($requestMethod) {
                case 'GET':
                    if ($id) {
                        $api->getById($id);
                    } else {
                        $api->getAll();
                    }
                    break;
                case 'POST':
                    $api->create();
                    break;
                case 'PUT':
                case 'PATCH':
                    if ($id) {
                        $api->update($id);
                    } else {
                        Response::badRequest("Transaction ID required");
                    }
                    break;
                case 'DELETE':
                    if ($id) {
                        $api->delete($id);
                    } else {
                        Response::badRequest("Transaction ID required");
                    }
                    break;
                default:
                    Response::badRequest("Method not allowed");
            }
            break;

        case 'dues':
            require_once __DIR__ . '/dues.php';
            $api = new DuesAPI();
            
            switch ($requestMethod) {
                case 'GET':
                    if ($id) {
                        $api->getById($id);
                    } else {
                        $api->getAll();
                    }
                    break;
                case 'POST':
                    $api->create();
                    break;
                case 'PUT':
                case 'PATCH':
                    if ($id) {
                        $api->update($id);
                    } else {
                        Response::badRequest("Due ID required");
                    }
                    break;
                case 'DELETE':
                    if ($id) {
                        $api->delete($id);
                    } else {
                        Response::badRequest("Due ID required");
                    }
                    break;
                default:
                    Response::badRequest("Method not allowed");
            }
            break;

        case 'profile':
            require_once __DIR__ . '/profile.php';
            $api = new ProfileAPI();
            
            switch ($requestMethod) {
                case 'GET':
                    $api->get();
                    break;
                case 'PUT':
                case 'PATCH':
                    $api->update();
                    break;
                default:
                    Response::badRequest("Method not allowed");
            }
            break;

        case 'extract':
            require_once __DIR__ . '/extract.php';
            $api = new ExtractAPI();
            
            switch ($requestMethod) {
                case 'POST':
                    $api->extract();
                    break;
                default:
                    Response::badRequest("Method not allowed");
            }
            break;

        default:
            Response::notFound("API endpoint not found");
    }
} catch (Exception $e) {
    Response::serverError($e->getMessage());
}