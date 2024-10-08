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
						<? if($access_level_id > 0) { ?>
								<? include 'plugin/objects-list.post.php'; ?>
								<div _grid="h">
									<button><?= D['button_remove']; ?></button>
									<? if($access_level_id > 4) { ?>
										<button><?= D['button_delete']; ?></button>
									<? } ?>
								</div>
						<? } else {
							echo D['string_no_access_to_object'].' '.$object->id;
						} ?>
					</div>
				</div>
			</li>
		<? } ?>
	</ul>
<? } ?>