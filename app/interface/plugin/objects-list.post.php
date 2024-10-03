<div _post>
	<? if(isset($object->poster)) { ?>
		<a __poster href="<?= $object_url; ?>" title="<?= e($object->title); ?>">
			<img _image src="/get/<?= $object->poster->id; ?>">
		</a>
	<? } ?>
	<div _grid="v stacked">
		<b><a href="<?= $object_url; ?>"><?= e($object->title); ?></a></b>
		<?
			if(!$object->getSetting('hide_author_and_times')) {
				$template = new Template('user');
				$template->object = $object;
				$template->primary_time = $object->creation_time;
				$template->render(true);
			}
		?>
	</div>
	<? if(!empty($object->description)) { ?>
		<div _description="short"><?= template_parseBB(e($object->description)); ?></div>
	<? }
	if($object->inclusions_count+$object->files_count+$object->comments_count > 0) { ?>
		<div _grid="h">
			<? if($object->inclusions_count > 0) { ?>
				<div><?= D['string_inclusions_count']; ?><div _badge><?= $object->inclusions_count; ?></div></div>
			<? } ?>
			<? if($object->files_count > 0) { ?>
				<div><?= D['string_files_count']; ?><div _badge><?= $object->files_count; ?></div></div>
			<? } ?>
			<? if($object->comments_count > 0) { ?>
				<a href="/view_comments/<?= $object->id; ?>"><?= D['string_comments_count']; ?><div _badge><?= $object->comments_count; ?></div></a>
			<? } ?>
		</div>
	<? } ?>
</div>