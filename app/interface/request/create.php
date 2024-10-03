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
	$type_id = intval($type_ids[0]);
	$subtype_id = isset($type_ids[1]) ? intval($type_ids[1]) : null;

	if(
							   !isset($types[$type_id]) ||
		!empty($subtype_id) && !isset($types[$type_id][$subtype_id])
	) {
		error_404:
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if($type_id != 4 && !Session::set()) {
		return include 'generic/login.php';
	}

	if(
		!empty($subtype_id) && (
			!in_array(null, $types[$type_id][$subtype_id]) || isset($_GET['to_id'])
		)
	) {
		try {
			$to = new Object_($_GET['to_id'] ?? null);
		} catch(Exception $e) {
			goto error_404;
		}

		if(!in_array($to->type_id, $types[$type_id][$subtype_id])) {
			goto error_404;
		}
	}

	if($type_id == 3 && (
		$subtype_id == 4 && ($to->access_level_id < 3 || $to->getSetting('deny_nonbookmark_inclusion')) ||  // TODO: Non session user page direct creation
		$subtype_id == 5 && $to->access_level_id < 2 ||
		$subtype_id == 8 && $to->getSetting('deny_claims')
	)) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$object_id = $type_id == 1 ? Object_::createGroupID() :
				($type_id == 3 ? Object_::createPlainID() :
				($type_id == 4 ? Object_::createSharedID() : null));

	if(!empty($object_id) && !empty($subtype_id)) {
		Link::createID($object_id, $to->id ?? null, $subtype_id);
	}

	exit(header("Location: /edit/$object_id"));
?>