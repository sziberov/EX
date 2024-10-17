<?
	// Outer: page, items_per_page, items, buttons, rss_id

	$page = $this->page;
	$items_per_page = $this->items_per_page;
	$items = $this->items;
	$buttons = $this->buttons ?? 7;
	$rss_id = $this->rss_id;
	$pages = max(1, ceil($items/$items_per_page));
	$first_page = 0;
	$last_page = $pages-1;
?>
<form _flex="h" type="get">
	<? if($page != $first_page) { ?>
		<a _button icon_="to_first" href="?<?= http_build_query(array_merge($_GET, ['page' => $first_page])); ?>" title="<?= D['button_to_first_tooltip']; ?>"></a>
	<? } else { ?>
		<a _button icon_="to_first" disabled_></a>
	<? } ?>
	<?
		$half_buttons = floor($buttons/2);
		$start_page = max(0, $page-$half_buttons);
		$end_page = $start_page+$buttons-1;

		if($end_page >= $pages) {
			$start_page = max(0, $pages-$buttons);
			$end_page = $pages-1;
		}

		for($page_ = $start_page; $page_ <= $end_page; $page_++) {
			$from_item = min($items, $items_per_page*$page_+1);
			$to_item = min($items, $items_per_page*($page_+1));

			if($page_ == $page) {
				if($page != $first_page) { ?>
					<div fallback_>← Ctrl</div>
					<a _button icon_="to_previous" data-navigate="previous" href="?<?= http_build_query(array_merge($_GET, ['page' => $page_-1])); ?>" title="<?= D['button_to_previous_tooltip']; ?>"></a>
				<? } else { ?>
					<a _button icon_="to_previous" disabled_></a>
				<? } ?>
				<b fallback_><?= $from_item.'..'.$to_item; ?></b>
				<? if($page != $last_page) { ?>
					<a _button icon_="to_next" data-navigate="next" href="?<?= http_build_query(array_merge($_GET, ['page' => $page_+1])); ?>" title="<?= D['button_to_next_tooltip']; ?>"></a>
					<div fallback_>Ctrl →</div>
				<? } else { ?>
					<a _button icon_="to_next" disabled_></a>
				<? }
			} else { ?>
				<a href="?<?= http_build_query(array_merge($_GET, ['page' => $page_])); ?>"><?= $from_item.'..'.$to_item; ?></a>
			<? }
		}
	?>
	<? if($page != $last_page) { ?>
		<a _button icon_="to_last" href="?<?= http_build_query(array_merge($_GET, ['page' => $last_page])); ?>" title="<?= D['button_to_last_tooltip']; ?>"></a>
	<? } else { ?>
		<a _button icon_="to_last" disabled_></a>
	<? } ?>
	<? foreach($_GET as $key => $value) {
		if($key == 'items_per_page')
			continue; ?>
		<input type="hidden" name="<?= $key; ?>" value="<?= $value; ?>">
	<? } ?>
	<select fallback_ name="items_per_page" onchange="this.form.submit();" title="<?= D['select_items_per_page_tooltip']; ?>">
		<?
			$ipp_options = [4];

			for($i = 0; $i < 12; $i++) {
			    $ipp_options[] = round($ipp_options[$i]*($i%2 == 0 ? 1.5 : 1.3333));
			}

			if(!in_array($items_per_page, $ipp_options)) {
				$ipp_options[] = $items_per_page;
			}

			foreach($ipp_options as $ipp_option) { ?>
				<option value="<?= $ipp_option; ?>" <?= $ipp_option == $items_per_page ? 'selected' : ''; ?>><?= $ipp_option; ?></option>
			<? }
		?>
	</select>
	<? if(!empty($rss_id)) { ?>
		<a fallback_ href="/rss/<?= $rss_id; ?>">RSS</a>
	<? } ?>
</form>