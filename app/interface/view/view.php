<?
	$object_id = $page == 'view' ? $path[1] ?? null : ($page == 'user' ? Object_::getUserID($path[1]) : $path[0]);

	try {
		$object = new Object_($object_id);
	} catch(Exception $e) {
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if($object->access_level_id == 0) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$object->createVisitID();

	if($object->type_id == 3) {
		$display_mode_id = http_getArgument('display_mode_id') ?? $object->getSetting('display_mode_id') ?? 0;
		$sort_mode_id = http_getArgument('sort_mode_id') ?? $object->getSetting('sort_mode_id') ?? 0;
	} else {
		$display_mode_id = 0;
		$sort_mode_id = 0;
	}

	if($object->type_id != 4) {
		$hide_default_referrer = $object->type_id != 3 || $object->getSetting('hide_default_referrer');  // TODO: Create physical settings

		try {
			$referrer = new Object_($object->getValidReferrerID(http_getArgument('referrer_id'), !$hide_default_referrer));
		} catch(Exception $e) {}

		$referred_title = $object->title.(!empty($referrer) ? ' - '.$referrer->title : '');
	} else {
		$referred_title = $object->title;
	}

	$page_title = $object->type_id == 1 ? D['title_group'].' '.$object->title :
				 ($object->type_id == 2 ? D['title_user'].' '.$object->title :
				 ($object->type_id == 3 ? $referred_title :
										  $object->title));
	$page_description = mb_strimwidth(template_clearBB($object->description ?? ''), 0, 384, '...');

	$template = new Template('referrer');
	$template->object = $object;
	$template->referrer = $referrer ?? null;
	$template->display_mode_id = 1;
	$template->render(true);

	if($object->getSetting('awaiting_save')) { ?>
		<div _title="small" fallback_>Черновик</div>
	<? }

	include 'view.post.php';
?>
<? if($object->type_id == 2) {
	if(count($friends) > 0) { ?>
		<div _grid="v stacked">
			<b><?= $login != $session_login ? D['link_friends'] : '<a href="/friends">'.D['link_friends'].'</a>'; ?></b>
			<div _flex="h wrap">
				<? foreach($friends as $friend) {
					$template = new Template('user');
					$template->object = $friend;
					$template->mutual = $object->areFriendOf($friend->id);
					$template->time_display_mode_id = 0;
					$template->render(true);
				} ?>
			</div>
		</div>
	<? }
	if($login == $session_login && count($notifications) > 0) { ?>
		<div _grid="v stacked">
			<b><a href="/notifications"><?= D['link_notifications']; ?></a></b>
			<div _grid="h">
				<div><?= D['string_groups_invites_count']; ?><div _badge>1</div></div>
				<div><?= D['string_friends_count']; ?><div _badge>1</div></div>
				<div><?= D['string_private_messages_count']; ?><div _badge>1</div></div>
				<div><?= D['string_comments_count']; ?><div _badge>1</div></div>
				<div><?= D['string_recommendations_count']; ?><div _badge>1</div></div>
			</div>
		</div>
	<? }
} else
if($object->inclusions_count > 0) { ?>
	<div _grid="h">
		<form _grid="h">
			<? foreach($_GET as $key => $value) {
				if(
					str_starts_with('sort_mode_id', $key) ||
					str_starts_with('display_mode_id', $key)
				)
					continue; ?>
				<input type="hidden" name="<?= $key; ?>" value="<?= $value; ?>">
			<? } ?>
			<select name="<?= http_getShortParameter('display_mode_id'); ?>" onchange="this.form.submit();">
				<?
					$dmid_options = ['cells', 'list'];

					foreach($dmid_options as $k => $dmid_option) { ?>
						<option value="<?= $k; ?>" <?= $k == $display_mode_id ? 'selected' : ''; ?>><?= D['string_as_'.$dmid_option]; ?></option>
					<? }
				?>
			</select>
			<select name="<?= http_getShortParameter('sort_mode_id'); ?>" onchange="this.form.submit();">
				<?
					$smid_options = [
						'inclusion_time',
						'edit_time',
						'creation_time',
						'popularity',
						'visits',
						'comments',
						'recommendations',
						'title'
					];

					foreach($smid_options as $k => $smid_option) { ?>
						<option value="<?= $k; ?>" <?= $k == $sort_mode_id ? 'selected' : ''; ?>><?= D['string_by_'.$smid_option]; ?></option>
					<? }
				?>
			</select>
		</form>
		<? if($object->getSetting('display_search_bar')) { ?>
			<form _grid="h" action="/search">
				<input size_="medium" name="q" type="text">
				<input name="section_id" type="hidden" value="<?= $object->id; ?>">
				<button type="submit"><?= D['button_search']; ?></button>
			</form>
		<? } ?>
	</div>
<? }

	$template = new Template('entities');

	if($object->type_id != 2) {
		$template->navigation_mode_id = 1;
		$template->navigation_items_per_page = $object->getSetting('display_amount') ?: 24;
		$template->navigation_rss_id = $object->id;
		$template->template_title = 'objects-'.($display_mode_id == 0 ? 'cells' : 'list');
		$template->template_namespace = [
			'referrer_id' => $object->id
		];
	}

	$template->search_condition = "JOIN links AS l ON l.from_id = o.id AND l.to_id = $object->id AND l.type_id = 4 ";

	if($sort_mode_id == 0) {
		$template->search_condition .= "ORDER BY l.creation_time DESC, l.id DESC";
	}
	if($sort_mode_id == 1) {
		$template->search_condition .= "ORDER BY o.edit_time DESC, o.id DESC";
	}
	if($sort_mode_id == 2) {
		$template->search_condition .= "ORDER BY o.creation_time DESC, o.id DESC";
	}
	if($sort_mode_id == 7) {
		$template->search_condition .= "ORDER BY o.title DESC, o.id DESC";
	}

	$template->render(true);

	include 'view.lists.php';
	include 'plugin/actions.php';
?>