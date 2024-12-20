<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}

	$user = new Object_(Session::getUserID());
	$page_title = D['title_notifications'];
?>
<div _title><?= D['title_notifications']; ?></div>
<div _table="list" small_ style="--columns: repeat(4, minmax(96px, auto));">
	<div header_>
		<div><?= D['string_event']; ?></div>
		<div><?= D['string_object']; ?></div>
		<div><?= D['string_redaction']; ?></div>
		<div></div>
	</div>
	<? foreach($user->notifications as $notification) {
		$notification_url = !empty($notification->from->alias) ? '/'.$notification->from->alias : ($notification->from->type_id == 2 ? '/user/'.$notification->from->login : '/'.$notification->from->id);
	?>
		<div>
			<div><?= D['string_event_'.$notification->getSetting('event_id')]; ?></div>
			<div>
				<a _description="short straight" href="<?= $notification_url; ?>"><?= e($notification->from->title); ?></a>
			</div>
			<div>
				<?
					$template = new Template('user');
					$template->object = $notification->user;
					$template->primary_time = $notification->creation_time;
					$template->render(true);
				?>
			</div>
			<div>
				<? if($notification->getSetting('event_id') == 0) { ?>
					<button><?= D['button_accept']; ?></button>
				<? } ?>
				<? if(in_array($notification->getSetting('event_id'), [0, 2, 3, 4])) { ?>
					<a _button href="<?= $notification_url; ?>"><?= D['button_view']; ?></a>
				<? } ?>
				<a _button href="/destroy_link/<?= $notification->id; ?>"><?= D['button_remove']; ?></a>
			</div>
		</div>
	<? } ?>
</div>
<div _grid="h">
	<button><?= D['button_remove_all']; ?></button>
</div>