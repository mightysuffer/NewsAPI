<?php
	require __DIR__ . '/vendor/autoload.php';
	$client = OpenAI::client('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');
	
	function getCategoryFromProcessedText($processedText) {
		$client = OpenAI::client('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');
		$data = $client->completions()->create([
		'model' => 'text-davinci-003',
		'prompt' => "Определи категорию следующей новости и выведи в ответ только одно слово без точек и каких либо других слов, например Политика, Искусство, Финансы и так далее:".$processedText
		]);
		$category = $data['choices'][0]['text'];
		return $category;
	}
	$rssFeed =  "https://rss.newsru.com/all_news";
	$xml = simplexml_load_file($rssFeed);
	if ($xml) {
		foreach ($xml->channel->item as $item) {
			$title = $item->title;
			$date = date("Y-m-d H:i:s", strtotime($item->pubDate));
			$description = $item->description;
			
			
			$category = getCategoryFromProcessedText($description);
			
			
			
			echo $category;
		}
	}
	
?>