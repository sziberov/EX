<nav>
	<div __logo></div>
	<?
		$menu_items = Session::set() && Session::getSetting('use_personal_menu') ? Session::getMenuItems() : null;
		$menu_items ??= [
			['title' => D['link_video'],	'url' => '/video'],
			['title' => D['link_audio'],	'url' => '/audio'],
			['title' => D['link_images'],	'url' => '/images'],
			['title' => D['link_texts'],	'url' => '/texts'],
			['title' => D['link_apps'],		'url' => '/apps'],
			['title' => D['link_games'],	'url' => '/games'],
			['title' => D['link_about'],	'url' => '/about'],
			['title' => D['link_search'],	'url' => '/search']
		];

		array_unshift($menu_items, ['title' => D['link_files'], 'url' => '/']);

		foreach($menu_items as $menu_item) { ?>
		    <a href="<?= e($menu_item['url']); ?>"><?= e($menu_item['title']); ?></a>
		<? }
	?>
</nav>
<nav>
	<?
		if(Session::set()) {
			$template = new Template('user');
			$template->object = Session::getUser();
			$template->online = false;
			$template->notifications_count = Session::getNotificationsCount();
			$template->time_display_mode_id = 0;
			$template->render(true);
		?>
			<a href="/settings"><?= D['link_settings']; ?></a>
			<a href="/logout"><?= D['link_logout']; ?></a>
		<? } else { ?>
			<a href="/login"><?= D['link_login']; ?></a>
		<? }
	?>
</nav>