<div _table="list" wide_ switch_ style="--columns: repeat(4, minmax(96px, auto));" data-sync="members_list">
	<div header_>
		<div>
			<select data-sync-ref="list">
				<option value="members_list"><?= D['string_members']; ?></option>
			</select>
		</div>
		<div><?= D['string_access']; ?></div>
		<div><?= D['string_privileges']; ?></div>
	</div>
	<? foreach($object->user_group_access_links as $uga_link) { ?>
		<div>
			<div>
				<?
					$template = new Template('user');
					$template->object = $uga_link->from;
					$template->time_display_mode_id = 0;
					$template->render(true);
				?>
			</div>
			<div>
				<div _description="short straight"><?= D['string_access_level_'.$uga_link->getSetting('access_level_id')]; ?></div>
			</div>
			<div>
				<? if(in_array(true, $uga_link->privileges)) { ?>
					<div _flex="v stacked right">
						<? foreach(array_filter($uga_link->privileges) as $k => $v) { ?>
							<div><?= D['string_'.$k]; ?></div>
						<? } ?>
					</div>
				<? } ?>
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
			<input size_="big" type="text" value="UserOut">
		</div>
		<div>
			<select>
				<? for($i = 0; $i < 6; $i++) { ?>
					<option <?= $i == 2 ? 'selected' : ''; ?>><?= D['string_access_level_'.$i]; ?></option>
				<? } ?>
			</select>
		</div>
		<div>
			<div _flex="v stacked left">
				<? foreach(array_filter(Link::$settings_filters[1], fn($k) => str_starts_with($k, 'allow_'), ARRAY_FILTER_USE_KEY) as $k => $v) { ?>
					<label _check>
						<input name="<?= $k; ?>" type="checkbox">
						<div></div>
						<div><?= D['string_'.$k]; ?></div>
					</label>
				<? } ?>
			</div>
		</div>
		<div>
			<button><?= D['button_save']; ?></button>
			<button data-switch-ref="edit"><?= D['button_cancel']; ?></button>
		</div>
	</div>
</div>