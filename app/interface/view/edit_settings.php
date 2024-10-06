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

	if($object->access_level_id < 4) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$page_title = $object->title.' - '.D['title_edit_settings'];

	$template = new Template('referrer');
	$template->object = $object;
	$template->render(true);
?>
<div _title><?= D['title_edit_settings']; ?></div>
<div _table="list" wide_ style="--columns: repeat(4, minmax(96px, auto));">
	<div header_>
		<div><?= D['string_setting']; ?></div>
		<div><?= D['string_value']; ?></div>
		<div><?= D['string_redaction']; ?></div>
		<div></div>
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
			<input size_="big" type="text" list="settings" placeholder="<?= D['string_key']; ?>">
			<datalist id="settings">
				<? foreach(Object_::$settings_filters[$object->type_id] ?? [] as $key => $filter_id) { ?>
					<option value="<?= $key; ?>"><?= D['string_'.$key].' '.($filter_id == 0 ? '(boolean)' : ($filter_id == 1 ? '(integer)' : '(string)')); ?></option>
				<? } ?>
			</datalist>
		</div>
		<div>
			<input size_="big" type="text" placeholder="<?= D['string_value']; ?>">
		</div>
		<div></div>
		<div>
			<button><?= D['button_save']; ?></button>
			<button data-switch-ref="edit"><?= D['button_cancel']; ?></button>
		</div>
	</div>
</div>