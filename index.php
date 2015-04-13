<?php
require __DIR__ . '/vendor/autoload.php';

$http = new \Damon\Http('http://google.com');

$html = $http->connect()->getRawHtml();

$lexer = new \Damon\Lexer;

echo '<pre>';

print_r($lexer->parse($html));

echo '</pre>';