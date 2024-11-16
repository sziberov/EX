<?
	// Outer: object, online, mutual, notifications_count, primary_time, secondary_time, time_display_mode_id

	$object = $this->object;
	$user = $object->type_id == 2 ? $object : $object->user;
	$user_url = !empty($user) ? (!empty($user->alias) ? '/'.$user->alias : '/user/'.$user->login) : null;
	$online = $this->online ?? $user->online ?? false;
	$non_mutual = isset($this->mutual) && !$this->mutual;
	$notifications_count = $this->notifications_count ?? 0;
	$avatar = $object->avatar ?? ($user != $object ? $user->avatar ?? null : null);
	$avatar_url = !empty($avatar) ? (!empty($avatar->alias) ? '/'.$avatar->alias : '/'.$avatar->id) : null;
	$primary_time = $this->primary_time ?? $object->creation_time ?? null;
	$secondary_time = $this->secondary_time ?? $object->edit_time ?? null;
	$time_display_mode_id = $this->time_display_mode_id ?? 1;
?>
<div _user>
	<? if(!empty($user) && !empty($avatar)) { ?>
		<a _avatar="small" href="<?= $user_url; ?>" href-alt="<?= $avatar_url; ?>" title="<?= e($user->title.': '.$avatar->title); ?>">
			<img src="/get/<?= $avatar->poster->id; ?>">
		</a>
	<? } ?>
	<? if(!empty($user)) { ?>
		<a <?= $non_mutual ? 'fallback_' : ''; ?> href="<?= $user_url; ?>"><?= ($online ? '<u>'.e($user->title).'</u>' : e($user->title)).($notifications_count > 0 ? " <sup>$notifications_count</sup>" : ''); ?></a>
	<? } ?>
	<? if($time_display_mode_id >= 1 && !empty($primary_time)) { ?>
		<small><?= template_formatTime($primary_time); ?></small>
	<? } ?>
	<? if($time_display_mode_id >= 2 && !empty($secondary_time) && $secondary_time != $primary_time) { ?>
		<small fallback_><?= template_formatTime($secondary_time); ?></small>
	<? } ?>
</div>