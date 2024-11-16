<div _table="list" small_ style="--columns: repeat(5, minmax(96px, auto));">
	<div header_>
		<div><?= D['string_user']; ?></div>
		<div><?= D['string_was_added']; ?></div>
		<div><?= D['string_has_added']; ?></div>
		<div><?= D['string_online']; ?></div>
		<div></div>
	</div>
	<? foreach($entities as $link) { ?>
		<div data-link-id="<?= $link->id; ?>">
			<div>
				<div _description="short straight">
					<?
						$template = new Template('user');
						$template->object = $link->from;
						$template->time_display_mode_id = 0;
						$template->render(true);
					?>
				</div>
			</div>
			<div><?= template_formatTime($link->creation_time); ?></div>
			<div><?= !empty($link->mutual_creation_time) ? template_formatTime($link->mutual_creation_time) : ''; ?></div>
			<div><?= template_formatTime($link->from->edit_time); ?></div>
			<div>
				<a _button href="/destroy_link/<?= $link->id; ?>"><?= D['button_remove']; ?></a>
			</div>
		</div>
	<? } ?>
	<div footer_ switch_="current" data-switch="edit">
		<div>
			<a data-switch-ref="edit"><u><?= D['link_add']; ?></u></a>
		</div>
	</div>
	<form footer_ switch_ data-switch="edit" action="/create_link" method="get">
		<div>
			<input size_="big" type="text" name="from_id" placeholder="<?= D['string_login']; ?>">
			<input type="hidden" name="to_id" value="<?= $user_id; ?>">
			<input type="hidden" name="type_id" value="2">
		</div>
		<div></div>
		<div></div>
		<div></div>
		<div>
			<button type="submit"><?= D['button_save']; ?></button>
			<button type="button" data-switch-ref="edit"><?= D['button_cancel']; ?></button>
		</div>
	</form>
</div>