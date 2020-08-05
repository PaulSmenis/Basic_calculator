<?php
function check_correctness(string $expression): array {
	$negative = 0;
	$parentheses = 0;
	$parses_number = 0;
	$point = 0;
	$operand_number = 0;
	$last = 'start';
	$operand_indexes = [ '-' => [], '+' => [], '/' => [], '*' => [], '~' => [], '^' => [] ];

	for ($i = 0; $i < strlen($expression); $i++) {
		$letter = $expression[$i];

		switch($letter) {
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
				exit('-1');
			} else {
				$parses_number = 1;
				$last = 'number';
			}
			break;

			case '0':
			if (!$parses_number && $last === '0') {
				exit('-2');
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
				exit('-3');
			}
			break;

			case '(':
			if (!in_array($last, ['sign', '(', 'start'])) {
				exit('-4');
			} else {
				$parentheses += 1;
				$parses_number = 0;
				$last = '(';
				$operand_number = 0;
			}
			break;

			case ')':
			if ((!$operand_number && ($last != ')' && !$negative)) || !in_array($last, ['number', ')', '0']) || $parentheses < 1) {
				exit('-5');
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
				if (!$parentheses && in_array($letter, array_keys($operand_indexes)))
					$operand_indexes[$letter][] = $i;
				$last = 'sign';
			} else {
				exit('-6');
			}
			break;
		}
	}
	if (($last != ')' && $last != 'number' && $last != '0') || $parentheses) {
		exit('-7');
	} else {
		return $operand_indexes;
	}
}

function dissect_binom(string $expression): float {
	$binom_operator;	// Индекс первой по приоритету 
	// операции в строке

	while (!array_filter($operations = check_correctness($expression)) &&
			$expression[0] === '(') {	
		$expression = substr($expression, 1, strlen($expression) - 2);
	} // Это для удаления скобок типа ((((((выражение)))))), если такие есть

	if (array_column($operations, 0)) {	// Операнд это выражение
		$binom_operator = choose_operator_cascade($operations, 0);

		$substring1 = substr($expression, 0, $binom_operator);
		$substring2 = substr($expression, $binom_operator + 1, strlen($expression) - $binom_operator);

		$binom_operator = $expression[$binom_operator];
		switch ($binom_operator) {
			case '+':
			return dissect_binom($substring1) + dissect_binom($substring2);
			case '-':
			return dissect_binom($substring1) - dissect_binom($substring2);
			case '/':
			return dissect_binom($substring1) / dissect_binom($substring2);
			case '*':
			return dissect_binom($substring1) * dissect_binom($substring2);
			case '^':
			return dissect_binom($substring1) ** dissect_binom($substring2);
			case '~':
			return gmp_root(dissect_binom($substring1), dissect_binom($substring2));	// Целая часть после извлечения корня. Хз, норм корня нету
		}
	} else {	// Операнд это число
		return dissect_number($expression);
	}	
}

function dissect_number(string $expression): float {
	$sign = 0;
	$whole_part = 0;
	$frac_part = 0;

	if ($expression[0] === '-') {		// Отрицательное число
		if (substr_count($expression, '-') % 2) {
			$sign = 1;
		}
		$expression = str_replace('-', '', $expression);
	}

	if (substr_count($expression, '.')) { // Десятичная дробь
		$k = strripos($expression, '.') - 1;
		$whole_part = (int) $expression[$k];
		$frac_part = (float) $expression[$k + 2];

		$z = 1;
		for ($i = $k + 3; $i < strlen($expression); $z++, $i++) {
			$frac_part += (float) $expression[$i] / (10**$z);
		}
		$frac_part /= 10;
	} else {
		$whole_part = (int) $expression[-1];
		$k = strlen($expression) - 1;
	}

	for ($i = 1; $k > 0; $k--, $i++) {
		$whole_part += (int) $expression[$k-1] * 10**$i;
	}

	$whole_part += $frac_part;	// Неявное преобразование в float
	return ($sign) ? -$whole_part : $whole_part;
}

function choose_operator_cascade(array $operations, int $n): int {
	$first = $operations[array_keys($operations)[$n]];
	$second = $operations[array_keys($operations)[$n + 1]];
	if ($first) {
		if ($second) {
			return ($first[0] < $second[0]) ? $first[0] : $second[0];
		} else {
			return $first[0];
		}
	} else {
		if ($second) {
			return $second[0];
		} else {
			return choose_operator_cascade($operations, $n + 1);
		}
	}
}

?>