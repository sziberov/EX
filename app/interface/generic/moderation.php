<?
	/*
						Неподтверждённые включения всех пользователей во все разделы
	user_id				Неподтверждённые включения во все разделы пользователя
	section_id			Неподтверждённые включения всех пользователей в раздел
	section_id, user_id	Неподтверждённые включения в раздел пользователя
	*/

	if(!Session::set()) {
		return include 'generic/login.php';
	}

	try {
		$user = new Object_($_GET['user_id'] ?? null);

		if($user->type_id != 2) {
			$user = null;
		}
	} catch(Exception $e) {}
	try {
		$section = new Object_($_GET['section_id'] ?? null);

		if($section->type_id != 3) {
			$section = null;
		}
	} catch(Exception $e) {}

	$allow_advanced_control = Session::getSetting('allow_advanced_control');

	if(!$allow_advanced_control && (empty($user) || $user->id != Session::getUserID())) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$page_title = D['title_moderation'];
?>
<div _title><?= D['title_moderation']; ?></div>
<? if($allow_advanced_control && !empty($user) || !empty($section)) { ?>
	<div _grid="h">
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
		   if(!empty($section)) { ?>
			<div><?= D['string_section']; ?><b _badge><a href="<?= !empty($section->alias) ? '/'.$section->alias : '/'.$section->id; ?>"><?= e($section->title); ?></a></b></div>
		<? } ?>
	</div>
<? }

	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->search_entity = 'links';
	$template->search_class = 'Link';
	$template->search_condition = "JOIN objects AS o ON o.id = l.from_id
								   JOIN settings AS s ON s.link_id = l.id AND s.key = 'awaiting_moderation' AND s.value = 'true'
								   WHERE l.type_id = 4 ".(!empty($user) ? " AND o.user_id = $user->id" : '').(!empty($section) ? " AND l.to_id = $section->id" : '').'
								   GROUP BY o.id, l.id
								   ORDER BY l.creation_time DESC, l.id DESC';
	$template->template_title = 'generic/moderation.objects';
	$template->render(true);

	if($allow_advanced_control && !empty($user) || !empty($section)) { ?>
		<div _grid="h">
			<a _button href="/moderation<?= !$allow_advanced_control ? '?user_id='.$user->id : ''; ?>"><?= D['button_view_all']; ?></a>
		</div>
	<? }
?>