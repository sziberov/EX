<div _table="list" wide_ style="--columns: minmax(48px, max-content) repeat(5, minmax(96px, auto));">
	<div header_>
		<div fallback_></div>
		<div><?= D['string_object']; ?></div>
		<!--<div><?= D['string_recommendations_count']; ?></div>-->
		<div><?= D['string_inclusions_count']; ?></div>
		<div><?= D['string_hits_count']; ?></div>
		<div><?= D['string_hosts_count']; ?></div>
		<div><?= D['string_guests_count']; ?></div>
	</div>
	<? foreach($entities as $k => $object) {
		$object_url = !empty($object->alias) ? '/'.$object->alias : '/'.$object->id;
	?>
		<div>
			<div fallback_><?= $k+1; ?></div>
			<? if($object->access_level_id > 0) { ?>
				<div>
					<? include 'plugin/objects-list.post.php'; ?>
				</div>
				<!--<div><?= $object->recommendations_count; ?></div>-->
				<div>
					<? if($object->self_inclusions_count > 0) { ?>
						<a href="/view_inclusions/<?= $object->id; ?>"><?= $object->self_inclusions_count; ?></a>
					<? } else {
						echo 0;
					} ?>
				</div>
				<div>
					<? if($object->hits_count > 0) { ?>
						<a href="/visits/<?= $object->id; ?>"><?= $object->hits_count; ?></a>
					<? } else {
						echo 0;
					} ?>
				</div>
				<div><?= $object->hosts_count; ?></div>
				<div><?= $object->guests_count; ?></div>
			<? } else { ?>
				<div><?= D['string_no_access_to_object'].' '.$object->id; ?></div>
			<? } ?>
		</div>
	<? } ?>
</div>