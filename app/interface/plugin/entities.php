<?
	// Outer: navigation_mode_id, navigation_template_title, navigation_page, navigation_items_per_page, navigation_rss_id, search_entity, search_class, search_fields, search_condition, template_title, template_namespace

	$navigation_mode_id = $this->navigation_mode_id ?? 0;
	$navigation_template_title = $this->navigation_template_title ?? 'navigation';
	$search_settings = [
		$this->search_entity ?? 'objects',
		$this->search_class ?? 'Object_',
		$this->search_fields ?? null,
		$this->search_condition ?? null
	];

	if($navigation_mode_id > 0) {
		$navigation_page = $this->navigation_page ?? 0;
		$navigation_items_per_page = $this->navigation_items_per_page ?? 24;
		$navigation_rss_id = $this->navigation_rss_id;

		if(filter_var($navigation_page, FILTER_VALIDATE_INT) === false || $navigation_page < 0) {
			$navigation_page = 0;
		}
		if(filter_var($navigation_items_per_page, FILTER_VALIDATE_INT) === false || $navigation_items_per_page < 1) {
			$navigation_items_per_page = 1;
		}

		$search_settings[] = $navigation_items_per_page;
		$search_settings[] = $navigation_items_per_page*$navigation_page;
	}

	$search = Entity::search(...$search_settings);
	$entities = $search['entities'];
	$count = $search['count'];
	$template_title = $this->template_title ?? 'objects-list';

	if($navigation_mode_id > 0) {
		$navigation_template = new Template($navigation_template_title);
		$navigation_template->page = $navigation_page;
		$navigation_template->items_per_page = $navigation_items_per_page;
		$navigation_template->items = $count;
		$navigation_template->rss_id = $navigation_rss_id;
	}
	if($navigation_mode_id == 1 && $count > 0) {
		$navigation_template->render(true);
	}

	extract($this->template_namespace ?? []);

	include "$template_title.php";

	if($navigation_mode_id == 1 && $count > 0 || $navigation_mode_id == 2) {
		$navigation_template->render(true);
	}
?>