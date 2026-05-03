<?php

require_once dirname(__DIR__) . '/includes/Database.php';
require_once dirname(__DIR__) . '/includes/Response.php';

class CashInAPI {
    private $db;
    private $userId = 1;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

        $stmt = $this->db->getConnection()->prepare("
            SELECT id, user_id as userId, title, amount, date, created_at as createdAt
            FROM cash_in 
            ORDER BY date DESC, id DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $entries = $stmt->fetchAll();
        $total = $this->getTotal();

        Response::success([
            "entries" => $entries,
            "pagination" => [
                "limit" => $limit,
                "offset" => $offset
            ],
            "summary" => [
                "total" => $total
            ]
        ]);
    }

    public function getById($id) {
        $stmt = $this->db->getConnection()->prepare("
            SELECT id, user_id as userId, title, amount, date, created_at as createdAt
            FROM cash_in WHERE id = ?
        ");
        $stmt->execute([$id]);
        $entry = $stmt->fetch();

        if (!$entry) {
            Response::notFound("Cash in entry not found");
        }

        Response::success($entry);
    }

    public function create() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        if (!isset($input['title']) || !isset($input['amount']) || !isset($input['date'])) {
            Response::badRequest("Missing required fields: title, amount, date");
        }

        $stmt = $this->db->getConnection()->prepare("
            INSERT INTO cash_in (user_id, title, amount, date) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $this->userId,
            $input['title'],
            (float)$input['amount'],
            $input['date']
        ]);

        $id = $this->db->getConnection()->lastInsertId();

        Response::success([
            "id" => (int)$id,
            "title" => $input['title'],
            "amount" => (float)$input['amount'],
            "date" => $input['date']
        ], "Cash in entry created successfully");
    }

    public function update($id) {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        if (!isset($input['title']) && !isset($input['amount']) && !isset($input['date'])) {
            Response::badRequest("No fields to update");
        }

        $fields = [];
        $values = [];

        if (isset($input['title'])) {
            $fields[] = "title = ?";
            $values[] = $input['title'];
        }
        if (isset($input['amount'])) {
            $fields[] = "amount = ?";
            $values[] = (float)$input['amount'];
        }
        if (isset($input['date'])) {
            $fields[] = "date = ?";
            $values[] = $input['date'];
        }

        $values[] = $id;

        $sql = "UPDATE cash_in SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute($values);

        if ($stmt->rowCount() === 0) {
            Response::notFound("Cash in entry not found");
        }

        $this->getById($id);
    }

    public function delete($id) {
        $stmt = $this->db->getConnection()->prepare("DELETE FROM cash_in WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            Response::notFound("Cash in entry not found");
        }

        Response::success(null, "Cash in entry deleted successfully");
    }

    private function getTotal() {
        $stmt = $this->db->getConnection()->query("
            SELECT COALESCE(SUM(amount), 0) as total FROM cash_in
        ");
        return (float)$stmt->fetch()['total'];
    }
}

if (basename($_SERVER['SCRIPT_FILENAME']) === 'cash-in.php') {
    $api = new CashInAPI();
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