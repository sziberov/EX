<?
	$value = $_GET['language'];

	setcookie('language', $value, 2147483647, '/');

	exit(header("Location: {$_SERVER['HTTP_REFERER']}"));
?>