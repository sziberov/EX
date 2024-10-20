<div _table="list" wide_ style="--columns: minmax(48px, max-content) repeat(12, minmax(max-content, auto));">
	<div header_>
		<div fallback_></div>
		<div><?= D['string_user']; ?></div>
		<div><?= D['string_friends_count']; ?></div>
		<div><?= D['string_objects_count']; ?></div>
		<div><?= D['string_originals_count']; ?></div>
		<div><?= D['string_summary_size']; ?></div>
		<div><?= D['string_duplicates_count']; ?></div>
		<div><?= D['string_summary_size']; ?></div>
		<div><?= D['string_registered']; ?></div>
		<div><?= D['string_online']; ?></div>
		<div><?= D['string_hits_count']; ?></div>
		<div><?= D['string_hosts_count']; ?></div>
		<div><?= D['string_guests_count']; ?></div>
	</div>
	<? foreach($entities as $k => $object) {
		$object_url = !empty($object->alias) ? '/'.$object->alias : '/'.$object->id;
	?>
		<div>
			<div fallback_><?= $k+1; ?></div>
			<div>
				<div _description="short straight">
					<?
						$template = new Template('user');
						$template->object = $object;
						$template->time_display_mode_id = 0;
						$template->render(true);
					?>
				</div>
			</div>
			<div><?= $object->friends_count; ?></div>
			<div><?= $object->archive_count; ?></div>
			<div><?= template_formatSize($object->originals_count); ?></div>
			<div><?= template_formatSize($object->originals_size); ?></div>
			<div><?= template_formatSize($object->duplicates_count); ?></div>
			<div><?= template_formatSize($object->duplicates_size); ?></div>
			<div><?= template_formatTime($object->creation_time); ?></div>
			<div><?= template_formatTime($object->edit_time); ?></div>
			<div><?= $object->hits_count; ?></div>
			<div><?= $object->hosts_count; ?></div>
			<div><?= $object->guests_count; ?></div>
		</div>
	<? } ?>
</div>