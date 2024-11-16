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

	if($object->access_level_id < 4) {
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
			$referrer = new Object_($object->getValidReferrerID(http_getArgument('referrer_id'), !$hide_default_referrer));
		} catch(Exception $e) {}
	}

	$page_title = $object->title.' - '.D['title_edit'];
?>
<? if(!empty($awaiting_save)) { ?>
	<div _title="small">
		<?
			$primary_link = $object->primary_link;
			$display_type = $object->display_type;
			$to_id = $primary_link->to_id ?? null;
			$draft_type = D["string_of_$display_type"];

			if(!empty($to_id)) {
				$draft_type .= ' '.match($display_type) {
					'comment',
					'claim' => D['string_to'],
					'template',
					'private_message' => D['string_for'],
					'article',
					'section' => $to_id == $object->user_id ? D['string_on_page'] : D['string_in']
				};
			}
		?>
		<span fallback_><?= D['string_draft'].' '.$draft_type; ?></span>
		<? if(!empty($primary_link->to_id)) { ?>
			<a href="/<?= $primary_link->to->id; ?>"><?= $primary_link->to->title; ?></a>
		<? } ?>
	</div>
<? } else
if(!empty($referrer)) {
	$template = new Template('referrer');
	$template->object = $referrer;
	$template->render(true);
} ?>
<script src="/app/plugin/editor.js"></script>
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
				<button type="button" onclick="Editor.addBB('b');"><b><?= D['button_bold']; ?></b></button>
				<button type="button" onclick="Editor.addBB('i');"><i><?= D['button_italic']; ?></i></button>
				<button type="button" onclick="Editor.addBB('u');"><u><?= D['button_underscored']; ?></u></button>
				<button type="button" onclick="Editor.addBB('s');"><s><?= D['button_striked']; ?></s></button>
				<button type="button" onclick="Editor.addBB('sup');"><sup><?= D['button_super']; ?></sup></button>
				<button type="button" onclick="Editor.addBB('sub');"><sub><?= D['button_sub']; ?></sub></button>
				<button type="button" onclick="Editor.addBB('url', 'Link', 'http://example.com');"><?= D['button_link']; ?></button>
				<button type="button" onclick="Editor.addBB('color', 'Text', '#000000');"><?= D['button_color']; ?></button>
				<button type="button" onclick="Editor.addBB('lang', 'Text', '<?= $language; ?>');"><?= D['button_language']; ?></button>
				<button type="button" onclick="Editor.addBB('code');"><pre>&lt;/&gt;</pre></button>
				<button type="button" onclick="Editor.addBB('left');">←</button>
				<button type="button" onclick="Editor.addBB('center');">→←</button>
				<button type="button" onclick="Editor.addBB('right');">→</button>
				<button type="button" onclick="Editor.addBB('just');">←→</button>
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
							<option value="<?= $avatar->id; ?>" data-id="<?= $avatar->poster->id; ?>" <?= $avatar->id == $object->getSetting('avatar_id') ? 'selected' : ''; ?>><?= e($avatar->title); ?></option>
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
			<button type="submit" onclick="/*Editor.save(<?= $object->id; ?>);*/"><?= D['button_save']; ?></button>
			<? if($object->type_id != 4) { ?>
				<button type="button" onclick="history.back();"><?= D['button_cancel']; ?></button>
				<a _button href="/edit_settings/<?= $object->id; ?>"><?= D['button_settings']; ?></a>
				<? if($object->access_level_id == 5) { ?>
					<a _button href="/edit_access/<?= $object->id; ?>"><?= D['button_access']; ?></a>
				<? }
			} else { ?>
				<a _button href="/destroy/<?= $object->id; ?>"><?= D['button_delete']; ?></a>
			<? } ?>
		</div>
	</div>
</form>
<? include 'edit.lists.php'; ?>