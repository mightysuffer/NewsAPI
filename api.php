<?php
	if (isset($_GET['api_key'])){
		$api_key = $_GET['api_key'];
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "vkr_news";
		$conn = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) {
		die("Ошибка подключения: " . $conn->connect_error);}
		$keyMatch = false;
		$keyQuery = "SELECT api_key FROM api_keys WHERE api_key IS NOT NULL";
		$keyResult = mysqli_query($conn, $keyQuery);
		while ($api_row = mysqli_fetch_assoc($keyResult)) {
			$key = $api_row['api_key'];
			if ($key === $api_key) {
				$keyMatch = true;
			break;}}	
			if ($keyMatch) {
				$currentDateTime = date("Y-m-d H:i:s");
				$getid_Query = "SELECT user_id FROM api_keys WHERE api_key = '$api_key'";
				$getid_Result = mysqli_query($conn, $getid_Query);
				$id_row = mysqli_fetch_assoc($getid_Result);
				$user_id = $id_row['user_id'];
				$api_query = "INSERT INTO api_requests (datetime, user_id) VALUES 
				('$currentDateTime','$user_id')";
				$api_result = mysqli_query($conn, $api_query);
				$category = isset($_GET['category']) ? $_GET['category'] : '';
				$query = "SELECT a.date, a.time, n.title, n.full_text, s.name, 
				c.category, n.link FROM news n JOIN attributes a ON n.id = a.news_id JOIN 
				categories c ON a.category_id = c.id JOIN sources s ON n.source_id = s.id";
				if (!empty($category)) {
					$category = $conn->real_escape_string($category);
				$query .= " WHERE c.category = '$category'";}
				$result = $conn->query($query);
				if ($result->num_rows > 0) {
					$data = array();
					while ($row = $result->fetch_assoc()) {
					$data[] = $row;}
					header('Content-Type: application/json');
					echo json_encode($data);
					} else {
					header('Content-Type: application/json');
					$response = array('succes' => true, 'response' =>"Нет данных для отображения.");
				echo json_encode($response);}
				} else {
				header('Content-Type: application/json');
				$response = array('succes' => false, 'response' => 'Неверный api ключ.');
				echo json_encode($response);
			exit();}
	$conn->close();}
	else {
		header('Content-Type: application/json');
		$response = array(
		'succes' => false,
		'response' => 'Ошибка получения api-ключа.');
	echo json_encode($response);}
?>
