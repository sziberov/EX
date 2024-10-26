<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	$allow_advanced_control = Session::getSetting('allow_advanced_control');

	if(!$allow_advanced_control) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$page_title = D['title_users_stats'];
?>
<div _title><?= D['title_users_stats']; ?></div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->search_entity = 'objects';
	$template->search_fields = 'o.*,
								ROW_NUMBER() OVER (ORDER BY COALESCE(originals_size, 0) DESC, o.id ASC) AS position,
								COALESCE(originals_count, 0) AS originals_count,
								COALESCE(originals_size, 0) AS originals_size,
								COALESCE(duplicates_count, 0) AS duplicates_count,
								COALESCE(duplicates_size, 0) AS duplicates_size,
								COALESCE(hits_count, 0) AS hits_count,
								COALESCE(hosts_count, 0) AS hosts_count,
								COALESCE(guests_count, 0) AS guests_count';
	$template->search_condition = 'LEFT JOIN uploads_stats AS us ON us.user_id = o.id
								   LEFT JOIN visits_stats AS vs ON vs.object_id = o.id
								   WHERE o.type_id = 2
								   ORDER BY position ASC';
	$template->template_title = 'generic/users_stats.objects';
	$template->render(true);
?>