<?
	$page_title = D['title_files'];
?>
<div _grid="padded">
	<div _grid="v">
		<?
			$template = new Template('entities');
			$template->navigation_mode_id = 2;
			$template->navigation_page = $navigation_page;
			$template->navigation_items_per_page = $navigation_items_per_page;
			$template->search_entity = 'public_objects o';
			$template->search_condition = "JOIN links AS l ON l.from_id = o.id AND l.type_id = 4
										   WHERE o.type_id = 3
										   GROUP BY o.id
										   ORDER BY o.creation_time DESC, o.id DESC";
			$template->template_title = 'generic/files.objects';
			$template->render(true);
		?>
	</div>
	<div _grid="v">
		<?= new Template('most'); ?>
	</div>
</div>