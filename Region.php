<?php

class Region {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function createTable(): void {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS regions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                duration INT NOT NULL
            )
        ");
    }

    public function seedData(): void {
        $regions = [
            ['Санкт-Петербург', 3],
            ['Уфа', 5],
            ['Нижний Новгород', 2],
            ['Владимир', 1],
            ['Кострома', 4],
            ['Екатеринбург', 6],
            ['Ковров', 3],
            ['Воронеж', 2],
            ['Самара', 5],
            ['Астрахань', 7],
        ];

        foreach ($regions as $region) {
            $stmt = $this->db->prepare("INSERT INTO regions (name, duration) VALUES (?, ?)");
            $stmt->execute($region);
        }
    }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM regions")->fetchAll();
    }
}