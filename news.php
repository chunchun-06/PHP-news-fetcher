<?php
if ($argc < 2) {
    echo "Please provide a keyword\n";
    exit;
}
require 'config.php';
$key = urlencode($argv[1]); 
// $URL = "https://newsapi.org/v2/top-headlines?q=$key&language=en&sortBy=publishedAt&pageSize=5&apiKey=$API";
$URL = "https://newsapi.org/v2/everything?q=$key&language=en&sortBy=publishedAt&pageSize=5&apiKey=$API";


$options = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: NewsAPI/1.0 (chunchunrai23@gmail.com)\r\n"
    ]
];
$context = stream_context_create($options);
$response = @file_get_contents($URL, false, $context);
if ($response === false) {
    echo "Failed to fetch\n";
    exit;
}
$value = json_decode($response, true);
if ($value && $value['status'] === 'ok' && count($value['articles']) > 0) {
    $olddate=[];
    if(file_exists('index.json')){
        $olddata=json_decode(file_get_contents('index.json'),true);
    }
    $merge=array_merge($olddata['articles'] ?? [],$value['articles']);
    file_put_contents('index.json', json_encode(['articles' => $merge], JSON_PRETTY_PRINT));
    $articles = $value['articles'];
    for ($i = 0; $i < count($articles); $i++) {
        echo "Title: " . $articles[$i]['title'] . "\n";
        echo "URL: " . $articles[$i]['url'] . "\n";
        echo "--------------------------\n";
    }
} else {
    echo "No articles found or error occurred\n";
    if (isset($value['message'])) {
        echo $value['message'] . "\n";
    }
}
?>
