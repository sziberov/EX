<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	$user_id = Session::getUserID();
	$page_title = D['title_friends'];
?>
<div _title><?= D['title_friends']; ?></div>
<?
	$template = new Template('entities');
	$template->search_entity = 'links';
	$template->search_class = 'Link';
	$template->search_fields = 'l.*, l_1.creation_time AS mutual_creation_time';
	$template->search_condition = "LEFT JOIN links AS l_1 ON l_1.from_id = l.to_id AND l_1.to_id = l.from_id AND l_1.type_id = l.type_id
								   WHERE l.to_id = $user_id AND l.type_id = 2
								   ORDER BY l.creation_time ASC, l.id ASC";
	$template->template_title = 'generic/friends.objects';
	$template->template_namespace = [
		'user_id' => $user_id
	];
	$template->render(true);
?>