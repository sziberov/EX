<? if(count($entities) > 0) { ?>
	<div _grid="list">
		<div _title="small" header_><?= $title; ?></div>
		<? foreach($entities as $object) {
			if($object->access_level_id > 0) {
				$object_url = !empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id);

				echo '<a href="'.$object_url.'">'.e($object->title).'</a>';
			} else {
				echo '<div>'.D['string_no_access_to_object'].' '.$object->id.'</div>';
			}
		} ?>
		<a href="/most<?= $most_id == 0 ? '' : '/'.$most_id; ?>" footer_><b><?= D['link_complete_list']; ?></b></a>
	</div>
<? } ?>