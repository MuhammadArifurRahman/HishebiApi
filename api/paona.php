<?php

require_once dirname(__DIR__) . '/includes/Database.php';
require_once dirname(__DIR__) . '/includes/Response.php';

class PaonaAPI {
    private $db;
    private $userId = 1;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $stmt = $this->db->getConnection()->query("
            SELECT id, user_id as userId, name, address, mobile, reason, amount, due_date as dueDate, created_at as createdAt
            FROM paona 
            ORDER BY id DESC
        ");

        $entries = $stmt->fetchAll();
        $total = $this->getTotal();

        Response::success([
            "entries" => $entries,
            "summary" => [
                "totalReceivable" => $total
            ]
        ]);
    }

    public function getById($id) {
        $stmt = $this->db->getConnection()->prepare("
            SELECT id, user_id as userId, name, address, mobile, reason, amount, due_date as dueDate, created_at as createdAt
            FROM paona WHERE id = ?
        ");
        $stmt->execute([$id]);
        $entry = $stmt->fetch();

        if (!$entry) {
            Response::notFound("Paona entry not found");
        }

        Response::success($entry);
    }

    public function create() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        if (!isset($input['name']) || !isset($input['mobile']) || !isset($input['amount'])) {
            Response::badRequest("Missing required fields: name, mobile, amount");
        }

        $stmt = $this->db->getConnection()->prepare("
            INSERT INTO paona (user_id, name, address, mobile, reason, amount, due_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $this->userId,
            $input['name'],
            $input['address'] ?? null,
            $input['mobile'],
            $input['reason'] ?? null,
            (float)$input['amount'],
            $input['dueDate'] ?? null
        ]);

        $id = $this->db->getConnection()->lastInsertId();

        Response::success([
            "id" => (int)$id,
            "name" => $input['name'],
            "mobile" => $input['mobile'],
            "amount" => (float)$input['amount']
        ], "Paona entry created successfully");
    }

    public function update($id) {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        if (!isset($input['name']) && !isset($input['mobile']) && !isset($input['amount']) 
            && !isset($input['reason']) && !isset($input['dueDate'])) {
            Response::badRequest("No fields to update");
        }

        $fields = [];
        $values = [];

        if (isset($input['name'])) {
            $fields[] = "name = ?";
            $values[] = $input['name'];
        }
        if (isset($input['mobile'])) {
            $fields[] = "mobile = ?";
            $values[] = $input['mobile'];
        }
        if (isset($input['address'])) {
            $fields[] = "address = ?";
            $values[] = $input['address'];
        }
        if (isset($input['reason'])) {
            $fields[] = "reason = ?";
            $values[] = $input['reason'];
        }
        if (isset($input['amount'])) {
            $fields[] = "amount = ?";
            $values[] = (float)$input['amount'];
        }
        if (isset($input['dueDate'])) {
            $fields[] = "due_date = ?";
            $values[] = $input['dueDate'];
        }

        $values[] = $id;

        $sql = "UPDATE paona SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute($values);

        if ($stmt->rowCount() === 0) {
            Response::notFound("Paona entry not found");
        }

        $this->getById($id);
    }

    public function delete($id) {
        $stmt = $this->db->getConnection()->prepare("DELETE FROM paona WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            Response::notFound("Paona entry not found");
        }

        Response::success(null, "Paona entry deleted successfully");
    }

    private function getTotal() {
        $stmt = $this->db->getConnection()->query("
            SELECT COALESCE(SUM(amount), 0) as total FROM paona
        ");
        return (float)$stmt->fetch()['total'];
    }
}

if (basename($_SERVER['SCRIPT_FILENAME']) === 'paona.php') {
    $api = new PaonaAPI();
    $method = $_SERVER['REQUEST_METHOD'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    switch ($method) {
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
                Response::badRequest("ID required");
            }
            break;
        case 'DELETE':
            if ($id) {
                $api->delete($id);
            } else {
                Response::badRequest("ID required");
            }
            break;
        default:
            Response::badRequest("Method not allowed");
    }
}