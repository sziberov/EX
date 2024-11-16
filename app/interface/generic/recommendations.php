<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	$user_id = Session::getUserID();
	$page_title = D['title_recommendations'];
?>
<div _title><?= D['title_recommendations']; ?></div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->search_entity = 'links';
	$template->search_class = 'Link';
	$template->search_condition = "WHERE l.user_id = $user_id AND l.type_id = 6
								   ORDER BY l.creation_time DESC, l.id DESC";
	$template->template_title = 'generic/recommendations.objects';
	$template->render(true);
?>
<div _grid="h">
	<a _button href="/destroy_link?type_id=6"><?= D['button_remove_all']; ?></a>
</div>