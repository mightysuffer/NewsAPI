<?php
	$host = 'localhost';
	$username = 'root';
	$password = '';
	$dbName = 'vkr_news';
	if (isset($_POST['logout'])) {
		session_start();
		session_unset();
		session_destroy();
		header("Location: login.php");
		exit;
	}
	$connection = mysqli_connect($host, $username, $password, $dbName);
	if (!$connection) {
		die("Ошибка подключения к базе данных: " . mysqli_connect_error());
	}
	session_start();
	$loggedInUser = $_SESSION['username'];
	$user_id = $_SESSION['user_id'];
	$api_query = "SELECT api_key FROM api_keys WHERE user_id = '$user_id'";
	$api_result = mysqli_query($connection, $api_query);
	$api_row = mysqli_fetch_assoc($api_result);
	$api_key = $api_row['api_key'];
	$categoriesQuery = "SELECT DISTINCT category FROM categories";
	$categoriesResult = mysqli_query($connection, $categoriesQuery);
	$categories = mysqli_fetch_all($categoriesResult, MYSQLI_ASSOC);
	$newsQuery = "SELECT a.date, a.time, n.title, n.full_text, n.source_id, c.category FROM news n JOIN attributes a ON n.id = a.news_id JOIN categories c ON a.category_id = c.id";
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$category = isset($_POST['category']) ? $_POST['category'] : '';
		if (!empty($category)) {
			$newsQuery .= " WHERE c.category = '$category'";
		}
		$newsQuery .= " ORDER BY date DESC";
		$newsResult = mysqli_query($connection, $newsQuery);
		$news = mysqli_fetch_all($newsResult, MYSQLI_ASSOC);
	}
	$newsResult = mysqli_query($connection, $newsQuery);
	$news = mysqli_fetch_all($newsResult, MYSQLI_ASSOC);
	mysqli_close($connection);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Новостная панель</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script>
			function runNewsCollection() {
				$.ajax({
					url: 'news_collect.php',
					method: 'GET',
					success: function(response) {
						console.log('Скрипт успешно запущен в фоновом режиме.');
					},
					error: function(xhr, status, error) {
						console.error('Ошибка при запуске скрипта в фоновом режиме:', error);
					}
				});
			}
			function copyToClipboard(text) {
				var tempInput = document.createElement("input");
				tempInput.value = text;
				document.body.appendChild(tempInput);
				tempInput.select();
				document.execCommand("copy");
				document.body.removeChild(tempInput);
				alert("Ключ скопирован в буфер обмена");
			}
		</script>
		<button onclick="runNewsCollection()">Запустить сбор новостей</button>
		<style>
			.panel {
			padding: 20px;
			background-color: #f1f1f1;
			}	
			.panel-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			}	
			.panel-username {
			margin-right: 20px;
			}
			.news-list {
			list-style: none;
			padding: 0;
			margin: 0;
			}	
			.news-item {
			margin-bottom: 10px;
			}	
			.news-title {
			font-weight: bold;
			}	
			.news-date {
			color: #888;
			}
			.news-text {
			white-space: pre-wrap;
			}
		</style>
	</head>
	<body>
		<div class="panel">
			<div class="panel-header">
				<div class="panel-username">Привет, <?php echo $loggedInUser; ?>
					<button onclick="copyToClipboard('<?php echo $api_key; ?>')">Скопировать API-ключ</button>
				</div>
				<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="hidden" name="logout" value="true">
					<button type="submit">Выйти</button>
				</form>
			</div>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<select name="category">
					<option value="">Все категории</option>
					<?php foreach ($categories as $categoryItem): ?>
					<option value="<?php echo $categoryItem['category']; ?>"><?php echo $categoryItem['category']; ?></option>
					<?php endforeach; ?>
				</select>
				<button type="submit">Фильтровать</button>
			</form>
			<ul class="news-list">
				<?php foreach ($news as $newsItem) : ?>
				<li class="news-item">
					<h3 class="news-title"><?php echo $newsItem['title']; ?></h3>
					<p class="news-date"><?php echo $newsItem['date']." ";  echo $newsItem['time'];?></p>
					<p class="news-category"><?php echo "Категория: ".$newsItem['category']; ?></p>
					<div class="news-text"><?php echo $newsItem['full_text']; ?></div>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</body>
</html>




