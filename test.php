<?php
	/*$arr = openssl_get_cipher_methods();
		foreach ($arr as $n => $row) {
		echo ($n + 1) . '.' . $row . "<br>\r\n";
	}*/
		
	$textToEncrypt = "admin";
	$secretKey = "admin";
	$method = "aes-128-cbc";
	for($i = 0;$i<5;$i++) {
		$encrypted = openssl_encrypt($textToEncrypt, $method, $secretKey);
		echo $method . ': ' . $encrypted . "\n";
	}
	echo '</pre>';
?>