<? if(count($entities) > 0) { ?>
	<ul _list>
		<? foreach($entities as $k => $object) {
			$object_url = !empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id);
		?>
			<li>
				<div _grid="h">
					<div fallback_><?= $k+1; ?></div>
					<? if($object->access_level_id > 0) {
						include 'plugin/objects-list.post.php';
					} else {
						echo D['string_no_access_to_object'].' '.$object->id;
					} ?>
				</div>
			</li>
		<? } ?>
	</ul>
<? } ?>