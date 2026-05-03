<?php

require_once dirname(__DIR__) . '/includes/Database.php';
require_once dirname(__DIR__) . '/includes/Response.php';

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$path = parse_url($requestUri, PHP_URL_PATH);

// Strip base path up to and including /api
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($basePath !== '' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
$path = trim($path, '/');

if ($path === '' || $path === 'index.php') {
    require_once dirname(__DIR__) . '/index.php';
    exit;
}

$parts = explode('/', $path);
$resource = $parts[0] ?? '';
$id = $parts[1] ?? null;

try {
    switch ($resource) {
        case 'cash-in':
            require_once __DIR__ . '/cash-in.php';
            $api = new CashInAPI();
            switch ($requestMethod) {
                case 'GET':
                    $id ? $api->getById($id) : $api->getAll();
                    break;
                case 'POST':
                    $api->create();
                    break;
                case 'PUT':
                case 'PATCH':
                    $id ? $api->update($id) : Response::badRequest("ID required");
                    break;
                case 'DELETE':
                    $id ? $api->delete($id) : Response::badRequest("ID required");
                    break;
                default:
                    Response::badRequest("Method not allowed");
            }
            break;

        case 'cash-out':
            require_once __DIR__ . '/cash-out.php';
            $api = new CashOutAPI();
            switch ($requestMethod) {
                case 'GET':
                    $id ? $api->getById($id) : $api->getAll();
                    break;
                case 'POST':
                    $api->create();
                    break;
                case 'PUT':
                case 'PATCH':
                    $id ? $api->update($id) : Response::badRequest("ID required");
                    break;
                case 'DELETE':
                    $id ? $api->delete($id) : Response::badRequest("ID required");
                    break;
                default:
                    Response::badRequest("Method not allowed");
            }
            break;

        case 'dena':
            require_once __DIR__ . '/dena.php';
            $api = new DenaAPI();
            switch ($requestMethod) {
                case 'GET':
                    $id ? $api->getById($id) : $api->getAll();
                    break;
                case 'POST':
                    $api->create();
                    break;
                case 'PUT':
                case 'PATCH':
                    $id ? $api->update($id) : Response::badRequest("ID required");
                    break;
                case 'DELETE':
                    $id ? $api->delete($id) : Response::badRequest("ID required");
                    break;
                default:
                    Response::badRequest("Method not allowed");
            }
            break;

        case 'paona':
            require_once __DIR__ . '/paona.php';
            $api = new PaonaAPI();
            switch ($requestMethod) {
                case 'GET':
                    $id ? $api->getById($id) : $api->getAll();
                    break;
                case 'POST':
                    $api->create();
                    break;
                case 'PUT':
                case 'PATCH':
                    $id ? $api->update($id) : Response::badRequest("ID required");
                    break;
                case 'DELETE':
                    $id ? $api->delete($id) : Response::badRequest("ID required");
                    break;
                default:
                    Response::badRequest("Method not allowed");
            }
            break;

        case 'transactions':
            require_once __DIR__ . '/transactions.php';
            $api = new TransactionAPI();
            switch ($requestMethod) {
                case 'GET':
                    $id ? $api->getById($id) : $api->getAll();
                    break;
                case 'POST':
                    $api->create();
                    break;
                case 'PUT':
                case 'PATCH':
                    $id ? $api->update($id) : Response::badRequest("Transaction ID required");
                    break;
                case 'DELETE':
                    $id ? $api->delete($id) : Response::badRequest("Transaction ID required");
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
                    $id ? $api->getById($id) : $api->getAll();
                    break;
                case 'POST':
                    $api->create();
                    break;
                case 'PUT':
                case 'PATCH':
                    $id ? $api->update($id) : Response::badRequest("Due ID required");
                    break;
                case 'DELETE':
                    $id ? $api->delete($id) : Response::badRequest("Due ID required");
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
