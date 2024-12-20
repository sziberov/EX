<?
	$types = [
		1 => [		// group
			1 => [		// access
				1,			// group
				2,			// user
				3			// plain
			],
			4 => [		// inclusion
				2			// user
			],
			6 => [		// recommendation
				null		// nothing
			],
			10 => [		// bookmark
				null		// nothing
			]
		],
		2 => [		// user
			2 => [		// friend
				2			// user
			],
			4 => [		// inclusion
				2			// user
			],
			6 => [		// recommendation
				null		// nothing
			],
			10 => [		// bookmark
				null		// nothing
			]
		],
		3 => [		// plain
			4 => [		// inclusion
				2			// user
			],
			6 => [		// recommendation
				null		// nothing
			],
			7 => [		// avatar
				null		// nothing
			],
			9 => [		// template
				null		// nothing
			],
			10 => [		// bookmark
				null		// nothing
			]
		]
	];

	$from_id = $_GET['from_id'] ?? null;

	try {
		$from = new Object_($from_id);
	} catch(Exception $e) {
		error_404:
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	$link_type_id = $_GET['type_id'] ?? null;

	if(
		!isset($types[$from->type_id]) ||
		!isset($types[$from->type_id][$link_type_id])
	) {
		goto error_404;
	}

	if(!Session::set()) {
		return include 'generic/login.php';
	}

	if($from->access_level_id == 0 || $from->getSetting('awaiting_save')) {
		error_403:
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$to_id = $_GET['to_id'] ?? null;

	if(!in_array(null, $types[$from->type_id][$link_type_id]) || isset($to_id)) {
		try {
			$to = new Object_($to_id);
		} catch(Exception $e) {
			goto error_404;
		}

		if(!in_array($to->type_id, $types[$from->type_id][$link_type_id])) {
			goto error_404;
		}

		if(
								  $to->access_level_id < 1 ||
			$link_type_id != 1 && 							  $to->getSetting('awaiting_save') ||
			$link_type_id == 1 && $to->access_level_id < 5 ||
			$link_type_id == 2 &&							  $to->id != Session::getUserID() ||
			$link_type_id == 4 && $to->access_level_id < 3
		) {
			goto error_403;
		}
	}

	Link::createID($from->id, $to->id ?? null, $link_type_id);

	$referrer = $_SERVER['HTTP_REFERER'] ?? '/';
	$referrer_page = explode('/', parse_url($referrer, PHP_URL_PATH))[1];
	$location = $referrer_page == 'create_link' ? '/' : $referrer;

	exit(header('Location: '.$location));
?>