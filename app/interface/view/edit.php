<?
	try {
		$object = new Object_($path[1] ?? null);
	} catch(Exception $e) {
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if($object->type_id != 4) {
		if(!Session::set()) {
			return include 'generic/login.php';
		}

		$user = new Object_(Session::getUserID());
	}

	$allow_advanced_control = Session::getSetting('allow_advanced_control');

	if($object->access_level_id < 4 || $object->type_id == 2 && !$allow_advanced_control) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	if(!empty($_POST)) {
		$title = $_POST['title'] ?? null;
		$description = $_POST['description'] ?? null;

		Object_::saveID($object->id, $title, $description);

		exit(header('Location: '.$_SERVER['REQUEST_URI']));
	}

	if($object->type_id != 4) {
		$awaiting_save = $object->getSetting('awaiting_save');
		$hide_default_referrer = $object->type_id != 3 || $object->getSetting('hide_default_referrer');  // TODO: Create physical settings

		try {
			$referrer = new Object_($object->getValidReferrerID($_GET['referrer_id'] ?? null, !$hide_default_referrer));
		} catch(Exception $e) {}

		$referred_title = $object->title.(!empty($referrer) ? ' - '.$referrer->title : '');
	} else {
		$referred_title = $object->title;
	}
?>
<title><?= dictionary_getPageTitle($referred_title.' - '.D['title_edit']); ?></title>
<? if($awaiting_save && count($object->links) <= 1) { ?>
	<div _flex="h wrap" _title="small">
		<? if(count($object->links) == 0) { ?>
			<div fallback_>Создание статьи</div>
		<? } else
		if(count($object->links) == 1) {
			$first_link = $object->links[0];

			if($first_link->type_id == 4) {
				if($first_link->to_id == $object->user_id) { ?>
					<div fallback_>Создание статьи на странице пользователя</div>
				<? } else { ?>
					<div fallback_>Создание статьи в разделе </div>
					<? if(!empty($referrer)) { ?>
						<a href="/<?= $referrer->id; ?>"><?= $referrer->title; ?></a>
					<? }
				}
			} else
			if($first_link->type_id == 5) { ?>
				<div fallback_>Создание комментария к </div>
				<? if(!empty($referrer)) { ?>
					<a href="/<?= $referrer->id; ?>"><?= $referrer->title; ?></a>
				<? }
			} else
			if($first_link->type_id == 7) { ?>
				<div fallback_>Создание аватара</div>
			<? } else
			if($first_link->type_id == 8) { ?>
				<div fallback_>Создание жалобы к </div>
			<? } else
			if($first_link->type_id == 11) { ?>
				<div fallback_>Создание личного сообщения для </div>
			<? }
		} ?>
	</div>
<? } else
if(!empty($referrer)) {
	$template = new Template('referrer');
	$template->object = $referrer;
	$template->render(true);
} ?>
<form _table style="--columns: minmax(96px, max-content) minmax(96px, auto) minmax(96px, max-content);" method="post">
	<div>
		<div></div>
		<div _title centered_><?= D['title_edit']; ?></div>
	</div>
	<? if($object->type_id == 4) { ?>
		<div>
			<div>Ключ доступа</div>
			<div _flex="v stacked left">
				<div>Номер <b><?= $object->id; ?></b> или ссылка <b><?= DOMAIN_ROOT.'/'.$object->id; ?></b></div>
				<div fallback_>Сохраните эти данные, доступ к объекту возможен только по ним</div>
			</div>
		</div>
	<? } ?>
	<div>
		<div><?= D['string_title']; ?></div>
		<div>
			<input size_="max" name="title" type="text" value="<?= e($object->_title); ?>">
		</div>
	</div>
	<div>
		<div><?= D['string_description']; ?></div>
		<div _flex="v left">
			<textarea size_="max" name="description"><?= e($object->description); ?></textarea>
			<div _flex="h wrap">
				<button onclick="Editor.addBB('b');"><b><?= D['button_bold']; ?></b></button>
				<button onclick="Editor.addBB('i');"><i><?= D['button_italic']; ?></i></button>
				<button onclick="Editor.addBB('u');"><u><?= D['button_underscored']; ?></u></button>
				<button onclick="Editor.addBB('s');"><s><?= D['button_striked']; ?></s></button>
				<button onclick="Editor.addBB('sup');"><sup><?= D['button_super']; ?></sup></button>
				<button onclick="Editor.addBB('sub');"><sub><?= D['button_sub']; ?></sub></button>
				<button onclick="Editor.addBB('url', 'Link', 'http://example.com');"><?= D['button_link']; ?></button>
				<button onclick="Editor.addBB('color', 'Text', '#000000');"><?= D['button_color']; ?></button>
				<button onclick="Editor.addBB('lang', 'Text', '<?= $language; ?>');"><?= D['button_language']; ?></button>
				<button onclick="Editor.addBB('code');"><pre>&lt;/&gt;</pre></button>
				<button onclick="Editor.addBB('left');">←</button>
				<button onclick="Editor.addBB('center');">→←</button>
				<button onclick="Editor.addBB('right');">→</button>
				<button onclick="Editor.addBB('just');">←→</button>
			</div>
		</div>
	</div>
	<? if($object->type_id != 4) { ?>
		<div>
			<div><?= D['string_avatar']; ?></div>
			<div>
				<? if(count($user->avatars) > 0) { ?>
					<select>
						<option value="-1"><?= $object->type_id != 2 ? 'По умолчанию' : 'Нет' ; ?></option>
						<? foreach($user->avatars as $avatar) { ?>
							<option value="<?= $avatar->id; ?>" data-id="<?= $avatar->poster->id; ?>" <?= $avatar->id == $object->getSettings('avatar_id') ? 'selected' : ''; ?>><?= e($avatar->title); ?></option>
						<? } ?>
					</select>
					<a _avatar href="/user/_USER_TITLE_" title="DIES">
						<img src="/app/image/background.png">
					</a>
				<? } else { ?>
					<div>Для выбора аватара его необходимо создать <a href="/avatars">здесь</a></div>
				<? } ?>
			</div>
		</div>
		<? if($object->access_level_id == 5 || $allow_advanced_control) { ?>
			<div>
				<div><?= D['string_access_for_everyone']; ?></div>
				<div>
					<select name="everyone_access_level_id">
						<?
							$everyone_access_level_id = 0;

							foreach($object->group_object_access_links as $goa_link) {
								if($goa_link->from_id == 1) {
									$everyone_access_level_id = $goa_link->getSetting('access_level_id');

									break;
								}
							}

							for($i = 0; $i < 6; $i++) { ?>
								<option value="<?= $i; ?>" <?= $i == $everyone_access_level_id ? 'selected' : ''; ?>><?= D['string_access_level_'.$i]; ?></option>
							<? }
						?>
					</select>
				</div>
			</div>
		<? }
	} ?>
	<div>
		<div></div>
		<div>
			<?
				$object_url = (!empty($object->alias) ? '/'.$object->alias : ($object->type_id == 2 ? '/user/'.$object->login : '/'.$object->id)).(!empty($referrer) ? '?referrer_id='.$referrer->id : '');
			?>
			<a _button href="<?= $object_url; ?>"><?= D['button_view']; ?></a>
			<button type="submit"><?= D['button_save']; ?></button>
			<!--<button onclick="Editor.save(<?= $object->id; ?>);"><?= D['button_save']; ?></button>-->
			<? if($object->access_level_id == 5) { ?>
				<a _button href="/destroy/<?= $object->id; ?>"><?= D['button_delete']; ?></a>
			<? }
			if($object->type_id != 4) { ?>
				<a _button href="/edit_settings/<?= $object->id; ?>"><?= D['button_settings']; ?></a>
				<? if($object->access_level_id == 5) { ?>
					<a _button href="/edit_access/<?= $object->id; ?>"><?= D['button_access']; ?></a>
				<? }
			} ?>
		</div>
	</div>
</form>
<? include 'edit.lists.php'; ?>