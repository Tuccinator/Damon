<?php
ini_set('display_errors', 'Off');

require __DIR__ . '/vendor/autoload.php';

$http = new \Damon\Http('http://spiralsolutions.club/apis/sample.html');

$html = $http->connect()->getRawHtml();

$lexer = new \Damon\Lexer;

$parents = <<<'HTML'
	<div id="firstParent">
		<p id="paragraph1">Hi</p>
	</div>
	<div id="secondParent">
		<p id="paragraph2">Bye</p>
	</div>
HTML;

echo '<pre>';

$content = $lexer->parse($html);

$children = $lexer->getChildren('tr');

function getTableField($key) {
	switch($key) {
		case 0:
			return 'Quarter1';
			break;
		case 1:
			return 'Quarter2';
			break;
		case 2:
			return 'Quarter3';
			break;
		case 3:
			return 'Quarter4';
			break;
		case 4:
			return 'Time';
			break;
		case 5:
			return 'Days';
			break;
		case 6:
			return 'Room';
			break;
		case 7:
			return 'Class';
			break;
		case 8:
			return '1stPR';
			break;
		case 9:
			return '1stMP';
			break;
		case 10:
			return '2ndPR';
			break;
		case 11:
			return '2ndMP';
			break;
		case 12:
			return '3rdPR';
			break;
		case 13:
			return '3rdMP';
			break;
		case 14:
			return '4thPR';
			break;
		case 15:
			return '4thMP';
			break;
		case 16:
			return 'RegExam';
			break;
		case 17:
			return 'FinalExam';
			break;
		case 18:
			return 'FinalAvg';
			break;
		case 19:
			return 'Staff';
			break;
	}
}

$tableRows = $lexer->getChildren('tbody');

$row = 0;
$rows = [];
foreach ($tableRows as $key => $tableRow) {
	$columns = $lexer->getChildren('tr');
	// print_r($columns);

	$i = 0;
	foreach($columns as $column) {
		if($tableRow['id'] == $column['parent']) {
			$value = $column['value'];
			if($column['attributes']['nowrap'] == 'nowrap' || $column['attributes']['class'] == 'text-center') {
				$ahrefs = $lexer->getElementsByTagName('a');

				foreach($ahrefs as $ahref) {
					if($ahref['parent'] == $column['id']) {
						$value = '<a href="' . $ahref['attributes']['href'] . '">' . $ahref['value'] . '</a>';
					}
				}
			}
			
			$rows[$row][getTableField($i)] = $value;
			$i++;
		}
	}
	$row++;
}

print_r($rows);

echo '</pre>';