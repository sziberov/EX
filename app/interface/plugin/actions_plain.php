<?
	$rows = [];
	$row = [];
	$awaiting_save = $object->getSetting('awaiting_save');

	if($object->access_level_id >= 3 && !$awaiting_save) {
		$columns = [];

		if(!$object->getSetting('deny_nonbookmark_inclusion')) {
			$columns[] = ["/create?to_id=$object->id&type_id=3,4", D['button_create_article_in_section']];
			$columns[] = ["/upload/$object->id", D['button_upload_files_to_section']];
		}

		$columns[] = ["/include/$object->id", D['button_include_from_bookmarks']];
		$row[] = $columns;
	}

	$columns = [];

	if($object->access_level_id >= 4 && $object->self_inclusions_count > 0) {
		$columns[] = ["/view_inclusions/$object->id", D['button_inclusions'], ['badge' => $object->self_inclusions_count]];
	}
	if($object->comments_count > 0) {
		$columns[] = ["/view_comments/$object->id", D['button_comments'], ['badge' => $object->comments_count]];
	} else
	if($object->access_level_id >= 2 && !$awaiting_save) {
		$columns[] = ["/create?to_id=$object->id&type_id=3,5", D['button_comment']];
	}

	$link_id = Link::getID($object->id, null, Session::getUserID(), 6);

	if(empty($link_id)) {
		if(!$awaiting_save) {
			$columns[] = ["/create_link?from_id=$object->id&type_id=6", D['button_recommend']];
		}
	} else {
		$columns[] = ["/destroy_link/$link_id", D['button_recommend'], ['toggled' => true]];
	}
	if(count($columns) > 0) {
		$row[] = $columns;
	}
//	if(count($row) == 2 && $object->inclusions_count == 0) {
//		$row = array_reverse($row);
//	}
	if(Session::set()) {
		$columns = [];
		$link_id = Link::getID($object->id, null, Session::getUserID(), 10);

		if(empty($link_id)) {
			if(!$awaiting_save) {
				$columns[] = ["/create_link?from_id=$object->id&type_id=10", D['button_to_bookmarks']];
			}
		} else {
			$columns[] = ["/destroy_link/$link_id", D['button_to_bookmarks'], ['toggled' => true]];
		}
		if(!$awaiting_save) {
			$columns[] = ["/my/$object->id", D['button_to_my']];
		}

		if(count($columns) > 0) {
			$row[] = $columns;
		}
	}
	if(count($row) > 0) {
		$rows[] = $row;
	}
	if($object->access_level_id >= 4) {
		$columns = [
			["/edit/$object->id".(!empty($referrer) ? "?r=$referrer->id" : ''), D['button_edit']]
		];

		if($object->access_level_id == 5) {
			$columns[] = ["/destroy/$object->id", D['button_delete']];
		}

		$rows[] = [$columns];
	}
	if(!$awaiting_save) {
		$columns = [];

		if($object->claims_count > 0 && ($allow_advanced_control || $object->user->id == Session::getUserID())) {
			$columns[] = ["/claims?object_id=$object->id", D['button_claims'], ['badge' => $object->claims_count]];
		} else
		if(!$object->getSetting('deny_claims')) {
			$columns[] = ["/create?to_id=$object->id&type_id=3,8", D['button_claim']];
		}

		$columns[] = ["/template/$object->id", D['button_template']];
		$rows[] = [$columns];
	}
?>