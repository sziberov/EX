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

	$user = new Object_(Session::getUserID());
?>
<title><?= dictionary_getPageTitle($object->title.' - '.D['title_edit_access']); ?></title>
<?
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
		<div></div>
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
				<button><?= D['button_remove']; ?></button>
			</div>
		</div>
	<? } ?>
	<div switch_ data-switch="edit">
		<div>
			<input type="text" list="groups" placeholder="<?= D['string_object_id']; ?>">
			<datalist id="groups">
				<? foreach($user->user_group_access_links as $uga_link) { ?>
					<option value="<?= $uga_link->to->id; ?>"><?= $uga_link->to->title; ?></option>
				<? } ?>
			</datalist>
		</div>
		<div>
			<select name="access_level_id">
				<? for($i = 0; $i < 6; $i++) { ?>
					<option <?= $i == 2 ? 'selected' : ''; ?>><?= D['string_access_level_'.$i]; ?></option>
				<? } ?>
			</select>
		</div>
		<div></div>
		<div>
			<button><?= D['button_save']; ?></button>
			<button data-switch-ref="edit"><?= D['button_cancel']; ?></button>
		</div>
	</div>
</div>
<div _grid="h spaced">
	<div>
		<a switch_="current" data-switch="edit" data-switch-ref="edit"><u><?= D['link_add']; ?></u></a>
	</div>
	<div fallback_>Редактирование связи со своей основной группой может привести к <a><u>потере доступа</u></a></div>
</div>