<?
	$types = [
		2 =>  ['title' => D['string_friends'],			'url' => '/friends'],
		4 =>  ['title' => D['string_page'],				'url' => '/user/'.$user->login],
		6 =>  ['title' => D['string_recommendations'],	'url' => '/recommendations'],
		7 =>  ['title' => D['string_avatars'],			'url' => '/avatars'],
		9 =>  ['title' => D['string_templates'],		'url' => '/templates'],
		10 => ['title' => D['string_bookmarks'],		'url' => '/bookmarks']
	];

	if($object->type_id != 2) {
		unset($types[2]);
	}
	if($object->type_id != 3) {
		unset($types[7]);
		unset($types[9]);
	}
?>
<div _table="list" wide_ style="--columns: repeat(4, minmax(96px, auto));">
	<div header_>
		<div><?= D['string_section']; ?></div>
		<div><?= D['string_inclusions_count']; ?></div>
		<div><?= D['string_redaction']; ?></div>
		<div></div>
	</div>
	<? foreach($entities as $link) { ?>
		<div>
			<div><?= $types[$link->type_id]['title']; ?></div>
			<div>
				<a href="<?= $types[$link->type_id]['url']; ?>"><?= $link->inclusions_count; ?></a>
			</div>
			<div>
				<?
					$template = new Template('user');
					$template->object = $link->user;
					$template->primary_time = $link->creation_time;
					$template->render(true);
				?>
			</div>
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
			<script>
				let toggleToID = () => $('[name="to_id"]').prop('disabled', () => ![2, 4].includes($('[name="type_id"]').val()*1));

				$(toggleToID);
			</script>
			<input type="hidden" name="from_id" value="<?= $object->id; ?>">
			<input type="hidden" name="to_id" value="<?= $user->id; ?>">
			<select name="type_id" onchange="toggleToID();">
				<? foreach($types as $k => $v) { ?>
					<option value="<?= $k; ?>"><?= $v['title']; ?></option>
				<? } ?>
			</select>
		</div>
		<div></div>
		<div></div>
		<div>
			<button type="submit"><?= D['button_save']; ?></button>
			<button type="button" data-switch-ref="edit"><?= D['button_cancel']; ?></button>
		</div>
	</form>
</div>