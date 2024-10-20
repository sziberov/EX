<? if(count($entities) > 0) { ?>
	<ul _list>
		<? foreach($entities as $object) {
			$object_url = (!empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id)).(!empty($referrer_id) ? '?referrer_id='.$referrer_id : '');
		?>
			<li>
				<? if($object->access_level_id > 0) {
					include 'objects-list.post.php';
					/*if(Session::set() && isset($object->user) && Session::getSetting('login') == $object->user->login) { ?>
						<div _grid="h">
							<a _button href="/edit/<?= $object->object_id; ?>"><?= D['button_edit']; ?></a>
							<button><?= D['button_delete']; ?></button>
							<button><?= D['button_remove']; ?></button>
						</div>
					<? }*/
				} else {
					echo D['string_no_access_to_object'].' '.$object->id;
				} ?>
			</li>
		<? } ?>
	</ul>
<? } ?>