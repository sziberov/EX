<?
	try {
		$object = new Object_($path[1] ?? null);
	} catch(Exception $e) {
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if($object->type_id != 4 && !Session::set()) {
		return include 'generic/login.php';
	}

	if($object->access_level_id < 5) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

//	$object->destroy();
	echo 'destroyed';

	$referrer = $_SERVER['HTTP_REFERER'] ?? '/';
	$referrer_page = explode('/', parse_url($referrer, PHP_URL_PATH))[1];
	$location = $referrer_page == 'destroy' ? '/' : $referrer;

//	exit(header('Location: '.$location));
?>