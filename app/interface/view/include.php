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

	$user_id = Session::getUserID();
	$page_title = $object->title.' - '.D['title_include'];

	$template = new Template('referrer');
	$template->object = $object;
	$template->render(true);
?>
<div _title><?= D['title_include']; ?></div>
<form _grid="v" method="post">
	<?
		$template = new Template('entities');
		$template->search_entity = 'links';
		$template->search_class = 'Link';
		$template->search_fields = 'l.*, COUNT(l_1.id) > 0 AS included';
		$template->search_condition = "LEFT JOIN links AS l_1 ON l_1.from_id = l.from_id AND l_1.to_id = $object->id AND l_1.user_id = l.user_id AND l_1.type_id = 4
									   WHERE l.user_id = $user_id AND l.type_id = 10
									   GROUP BY l.id
									   ORDER BY l.creation_time DESC, l.id DESC";
		$template->template_title = 'view/include.objects';
		$template->render(true);
	?>
	<div _flex="h">
		<button type="submit"><?= D['button_save']; ?></button>
		<button type="button" onclick="history.back();"><?= D['button_cancel']; ?></button>
		<button type="button" small_ onclick="$('form').find('input[type=&quot;checkbox&quot;]').prop('checked', true);"><?= D['button_select_all']; ?></button>
		<button type="button" small_ onclick="$('form').find('input[type=&quot;checkbox&quot;]').prop('checked', false);"><?= D['button_clear_all']; ?></button>
	</div>
</form>