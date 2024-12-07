<?php

class Database {
    private static ?PDO $pdo = null;

    private function __construct() {}

    public static function getConnection(): PDO {
        if (self::$pdo === null) {
            $host = 'localhost';
            $dbname = 'courier_schedule';
            $user = 'root';
            $password = '';
            try {
                self::$pdo = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8",
                    $user,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                die("Ошибка подключения к базе данных: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
