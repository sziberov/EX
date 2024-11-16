<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	$user_id = Session::getUserID();
	$page_title = D['title_templates'];
?>
<div _title><?= D['title_templates']; ?></div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->search_condition = "JOIN links AS l ON l.from_id = o.id AND l.user_id = $user_id AND l.type_id = 9";
	$template->render(true);
?>
<div _grid="h">
	<a _button href="/create?type_id=3,9"><?= D['button_create']; ?></a>
	<a _button href="/destroy_link?type_id=9"><?= D['button_remove_all']; ?></a>
</div>