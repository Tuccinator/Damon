<?php
require __DIR__ . '/vendor/autoload.php';

$http = new \Damon\Http('http://google.com');

$html = $http->connect()->getRawHtml();

$lexer = new \Damon\Lexer;

$parents = <<<'HTML'
	<div id="firstParent">
		<p id="paragraph">Hi</p>
	</div>
	<div id="secondParent">
		<p id="paragraph">Bye</p>
	</div>
HTML;

echo '<pre>';

print_r($lexer->parse($parents));

print_r($lexer->getParent('p'));

echo '</pre>';