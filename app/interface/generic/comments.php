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
	$template->search_entity = 'links l_0';
	$template->search_class = 'Link';
	$template->search_fields = 'l_0.*, l_1.id IS NOT NULL AS reply';
	$template->search_condition = "LEFT JOIN links AS l_1 ON l_1.from_id = l_0.to_id AND l_1.type_id = 5
								   JOIN objects AS o_0 ON o_0.id = l_0.from_id
								   JOIN objects AS o_1 ON o_1.id = l_0.to_id
								   WHERE l_0.type_id = 5 AND (o_0.user_id = $user_id OR o_1.user_id = $user_id)
								   ORDER BY o_0.creation_time DESC,
											o_0.id DESC";
	$template->template_title = 'generic/comments.objects';
	$template->render(true);
?>