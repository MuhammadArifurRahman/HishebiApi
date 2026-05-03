<?php

require_once dirname(__DIR__) . '/includes/Database.php';
require_once dirname(__DIR__) . '/includes/Response.php';

class ProfileAPI {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function get() {
        $stmt = $this->db->getConnection()->query("
            SELECT name, email, updated_at as updatedAt
            FROM user_profile WHERE id = 1
        ");
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

        $fields[] = "updated_at = CURRENT_TIMESTAMP";

        $sql = "UPDATE user_profile SET " . implode(", ", $fields) . " WHERE id = 1";
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