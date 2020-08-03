<?php
declare(strict_types = 1);
header("Content-Type: text/html; charset=utf-8");

$expression = "(3 * 3 * (- 2 + 2 + (3 * 3 + 3 + 3 - 3)))";
$expression = str_replace(' ', '', $expression);

function check_correcntess(string $expression): int {
	$parentheses = 0;
	$parses_number = 0;
	$point = 0;
	$operand_number = 0;
	$last = "start";

	for ($i = 0; $i < strlen($expression); $i++) {
		switch($expression[$i]) {
			case "1":case "2":case "3":case "4":case "5":
			case "6":case "7":case "8":case "9": {
				if ($last === "0" && !$parses_number) {
					return -1;
				} else {
					$parses_number = 1;
					$last = "number";
				}
			}
			break;

			case "0": {
				if (!$parses_number && $last === "0") {
					return -2;
				} else {
					$last = "0";
				}
			}
			break;

			case ".": {
				if ($parses_number && !$point) {
					$point = 1;
				} else if (!$parses_number && $last === "0") {
					$point = 1;
					$parses_number = 1;
				} else {
					return -3;
				}
			}
			break;

			case "(": {
				if (!in_array($last, ["sign", "(", "start"])) {
					return -4;
				} else {
					$parentheses += 1;
					$parses_number = 0;
					$last = "(";
					$operand_number = 0;
					echo "\n\n";
				}
			}
			break;

			case ")": {
				if (!$operand_number || !in_array($last, ["number", ")", "0"]) || $parentheses < 1) {
					return -5;
				} else {
					$parentheses -= 1;
					$parses_number = 0;
					$last = ")";
				}
			}
			break;

			case "-": {
				if (in_array($last, ["sign", "start", "("])) { 
					break;
				}
			}
			case "+":case "~":case "/":case "*":case "^": {
				if (($parses_number && $expression[$i-1] !== "-") || $last === "0" || $last === ")") {
					$parses_number = 0;
					$operand_number += 1;
					echo ($operand_number);
					$last = "sign";
				} else {
					return -6;
				}
			} 
			break;
		}
	}
	if ($last != ")" && $last != "number") {
		return -7;
	} else {
		return $operand_number + 1;
	}
}

var_dump(check_correcntess($expression));


?>