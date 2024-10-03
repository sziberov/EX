<?
	try {
		$object = new Object_($path[1] ?? null);
	} catch(Exception $e) {
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if(!Session::set()) {
		return include 'generic/login.php';
	}

	if($object->access_level_id < 3) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}

	$user = new Object_(Session::getUserID());
?>
<title><?= dictionary_getPageTitle($object->title.' - '.D['title_upload']); ?></title>
<?
	$template = new Template('referrer');
	$template->object = $object;
	$template->render(true);
?>
<div _table style="--columns: minmax(96px, max-content) minmax(96px, max-content)">
	<div>
		<div></div>
		<div _title centered_><?= D['title_upload']; ?></div>
	</div>
	<div>
		<div><?= D['string_avatar']; ?></div>
		<!--
		<div>Для выбора аватара его необходимо создать <a href="#avatars">здесь</a>.</div>
		-->
		<div>
			<select>
				<option>Нет</option>
				<option selected>По умолчанию</option>
				<option>DIES</option>
			</select>
			<div _avatar onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
				<img src="/get/4">
			</div>
		</div>
	</div>
	<div>
		<div></div>
		<div>Для каждого загруженного файла будет создана отдельная статья в разделе</div>
	</div>
	<div>
		<div></div>
		<div>
			<button onclick="Hash.set('view/_ID_')"><?= D['button_view']; ?></button>
			<button onclick="Hash.set('edit/_ID_')"><?= D['button_return']; ?></button>
		</div>
	</div>
</div>
<div _table="list" wide_ style="--columns: repeat(4, minmax(96px, auto));">
	<div header_>
		<div>Файлы</div>
		<div></div>
		<div></div>
		<div></div>
	</div>
	<div data-file-id="_FILE_ID_">
		<div>
			<div _description="short straight">background.png</div>
		</div>
		<div>
			<img _image src="/get/3">
		</div>
		<div>2,558,625</div>
		<div>
			<button>↺</button>
			<button>↻</button>
			<button onclick="Hash.set('copy/_ID_/_FILE_ID_')">Копировать</button>
		</div>
	</div>
	<div data-file-id="_FILE_ID_">
		<div>
			<div _description="short straight">video.mp4</div>
		</div>
		<div upload_="finished">Загружено</div>
		<div>386,537,472</div>
		<div>
			<button onclick="Hash.set('copy/_ID_/_FILE_ID_')">Копировать</button>
		</div>
	</div>
</div>
<div _grid="h spaced">
	<a><u>Загрузить</u></a>
	<div fallback_>fs1</div>
	<div fallback_>Для загрузки нескольких файлов используйте Ctrl и Shift при выделении</div>
</div>