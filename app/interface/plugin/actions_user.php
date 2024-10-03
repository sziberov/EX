<?
	$rows = [];
	$row = [];
	$current_session_user = Session::set() && Session::getUserID() == $object->id;

	if($current_session_user) {
		$columns = [
			["/create?to_id=$object->id&type_id=3,4", D['button_create']],
			['/archive', D['button_archive'], $object->archive_count ?: null]
		];

		if($object->user_comments_count > 0) {
			$columns[] = ['/comments', D['button_comments'], $object->user_comments_count];
		}
		if($object->recommendations_count > 0) {
			$columns[] = ['/recommendations', D['button_recommendations'], $object->recommendations_count];
		}

		$columns[] = ['/avatars', D['button_avatars'], $object->avatars_count ?: null];

		if($object->user_claims_count > 0) {
			$columns[] = ['/claims', 'Жалобы', $object->user_claims_count];
		}

		$columns[] = ['/templates', D['button_templates'], $object->templates_count ?: null];

		if($object->drafts_count > 0) {
			$columns[] = ['/drafts', D['button_drafts'], $object->drafts_count];
		}
		if($object->bookmarks_count > 0) {
			$columns[] = ['/bookmarks', D['button_bookmarks'], $object->bookmarks_count];
		}

		$columns[] = ['/groups', D['button_groups'], $object->groups_count_alien.' / '.$object->groups_count_own];
		$row[] = $columns;
	}

	$columns = [];

	if($current_session_user && $object->inbox_count+$object->outbox_count > 0) {
		$columns[] = ['/private_messages', D['button_private_messages'], $object->inbox_count.' / '.$object->outbox_count];
	} else {
		$columns[] = ["/create?to_id=$object->id&type_id=3,11", D['button_private_message']];
	}
	if($object->access_level_id >= 4 && $object->self_inclusions_count > 0) {
		$columns[] = ["/view_inclusions/$object->id", D['button_inclusions'], $object->self_inclusions_count];
	}
	if($object->comments_count > 0) {
		$columns[] = ["/view_comments/$object->id", D['button_comments'], $object->comments_count];
	} else
	if($object->access_level_id >= 2) {
		$columns[] = ["/create?to_id=$object->id&type_id=3,5", D['button_comment']];
	}

	$columns[] = ["/create_link?from_id=$object->id&type_id=6", D['button_recommend'], null, 'Button.toggle(this);'];
	$row[] = $columns;

	if(Session::set()) {
		$columns = [];

		if(!$current_session_user) {
			$columns[] = ["/create_link?from_id=$object->id&to_id=".Session::getUserID()."&type_id=2", D['button_to_friends'], null, 'Button.toggle(this);'];
		}

		$columns[] = ["/create_link?from_id=$object->id&type_id=10", D['button_to_bookmarks'], null, 'Button.toggle(this);'];
		$columns[] = ["/my/$object->id", D['button_to_my']];
		$row[] = $columns;
	}
	if($current_session_user || $allow_advanced_control) {
		$columns = [  // TODO: Display buttons conditionally
			["/objects_stats?user_id=$object->id", D['button_objects_stats']],
			["/moderation?user_id=$object->id", D['button_moderation']],
			["/claims?user_id=$object->id", D['button_claims']]
		];
		$row[] = $columns;
	}
	if($current_session_user && $allow_advanced_control) {
		$row[] = [
			['/aliases', D['button_aliases']],
			['/banners', D['button_banners']],
			['/fs', D['button_fs']]
		];
	}

	$rows[] = $row;

	if($allow_advanced_control && $object->access_level_id >= 4) {
		$columns = [
			["/edit/$object->id".(!empty($referrer) ? "?referrer_id={$referrer->id}" : ''), D['button_edit']]
		];

		if($object->access_level_id == 5) {
			$columns[] = ["/destroy/$object->id", D['button_delete']];
		}

		$rows[] = [$columns];
	}

	$columns = [];

	if($object->claims_count > 0 && ($allow_advanced_control || $object->user->id == Session::getUserID())) {
		$columns[] = ["/claims?object_id=$object->id", D['button_claims'], $object->claims_count];
	} else {
		$columns[] = ["/create?to_id=$object->id&type_id=3,8", D['button_claim']];
	}

	$columns[] = ["/template/$object->id", D['button_template']];
	$rows[] = [$columns];

	if($current_session_user) { ?>
		<div><?= D['string_content_rating_first']; ?><b>1</b><?= D['string_content_rating_second']; ?></div>
		<div>&nbsp;</div>
	<? }
?>