<?php

class Courier {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function createTable(): void {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS couriers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL
            )
        ");
    }

    public function seedData(): void {
        for ($i = 1; $i <= 10; $i++) {
            $stmt = $this->db->prepare("INSERT INTO couriers (name) VALUES (?)");
            $stmt->execute(["Курьер №$i"]);
        }
    }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM couriers")->fetchAll();
    }
}
