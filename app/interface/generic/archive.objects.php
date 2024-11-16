<? if(count($entities) > 0) { ?>
	<ul _list small_>
		<? foreach($entities as $object) {
			$object_url = (!empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id));
		?>
			<li>
				<? if($object->access_level_id > 0) {
					include 'plugin/objects.post.php';

					if($object->access_level_id >= 4) { ?>
						<div _grid="h">
							<a _button href="/edit/<?= $object->id; ?>"><?= D['button_edit']; ?></a>
							<? if($object->access_level_id == 5) { ?>
								<a _button href="/destroy/<?= $object->id; ?>"><?= D['button_delete']; ?></a>
							<? } ?>
						</div>
					<? }
				} else {
					echo D['string_no_access_to_object'].' '.$object->id;
				} ?>
			</li>
		<? } ?>
	</ul>
<? } ?>