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

	if($object->access_level_id < 5) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$page_title = $object->title.' - '.D['title_edit_access'];

	$template = new Template('referrer');
	$template->object = $object;
	$template->render(true);
?>
<div _title><?= D['title_edit_access']; ?></div>
<div _table="list" wide_ style="--columns: repeat(4, minmax(96px, auto));">
	<div header_>
		<div><?= D['string_group']; ?></div>
		<div><?= D['string_access']; ?></div>
		<div><?= D['string_redaction']; ?></div>
	</div>
	<? foreach($object->group_object_access_links as $goa_link) { ?>
		<div>
			<div>
				<a _description="short straight" href="/<?= $goa_link->from->id; ?>"><?= $goa_link->from->title; ?></a>
			</div>
			<div>
				<div _description="short straight"><?= D['string_access_level_'.$goa_link->getSetting('access_level_id')]; ?></div>
			</div>
			<div>
				<?
					$template = new Template('user');
					$template->object = $goa_link->user;
					$template->primary_time = $goa_link->creation_time;
					$template->render(true);
				?>
			</div>
			<div>
				<a _button href="/destroy_link/<?= $goa_link->id; ?>"><?= D['button_remove']; ?></a>
			</div>
		</div>
	<? } ?>
	<form switch_ data-switch="edit" action="/create_link" method="get">
		<div>
			<input type="text" name="from_id" list="groups" placeholder="<?= D['string_object_id']; ?>" required>
			<datalist id="groups">
				<? foreach(Session::getUser()->user_group_access_links as $uga_link) { ?>
					<option value="<?= $uga_link->to->id; ?>"><?= $uga_link->to->title; ?></option>
				<? } ?>
			</datalist>
			<input type="hidden" name="to_id" value="<?= $object->id; ?>">
			<input type="hidden" name="type_id" value="1">
		</div>
		<div>
			<select name="access_level_id">
				<? for($i = 0; $i < 6; $i++) { ?>
					<option value="<?= $i; ?>" <?= $i == 2 ? 'selected' : ''; ?>><?= D['string_access_level_'.$i]; ?></option>
				<? } ?>
			</select>
		</div>
		<div></div>
		<div>
			<button type="submit"><?= D['button_save']; ?></button>
			<button type="button" data-switch-ref="edit"><?= D['button_cancel']; ?></button>
		</div>
	</form>
</div>
<div _grid="h spaced">
	<div>
		<a switch_="current" data-switch="edit" data-switch-ref="edit"><u><?= D['link_add']; ?></u></a>
	</div>
	<div fallback_>Редактирование связи со своей основной группой может привести к <a><u>потере доступа</u></a></div>
</div>