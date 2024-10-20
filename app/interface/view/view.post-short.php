<div _post>
	<?
		$object_url = !empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id);
	?>
	<? if(isset($object->poster)) { ?>
		<a __poster href="<?= $object_url; ?>">
			<img _image src="/get/<?= $object->poster->id; ?>">
		</a>
	<? } ?>
	<div _title>
		<a href="<?= $object_url; ?>"><?= e($object->title); ?></a>
	</div>
	<?
		if(!$object->getSetting('hide_author_and_times')) {
			$template = new Template('user');
			$template->object = $object;
			$template->time_display_mode_id = 2;
			$template->render(true);
		}
	?>
	<? if(!empty($object->description)) { ?>
		<div _description="short"><?= template_parseBB(e($object->description)); ?></div>
	<? } ?>
	<? if($object->inclusions_count+$object->files_count+$object->comments_count > 0) { ?>
		<div _grid="h">
			<? if($object->inclusions_count > 0) { ?>
				<div><?= D['string_inclusions_count']; ?><div _badge><?= $object->inclusions_count; ?></div></div>
			<? } ?>
			<? if($object->files_count > 0) { ?>
				<div><?= D['string_files_count']; ?><div _badge><?= $object->files_count; ?></div></div>
			<? } ?>
			<? if($object->comments_count > 0) { ?>
				<div><?= D['string_comments_count']; ?><div _badge><?= $object->comments_count; ?></div></div>
			<? } ?>
		</div>
	<? } ?>
</div>