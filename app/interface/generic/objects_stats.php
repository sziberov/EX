<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	if(!empty($_GET['user_id'])) {
		try {
			$user = new Object_($_GET['user_id']);

			if($user->type_id != 2) {
				$user = null;
			}
		} catch(Exception $e) {}
	}

	$allow_advanced_control = Session::getSetting('allow_advanced_control');

	if(!$allow_advanced_control && (empty($user) || $user->id != Session::getUserID())) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$page_title = D['title_objects_stats'];
?>
<div _title><?= D['title_objects_stats']; ?></div>
<? if($allow_advanced_control && !empty($user)) { ?>
	<div _flex="h wrap">
		<div><?= D['string_user']; ?></div>
		<?
			$template = new Template('user');
			$template->object = $user;
			$template->time_display_mode_id = 0;
			$template->render(true);
		?>
	</div>
<? }

	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->search_condition = "LEFT JOIN visits AS v ON v.object_id = o.id
								   WHERE o.type_id IN (1, 3)".(!empty($user) ? " AND o.user_id = $user->id" : '')."
								   GROUP BY o.id
								   ORDER BY COUNT(v.id) DESC, o.creation_time DESC, o.id DESC";
	$template->template_title = 'generic/objects_stats.objects';
	$template->render(true);

	if($allow_advanced_control && !empty($user)) { ?>
		<div _grid="h">
			<a _button href="/objects_stats"><?= D['button_view_all']; ?></a>
		</div>
	<? }
?>