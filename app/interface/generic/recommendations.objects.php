<? if(count($entities) > 0) { ?>
	<ul _list>
		<? foreach($entities as $link) {
			$object = $link->from;
			$object_url = (!empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id));
		?>
			<li>
				<? if($object->access_level_id > 0) {
					include 'plugin/objects-list.post.php';
				} else {
					echo D['string_no_access_to_object'].' '.$object->id;
				} ?>
				<div _grid="h">
					<a _button href="/destroy_link/<?= $link->id; ?>"><?= D['button_remove']; ?></a>
				</div>
			</li>
		<? } ?>
	</ul>
<? } ?>