<?
	$page_title = D['title_banners'];
?>
<div _title><?= D['title_banners']; ?></div>
<div _table="list" wide_ style="--columns: repeat(8, minmax(96px, auto));">
	<div header_>
		<div><?= D['string_banner']; ?></div>
		<div>URL</div>
		<div><?= D['string_frequency']; ?></div>
		<div><?= D['string_start']; ?></div>
		<div><?= D['string_end']; ?></div>
		<div><?= D['string_language']; ?></div>
		<div><?= D['string_template']; ?></div>
		<div></div>
	</div>
	<div footer_ switch_="current" data-switch="edit">
		<div>
			<a data-switch-ref="edit"><u><?= D['link_add']; ?></u></a>
		</div>
	</div>
	<div footer_ switch_ data-switch="edit">
		<div>
			<input type="text" placeholder="<?= D['string_object_id']; ?>">
		</div>
		<div>
			<input size_="large" type="text">
		</div>
		<div>
			<select name="frequency">
				<? for($i = -5; $i <= 5; $i++) { ?>
					<option value="<?= $i; ?>" <?= $i == 0 ? 'selected' : ''; ?>><?= $i; ?></option>
				<? } ?>
			</select>
		</div>
		<div>
			<input type="date">
		</div>
		<div>
			<input type="date">
		</div>
		<div>
			<select name="language">
				<option value="-1">-</option>
				<? foreach($languages as $language) { ?>
					<option value="<?= $language; ?>" <?= ($_COOKIE['language'] ?? '') == $language ? 'selected' : ''; ?>><?= D['string_language_'.$language]; ?></option>
				<? } ?>
			</select>
		</div>
		<div>
			<input type="text">
		</div>
		<div>
			<button><?= D['button_save']; ?></button>
			<button data-switch-ref="edit"><?= D['button_cancel']; ?></button>
		</div>
	</div>
</div>