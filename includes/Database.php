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
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL,
                email TEXT UNIQUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS cash_in (
                id INTEGER PRIMARY KEY,
                user_id INTEGER,
                title TEXT NOT NULL,
                amount REAL NOT NULL,
                date TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS cash_out (
                id INTEGER PRIMARY KEY,
                user_id INTEGER,
                title TEXT NOT NULL,
                amount REAL NOT NULL,
                date TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS dena (
                id INTEGER PRIMARY KEY,
                user_id INTEGER,
                name TEXT NOT NULL,
                address TEXT,
                mobile TEXT NOT NULL,
                reason TEXT,
                amount REAL NOT NULL,
                due_date TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS paona (
                id INTEGER PRIMARY KEY,
                user_id INTEGER,
                name TEXT NOT NULL,
                address TEXT,
                mobile TEXT NOT NULL,
                reason TEXT,
                amount REAL NOT NULL,
                due_date TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            );

            INSERT OR IGNORE INTO users (id, name, email) VALUES (1, 'User', 'user@example.com');
        ";

        $this->pdo->exec($sql);
    }

    public function getConnection() {
        return $this->pdo;
    }
}