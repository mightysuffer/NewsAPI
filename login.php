<?php
	session_start();	
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "vkr_news";
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
	die("Ошибка подключения: " . $conn->connect_error);}
	if (isset($_POST['submit'])) {
		$email_or_login = $_POST['email_or_login'];
		$password = $_POST['password'];
		$query = "SELECT * FROM user WHERE (email = '$email_or_login' OR login = '$email_or_login')";
		$result = $conn->query($query);
		if ($result->num_rows == 0) {
			echo "Пользователь не найден. <a href='registration.php' style='color: red;'>Зарегистрируйтесь</a>.";
			} else {
			$row = $result->fetch_assoc();
			$stored_password = $row['pass'];
			$login =  $row['login'];
			$user_id =  $row['id'];
			if ($password == $stored_password) {
				$_SESSION['username'] = $login;
				$_SESSION['user_id'] = $user_id;
				header("Location: dashboard.php");
				exit();
				} else {
				echo "Неверный пароль.";
				$password = "";
			}
		}
	}
	$conn->close();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Окно авторизации</title>
		<style>
			body {
			font-family: 'Roboto', Arial, sans-serif;
			text-align: center;
			}
			h2 {
			font-size: 24px;
			}
			.password-container {
			position: relative;
			display: inline-block;
			vertical-align: middle;
			margin-left: 5px;
			}
			.password-toggle-icon {
			position: absolute;
			top: 50%;
			right: 5px;
			transform: translateY(-50%);
			width: 32px;
			height: 32px;
			}
			.input-label {
			display: inline-block;
			text-align: right;
			width: 150px;
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
		<script>
			function togglePasswordVisibility() {
				var passwordInput = document.getElementById("password");
				var passwordToggle = document.getElementById("password-toggle");
				
				if (passwordInput.type === "password") {
					passwordInput.type = "text";
					passwordToggle.classList.add("show");
					} else {
					passwordInput.type = "password";
				passwordToggle.classList.remove("show");}
			}
		</script>
	</head>
	<body>
		<h2>Авторизация</h2>
		<form method="POST" action="">
			<label class="input-label" for="email_or_login">Email или логин:</label>
			<div class="password-container">
				<input type="text" id="email_or_login" name="email_or_login" required>
			</div><br><br>		
			<label class="input-label" for="password">Пароль:</label>
			<div class="password-container">
				<input type="password" id="password" name="password" required>
				<div id="password-toggle" class="password-toggle" onclick="togglePasswordVisibility()">
					<img src="hide.png" alt="Показать пароль" class="password-toggle-icon">
				</div>
			</div><br><br>	
			<input type="submit" name="submit" value="Войти">
			<p><a href="registration.php" style="font-weight: bold;">Зарегистрироваться</a></p>
		</form>
	</body>
</html>




