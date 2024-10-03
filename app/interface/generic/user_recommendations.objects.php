<? if(count($entities) > 0) { ?>
	<ul _list>
		<? foreach($entities as $k => $link) {
			$object = $link->from;
			$object_url = !empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id);
		?>
			<li>
				<div _grid="v">
					<div fallback_><?= template_formatTime($link->creation_time, true); ?></div>
					<? if($object->access_level_id > 0) {
						include 'plugin/objects-list.post.php';
					} else { ?>
						<div><?= D['string_no_access_to_object'].' '.$object->id; ?></div>
					<? } ?>
				</div>
			</li>
		<? } ?>
	</ul>
<? } ?>