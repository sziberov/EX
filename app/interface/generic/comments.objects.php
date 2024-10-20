<? if(count($entities) > 0) { ?>
	<ul _list>
		<? foreach($entities as $link) {
			$object = $link->from;
			$object_url = '/'.$object->id;
			$object_to = $link->to;
			$object_to_url = !empty($object_to->alias) ? '/'.$object_to->alias : ($object_to->type_id == 2 ? '/user/'.$object_to->login : '/'.$object_to->id);
			$access_level_id = $object->access_level_id;
		?>
			<li>
				<div _grid="v">
					<div><?= D['string_reply_to']; ?> <b><a href="<?= $object_to_url; ?>"><?= e($object_to->title); ?></a></b></div>
					<div _grid="v" style="padding-left: 24px;">
						<? if($access_level_id > 0) {
							include 'plugin/objects-list.post.php';
						} else {
							echo D['string_no_access_to_object'].' '.$object->id;
						} ?>
						<div _grid="h">
							<a _button href="/destroy_link/<?= $link->id; ?>"><?= D['button_remove']; ?></a>
							<? if($access_level_id >= 5) { ?>
								<a _button href="/destroy/<?= $object->id; ?>"><?= D['button_delete']; ?></a>
							<? } ?>
						</div>
					</div>
				</div>
			</li>
		<? } ?>
	</ul>
<? } ?>