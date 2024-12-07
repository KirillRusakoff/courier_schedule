<?php
require 'Database.php';
require 'Region.php';
require 'Courier.php';
require 'Trip.php';

// Подключение к базе данных
$db = Database::getConnection();

// Инициализация классов
$region = new Region($db);
$courier = new Courier($db);
$trip = new Trip($db);

// Автоматическая инициализация базы данных
function initializeDatabase(PDO $db, Region $region, Courier $courier, Trip $trip) {
    // Проверяем, существует ли таблица regions
    $result = $db->query("SHOW TABLES LIKE 'regions'");
    if ($result->rowCount() === 0) {
        // Создаем таблицы и наполняем их данными
        $region->createTable();
        $courier->createTable();
        $trip->createTable();
        $region->seedData();
        $courier->seedData();
        echo "<p>База данных успешно инициализирована!</p>";
    }
}

// Выполняем инициализацию
initializeDatabase($db, $region, $courier, $trip);

// Получаем данные для интерфейса
$regions = $region->getAll();
$couriers = $courier->getAll();

// Если передана дата, отображаем поездки
$selectedDate = $_GET['date'] ?? null;
$trips = $selectedDate ? $trip->getTripsByDate($selectedDate) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расписание курьеров</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body class="container py-4">
    <h1 class="mb-4">Расписание поездок курьеров</h1>

    <!-- Форма добавления поездки -->
    <form id="add-trip-form" class="mb-4">
        <h3>Добавить поездку</h3>
        <div class="mb-3">
            <label for="region" class="form-label">Регион</label>
            <select id="region" name="region_id" class="form-select" required>
                <option value="" disabled selected>Выберите регион</option>
                <?php foreach ($regions as $region): ?>
                    <option value="<?= $region['id'] ?>"><?= htmlspecialchars($region['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="courier" class="form-label">Курьер</label>
            <select id="courier" name="courier_id" class="form-select" required>
                <option value="" disabled selected>Выберите курьера</option>
                <?php foreach ($couriers as $courier): ?>
                    <option value="<?= $courier['id'] ?>"><?= htmlspecialchars($courier['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="departure_date" class="form-label">Дата выезда</label>
            <input type="date" id="departure_date" name="departure_date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Добавить</button>
        <div id="add-trip-response" class="mt-3"></div>
    </form>

    <!-- Форма фильтрации поездок -->
    <form method="GET" class="mb-4">
        <h3>Просмотреть поездки</h3>
        <div class="mb-3">
            <label for="date" class="form-label">Дата</label>
            <input type="date" id="date" name="date" class="form-control" value="<?= htmlspecialchars($selectedDate) ?>" required>
        </div>
        <button type="submit" class="btn btn-secondary">Показать поездки</button>
    </form>

    <!-- Таблица поездок -->
    <?php if ($selectedDate): ?>
        <h4>Поездки на <?= date('d-m-Y', strtotime($selectedDate)) ?>:</h4>
        <?php if ($trips): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Регион</th>
                        <th>Курьер</th>
                        <th>Дата выезда</th>
                        <th>Дата прибытия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trips as $trip): ?>
                        <tr>
                            <td><?= htmlspecialchars($trip['region_name']) ?></td>
                            <td><?= htmlspecialchars($trip['courier_name']) ?></td>
                            <td><?= date('d-m-Y', strtotime($trip['departure_date'])) ?></td> 
                            <td><?= date('d-m-Y', strtotime($trip['arrival_date'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>На выбранную дату поездок нет.</p>
        <?php endif; ?>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#add-trip-form').on('submit', function(event) {
                event.preventDefault();
                $.ajax({
                    url: 'add_trip.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#add-trip-response').text(response).addClass('text-success');
                    },
                    error: function() {
                        $('#add-trip-response').text('Ошибка добавления поездки').addClass('text-danger');
                    }
                });
            });
        });
    </script>
</body>
</html>