<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	$user_id = Session::getUserID();
?>
<title><?= dictionary_getPageTitle(D['title_drafts']); ?></title>
<div _title><?= D['title_drafts']; ?></div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->navigation_page = $navigation_page;
	$template->navigation_items_per_page = $navigation_items_per_page;
	$template->search_condition = "JOIN settings AS s ON s.object_id = o.id WHERE o.user_id = $user_id AND s.key = 'awaiting_save' AND s.value = 'true'
								   ORDER BY o.creation_time DESC, o.id DESC";
	$template->render(true);
?>
<div _grid="h">
	<button><?= D['button_delete_all']; ?></button>
</div>