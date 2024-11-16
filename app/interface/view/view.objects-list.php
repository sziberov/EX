<? if(count($entities) > 0) { ?>
	<ul _list small_>
		<? foreach($entities as $link) {
			$object = $link->from;
			$object_url = (!empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id)).(!empty($referrer_id) ? '?r='.$referrer_id : '');
		?>
			<li>
				<? if($object->access_level_id > 0) {
					include 'plugin/objects.post.php';
				} else {
					echo D['string_no_access_to_object'].' '.$object->id;
				} ?>
				<? if($link->access_level_id > 0) { ?>
					<div _grid="h">
						<a _button href="/destroy_link/<?= $link->id; ?>"><?= D['button_remove']; ?></a>
					</div>
				<? } ?>
			</li>
		<? } ?>
	</ul>
<? } ?>