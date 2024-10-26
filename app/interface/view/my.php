<?
	try {
		$object = new Object_($path[1] ?? null);
	} catch(Exception $e) {
		error_404:
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if($object->type_id == 4) {
		goto error_404;
	}

	if(!Session::set()) {
		return include 'generic/login.php';
	}

	if($object->access_level_id == 0 || $object->getSetting('awaiting_save')) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$user = Session::getUser();
	$page_title = $object->title.' - '.D['title_my'];

	$template = new Template('referrer');
	$template->object = $object;
	$template->render(true);
?>
<div _title><?= D['title_my']; ?></div>
<?
	$template = new Template('entities');
	$template->search_entity = 'links';
	$template->search_class = 'Link';
	$template->search_fields = 'l.*, COUNT(l_1.id) AS inclusions_count';
	$template->search_condition = "JOIN links AS l_1 ON l_1.type_id = l.type_id AND (l_1.user_id = l.user_id AND l_1.type_id != 4 OR
																					 l_1.to_id = l.to_id)
								   WHERE l.from_id = $object->id AND (l.user_id = $user->id AND l.type_id IN (2, 6, 7, 9, 10) OR
																	  l.to_id = $user->id AND l.type_id = 4)
								   GROUP BY l.id
								   ORDER BY l.creation_time ASC, l.id ASC";
	$template->template_title = 'view/my.objects';
	$template->template_namespace = [
		'object' => $object,
		'user' => $user
	];
	$template->render(true);
?>