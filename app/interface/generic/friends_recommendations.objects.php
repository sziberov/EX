<? if(count($entities) > 0) { ?>
	<ul _list>
		<? foreach($entities as $k => $link) {
			$object = $link->from;
			$object_url = !empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id);
			$user = $link->user;
			$user_url = !empty($user->alias) ? '/'.$user->alias : '/user/'.$user->login;
		?>
			<li>
				<div _grid="v">
					<div _grid="v stacked" fallback_>
						<b><a href="<?= $user_url; ?>"><?= e($user->title); ?></a></b>
						<div><?= template_formatTime($link->creation_time, true); ?></div>
					</div>
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