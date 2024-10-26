<?
	$query = trim(http_getArgument('query') ?? '');

	try {
		$user = new Object_($_GET['user_id'] ?? null);

		if($user->type_id != 2) {
			$user = null;
		}
	} catch(Exception $e) {}
	try {
		$section = new Object_($_GET['section_id'] ?? null);

		if($section->type_id != 3) {
			$section = null;
		}
	} catch(Exception $e) {}

	$page_title = D['title_search'].(!empty($query) ? ' "'.$query.'"' : '');
?>
<form _grid="h">
	<input size_="large" name="<?= http_getShortParameter('query'); ?>" type="text" value="<?= e($query); ?>">
	<? if(!empty($user)) { ?>
		<input name="user_id" type="hidden" value="<?= $user->id; ?>">
	<? }
	   if(!empty($section)) { ?>
		<input name="section_id" type="hidden" value="<?= $section->id; ?>">
	<? } ?>
	<button primary_ type="submit"><?= D['button_search']; ?></button>
</form>
<div>Не можете найти что-то - напишите в <b><a href="/view/0">Ищем и находим</a></b></div>
<? if(!empty($user) || !empty($section)) { ?>
	<div _grid="h">
		<? if(!empty($user)) { ?>
			<div _flex="h wrap">
				<div><?= D['string_user']; ?></div>
				<?
					$template = new Template('user');
					$template->object = $user;
					$template->time_display_mode_id = 0;
					$template->render(true);
				?>
			</div>
		<? }
		   if(!empty($section)) { ?>
			<div><?= D['string_section']; ?><b _badge><a href="<?= !empty($section->alias) ? '/'.$section->alias : '/'.$section->id; ?>"><?= e($section->title); ?></a></b></div>
		<? } ?>
	</div>
<? }

	if(!empty($query)) {
		$template = new Template('entities');
		$template->navigation_mode_id = 2;
		$template->search_entity = 'public_objects o';
		$template->search_condition = Object_::getGenericSearchCondition($query, $user->id ?? null, $section->id ?? null);
		$template->render(true);

		if(!empty($user) || !empty($section)) { ?>
			<div _grid="h">
				<a _button href="?<?= http_getShortParameter('query').'='.e($query); ?>"><?= D['button_search_everywhere']; ?></a>
			</div>
		<? }
	}
?>