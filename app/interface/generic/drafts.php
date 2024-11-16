<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	$user_id = Session::getUserID();
	$page_title = D['title_drafts'];
?>
<div _title><?= D['title_drafts']; ?></div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->search_entity = 'objects';
	$template->search_condition = "JOIN settings AS s ON s.object_id = o.id WHERE o.user_id = $user_id AND s.key = 'awaiting_save' AND s.value = 'true'
								   ORDER BY o.creation_time DESC, o.id DESC";
	$template->template_title = 'generic/archive.objects';
	$template->render(true);
?>
<div _grid="h">
	<button><?= D['button_delete_all']; ?></button>
</div>