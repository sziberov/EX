<div __menu>
	<div __logo></div>
	<?
		$menu_items = Session::set() && Session::getSetting('use_personal_menu') ? Session::getMenuItems() : null;
		$menu_items ??= [
			['url' => '/video', 'title' => D['link_video']],
			['url' => '/audio', 'title' => D['link_audio']],
			['url' => '/images', 'title' => D['link_images']],
			['url' => '/texts', 'title' => D['link_texts']],
			['url' => '/apps', 'title' => D['link_apps']],
			['url' => '/games', 'title' => D['link_games']],
			['url' => '/about', 'title' => D['link_about']],
			['url' => '/search', 'title' => D['link_search']]
		];

		array_unshift($menu_items, ['url' => '/', 'title' => D['link_files']]);

		foreach($menu_items as $menu_item) { ?>
		    <a href="<?= $menu_item['url']; ?>"><?= $menu_item['title']; ?></a>
		<? }
	?>
</div>
<div __menu>
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
</div>