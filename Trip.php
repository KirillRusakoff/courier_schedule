<?php

class Trip {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function createTable(): void {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS trips (
                id INT AUTO_INCREMENT PRIMARY KEY,
                region_id INT NOT NULL,
                courier_id INT NOT NULL,
                departure_date DATE NOT NULL,
                arrival_date DATE NOT NULL,
                FOREIGN KEY (region_id) REFERENCES regions(id),
                FOREIGN KEY (courier_id) REFERENCES couriers(id)
            )
        ");
    }

    public function addTrip(int $regionId, int $courierId, string $departureDate): bool {
        // Проверяем, свободен ли курьер
        $stmt = $this->db->prepare("
            SELECT 1 FROM trips 
            WHERE courier_id = ? AND 
                (departure_date <= ? AND arrival_date >= ?)
        ");
        $stmt->execute([$courierId, $departureDate, $departureDate]);

        if ($stmt->fetch()) {
            return false; // Курьер занят
        }

        // Вычисляем дату прибытия
        $stmt = $this->db->prepare("SELECT duration FROM regions WHERE id = ?");
        $stmt->execute([$regionId]);
        $duration = $stmt->fetchColumn();

        $arrivalDate = (new DateTime($departureDate))->modify("+$duration days")->format('Y-m-d');

        // Добавляем поездку
        $stmt = $this->db->prepare("
            INSERT INTO trips (region_id, courier_id, departure_date, arrival_date)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$regionId, $courierId, $departureDate, $arrivalDate]);

        return true;
    }

    public function getTripsByDate(string $date): array {
        $stmt = $this->db->prepare("
            SELECT trips.*, regions.name AS region_name, couriers.name AS courier_name
            FROM trips
            JOIN regions ON trips.region_id = regions.id
            JOIN couriers ON trips.courier_id = couriers.id
            WHERE ? BETWEEN departure_date AND arrival_date
        ");
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }
}
