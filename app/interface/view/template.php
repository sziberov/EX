<?
	if(!empty($path[1])) {
		try {
			$object = new Object_($path[1] ?? null);
		} catch(Exception $e) {
			$error = D['error_page_not_found'];
			http_response_code(404);
			return include 'plugin/error.php';
		}
	}

	if(!Session::set()) {
		return include 'generic/login.php';
	}

	if(!empty($object) && $object->access_level_id == 0) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$user = Session::getUser();
	$page_title = (!empty($object) ? $object->title.' - ' : '').D['title_template'];

	if(!empty($object)) {
		$template = new Template('referrer');
		$template->object = $object;
		$template->render(true);
	}
?>
<div _table style="--columns: repeat(2, minmax(0, max-content));">
	<div>
		<div></div>
		<div _title centered_><?= D['title_template']; ?></div>
	</div>
	<? if(!empty($object)) { ?>
		<div>
			<div><?= D['string_i_see_as']; ?></div>
			<div>
				<select name="1">
					<option selected>-</option>
					<option>InfoStore</option>
					<option>Modern</option>
				</select>
			</div>
		</div>
		<? if($object->access_level_id > 3) { ?>
			<div>
				<div><?= D['string_everyone_see_as']; ?></div>
				<div>
					<select name="2">
						<option selected>-</option>
						<option>InfoStore</option>
						<option>Modern</option>
					</select>
				</div>
			</div>
		<? } ?>
	<? } else { ?>
		<div>
			<div><?= D['string_i_see_everything_as']; ?></div>
			<div>
				<select name="3">
					<option selected>-</option>
					<option>InfoStore</option>
					<option>Modern</option>
				</select>
			</div>
		</div>
		<div>
			<div><?= D['string_everyone_see_everything_my_as']; ?></div>
			<div>
				<select name="4">
					<option selected>-</option>
					<option>InfoStore</option>
					<option>Modern</option>
				</select>
			</div>
		</div>
	<? } ?>
	<div>
		<div></div>
		<div>
			<button><?= D['button_save']; ?></button>
			<button onclick="history.back();"><?= D['button_cancel']; ?></button>
		</div>
	</div>
</div>