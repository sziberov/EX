<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	extract(Session::getSettings());

	$page_title = D['title_settings'];
?>
<div _grid="h">
	<form _table style="--columns: repeat(2, minmax(0, max-content));" method="post">
		<div>
			<div></div>
			<div _title centered_><?= D['title_settings']; ?></div>
		</div>
		<div>
			<div><?= D['string_login']; ?></div>
			<div>
				<div _flex="v left">
					<div _flex="h">
						<input name="login" type="text" value="<?= $login; ?>">
						<button><?= D['button_check']; ?></button>
					</div>
					<!--
					<div fallback_><?= D['error_login_is_already_in_use']; ?></div>
					<div fallback_><?= D['error_login_is_misspelled']; ?></div>
					<div fallback_>Логин доступен.</div>
					-->
				</div>
			</div>
		</div>
		<div>
			<div><?= D['string_password']; ?></div>
			<div>
				<input name="password" type="password">
			</div>
		</div>
		<div>
			<div><?= D['string_password_repeat']; ?></div>
			<div>
				<input name="passwordRepeat" type="password">
			</div>
		</div>
		<div>
			<div><?= D['string_email']; ?></div>
			<div>
				<input size_="big" name="email" type="text" value="<?= $email ?? ''; ?>">
			</div>
		</div>
		<div>
			<div><?= D['string_name']; ?></div>
			<div>
				<input size_="big" name="title" type="text" value="<?= $title ?? ''; ?>">
			</div>
		</div>
		<div>
			<div><?= D['string_about_self']; ?></div>
			<div>
				<textarea size_="big" name="description" type="text"><?= $description ?? ''; ?></textarea>
			</div>
		</div>
		<? if($allow_advanced_control ?? false) { ?>
			<div>
				<div><?= D['string_search']; ?></div>
				<div _flex="v stacked left">
					<label _check>
						<input name="hide_from_search" type="checkbox" <?= $hide_from_search ? 'checked' : ''; ?>>
						<div></div>
						<div><?= D['string_hide_from_search'].' '.D['string_hide_from_search_inherition']; ?></div>
					</label>
					<label _check>
						<input name="hide_default_referrer" type="checkbox" <?= $hide_default_referrer ?? false ? 'checked' : ''; ?>>
						<div></div>
						<div><?= D['string_hide_default_referrer']; ?></div>
					</label>
				</div>
			</div>
		<? } ?>
		<div>
			<div><?= D['string_menu']; ?></div>
			<div>
				<label _check>
					<input name="hide_from_search" type="checkbox" <?= ($menu['use_personal_menu'] ?? '') ? 'checked' : ''; ?>>
					<div></div>
					<div><?= D['string_use']; ?> <a href="/menu_items"><?= D['link_personal_menu']; ?></a></div>
				</label>
			</div>
		</div>
		<? if($allow_advanced_control ?? false) { ?>
			<div>
				<div>Основная группа</div>
				<div>
					<select name="group">
						<option selected>group_system</option>
					</select>
				</div>
			</div>
		<? } ?>
		<div>
			<div><?= D['string_editor']; ?></div>
			<div>
				<input name="editor" type="text" placeholder="<?= D['string_upload_id']; ?>" value="<?= $editor_id ?? ''; ?>">
			</div>
		</div>
		<div>
			<div><?= D['string_default_avatar']; ?></div>
			<div>
				<select name="avatar">
					<option>Нет</option>
					<option selected>DIES</option>
				</select>
			</div>
		</div>
		<div>
			<div><?= D['string_templates']; ?></div>
			<div>
				<a _button href="/template"><?= D['button_select_template']; ?></a>
			</div>
		</div>
		<div>
			<div><?= D['string_notifications']; ?></div>
			<div _flex="v stacked left">
				<label _check>
					<input name="notify_friendship" type="checkbox" <?= ($notifications['friendship'] ?? false) ? 'checked' : ''; ?>>
					<div></div>
					<div><?= D['string_friendship']; ?></div>
				</label>
				<label _check>
					<input name="notify_private_messages" type="checkbox" <?= ($notifications['private_messages'] ?? false) ? 'checked' : ''; ?>>
					<div></div>
					<div><?= D['string_private_messages']; ?></div>
				</label>
				<label _check>
					<input name="notify_inclusions" type="checkbox" <?= ($notifications['inclusions'] ?? false) ? 'checked' : ''; ?>>
					<div></div>
					<div><?= D['string_inclusions']; ?></div>
				</label>
				<label _check>
					<input name="notify_comments" type="checkbox" <?= ($notifications['comments'] ?? false) ? 'checked' : ''; ?>>
					<div></div>
					<div><?= D['string_comments']; ?></div>
				</label>
				<label _check>
					<input name="notify_recommendations" type="checkbox" <?= ($notifications['recommendations'] ?? false) ? 'checked' : ''; ?>>
					<div></div>
					<div><?= D['string_recommendations']; ?></div>
				</label>
			</div>
		</div>
		<? if($allow_advanced_control ?? false) { ?>
			<div>
				<div><?= D['string_privileges']; ?></div>
				<div _flex="v stacked left">
					<label _check>
						<input name="allow_any_upload_size" type="checkbox" <?= ($allow_any_upload_size ?? false) ? 'checked' : ''; ?>>
						<div></div>
						<div><?= D['string_allow_any_upload_size']; ?></div>
					</label>
					<label _check>
						<input name="allow_advanced_control" type="checkbox" <?= ($allow_advanced_control ?? false) ? 'checked' : ''; ?>>
						<div></div>
						<div><?= D['string_allow_advanced_control']; ?></div>
					</label>
					<label _check>
						<input name="allow_max_access_ignoring_groups" type="checkbox" <?= ($allow_max_access_ignoring_groups ?? false) ? 'checked' : ''; ?>>
						<div></div>
						<div><?= D['string_allow_max_access_ignoring_groups']; ?></div>
					</label>
				</div>
			</div>
		<? } ?>
		<div>
			<div></div>
			<div>
				<button primary_ type="submit"><?= D['button_save']; ?></button>
			</div>
		</div>
	</form>
	<form _flex="v center" action="/destroy/<?= Session::getUserID(); ?>" method="post" onsubmit="return confirm('<?= D['string_account_deletion_confirmation']; ?>');">
		<div _title>&nbsp;</div>
		<div><?= template_parseBB(D['string_account_deletion']); ?></div>
		<button type="submit"><?= D['button_delete']; ?></button>
	</form>
</div>