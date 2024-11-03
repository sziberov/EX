<? if(count($entities) > 0) { ?>
	<div _grid="list" small_>
		<? foreach($entities as $link) {
			$object = $link->to;
			$object_url = !empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id);
		?>
			<div _grid="v">
				<? if($object->access_level_id > 0) {
					include 'plugin/objects-list.post.php'; ?>
					<div _flex="h wrap">
						<div fallback_><?= D['string_redaction']; ?></div>
						<?
							$template = new Template('user');
							$template->object = $link->user;
							$template->primary_time = $link->creation_time;
							$template->render(true);
						?>
					</div>
				<? } else {
					echo D['string_no_access_to_object'].' '.$object->id;
				}
				if($link->access_level_id > 0) { ?>
					<div _grid="h">
						<a _button href="/destroy_link/<?= $link->id; ?>"><?= D['button_remove']; ?></a>
						<!--<button>Реферер по умолчанию</button>-->
					</div>
				<? } ?>
			</div>
		<? } ?>
	</div>
<? } ?>