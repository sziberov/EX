<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	$user_id = Session::getUserID();
	$page_title = D['title_archive'];
?>
<div _title><?= D['title_archive']; ?></div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->search_condition = "WHERE o.user_id = $user_id
								   ORDER BY o.creation_time DESC, o.id DESC";
	$template->render(true);
?>
<div _grid="h">
	<a _button href="/create?type_id=3"><?= D['button_create']; ?></a>
	<button><?= D['button_delete_all']; ?></button>
</div>