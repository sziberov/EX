<?
	// Outer: navigation_items_per_page, root_object, level

	$navigation_page = http_getArgument('page') ?? 0;
	$navigation_items_per_page = $this->navigation_items_per_page ?? 24;

	if(filter_var($navigation_page, FILTER_VALIDATE_INT) === false || $navigation_page < 0) {
		$navigation_page = 0;
	}

	$root_object = $this->root_object;
	$search_condition = "JOIN links AS l ON l.from_id = o.id AND l.to_id = $root_object->id AND l.type_id = 5";
	$search = Entity::search('objects', 'Object_', null, $search_condition, $navigation_items_per_page, $navigation_items_per_page*$navigation_page);
	$objects = $search['entities'];
	$count = $search['count'];
	$level = $this->level ?? 0;

	if($count > 0 && $level == 0) {
		$navigation_template = new Template('navigation-short');
		$navigation_template->page = $navigation_page;
		$navigation_template->items_per_page = $navigation_items_per_page;
		$navigation_template->items = $count;
		$navigation_template->render(true);
	?>
		<div _grid="list" small_>
	<? }
		foreach($objects as $object) {
			$object_url = '/view_comments/'.$object->id;

			if($object->access_level_id > 0) { ?>
				<div _grid="v" style="padding-left: <?= $level*48; ?>px;">
					<? include 'comments.post.php'; ?>
					<div _grid="h">
						<? if($object->access_level_id >= 2) { ?>
							<a _button href="/create?to_id=<?= $object->id; ?>&type_id=3,5"><?= D['button_reply']; ?></a>
						<? } ?>
						<? if($object->access_level_id >= 4) { ?>
							<a _button href="/edit/<?= $object->id; ?>"><?= D['button_edit']; ?></a>
						<? } ?>
						<a _button href="/destroy_link/<?= $object->id; ?>"><?= D['button_remove']; ?></a>
					</div>
				</div>
				<? if($object->comments_count > 0) {
					$template = new Template('comments');
					$template->navigation_items_per_page = 4;
					$template->root_object = $object;
					$template->level = $level+1;
					$template->render(true);
				}
			} else { ?>
				<div _flex="h" style="padding-left: <?= $level*48; ?>px;">
					<div _icon="comment"></div>
					<div _grid="v">
						<div><?= D['string_no_access_to_object'].' '.$object->id; ?></div>
						<? if($object->comments_count > 0) { ?>
							<div><?= D['string_comments_count']; ?><div _badge><?= $object->comments_count; ?></div></div>
						<? } ?>
					</div>
				</div>
			<? }
		}
		if($count > $navigation_items_per_page && $level > 0) { ?>
			<div _flex="h" style="padding-left: <?= $level*48; ?>px;">
				<div _icon="comment"></div>
				<a href="/view_comments/<?= $root_object->id; ?>"><?= D['string_more_comments']; ?><div _badge><?= $count-$navigation_items_per_page; ?></div></a>
			</div>
		<? }
	if($count > 0 && $level == 0) { ?>
		</div>
		<?= $navigation_template; ?>
		<div>&nbsp;</div>
	<? }
?>