<?
	setcookie('language', http_getArgument('language'), 2147483647, '/');

	$referrer = $_SERVER['HTTP_REFERER'] ?? '/';
	$referrer_page = explode('/', parse_url($referrer, PHP_URL_PATH))[1];
	$location = $referrer_page == 'language' ? '/' : $referrer;

	exit(header('Location: '.$location));
?>