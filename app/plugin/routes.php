<?
	// Routes

	$requests = [  // Back-end
		'ad',
		'captcha',
		'check_login',
		'create',
		'create_link',
		'delete',
		'destroy',
		'destroy_link',
	//	'edit',
	//	'edit_access',
	//	'edit_settings',
		'filelist',
		'get',
		'language',
		'load',
	//	'login',
		'logout',
	//	'menu_items',
		'my',
		'playlist',
		'poster',
		'rotate',
		'rss',
	//	'search',
		'search_hint',
	//	'settings',
		'upload'
	];
	$generics = [  // Front-end
		'',
		'aliases',
		'archive',
		'avatars',
		'banners',
		'banners_stats',
		'bookmarks',
		'claims',
		'comments',
		'contacts',
		'copy',
		'copyright',
		'drafts',
		'friends',
		'friends_comments',
		'friends_recommendations',
		'fs',
		'fs_stats',
		'groups',
		'inbox',
		'languages',
		'login',
		'menu_items',
		'moderation',
		'most',
		'notifications',
		'objects_stats',
		'outbox',
		'password',
		'recommendations',
		'registration',
		'search',
		'settings',
		'templates',
		'user_comments',
		'user_recommendations',
		'users_stats'
	];
	$views = [  // Front-end (referring some object)
		'edit',
		'edit_access',
		'edit_settings',
		'include',
		'invite',
		'my',
		'template',
		'upload',
	//	'user',
		'view',
		'view_comments',
		'view_inclusions',
		'visits'
	];

	$path = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
	array_shift($path);
	$page = $path[0];

	function route_getPageTypeAndTitle($page) {
		global $requests,
			   $generics,
			   $views;

		if(preg_match('/^([rgv])_/', $page, $matches)) {
			$prefix = $matches[1];
			$page = substr($page, 2);
		} else {
			$prefix = '';
		}

		$request = in_array($page, $requests);
		$generic = in_array($page, $generics);
		$view    = in_array($page, $views);

		$main_type = $view ? 'view' : ($generic ? 'generic' : ($request ? 'request' : ''));

		if(empty($main_type) || $prefix === $main_type[0]) {
			return ['', ''];
		}

		if(empty($prefix)) {
			return [$main_type, $page];
		}
		if($prefix === 'g' && $generic) {
			return ['generic', $page];
		}
		if($prefix === 'r' && $request) {
			return ['request', $page];
		}

		return ['', ''];
	}

	function route_getViewObjectID() {
		if(array_key_exists($page, $views)) {
			return $path[1];
		} else
		if(!array_key_exists($page, $generics) && !array_key_exists($page, $requests)) {

		}
	}
?>