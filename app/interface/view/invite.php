<?
	try {
		$object = new Object_($path[1] ?? null);
	} catch(Exception $e) {
		error_404:
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if($object->type_id != 1) {
		goto error_404;
	}

	if(!Session::set()) {
		return include 'generic/login.php';
	}

	foreach($object->user_group_access_links as $uga_link) {  // TODO: Optimize by direct function
		if($uga_link->from_id == Session::getUserID()) {
			$privileges = $uga_link->privileges;

			break;
		}
	}

	if(empty($privileges) || !$privileges['allow_invites']) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$page_title = $object->title.' - '.D['title_invite'];

	$template = new Template('referrer');
	$template->object = $object;
	$template->render(true);
?>
<div _table style="--columns: repeat(2, minmax(0, max-content));">
	<div>
		<div></div>
		<div _title centered_><?= D['title_invite']; ?></div>
	</div>
	<div>
		<div><?= D['string_login']; ?></div>
		<div>
			<input name="login" type="text">
		</div>
	</div>
	<div>
		<div><?= D['string_access']; ?></div>
		<div>
			<select name="access_level_id">
				<? for($i = 0; $i < $object->access_level_id+1; $i++) { ?>
					<option <?= $i == 2 ? 'selected' : ''; ?>><?= D['string_access_level_'.$i]; ?></option>
				<? } ?>
			</select>
		</div>
	</div>
	<div>
		<div><?= D['string_privileges']; ?></div>
		<div _flex="v stacked left">
			<? foreach($privileges as $k => $v) { ?>
				<label _check <?= !$v ? 'disabled_' : ''; ?>>
					<input name="<?= $k; ?>" type="checkbox">
					<div></div>
					<div><?= D['string_'.$k]; ?></div>
				</label>
			<? } ?>
		</div>
	</div>
	<div>
		<div></div>
		<div>
			<button onclick="Hash.set('edit/_ID_')"><?= D['button_invite']; ?></button>
		</div>
	</div>
</div>
<div _table="list" small_ style="--columns: repeat(5, minmax(96px, auto));">
	<div header_>
		<div><?= D['string_user']; ?></div>
		<div><?= D['string_access']; ?></div>
		<div><?= D['string_privileges']; ?></div>
		<div><?= D['string_redaction']; ?></div>
		<div></div>
	</div>
	<div data-user-id="_GROUP_ID_">
		<div>
			<a _description="short straight" href="#user/DIES">DIES</a>
		</div>
		<div>Полный</div>
		<div>
			<div _flex="v stacked left">
				<div>Высший доступ</div>
				<div>Приглашение участников</div>
			</div>
		</div>
		<div>
			<a _description="short straight" href="#user/system">system</a>, 00:00, 01 января 2019
		</div>
		<div></div>
	</div>
	<div data-user-id="_GROUP_ID_">
		<div>
			<a _description="short straight" href="#user/UserIn">UserIn</a>
		</div>
		<div>Редактирование</div>
		<div>
			<div _flex="v stacked left">
				<div>Приглашение участников</div>
			</div>
		</div>
		<div>
			<a _description="short straight" href="#user/system">system</a>, 00:00, 01 января 2019
		</div>
		<div>
			<button><?= D['button_remove']; ?></button>
		</div>
	</div>
</div>
<div _grid="h spaced">
	<div></div>
	<div fallback_>Пользователь получит доступ только после принятия приглашения</div>
</div>