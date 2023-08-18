<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "vkr_news";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Ошибка подключения к базе данных: " . $conn->connect_error);
	}
	if (isset($_POST["register"])) {
		$email = $_POST["email"];
		$login = $_POST["login"];
		$password = $_POST["password"];
		$name = $_POST["name"];
		$checkQuery = "SELECT * FROM user WHERE email = '$email' OR login = '$login'";
		$checkResult = $conn->query($checkQuery);
		if ($checkResult->num_rows > 0) {
			$error = "Логин или почта уже используются";
			} else{
			$method = "aes-128-cbc";
			$secretKey = $login;
			$api_key = openssl_encrypt($login, $method, $secretKey);
			$cleankey = preg_replace('/[^a-zA-Z0-9]/', '', $api_key);	
			$insert_userdata_Query = "INSERT INTO user (email, login, pass, name) VALUES ('$email', '$login', '$password', '$name')";
			$insertResult = $conn->query($insert_userdata_Query);
			$getid_Query = "SELECT id FROM user WHERE login = '$login'";
			$getid_Result = mysqli_query($conn, $getid_Query);
			$id_row = mysqli_fetch_assoc($getid_Result);
			$user_id = $id_row['id'];
			$insert_api_Query = "INSERT INTO api_keys (api_key, user_id) VALUES ('$cleankey','$user_id')";
			$insert_api = $conn->query($insert_api_Query);
			if ($insertResult) {
				echo "<script>alert('Регистрация успешна!'); window.location.href = 'login.php';</script>";
				} else{
				$error = "Ошибка при регистрации";
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Окно регистрации</title>
		<style>
			body {
			font-family: 'Roboto', Arial, sans-serif;
			text-align: center;
			}	
			h2 {
			font-size: 24px;
			}	
			input[type="text"],
			input[type="password"],
			input[type="submit"] {
			display: block;
			margin: 0 auto;
			padding: 10px;
			font-size: 18px;
			width: 300px;
			}
		</style>
	</head>
	<body>
		<h2>Регистрация</h2>
		<form method="POST" action="">
			<label for="email">Email:</label>
			<input type="text" id="email" name="email" required><br><br>
			<label for="login">Логин:</label>
			<input type="text" id="login" name="login" required><br><br>	
			<label for="name">Имя:</label>
			<input type="text" id="name" name="name" required><br><br>	
			<label for="password">Пароль:</label>
			<input type="password" id="password" name="password" required><br><br>	
			<?php if (isset($error)) { ?>
				<p style="color: red;"><?php echo $error; ?></p>
			<?php } ?>		
			<input type="submit" name="register" value="Зарегистрироваться">
		</form>
	</body>
</html>
