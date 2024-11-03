<div _table="list" small_ style="--columns: minmax(48px, max-content) repeat(8, minmax(96px, auto));">
	<div header_>
		<div fallback_></div>
		<div><?= D['string_referrer_url']; ?></div>
		<div>
			<? if($sort_mode_id != 1) { ?>
				<a href="?<?= http_getShortParameterQuery('sort_mode_id', 1); ?>"><?= D['string_today']; ?></a>
			<? } else {
				echo D['string_today'];
			} ?>
		</div>
		<div><?= D['string_yesterday']; ?></div>
		<div><?= D['string_day_before_yesterday']; ?></div>
		<div><?= D['string_week']; ?></div>
		<div><?= D['string_month']; ?></div>
		<div>
			<? if($sort_mode_id != 0) { ?>
				<a href="?<?= http_getShortParameterQuery('sort_mode_id', 0); ?>"><?= D['string_total']; ?></a>
			<? } else {
				echo D['string_total'];
			} ?>
		</div>
		<div>
			<a href="?<?= http_getShortParameterQuery('display_mode_id', 0); ?>"><u><?= D['button_minimize']; ?></u></a>
		</div>
	</div>
	<? foreach($entities as $k => $visit) {
		$url = e($visit->referrer_url ?? '');
	?>
		<div>
			<small fallback_><?= $k+1; ?></small>
			<div>
				<a _description="straight" href="<?= $url; ?>"><?= $url; ?></a>
			</div>
			<div>
				<?= $visit->hits_today_count; ?> -
				<?= $visit->hosts_today_count; ?> /
				<?= $visit->guests_today_count; ?>
			</div>
			<div>
				<?= $visit->hits_yesterday_count; ?> -
				<?= $visit->hosts_yesterday_count; ?> /
				<?= $visit->guests_yesterday_count; ?>
			</div>
			<div>
				<?= $visit->hits_day_before_yesterday_count; ?> -
				<?= $visit->hosts_day_before_yesterday_count; ?> /
				<?= $visit->guests_day_before_yesterday_count; ?>
			</div>
			<div>
				<?= $visit->hits_week_count; ?> -
				<?= $visit->hosts_week_count; ?> /
				<?= $visit->guests_week_count; ?>
			</div>
			<div>
				<?= $visit->hits_month_count; ?> -
				<?= $visit->hosts_month_count; ?> /
				<?= $visit->guests_month_count; ?>
			</div>
			<div>
				<?= $visit->hits_count; ?> -
				<?= $visit->hosts_count; ?> /
				<?= $visit->guests_count; ?>
			</div>
			<small fallback_>
				<?
					$hits_ip_address = $allow_advanced_control && !empty($visit->hits_ip_address) ? $visit->hits_ip_address.', ' : '';
					$hosts_ip_address = $allow_advanced_control && !empty($visit->hosts_ip_address) ? $visit->hosts_ip_address.', ' : '';
					$guests_ip_address = $allow_advanced_control && !empty($visit->guests_ip_address) ? $visit->guests_ip_address.', ' : '';

					$hits_creation_time = !empty($visit->hits_creation_time) ? template_formatTime($visit->hits_creation_time) : '';
					$hosts_creation_time = !empty($visit->hosts_creation_time) ? template_formatTime($visit->hosts_creation_time) : '';
					$guests_creation_time = !empty($visit->guests_creation_time) ? template_formatTime($visit->guests_creation_time) : '';

					$redaction = [];

					if(!empty($hits_ip_address) || !empty($hits_creation_time)) {
						$redaction[] = $hits_ip_address.$hits_creation_time.', '.mb_strtolower(D['string_hit']);
					}
					if(!empty($hosts_ip_address) || !empty($hosts_creation_time)) {
						$redaction[] = $hosts_ip_address.$hosts_creation_time.', '.mb_strtolower(D['string_host']);
					}
					if(!empty($guests_ip_address) || !empty($guests_creation_time)) {
						$redaction[] = $guests_ip_address.$guests_creation_time.', '.mb_strtolower(D['string_guest']);
					}

					echo implode('<br><br>', $redaction);
				?>
			</small>
		</div>
	<? } ?>
</div>