<?
	try {
		$object = new Object_($path[1] ?? null);
	} catch(Exception $e) {
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if($object->access_level_id == 0) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}
?>
<title><?= dictionary_getPageTitle($object->title.' - '.D['title_view_comments']); ?></title>
<?
	$template = new Template('referrer');
	$template->object = $object;
	$template->display_mode_id = 2;
	$template->render(true);

	include 'view.post-short.php';
?>
<b><?= D['title_view_comments']; ?></b>
<?
	$template = new Template('comments');
	$template->navigation_page = $navigation_page;
	$template->root_object = $object;
	$template->render(true);
?>
<? if($object->access_level_id >= 2) { ?>
	<div _grid="h">
		<a _button href="/create?to_id=<?= $object->id; ?>&type_id=3,5"><?= D['button_comment']; ?></a>
	</div>
<? } ?>
<div>&nbsp;</div>