<div _table="list" wide_ style="--columns: minmax(48px, max-content) minmax(max-content, auto) minmax(48px, 384px) repeat(4, minmax(max-content, auto))">
	<div header_>
		<div fallback_></div>
		<div><?= D['string_server']; ?></div>
		<div></div>
		<div><?= D['string_used_size']; ?></div>
		<div><?= D['string_free_size']; ?></div>
		<div><?= D['string_summary_size']; ?></div>
		<div></div>
	</div>
	<? foreach($entities as $k => $fs) {
		$used_percents = round($fs->used_size/$fs->size*100);
		$free_percents = round($fs->free_size/$fs->size*100);
	?>
		<div>
			<div fallback_><?= $k+1; ?></div>
			<div><?= $fs->domain; ?></div>
			<div>
				<div _progress>
					<div <?= $used_percents >= 87.5 ? 'danger_' : ($used_percents >= 75 ? 'warning_' : ''); ?> style="width: <?= $used_percents; ?>%;"></div>
				</div>
			</div>
			<div>
				<div><?= template_formatSize($fs->used_size); ?><div _badge><?= $used_percents; ?>%</div></div>
			</div>
			<div>
				<div><?= template_formatSize($fs->free_size); ?><div _badge><?= $free_percents; ?>%</div></div>
			</div>
			<div><?= template_formatSize($fs->size); ?></div>
			<div fallback_><?= template_formatTime($fs->edit_time); ?></div>
		</div>
	<? } ?>
</div>