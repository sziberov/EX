<? if(count($entities) > 0) { ?>
	<ul _list>
		<? foreach($entities as $object) {
			$object_url = !empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id);
		?>
			<li>
				<? if($object->access_level_id > 0) {
					include 'objects.post.php';
				} else {
					echo D['string_no_access_to_object'].' '.$object->id;
				} ?>
			</li>
		<? } ?>
	</ul>
<? } ?>