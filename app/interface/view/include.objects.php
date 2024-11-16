<? if(count($entities) > 0) { ?>
	<ul _list>
		<? foreach($entities as $k => $link) {
			$object = $link->from;
			$object_url = $object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id;
		?>
			<li>
				<div _grid="h">
					<label _check>
						<input type="checkbox" name="objects_ids[]" value="<?= $object->id; ?>" <?= $link->included ? 'checked' : ''; ?>>
						<div></div>
					</label>
					<? if($object->access_level_id > 0) {
						include 'plugin/objects.post.php';
					} else { ?>
						<div><?= D['string_no_access_to_object'].' '.$object->id; ?></div>
					<? } ?>
				</div>
			</li>
		<? } ?>
	</ul>
<? } ?>