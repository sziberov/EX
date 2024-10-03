<div _grid="list">
	<div _title="small" header_>Крайние включения</div>
	<? foreach($entities as $object) {
		if($object->access_level_id > 0) {
			$object_url = !empty($object->alias) ? '/'.$object->alias : '/'.$object->id;

			include 'plugin/objects-list.post.php';
		} else { ?>
			<div><?= D['string_no_access_to_object'].' '.$object->id; ?></div>
		<? }
	} ?>
</div>