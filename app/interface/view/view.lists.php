<?
	$hide_files_list = $object->getSetting('hide_files_list');
	$allow_members_list_view = false;

	foreach($object->user_group_access_links as $uga_link) {  // TODO: Optimize by direct function
		if($uga_link->from_id == Session::getUserID()) {
			$allow_members_list_view = $uga_link->getSetting('allow_members_list_view');

			break;
		}
	}

	$allow_members_list_view = $allow_members_list_view || Session::getSetting('allow_max_access_ignoring_groups');

	if($object->type_id != 2 && $object->files_count > 0 && !$hide_files_list)			include 'view.lists.files.php';
	if($object->type_id == 1 && $object->members_count > 0 && $allow_members_list_view)	include 'view.lists.members.php';
?>