<div _table="list" small_ switch_ style="--columns: repeat(4, minmax(96px, auto));" data-sync="members_list">
	<div header_>
		<div>
			<select data-sync-ref="list">
				<option value="members_list"><?= D['string_members']; ?></option>
			</select>
		</div>
		<div><?= D['string_access']; ?></div>
		<div><?= D['string_privileges']; ?></div>
		<div><?= D['string_redaction']; ?></div>
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
				<?
					$template = new Template('user');
					$template->object = $uga_link->user;
					$template->primary_time = $uga_link->edit_time;
					$template->render(true);
				?>
			</div>
		</div>
	<? } ?>
</div>