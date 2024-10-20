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
				$template->time_display_mode_id = 2;
				$template->render(true);
			}
		?>
	</div>
	<? if(!empty($object->description)) { ?>
		<div _description="short"><?= template_parseBB(e($object->description)); ?></div>
	<? }
	if($object->files_count > 0) { ?>
		<a href="/<?= $object->id; ?>"><u><?= D['string_files_count']; ?><div _badge><?= $object->files_count; ?></div></u></a>
	<? } ?>
</div>