<?php

class Database {
    private $dbPath;
    private $pdo;

    public function __construct($dbPath = null) {
        if ($dbPath === null) {
            require_once dirname(__DIR__) . '/.env.php';
            $dbPath = defined('DB_PATH') ? DB_PATH : dirname(__DIR__) . '/database/hishebi.db';
        }
        
        $this->dbPath = $dbPath;
        $this->connect();
        $this->initTables();
    }

    private function connect() {
        try {
            $this->pdo = new PDO("sqlite:" . $this->dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(["error" => "Database connection failed: " . $e->getMessage()]));
        }
    }

    private function initTables() {
        $sql = "
            CREATE TABLE IF NOT EXISTS transactions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                amount REAL NOT NULL,
                type TEXT NOT NULL CHECK(type IN ('in', 'out')),
                date TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS dues (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                address TEXT,
                mobile TEXT NOT NULL,
                reason TEXT,
                amount REAL NOT NULL,
                type TEXT NOT NULL CHECK(type IN ('owe', 'receivable')),
                due_date TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS user_profile (
                id INTEGER PRIMARY KEY CHECK (id = 1),
                name TEXT NOT NULL DEFAULT 'User',
                email TEXT NOT NULL DEFAULT 'user@example.com',
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            INSERT OR IGNORE INTO user_profile (id, name, email) VALUES (1, 'User', 'user@example.com');
        ";

        $this->pdo->exec($sql);
    }

    public function getConnection() {
        return $this->pdo;
    }
}