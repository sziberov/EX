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
	<? foreach($object->user_group_access_links as $uga_link) {
		$privileges = ['allow_invites' => false, 'allow_members_list_view' => false, 'allow_higher_access_preference' => false];

		foreach($privileges as $k => $v) {
			$privileges[$k] = $uga_link->getSetting($k);
		}
	?>
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
				<? if(in_array(true, $privileges)) { ?>
					<div _flex="v stacked right">
						<? foreach(array_filter($privileges) as $k => $v) { ?>
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
				<label _check>
					<input name="allow_invites" type="checkbox">
					<div></div>
					<div><?= D['string_allow_invites']; ?></div>
				</label>
				<label _check>
					<input name="allow_members_list_view" type="checkbox">
					<div></div>
					<div><?= D['string_allow_members_list_view']; ?></div>
				</label>
				<label _check>
					<input name="allow_higher_access_preference" type="checkbox">
					<div></div>
					<div><?= D['string_allow_higher_access_preference']; ?></div>
				</label>
			</div>
		</div>
		<div>
			<button><?= D['button_save']; ?></button>
			<button data-switch-ref="edit"><?= D['button_cancel']; ?></button>
		</div>
	</div>
</div>