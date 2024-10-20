<?
	try {
		$object = new Object_($path[1] ?? null);
	} catch(Exception $e) {
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if($object->access_level_id < 4) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$page_title = $object->title.' - '.D['title_view_inclusions'];

	$template = new Template('referrer');
	$template->object = $object;
	$template->display_mode_id = 2;
	$template->render(true);

	include 'view.post-short.php';
?>
<b><?= D['title_view_inclusions']; ?></b>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 1;
	$template->navigation_template_title = 'navigation-short';
	$template->navigation_items_per_page = 24;
	$template->search_entity = 'links';
	$template->search_class = 'Link';
	$template->search_condition = "JOIN objects AS o ON o.id = l.to_id WHERE l.from_id = $object->id AND l.type_id = 4";
	$template->template_title = 'view/view_inclusions.objects';
	$template->render(true);
?>
<div>&nbsp;</div>