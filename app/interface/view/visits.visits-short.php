<div _table="list" small_ style="--columns: minmax(0, max-content) repeat(7, minmax(96px, auto));">
	<div header_>
		<div header_></div>
		<div><?= D['string_today']; ?></div>
		<div><?= D['string_yesterday']; ?></div>
		<div><?= D['string_day_before_yesterday']; ?></div>
		<div><?= D['string_week']; ?></div>
		<div><?= D['string_month']; ?></div>
		<div><?= D['string_total']; ?></div>
		<div>
			<a href="?<?= http_getShortParameterQuery('display_mode_id', 1); ?>"><u><?= D['button_maximize']; ?></u></a>
		</div>
	</div>
	<? foreach(['hits', 'hosts', 'guests'] as $type) {
	    $metric = (object)[];

	    foreach($entities[0] as $key => $value) {
	        if(str_starts_with($key, $type)) {
	            $metric->{substr($key, strlen($type)+1)} = $value;
	        }
	    }
   ?>
		<div <?= $type == 'guests' ? 'footer_' : ''; ?>>
			<div header_><?= D['string_'.$type.'_count']; ?></div>
			<div><?= template_formatSize($metric->today_count); ?></div>
			<div><?= template_formatSize($metric->yesterday_count); ?></div>
			<div><?= template_formatSize($metric->day_before_yesterday_count); ?></div>
			<div><?= template_formatSize($metric->week_count); ?></div>
			<div><?= template_formatSize($metric->month_count); ?></div>
			<div><?= template_formatSize($metric->count); ?></div>
			<small fallback_><?= !empty($metric->creation_time) ? template_formatTime($metric->creation_time) : ''; ?></small>
		</div>
	<? } ?>
</div>