<?
	$rows = [];
	$awaiting_save = $object->getSetting('awaiting_save');

	if(!$awaiting_save) {
		$row = [];

		if($object->access_level_id >= 3) {
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
			$columns[] = ["/view_inclusions/$object->id", D['button_inclusions'], $object->self_inclusions_count];
		}
		if($object->comments_count > 0) {
			$columns[] = ["/view_comments/$object->id", D['button_comments'], $object->comments_count];
		} else
		if($object->access_level_id >= 2) {
			$columns[] = ["/create?to_id=$object->id&type_id=3,5", D['button_comment']];
		}

		$columns[] = ["/create_link?from_id=$object->id&type_id=6", D['button_recommend']];
		$row[] = $columns;

		if(Session::set()) {
			$row[] = [
				["/create_link?from_id=$object->id&type_id=10", D['button_to_bookmarks']],
				["/my/$object->id", D['button_to_my']]
			];
		}

		$rows[] = $row;
	}
	if($object->access_level_id >= 4) {
		$columns = [
			["/edit/$object->id".(!empty($referrer) ? "?referrer_id=$referrer->id" : ''), D['button_edit']]
		];

		if($object->access_level_id == 5) {
			$columns[] = ["/destroy/$object->id", D['button_delete']];
		}

		$rows[] = [$columns];
	}
	if(!$awaiting_save) {
		$columns = [];

		if($object->claims_count > 0 && ($allow_advanced_control || $object->user->id == Session::getUserID())) {
			$columns[] = ["/claims?object_id=$object->id", D['button_claims'], $object->claims_count];
		} else
		if(!$object->getSetting('deny_claims')) {
			$columns[] = ["/create?to_id=$object->id&type_id=3,8", D['button_claim']];
		}

		$columns[] = ["/template/$object->id", D['button_template']];
		$rows[] = [$columns];
	}
?>