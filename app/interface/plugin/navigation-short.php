<?
	// Outer: page, items_per_page, items, buttons

	$page = $this->page ?? http_getArgument('page') ?? 0;
	$items_per_page = $this->items_per_page ?? http_getArgument('items_per_page') ?? 24;
	$items = $this->items;
	$buttons = $this->buttons ?? 11;
	$pages = max(1, ceil($items/$items_per_page));
?>
<div _grid="h" navigation_>
	<?
		$half_buttons = floor($buttons/2);
		$start_page = max(0, $page-$half_buttons);
		$end_page = $start_page+$buttons-1;

		if($end_page >= $pages) {
			$start_page = max(0, $pages-$buttons);
			$end_page = $pages-1;
		}

		if($start_page > 0) { ?>
			<a _button href="?<?= http_getShortParameterQuery('page', 0); ?>">1</a>
			<? if($start_page > 1) { ?>
				<div>..</div>
			<? }
		}
		for($page_ = $start_page; $page_ <= $end_page; $page_++) {
			if($page_ == $page) { ?>
				<a _button disabled_><?= $page_+1; ?></a>
			<? } else { ?>
				<a _button href="?<?= http_getShortParameterQuery('page', $page_); ?>"><?= $page_+1; ?></a>
			<? }
		}
		if($pages-$end_page > 1) { ?>
			<div>..</div>
			<? if($end_page < $pages-1) { ?>
				<a _button href="?<?= http_getShortParameterQuery('page', $pages-1); ?>"><?= $pages; ?></a>
			<? }
		}
	?>
</div>