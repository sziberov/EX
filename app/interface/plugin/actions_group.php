<?
	$rows = [];
	$row = [];
	$columns = [];

	foreach($object->user_group_access_links as $uga_link) {
		if($uga_link->from_id == Session::getUserID()) {
			$allow_invites = $uga_link->getSetting('allow_invites');

			break;
		}
	}
	if($allow_invites ?? false) {
		$row[] = [
			["/invite/$object->id", D['button_invite_users_to_group']]
		];
	}
	if($object->access_level_id >= 4 && $object->self_inclusions_count > 0) {
		$columns[] = ["/view_inclusions/$object->id", D['button_inclusions'], ['badge' => $object->self_inclusions_count]];
	}
	if($object->comments_count > 0) {
		$columns[] = ["/view_comments/$object->id", D['button_comments'], ['badge' => $object->comments_count]];
	} else
	if($object->access_level_id >= 2) {
		$columns[] = ["/create?to_id=$object->id&type_id=3,5", D['button_comment']];
	}

	$columns[] = ["/create_link?from_id=$object->id&type_id=6", D['button_recommend'], ['action' => 'Button.toggle(this);']];
	$row[] = $columns;

	if(Session::set()) {
		$columns = [];
		$link_id = Link::getID($object->id, null, Session::getUserID(), 10);

		if(empty($link_id)) {
			$columns[] = ["/create_link?from_id=$object->id&type_id=10", D['button_to_bookmarks']];
		} else {
			$columns[] = ["/destroy_link/$link_id", D['button_to_bookmarks'], ['toggled' => true]];
		}

		$columns[] = ["/my/$object->id", D['button_to_my']];
		$row[] = $columns;
	}

	$rows[] = $row;

	if($object->access_level_id >= 4) {
		$columns = [
			["/edit/$object->id".(!empty($referrer) ? "?r=$referrer->id" : ''), D['button_edit']]
		];

		if($object->access_level_id == 5) {
			$columns[] = ["/destroy/$object->id", D['button_delete']];
		}

		$rows[] = [$columns];
	}

	$columns = [];

	if($object->claims_count > 0 && ($allow_advanced_control || $object->user->id == Session::getUserID())) {
		$columns[] = ["/claims?object_id=$object->id", D['button_claims'], ['badge' => $object->claims_count]];
	} else {
		$columns[] = ["/create?to_id=$object->id&type_id=3,8", D['button_claim']];
	}

	$columns[] = ["/template/$object->id", D['button_template']];
	$rows[] = [$columns];
?>