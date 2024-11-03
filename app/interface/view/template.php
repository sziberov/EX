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
			<a _button href="/<?= !empty($object) ? $object->id : 'settings'; ?>"><?= D['button_cancel']; ?></a>
		</div>
	</div>
</div>
<!--
<div _table="list" small_ style="--columns: repeat(6, minmax(96px, auto));">
	<div header_>
		<div>Шаблон</div>
		<div></div>
		<div>Тип</div>
		<div>Кто</div>
		<div>Когда</div>
		<div>Действия</div>
	</div>
	<div>
		<div _flex="v left">
			<div _grid="v stacked">
				<b><a href="#_ID_">Без названия</a></b>
				<div _user>
					<div _avatar="small" onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
						<img src="/app/image/background.png">
					</div>
					<a href="#user/_USER_TITLE_">DIES</a>
					<div>00:00, 01 января 2019</div>
				</div>
			</div>
			<div _description="short">
				Идейные соображения высшего порядка, а также постоянное информационно-пропагандистское обеспечение нашей деятельности способствует подготовки и реализации дальнейших направлений развития. Идейные соображения высшего порядка, а также начало повседневной работы по формированию позиции представляет собой интересный эксперимент проверки систем массового участия. Равным образом дальнейшее развитие различных форм деятельности влечет за собой процесс внедрения и модернизации дальнейших направлений развития. Равным образом дальнейшее развитие различных форм деятельности обеспечивает широкому кругу (специалистов) участие в формировании направлений прогрессивного развития. С другой стороны дальнейшее развитие различных форм деятельности влечет за собой процесс внедрения и модернизации соответствующий условий активизации. Равным образом сложившаяся структура организации влечет за собой процесс внедрения и модернизации направлений прогрессивного развития.
				<br><br>
				Товарищи! реализация намеченных плановых заданий представляет собой интересный эксперимент проверки модели развития. Повседневная практика показывает, что начало повседневной работы по формированию позиции требуют от нас анализа направлений прогрессивного развития. Не следует, однако забывать, что постоянный количественный рост и сфера нашей активности представляет собой интересный эксперимент проверки форм развития.
			</div>
			<div _properties>
				<div>
					<div>Включений</div>
					<div>1</div>
				</div>
				<div>
					<div>Файлов</div>
					<div>2</div>
				</div>
				<div>
					<div>Комментариев</div>
					<div>2</div>
				</div>
			</div>
		</div>
		<div>
			<img _image src="/storage/example.jpg">
		</div>
		<div><?= D['string_i_see_as']; ?></div>
		<div>
			<a _description="short straight" href="#user/_USER_TITLE_">admin</a>
		</div>
		<div>00:00, 01 января 2019</div>
		<div>
			<button>Убрать</button>
		</div>
	</div>
</div>
<div _grid="h">
	<button>Убрать все</button>
</div>
-->