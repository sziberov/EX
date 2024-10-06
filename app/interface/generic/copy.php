<?
	try {
		$upload = new Upload($path[1] ?? null);
	} catch(Exception $e) {
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if(!Session::set()) {
	//	return include 'generic/login.php';
	}

	$object = $upload->object;

	if($object->access_level_id < 1) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$page_title = $upload->title.' - '.D['title_copy'];
?>
<div _table style="--columns: repeat(2, minmax(0, max-content));">
	<div>
		<div></div>
		<div _title centered_><?= D['title_copy']; ?></div>
	</div>
	<div>
		<div>Из</div>
		<div><?= e($object->title); ?></div>
	</div>
	<div>
		<div><?= D['string_title']; ?></div>
		<div><?= e($upload->title); ?></div>
	</div>
	<div>
		<div>Действие</div>
		<div _flex="v stacked left">
			<label _radio>
				<input name="action_id" value="0" type="radio" checked>
				<div></div>
				<div>Копирование</div>
			</label>
			<label _radio>
				<input name="action_id" value="1" type="radio">
				<div></div>
				<div>Перемещение</div>
			</label>
		</div>
	</div>
	<div>
		<div>В</div>
		<div>
			<input name="to" type="text" placeholder="<?= D['string_object_id']; ?>">
		</div>
	</div>
	<div>
		<div><?= D['string_title']; ?></div>
		<div>
			<input size_="large" name="title" type="text" value="<?= e($upload->title); ?>">
		</div>
	</div>
	<div>
		<div></div>
		<div>
			<button onclick="Hash.set('view/_ID_')">Выполнить</button>
			<a _button href="/<?= $object->id; ?>"><?= D['button_cancel']; ?></a>
		</div>
	</div>
</div>