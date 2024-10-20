<?
	try {
		$object = new Object_($path[1] ?? null);
	} catch(Exception $e) {
		error_404:
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if($object->type_id == 4) {
		goto error_404;
	}

	if(!Session::set()) {
		return include 'generic/login.php';
	}

	if($object->access_level_id == 0) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$presets = [
		2	=> 'Друзья',
		4	=> 'Страница',
		6	=> 'Рекомендации',
		7	=> 'Аватары',
		9	=> 'Шаблоны',
		10	=> 'Закладки'
	];

	if($object->type_id != 2) {
		unset($presets[2]);
	}
	if($object->type_id != 3) {
		unset($presets[7]);
		unset($presets[9]);
	}

	$page_title = $object->title.' - '.D['title_my'];

	$template = new Template('referrer');
	$template->object = $object;
	$template->render(true);
?>
<div _title><?= D['title_my']; ?></div>
<div _table="list" wide_ style="--columns: repeat(4, minmax(96px, auto));">
	<div header_>
		<div><?= D['string_section']; ?></div>
		<div><?= D['string_objects_count']; ?></div>
		<div><?= D['string_redaction']; ?></div>
	</div>
	<? foreach($object->settings as $setting) { ?>
		<div>
			<div><?= $setting->key; ?></div>
			<div>
				<div _description="short straight"><?= $setting->value; ?></div>
			</div>
			<div>
				<?
					$template = new Template('user');
					$template->object = $setting->user;
					$template->primary_time = $setting->edit_time;
					$template->render(true);
				?>
			</div>
			<div>
				<button><?= D['button_remove']; ?></button>
			</div>
		</div>
	<? } ?>
	<div footer_ switch_="current" data-switch="edit">
		<div>
			<a data-switch-ref="edit"><u><?= D['link_add']; ?></u></a>
		</div>
	</div>
	<div footer_ switch_ data-switch="edit">
		<div>
			<select name="section">
				<? foreach($presets as $k => $v) { ?>
					<option value="<?= $k; ?>"><?= $v; ?></option>
				<? } ?>
			</select>
		</div>
		<div></div>
		<div></div>
		<div>
			<button><?= D['button_save']; ?></button>
			<button data-switch-ref="edit"><?= D['button_cancel']; ?></button>
		</div>
	</div>
</div>