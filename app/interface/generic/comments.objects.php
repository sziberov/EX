<? if(count($entities) > 0) { ?>
	<ul _list small_>
		<? foreach($entities as $link) {
			$object = $link->from;
			$object_url = '/'.$object->id;
			$object_to = $link->to;
			$object_to_url = !empty($object_to->alias) ? '/'.$object_to->alias : ($object_to->type_id == 2 ? '/user/'.$object_to->login : '/'.$object_to->id);
		?>
			<li>
				<div _grid="v">
					<div><?= $link->reply ? D['string_reply_to'] : D['string_comment_to']; ?> <b><a href="<?= $object_to_url; ?>"><?= e($object_to->title); ?></a></b></div>
					<div _grid="v" style="padding-left: 24px;">
						<? if($object->access_level_id > 0) {
							include 'plugin/objects.post.php';
						} else {
							echo D['string_no_access_to_object'].' '.$object->id;
						} ?>
						<div _grid="h">
							<? if($object->access_level_id >= 2) { ?>
								<a _button href="/create?to_id=<?= $object->id; ?>&type_id=3,5"><?= D['button_reply']; ?></a>
							<? } ?>
							<? if($object->access_level_id >= 4) { ?>
								<a _button href="/edit/<?= $object->id; ?>"><?= D['button_edit']; ?></a>
							<? } ?>
							<a _button href="/destroy_link/<?= $link->id; ?>"><?= D['button_remove']; ?></a>
						</div>
					</div>
				</div>
			</li>
		<? } ?>
	</ul>
<? } ?>