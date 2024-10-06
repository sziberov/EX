<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	$user_id = Session::getUserID();
	$page_title = D['title_comments'];
?>
<div _title><?= D['title_comments']; ?></div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->navigation_page = $navigation_page;
	$template->navigation_items_per_page = $navigation_items_per_page;
	$template->search_entity = 'links';
	$template->search_class = 'Link';
	$template->search_condition = "JOIN objects AS o ON o.id = l.from_id WHERE l.type_id = 5 AND l.user_id = $user_id
								   GROUP BY o.id, l.id
								   ORDER BY o.creation_time DESC, o.id DESC";
	$template->template_title = 'generic/comments.objects';
	$template->render(true);
?>
<div _grid="h">
	<button><?= D['button_delete_all']; ?></button>
</div>