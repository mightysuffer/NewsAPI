<?php
// Подключаемся к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "news";
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверяем соединение
if ($conn->connect_error) {
die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

// Считываем RSS-ленту
$rss_url = "https://mnogoblog.ru/feed"; // Замените на реальную ссылку на RSS-ленту
$rss_feed = simplexml_load_file($rss_url);

// Обрабатываем каждую новость в ленте
foreach ($rss_feed->channel->item as $item) {
$title = $conn->real_escape_string($item->title); // Экранируем специальные символы
$content = $conn->real_escape_string($item->description);

// Записываем данные в базу данных
$sql = "INSERT INTO news (title, content) VALUES ('$title', '$content')";
if ($conn->query($sql) === false) {
echo "Ошибка при выполнении запроса: " . $conn->error;
}
else echo "Done";
}

// Закрываем соединение с базой данных
$conn->close();
?>