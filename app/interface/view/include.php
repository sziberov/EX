<?
	try {
		$object = new Object_($path[1] ?? null);
	} catch(Exception $e) {
		error_404:
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if($object->type_id != 3) {
		goto error_404;
	}

	if(!Session::set()) {
		return include 'generic/login.php';
	}

	if($object->access_level_id < 3) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$user = Session::getUser();
	$page_title = $object->title.' - '.D['title_include'];

	$template = new Template('referrer');
	$template->object = $object;
	$template->render(true);
?>
<div _title><?= D['title_include']; ?></div>
<?
	$template = new Template('entities');
	$template->search_entity = 'links';
	$template->search_class = 'Link';
	$template->search_condition = "JOIN objects AS o ON o.id = l.from_id WHERE l.type_id = 10 AND l.user_id = $user->id
								   GROUP BY o.id, l.id
								   ORDER BY l.creation_time DESC, l.id DESC";
	$template->template_title = 'view/include.objects';
	$template->render(true);
?>
<div _grid="h">
	<a _button href="/<?= $object->id; ?>"><?= D['button_save']; ?></a>
	<a _button href="/<?= $object->id; ?>"><?= D['button_cancel']; ?></a>
	<button wide_><?= D['button_select_all']; ?></button>
	<button wide_><?= D['button_clear_all']; ?></button>
</div>