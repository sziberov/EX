<?
	Session::logout();

	$referrer = $_SERVER['HTTP_REFERER'] ?? '/';
	$referrerPage = explode('/', parse_url($referrer, PHP_URL_PATH))[1];
	$location = $referrerPage == 'logout' ? '/' : $referrer;

	exit(header('Location: '.$location));
?>