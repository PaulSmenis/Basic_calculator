<?php
	declare(strict_types = 1);
	header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="zxx">
<head>
	<title>Calculator</title>
	<link rel="stylesheet" href="styles.css" />
</head>
<body>
	<?php
	$expression = "Введите выражение";
	if (isset($_GET['expression'])) {
		include 'script.php';

		$expression = $_GET['expression'];
		$expression = str_replace(' ', '', $expression);
		$expression = dissect_binom($expression);
	}
	?>
	<form action="" method="get">
		<input type="text" name="expression" autocomplete="off" placeholder="<?php echo $expression ?>"/>
	</form>
</body>
</html>

