<?
	try {
		$object = new Object_($path[1] ?? null);
	} catch(Exception $e) {
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	$allow_advanced_control = Session::getSetting('allow_advanced_control');

	if($object->access_level_id == 0 || $object->access_level_id < 4 && !$allow_advanced_control) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$page_title = $object->title.' - '.D['title_visits'];

	$template = new Template('referrer');
	$template->object = $object;
	$template->render(true);
?>
<div _title><?= D['title_visits']; ?></div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->navigation_page = $navigation_page;
	$template->navigation_items_per_page = $navigation_items_per_page;
	$template->search_entity = 'visits';
	$template->search_class = 'Visit';
	$template->search_fields = 'v.*, v_1.count';
	$template->search_condition = "JOIN (
									   SELECT MAX(id) AS id, COUNT(*) AS count
									   FROM visits
									   WHERE object_id = $object->id
									   GROUP BY referrer_url
								   ) AS v_1 ON v_1.id = v.id
								   ORDER BY v_1.count DESC, v.id DESC";
	$template->template_title = 'view/visits.visits';
	$template->template_namespace = [
		'allow_advanced_control' => $allow_advanced_control
	];
	$template->render(true);
?>