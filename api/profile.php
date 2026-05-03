<?php

require_once dirname(__DIR__) . '/includes/Database.php';
require_once dirname(__DIR__) . '/includes/Response.php';

class ProfileAPI {
    private $db;
    private $userId = 1;

    public function __construct() {
        $this->db = new Database();
    }

    public function get() {
        $stmt = $this->db->getConnection()->prepare("
            SELECT id, name, email, created_at as createdAt
            FROM users WHERE id = ?
        ");
        $stmt->execute([$this->userId]);
        $profile = $stmt->fetch();

        if (!$profile) {
            Response::notFound("Profile not found");
        }

        Response::success($profile);
    }

    public function update() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        if (!isset($input['name']) && !isset($input['email'])) {
            Response::badRequest("No fields to update");
        }

        $fields = [];
        $values = [];

        if (isset($input['name'])) {
            $fields[] = "name = ?";
            $values[] = $input['name'];
        }
        if (isset($input['email'])) {
            $fields[] = "email = ?";
            $values[] = $input['email'];
        }

        $values[] = $this->userId;

        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute($values);

        $this->get();
    }
}

if (basename($_SERVER['SCRIPT_FILENAME']) === 'profile.php') {
    $api = new ProfileAPI();
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
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
}