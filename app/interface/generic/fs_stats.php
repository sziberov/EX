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

	$page_title = D['title_fs_stats'];
?>
<div _title><?= D['title_fs_stats']; ?></div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->search_entity = 'fs';
	$template->search_class = 'FileServer';
	$template->template_title = 'generic/fs_stats.fs';
	$template->render(true);
?>