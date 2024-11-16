<?
	// Outer: object, referrer, display_mode_id

	$object = $this->object ?? null;
	$referrer = $this->referrer ?? null;
	$display_mode_id = $this->display_mode_id ?? 0;

	if($display_mode_id == 0) {
		$object_url = !empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id);
	?>
		<div _title="small">
			<a href="<?= $object_url; ?>"><?= e($object->title); ?></a>
		</div>
	<? } else {
		if($object->display_type != 'comment') {
			if($display_mode_id == 1 && !empty($referrer)) {
				$ancestors_ids = array_reverse(Link::getAncestorsIDs($object->id, $referrer->id, 4));
				$siblings_ids = Link::getSiblingsIDs($object->id, $referrer->id, 4);

				foreach($siblings_ids as $k => $v) {
					${str_replace('id', 'url', $k)} = !empty($v) ? "/$v?r=$referrer->id" : null;
				}
			?>
				<div>
					<? foreach($ancestors_ids as $k => $ancestor_id) {
						$last = $k == array_key_last($ancestors_ids);
						$section = new Object_($ancestor_id);

						if($section->type_id == 2) {
							continue;
						}

						$section_url = (!empty($section->alias) ? '/'.$section->alias : '/'.$section->id).(!empty($ancestors_ids[$k-1]) ? '?r='.$ancestors_ids[$k-1] : '');
					?>
						<a _title="small" href="<?= $section_url; ?>"><?= e($section->title); ?></a><?= !$last ? ' > ' : ''; ?>
					<? } ?>
				</div>
				<div _flex="h" fallback_>
					<a _button icon_="to_previous" <?= !empty($previous_url) ? 'data-navigate="previous" href="'.$previous_url.'" title="'.D['button_to_previous_tooltip'].'"' : 'disabled_'; ?>></a>
					<div fallback_>← Ctrl</div>
					<a _button icon_="to_random" <?= !empty($random_url) ? 'data-navigate="random" href="'.$random_url.'" title="'.D['button_to_random_tooltip'].'"' : 'disabled_'; ?>></a>
					<div fallback_>Ctrl →</div>
					<a _button icon_="to_next" <?= !empty($next_url) ? 'data-navigate="next" href="'.$next_url.'" title="'.D['button_to_next_tooltip'].'"' : 'disabled_'; ?>></a>
				</div>
			<? }
		} else {
			$ancestors_ids = Link::getAncestorsIDs($object->id, null, 5);
			$comment = new Object_($ancestors_ids[array_key_last($ancestors_ids)]);
			$comment_url = str_starts_with($page, 'view') ? "/$page" : '';
		?>
			<div _grid="v stacked">
				<div><?= D['string_all_comments'] ?>: <a _title="small" href="<?= "$comment_url/$comment->id"; ?>"><?= e($comment->title); ?></a></div>
				<? if(count($ancestors_ids) > 1) {
					$comment = new Object_($ancestors_ids[0]);
				?>
					<div><?= count($ancestors_ids) > 2 ? '...' : '&nbsp;'; ?></div>
					<div><?= D['string_previous_comment'] ?>: <a _title="small" href="<?= "$comment_url/$comment->id"; ?>"><?= e($comment->title); ?></a></div>
				<? } ?>
			</div>
		<? }
	}
?>