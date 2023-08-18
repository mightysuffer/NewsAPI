<?php
	require __DIR__ . '/vendor/autoload.php';
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "vkr_news";
	$conn = new mysqli($servername, $username, $password, $dbname);
	function removeLeadingNonLetter($str) {
		while (strlen($str) > 0 && !ctype_alpha($str[0])) {
			$str = substr($str, 1);
		}
		return $str;
	}
	function formatString($str) {
		$str = removeLeadingNonLetter($str);
		
		if (strlen($str) > 0) {
			$str = ucfirst($str);
			
			if ($str[strlen($str) - 1] !== '.') {
				$str .= '.';
			}
		}
		return $str;
	}
	function getCategoryFromProcessedText($title) {
		$client = OpenAI::client('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');
		$data = $client->completions()->create([
		'model' => 'text-davinci-003',
		'prompt' => "Define the category of the next news and output in response only one word without dots and any other words, such as Politics, Art, Finance, and so on:".$title
		]);
		$category = $data['choices'][0]['text'];
		return $category;
	}
	if ($conn->connect_error) {
		die("Ошибка подключения к базе данных: " . $conn->connect_error);
	}
	$count = 0;
	$link_query = "SELECT id, link FROM sources";
	$link_result = $conn->query($link_query);
	while ($link_row = $link_result->fetch_assoc()) {
		$rssFeed = $link_row['link'];
		$link_id = $link_row['id'];		
		$xml = simplexml_load_file($rssFeed);	
		if ($xml) {
			foreach ($xml->channel->item as $item) {
				$title = $item->title;
				$pubDate = $item->pubDate;
				$link = $item->link;
				$date = date("Y-m-d", strtotime($pubDate)); 
				$time = date("H:i:s", strtotime($pubDate)); 
				$description_full = $item->description;
				$description = preg_replace('/[^а-яА-ЯёЁ,\.\-\s]/u', '', $description_full);
				$checkQuery = "SELECT * FROM news WHERE title = '$title' or link = '$link'";
				$checkResult = $conn->query($checkQuery);	
				if ($checkResult->num_rows == 0) {
					$category = getCategoryFromProcessedText($title);
					$str_cat = formatString($category);
					$cat_checkQuery = "SELECT * FROM categories WHERE category = '$str_cat'";
					$cat_checkResult = $conn->query($cat_checkQuery);
					if ($cat_checkResult->num_rows == 0) {
						$cat_insertQ = "INSERT INTO categories (category) VALUES ('$str_cat')";
						$conn->query($cat_insertQ);
					}
					$cat_idrow = $cat_checkResult->fetch_assoc();
					$cat_id = $cat_idrow['id'];
					$news_insertQuery = "INSERT INTO news (title, full_text, link, source_id) VALUES ('$title', '$description', '$link', '$link_id')";
					$news_insertResult = $conn->query($news_insertQuery);
					$news_idQ = "SELECT id FROM news WHERE title = '$title'";
					$news_idR = $conn->query($news_idQ);
					$news_idrow = $news_idR->fetch_assoc();
					$news_id = $news_idrow['id'];
					$attr_insQ = "INSERT INTO attributes (date, time, category_id, news_id) VALUES ('$date', '$time', '$cat_id', '$news_id')";
					$attr_insR = $conn->query($attr_insQ);				
					$count++;
					sleep(2);
					if($count > 58){
						sleep(62);
						$count = 0;
					}
				}
			}
		}
	}
	$conn->close();
?>
