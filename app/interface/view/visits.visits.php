<div _table="list" wide_ style="--columns: minmax(48px, max-content) repeat(3, minmax(96px, auto));">
	<div header_>
		<div fallback_></div>
		<div><?= D['string_referrer_url']; ?></div>
		<div><?= D['string_count']; ?></div>
		<div><?= D['string_redaction']; ?></div>
	</div>
	<? foreach($entities as $k => $visit) {
		$url = e($visit->referrer_url ?? '');
		$ip_address = $allow_advanced_control && !empty($visit->ip_address) ? $visit->ip_address.', ' : '';
	?>
		<div>
			<div fallback_><?= $k+1; ?></div>
			<div>
				<a href="<?= $url; ?>"><?= $url; ?></a>
			</div>
			<div><?= $visit->count; ?></div>
			<div><?= $ip_address.template_formatTime($visit->creation_time); ?></div>
		</div>
	<? } ?>
</div>