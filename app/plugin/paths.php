<?
	// Paths

	define('ROOT', $_SERVER['DOCUMENT_ROOT']);

	if(
		isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
		isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
	) {
		define('PROTOCOL', 'https://');
	} else {
		define('PROTOCOL', 'http://');
	}

	define('SERVER_NAME', $_SERVER['SERVER_NAME']);
	define('DOMAIN_ROOT', PROTOCOL.SERVER_NAME);
?>