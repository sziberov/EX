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

	$display_mode_id = http_getArgument('display_mode_id');
	$display_mode_id = $display_mode_id == 0 || $display_mode_id == 1 ? $display_mode_id : 0;
	$sort_mode_id = http_getArgument('sort_mode_id');
	$sort_mode_id = $sort_mode_id == 0 || $sort_mode_id == 1 ? $sort_mode_id : 0;

	$page_title = $object->title.' - '.D['title_visits'];

	$template = new Template('referrer');
	$template->object = $object;
	$template->render(true);
?>
<div _title><?= D['title_visits']; ?></div>
<?
	$template = new Template('entities');
	$template->search_entity = 'visits';
	$template->search_class = 'stdClass';

	$template->search_fields = "SUBSTRING_INDEX(GROUP_CONCAT(IFNULL(ip_address, '') ORDER BY creation_time DESC), ',', 1) AS hits_ip_address,
								SUBSTRING_INDEX(GROUP_CONCAT(CASE WHEN h THEN ip_address END ORDER BY creation_time DESC), ',', 1) AS hosts_ip_address,
								SUBSTRING_INDEX(GROUP_CONCAT(CASE WHEN g THEN ip_address END ORDER BY creation_time DESC), ',', 1) AS guests_ip_address,

								MAX(creation_time) AS hits_creation_time,
								MAX(CASE WHEN h THEN creation_time END) AS hosts_creation_time,
								MAX(CASE WHEN g THEN creation_time END) AS guests_creation_time,

								SUM(DATE(creation_time) = CURDATE()) AS hits_today_count,
								SUM(DATE(creation_time) = CURDATE()-INTERVAL 1 DAY) AS hits_yesterday_count,
								SUM(DATE(creation_time) = CURDATE()-INTERVAL 2 DAY) AS hits_day_before_yesterday_count,
								SUM(DATE(creation_time) >= CURDATE()-INTERVAL 7 DAY) AS hits_week_count,
								SUM(DATE(creation_time) >= CURDATE()-INTERVAL 30 DAY) AS hits_month_count,
								SUM(1) AS hits_count,

								SUM(h AND DATE(creation_time) = CURDATE()) AS hosts_today_count,
								SUM(h AND DATE(creation_time) = CURDATE()-INTERVAL 1 DAY) AS hosts_yesterday_count,
								SUM(h AND DATE(creation_time) = CURDATE()-INTERVAL 2 DAY) AS hosts_day_before_yesterday_count,
								SUM(h AND DATE(creation_time) >= CURDATE()-INTERVAL 7 DAY) AS hosts_week_count,
								SUM(h AND DATE(creation_time) >= CURDATE()-INTERVAL 30 DAY) AS hosts_month_count,
								SUM(h) AS hosts_count,

								SUM(g AND DATE(creation_time) = CURDATE()) AS guests_today_count,
								SUM(g AND DATE(creation_time) = CURDATE()-INTERVAL 1 DAY) AS guests_yesterday_count,
								SUM(g AND DATE(creation_time) = CURDATE()-INTERVAL 2 DAY) AS guests_day_before_yesterday_count,
								SUM(g AND DATE(creation_time) >= CURDATE()-INTERVAL 7 DAY) AS guests_week_count,
								SUM(g AND DATE(creation_time) >= CURDATE()-INTERVAL 30 DAY) AS guests_month_count,
								SUM(g) AS guests_count";
	$template->search_condition = "JOIN (
									  SELECT
										   id,
										   ROW_NUMBER() OVER (PARTITION BY object_id, ip_address ORDER BY id ASC) = 1 AND ip_address IS NOT NULL AS h,
										   ROW_NUMBER() OVER (PARTITION BY object_id, referrer_url ORDER BY id ASC) = 1 AND referrer_url IS NOT NULL AND referrer_url NOT LIKE '/%' AS g
									   FROM visits
									   WHERE object_id = $object->id
								   ) AS v_1 ON v_1.id = v.id ";

	if($display_mode_id == 0) {
		$template->search_fields = 'object_id, '.$template->search_fields;
		$template->search_condition .= "GROUP BY object_id ";
		$template->template_title = 'view/visits.visits-short';
	} else
	if($display_mode_id == 1) {
		$template->navigation_mode_id = 2;
		$template->search_fields = 'referrer_url, '.$template->search_fields;
		$template->search_condition .= "GROUP BY referrer_url ";

		if($sort_mode_id == 0) {
			$template->search_condition .= "ORDER BY hits_count DESC,
													 hosts_count DESC,
													 guests_count DESC,
													 hits_month_count DESC,
													 hosts_month_count DESC,
													 guests_month_count DESC,
													 hits_week_count DESC,
													 hosts_week_count DESC,
													 guests_week_count DESC,
													 hits_day_before_yesterday_count DESC,
													 hosts_day_before_yesterday_count DESC,
													 guests_day_before_yesterday_count DESC,
													 hits_yesterday_count DESC,
													 hosts_yesterday_count DESC,
													 guests_yesterday_count DESC,
													 hits_today_count DESC,
													 hosts_today_count DESC,
													 guests_today_count DESC,
													 MIN(v.id) DESC";
		} else
		if($sort_mode_id == 1) {
			$template->search_condition .= "ORDER BY hits_today_count DESC,
													 hosts_today_count DESC,
													 guests_today_count DESC,
													 hits_yesterday_count DESC,
													 hosts_yesterday_count DESC,
													 guests_yesterday_count DESC,
													 hits_day_before_yesterday_count DESC,
													 hosts_day_before_yesterday_count DESC,
													 guests_day_before_yesterday_count DESC,
													 hits_week_count DESC,
													 hosts_week_count DESC,
													 guests_week_count DESC,
													 hits_month_count DESC,
													 hosts_month_count DESC,
													 guests_month_count DESC,
													 hits_count DESC,
													 hosts_count DESC,
													 guests_count DESC,
													 MIN(v.id) DESC";
		}

		$template->template_title = 'view/visits.visits';
		$template->template_namespace = [
			'allow_advanced_control' => $allow_advanced_control,
			'sort_mode_id' => $sort_mode_id
		];
	}

	$template->render(true);
?>