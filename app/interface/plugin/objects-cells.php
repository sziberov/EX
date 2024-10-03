<? if(count($entities) > 0) { ?>
	<ul _cells>
		<? foreach($entities as $object) {
			$object_url = (!empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id)).(!empty($referrer_id) ? '?referrer_id='.$referrer_id : '');
		?>
			<li>
				<? if($object->access_level_id > 0) {
					if(isset($object->poster)) { ?>
						<a href="<?= $object_url; ?>" title="<?= e($object->title); ?>">
							<img _image="big" src="/get/<?= $object->poster->id; ?>">
						</a>
					<? } ?>
					<div _grid="v stacked centered">
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
					<? if($object->inclusions_count+$object->files_count+$object->comments_count > 0) { ?>
						<div _properties>
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
					<? }
				} else {
					echo D['string_no_access_to_object'].' '.$object->id;
				} ?>
			</li>
		<? } ?>
	</ul>
<? } ?>