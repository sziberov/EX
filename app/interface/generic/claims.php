<?
	/*
						Жалобы на все объекты от всех пользователей									Все жалобы
	user_id				Жалобы на все объекты пользователя, жалобы пользователя на все объекты		Все на мои и исходящие на чужие
	object_id			Жалобы на объект от всех пользователей										Входящие на объект
	object_id, user_id	Жалобы на объект от пользователя											Входящие и мои исходящие на объект
																									Исходящие на чужие
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
		$section = new Object_($_GET['object_id'] ?? null);

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
?>
<title><?= dictionary_getPageTitle(D['title_claims']); ?></title>
<div _title><?= D['title_claims']; ?></div>
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
			<div><?= D['string_section']; ?><b _badge><a href="<?= !empty($section->alias) ? '/'.$section->alias : '/'.$section->id; ?>"><?= $section->title; ?></a></b></div>
		<? } ?>
	</div>
<? }

	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->navigation_page = $navigation_page;
	$template->navigation_items_per_page = $navigation_items_per_page;
	$template->search_entity = 'links';
	$template->search_class = 'Link';
	$template->search_condition = 'JOIN objects AS o ON o.id = l.from_id
								   WHERE l.type_id = 4 '.(!empty($user) ? " AND o.user_id = $user->id" : '').(!empty($section) ? " AND l.to_id = $section->id" : '').'
								   GROUP BY o.id, l.id
								   ORDER BY l.creation_time DESC, l.id DESC';
	$template->template_title = 'generic/claims.objects';
	$template->render(true);

	// TODO: Create of claim as on view_comments, but limited to one claim per user only

	if($allow_advanced_control && !empty($user) || !empty($section)) { ?>
		<div _grid="h">
			<a _button href="/claims<?= !$allow_advanced_control ? '?user_id='.$user->id : ''; ?>"><?= D['button_view_all']; ?></a>
		</div>
	<? }
?>