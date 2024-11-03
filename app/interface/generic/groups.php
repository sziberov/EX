<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	$page_title = D['title_groups'];
?>
<div _title><?= D['title_groups']; ?></div>
<div _table="list" small_ style="--columns: repeat(5, minmax(96px, auto));">
	<div header_>
		<div><?= D['string_group']; ?></div>
		<div><?= D['string_access']; ?></div>
		<div><?= D['string_privileges']; ?></div>
		<div><?= D['string_redaction']; ?></div>
	</div>
	<? foreach(Session::getUser()->user_group_access_links as $uga_link) { ?>
		<div>
			<div>
				<a _description="short straight" href="/<?= $uga_link->to->id; ?>"><?= e($uga_link->to->title); ?></a>
			</div>
			<div>
				<div _description="short straight"><?= D['string_access_level_'.$uga_link->getSetting('access_level_id')].($uga_link->to->id == Session::getSetting('group_id') ? ', основная группа' : ''); ?></div>
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
			<div>
				<? if($uga_link->privileges['allow_invites']) { ?>
					<a _button href="/invite/<?= $uga_link->to->id; ?>"><?= D['button_invite']; ?></a>
				<? }
				if($uga_link->to->access_level_id >= 4) { ?>
					<a _button href="/edit/<?= $uga_link->to->id; ?>"><?= D['button_edit']; ?></a>
					<? if($uga_link->to->access_level_id == 5) { ?>
						<button><?= D['button_delete']; ?></button>
					<? }
				} ?>
			</div>
		</div>
	<? } ?>
</div>
<div fallback_><?= D['string_groups_description']; ?></div>
<div _grid="h">
	<a _button href="/create?type_id=1"><?= D['button_create']; ?></a>
</div>