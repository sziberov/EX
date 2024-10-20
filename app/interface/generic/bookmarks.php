<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	$user_id = Session::getUserID();
	$page_title = D['title_bookmarks'];
?>
<div _title><?= D['title_bookmarks']; ?></div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->search_condition = "JOIN links AS l ON l.from_id = o.id AND l.user_id = $user_id AND l.type_id = 10
								   ORDER BY l.creation_time DESC, l.id DESC";
	$template->render(true);
?>
<div _grid="h">
	<button><?= D['button_remove_all']; ?></button>
</div>