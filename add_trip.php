<?php

require 'Database.php';
require 'Trip.php';

$db = Database::getConnection();
$trip = new Trip($db);

$regionId = $_POST['region_id'];
$courierId = $_POST['courier_id'];
$departureDate = $_POST['departure_date'];

if ($trip->addTrip($regionId, $courierId, $departureDate)) {
    echo "Поездка успешно добавлена!";
} else {
    echo "Ошибка: Курьер занят.";
}
