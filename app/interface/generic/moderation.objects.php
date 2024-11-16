<? if(count($entities) > 0) { ?>
	<ul _list small_>
		<? foreach($entities as $link) {
			$object = $link->from;
			$object_url = (!empty($object->alias) ? '/'.$object->alias : '/'.$object->id).(!empty($link->to->id) && $link->to->type_id != 2 ? '?r='.$link->to->id : '');
		?>
			<li>
				<div _grid="v">
					<div><?= D['string_inclusion_to']; ?>
						<?
							$ancestors_ids = array_reverse(Link::getAncestorsIDs($object->id, $link->to->id, 4));

							foreach($ancestors_ids as $k => $ancestor_id) {
								$last = $k == array_key_last($ancestors_ids);
								$section = new Object_($ancestor_id);
								$section_url = null;

								if($last) {
									$section_url = ($_GET['section_id'] ?? null) != $section->id ? '?'.http_build_query(array_merge($_GET, ['section_id' => $section->id])) : null;
								} else
								if($section->type_id == 2) {
									continue;
								}

								$section_url ??= !empty($section->alias) ? '/'.$section->alias : ($section->type_id == 2 ? '/user/'.$section->login : '/'.$section->id).(!empty($ancestors_ids[$k-1]) ? '?r='.$ancestors_ids[$k-1] : '');
							?>
								<b><a href="<?= $section_url; ?>"><?= e($section->title); ?></a></b><?= !$last ? ' > ' : ''; ?>
							<? }
						?>
					</div>
					<div _grid="v" style="padding-left: 24px;">
						<? if($object->access_level_id > 0) { ?>
							<? include 'plugin/objects.post.php'; ?>
						<? } else {
							echo D['string_no_access_to_object'].' '.$object->id;
						} ?>
						<div _grid="h">
							<a _button href="/create_link?from_id=<?= $link->from_id.(!empty($link->to_id) ? '&to_id='.$link->to_id : ''); ?>&type_id=4"><?= D['button_add']; ?></a>
							<a _button href="/destroy_link/<?= $link->id; ?>"><?= D['button_remove']; ?></a>
						</div>
					</div>
				</div>
			</li>
		<? } ?>
	</ul>
<? } ?>