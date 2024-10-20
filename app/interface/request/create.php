<?
	$types = [
		1 => [],	// group
		3 => [		// plain
			4 => [		// inclusion
				2,			// user
				3			// plain
			],
			5 => [		// comment
				1,			// group
				2,			// user
				3			// plain
			],
			7 => [		// avatar
				null		// nothing
			],
			8 => [		// claim
				1,			// group
				2,			// user
				3			// plain
			],
			11 => [		// private message
				2 			// user
			]
		],
		4 => []		// shared
	];

	$type_id = $_GET['type_id'] ?? '';
	$type_ids = preg_match('/^\d+(,\d+)?$/', $type_id) ? explode(',', $type_id) : ['-1'];
	$from_type_id = intval($type_ids[0]);
	$link_type_id = isset($type_ids[1]) ? intval($type_ids[1]) : null;

	if(
								 !isset($types[$from_type_id]) ||
		!empty($link_type_id) && !isset($types[$from_type_id][$link_type_id])
	) {
		error_404:
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if($from_type_id != 4 && !Session::set()) {
		return include 'generic/login.php';
	}

	$to_id = $_GET['to_id'] ?? null;

	if(
		!empty($link_type_id) && (
			!in_array(null, $types[$from_type_id][$link_type_id]) || isset($to_id)
		)
	) {
		try {
			$to = new Object_($to_id);
		} catch(Exception $e) {
			goto error_404;
		}

		if(!in_array($to->type_id, $types[$from_type_id][$link_type_id])) {
			goto error_404;
		}

		if($to->access_level_id == 0 || $to->getSetting('awaiting_save')) {
			error_403:
			$error = D['error_page_forbidden'];
			http_response_code(403);
			return include 'plugin/error.php';
		}
	}

	if($from_type_id == 3 && (
		$link_type_id == 4 && ($to->access_level_id < 3 || $to->getSetting('deny_nonbookmark_inclusion')) ||  // TODO: Non session user page direct creation
		$link_type_id == 5 &&  $to->access_level_id < 2 ||
		$link_type_id == 8 &&  $to->getSetting('deny_claims')
	)) {
		goto error_403;
	}

	$from_id = $from_type_id == 1 ? Object_::createGroupID() :
			  ($from_type_id == 3 ? Object_::createPlainID() :
			  ($from_type_id == 4 ? Object_::createSharedID() : null));

	if(!empty($from_id) && !empty($link_type_id)) {
		Link::createID($from_id, $to->id ?? null, $link_type_id);
	}

	exit(header("Location: /edit/$from_id"));
?>