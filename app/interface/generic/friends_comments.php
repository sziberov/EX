<?
	try {
		$user = new Object_(Object_::getUserID($path[1]));
	} catch(Exception $e) {
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	$user_url = !empty($user->alias) ? '/'.$user->alias : '/user/'.$user->login;
	$avatar = $user->avatar;
	$avatar_url = !empty($avatar) ? (!empty($avatar->alias) ? '/'.$avatar->alias : '/'.$avatar->id) : null;
	$page_title = D['title_friends_comments'].' '.$user->title;
?>
<div _grid="h spaced">
	<div _grid="h">
		<? if(!empty($avatar)) { ?>
			<a _avatar href="<?= $user_url; ?>" href-alt="<?= $avatar_url; ?>" title="<?= e($user->title.': '.$avatar->title); ?>">
				<img src="/get/<?= $avatar->poster->id; ?>">
			</a>
		<? } ?>
		<div _title><?= D['title_friends_comments']; ?> <b><?= e($user->title); ?></b></div>
	</div>
	<a _button href="<?= $user_url; ?>"><?= D['button_user_page']; ?></a>
</div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->search_condition = "JOIN links AS l_0 ON l_0.to_id = $user->id AND l_0.type_id = 2
								   JOIN links AS l_1 ON l_1.from_id = o.id AND l_1.user_id = l_0.from_id AND l_1.type_id = 5
								   ORDER BY o.creation_time DESC, o.id DESC";
	$template->render(true);
?>