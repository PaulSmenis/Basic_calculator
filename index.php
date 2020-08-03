<?php
declare(strict_types = 1);
header('Content-Type: text/html; charset=utf-8');

$expression = '4123+12'; // Убирать скобки типа (((((1+1)))))
$expression = str_replace(' ', '', $expression);

function check_correctness(string $expression): int {
	$negative = 0;
	$parentheses = 0;
	$parses_number = 0;
	$point = 0;
	$operand_number = 0;
	$last = 'start';

	for ($i = 0; $i < strlen($expression); $i++) {
		switch($expression[$i]) {
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9': 
			if ($last === '0' && !$parses_number) {
				return -1;
			} else {
				$parses_number = 1;
				$last = 'number';
			}
			break;

			case '0':
			if (!$parses_number && $last === '0') {
				return -2;
			} else {
				$last = '0';
			}
			break;

			case '.':
			if ($parses_number && !$point) {
				$point = 1;
			} else if (!$parses_number && $last === '0') {
				$point = 1;
				$parses_number = 1;
			} else {
				return -3;
			}
			break;

			case '(':
			if (!in_array($last, ['sign', '(', 'start'])) {
				return -4;
			} else {
				$parentheses += 1;
				$parses_number = 0;
				$last = '(';
				$operand_number = 0;
			}
			break;

			case ')':
			if ((!$operand_number && ($last != ")" && !$negative)) || !in_array($last, ['number', ')', '0']) || $parentheses < 1) {
				return -5;
			} else {
				if ($negative) {
					$negative = 0;
				}
				$parentheses -= 1;
				$parses_number = 0;
				$last = ')';
			}
			break;

			case '-':
			if (in_array($last, ['sign', 'start', '('])) {
				$negative = 1;
				break;
			}
			case '+':
			case '~':
			case '/':
			case '*':
			case '^':
			if (($parses_number && $expression[$i-1] !== '-') || $last === '0' || $last === ')') {
				$parses_number = 0;
				$operand_number += 1;
				if (!$parentheses && in_array($expression[$i], ["-", "+"]))
					echo $i." ";
				$last = 'sign';
			} else {
				return -6;
			}
			break;
		}
	}
	var_dump($parentheses);
	if (($last != ')' && $last != 'number') || $parentheses) {
		return -7;
	} else {
		return $operand_number + 1;
	}
}

var_dump((float) "-5");
var_dump(check_correctness($expression));

?>

<!DOCTYPE html>
<html>
<head>
	<title>Calculator</title>
	<style>
		.layer > div {
			border: 1px solid black;
			flex-grow: 1;
		}
		.layer {
			display: flex;
			flex-grow: 1;
		}
		#container {
			display: flex;
			flex-direction: column;
			width: 20rem;
			height: 10rem;
		}
	</style>
</head>
<body>
	<div id="container">
		<div class="layer">
			<div></div>
		</div>
		<div class="layer">
			<div></div>
			<div></div>
		</div>
		<div class="layer">
			<div></div>
			<div></div>
			<div></div>
		</div>
	</div>
</body>
</html>