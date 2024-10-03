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
?>
<title><?= dictionary_getPageTitle(D['title_user_recommendations'].' '.e($user->title)); ?></title>
<div _grid="h spaced">
	<div _grid="h">
		<? if(!empty($avatar)) { ?>
			<a _avatar href="<?= $user_url; ?>" href-alt="<?= $avatar_url; ?>" title="<?= e($user->title.': '.$avatar->title); ?>">
				<img src="/get/<?= $avatar->poster->id; ?>">
			</a>
		<? } ?>
		<div _title><?= D['title_user_recommendations']; ?> <b><?= e($user->title); ?></b></div>
	</div>
	<a _button href="<?= $user_url; ?>"><?= D['button_user_page']; ?></a>
</div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->navigation_page = $navigation_page;
	$template->navigation_items_per_page = $navigation_items_per_page;
	$template->search_entity = 'links';
	$template->search_class = 'Link';
	$template->search_condition = "WHERE l.user_id = $user->id AND l.type_id = 6
								   ORDER BY l.creation_time DESC, l.id DESC";
	$template->template_title = 'generic/user_recommendations.objects';
	$template->render(true);
?>