<div _post>
	<? if($object->type_id == 4) { ?>
		<div _title="small"><?= D['title_shared']; ?> временного хранения с ключём доступа <b><?= $object->id; ?></b></div>
	<? }

	if($object->type_id != 2 && !empty($object->poster)) { ?>
		<img __poster _image="<?= $object->inclusions_count > 0 ? 'big' : 'dynamic'; ?>" src="/get/<?= $object->poster->id; ?>" title="<?= $referred_title; ?>">
	<? }

	if($object->type_id == 1) { ?>
		<div _title><?= D['title_group']; ?> <b><?= e($object->title); ?></b></div>
	<? } else
	if($object->type_id == 2) {
		$login = $object->login;
		$object_url = !empty($object->alias) ? '/'.$object->alias : (!empty($object->login) ? '/user/'.$object->login : '/'.$object->id);
		$avatar = $object->avatar;
		$avatar_url = !empty($avatar) ? (!empty($avatar->alias) ? '/'.$avatar->alias : '/'.$avatar->id) : null;
		$friends = $object->friends;
		$notifications = $object->notifications;
		$session_login = Session::getSetting('login');
	?>
		<div _grid="h spaced">
			<div _grid="h">
				<? if(!empty($avatar)) { ?>
					<a _avatar href="<?= $object_url; ?>" href-alt="<?= $avatar_url; ?>" title="<?= e($object->title.': '.$avatar->title); ?>">
						<img src="/get/<?= $avatar->poster->id; ?>">
					</a>
				<? } ?>
				<div _title><?= D['title_user']; ?> <b><?= e($object->title); ?></b></div>
				<form _grid="h" action="/search">
					<input size_="medium" name="query" type="text">
					<input name="user_id" type="hidden" value="<?= $object->id; ?>">
					<button type="submit"><?= D['button_search']; ?></button>
				</form>
			</div>
			<div _flex="h right wrap">
				<? if(count($friends) > 0) { ?>
					<a _button href="/friends_comments/<?= $login; ?>"><?= D['button_friends_comments']; ?></a>
					<a _button href="/friends_recommendations/<?= $login; ?>"><?= D['button_friends_recommendations']; ?></a>
				<? } ?>
				<a _button href="/user_comments/<?= $login; ?>"><?= D['button_user_comments']; ?></a>
				<a _button href="/user_recommendations/<?= $login; ?>"><?= D['button_user_recommendations']; ?></a>
			</div>
		</div>
	<? } else { ?>
		<div _title><?= e($object->title); ?></div>
	<? }

	$hide_author_and_times = $object->type_id == 2 ? true : $object->getSetting('hide_author_and_times');

	if(!$hide_author_and_times) {
		$template = new Template('user');
		$template->object = $object;
		$template->time_display_mode_id = 2;
		$template->render(true);
	}

	if(!empty($object->description)) { ?>
		<div _description><?= template_parseBB(e($object->description)); ?></div>
	<? } ?>
</div>