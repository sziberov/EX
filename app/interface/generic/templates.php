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
	$template->navigation_page = $navigation_page;
	$template->navigation_items_per_page = $navigation_items_per_page;
	$template->search_condition = "JOIN links AS l ON l.from_id = o.id AND l.user_id = $user_id AND l.type_id = 9";
	$template->render(true);
?>
<div _grid="h">
	<button><?= D['button_create']; ?></button>
	<button><?= D['button_remove_all']; ?></button>
</div>