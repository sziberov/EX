<?
	$most_id = $path[1] ?? 0;

	if(!in_array($most_id, [0, 1, 2, 3])) {
		$most_id = 0;
	}

	$most = [
		D['title_most_popular'],
		D['title_most_visited'],
		D['title_most_commented'],
		D['title_most_recommended']
	];
	$page_title = $most[$most_id];
?>
<div _grid="h spaced">
	<div _title><?= $page_title; ?></div>
	<div _flex="h">
		<? foreach(array_filter($most, fn($k) => $k != $most_id, ARRAY_FILTER_USE_KEY) as $k => $v) { ?>
			<a _button href="/most<?= $k == 0 ? '' : '/'.$k; ?>"><?= $v; ?></a>
		<? } ?>
	</div>
</div>
<?
	$template = new Template('entities');
	$template->navigation_mode_id = 2;
	$template->search_entity = 'public_objects o';
	$template->search_fields = 'o.*,
								ROW_NUMBER() OVER (ORDER BY m.today_count DESC, m.yesterday_count DESC, m.count DESC, o.creation_time DESC, o.id DESC) AS today_position,
								ROW_NUMBER() OVER (ORDER BY						m.yesterday_count DESC, m.count DESC, o.creation_time DESC, o.id DESC) AS yesterday_position';
	$template->search_condition = Object_::getMostSearchCondition($most_id);
	$template->template_title = 'generic/most.objects';
	$template->render(true);
?>