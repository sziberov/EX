<?
	$types = [
	//	1,	// access
		2,	// friend
	//	3,	// notification
	//	4,	// inclusion
	//	5,	// comment
		6,	// recommendation
	//	7,	// avatar
	//	8,	// claim
	//	9,	// template
		10,	// bookmark
	//	11	// private message
	];

	try {
		$link = new Link($path[1] ?? null);
	} catch(Exception $e) {
		error_404:
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if(!in_array($link->type_id, $types)) {
		goto error_404;
	}

	if(!Session::set()) {
		return include 'generic/login.php';
	}

	if($link->access_level_id == 0) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$link->destroy();

	$referrer = $_SERVER['HTTP_REFERER'] ?? '/';
	$referrer_page = explode('/', parse_url($referrer, PHP_URL_PATH))[1];
	$location = $referrer_page == 'destroy' ? '/' : $referrer;

	exit(header('Location: '.$location));
?>