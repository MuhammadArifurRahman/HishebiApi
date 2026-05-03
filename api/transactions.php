<?php

require_once dirname(__DIR__) . '/includes/Database.php';
require_once dirname(__DIR__) . '/includes/Response.php';

class TransactionAPI {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

        $stmt = $this->db->getConnection()->prepare("
            SELECT id, title, amount, type, date, created_at as createdAt
            FROM transactions 
            ORDER BY date DESC, id DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $transactions = $stmt->fetchAll();

        $cashIn = $this->getTotalByType('in');
        $cashOut = $this->getTotalByType('out');

        Response::success([
            "transactions" => $transactions,
            "pagination" => [
                "limit" => $limit,
                "offset" => $offset
            ],
            "summary" => [
                "cashIn" => $cashIn,
                "cashOut" => $cashOut,
                "balance" => $cashIn - $cashOut
            ]
        ]);
    }

    public function getById($id) {
        $stmt = $this->db->getConnection()->prepare("
            SELECT id, title, amount, type, date, created_at as createdAt
            FROM transactions WHERE id = ?
        ");
        $stmt->execute([$id]);
        $transaction = $stmt->fetch();

        if (!$transaction) {
            Response::notFound("Transaction not found");
        }

        Response::success($transaction);
    }

    public function create() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        if (!isset($input['title']) || !isset($input['amount']) || !isset($input['type']) || !isset($input['date'])) {
            Response::badRequest("Missing required fields: title, amount, type, date");
        }

        $type = strtolower($input['type']);
        if (!in_array($type, ['in', 'out'])) {
            Response::badRequest("Type must be 'in' or 'out'");
        }

        $stmt = $this->db->getConnection()->prepare("
            INSERT INTO transactions (title, amount, type, date) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $input['title'],
            (float)$input['amount'],
            $type,
            $input['date']
        ]);

        $id = $this->db->getConnection()->lastInsertId();

        Response::success([
            "id" => (int)$id,
            "title" => $input['title'],
            "amount" => (float)$input['amount'],
            "type" => $type,
            "date" => $input['date']
        ], "Transaction created successfully");
    }

    public function update($id) {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        if (!isset($input['title']) && !isset($input['amount']) && !isset($input['type']) && !isset($input['date'])) {
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
        if (isset($input['type'])) {
            $type = strtolower($input['type']);
            if (!in_array($type, ['in', 'out'])) {
                Response::badRequest("Type must be 'in' or 'out'");
            }
            $fields[] = "type = ?";
            $values[] = $type;
        }
        if (isset($input['date'])) {
            $fields[] = "date = ?";
            $values[] = $input['date'];
        }

        $values[] = $id;

        $sql = "UPDATE transactions SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute($values);

        if ($stmt->rowCount() === 0) {
            Response::notFound("Transaction not found");
        }

        $this->getById($id);
    }

    public function delete($id) {
        $stmt = $this->db->getConnection()->prepare("DELETE FROM transactions WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            Response::notFound("Transaction not found");
        }

        Response::success(null, "Transaction deleted successfully");
    }

    private function getTotalByType($type) {
        $stmt = $this->db->getConnection()->prepare("
            SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE type = ?
        ");
        $stmt->execute([$type]);
        return (float)$stmt->fetch()['total'];
    }
}

if (basename($_SERVER['SCRIPT_FILENAME']) === 'transactions.php') {
    $api = new TransactionAPI();
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
}