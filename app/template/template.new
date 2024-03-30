<condition>
	<if>[hash.path.1]!=</if>
	<then>
		<module variables="...=@/app/api/object.php?id=[hash.path.1]">
			<title>[title] - [title_template]</title>
			<div _title="small">
				<a href="#view/[hash.path.1]">[title]</a>
			</div>
		</module>
	</then>
	<else>
		<title>[title_template]</title>
	</else>
</condition>
<div _table style="--columns: repeat(2, minmax(0, max-content));">
	<div>
		<div></div>
		<div _title centered_>[title_template]</div>
	</div>
	<!--
	<div>
		<div></div>
		<div>
			<div _icon="error"></div>
			<div _title="small">Не указан шаблон</div>
		</div>
	</div>
	-->
	<div>
		<div>Шаблон</div>
		<div>
			<select name="type">
				<option selected>-</option>
				<option>InfoStore</option>
				<option>Modern</option>
			</select>
		</div>
	</div>
	<div>
		<div>Тип</div>
		<div>
			<condition>
				<if>[hash.path.1]!=</if>
				<then>
					<select name="type">
						<option selected>[string_i_see_as]</option>
						<option>[string_everyone_see_as]</option>
					</select>
				</then>
				<else>
					<select name="type">
						<option>[string_i_see_everything_as]</option>
						<option>[string_everyone_see_everything_my_as]</option>
					</select>
				</else>
			</condition>
		</div>
	</div>
	<div>
		<div></div>
		<div>
			<button onclick="Hash.set('view/[hash.path.1]')">Добавить</button>
			<condition>
				<if>[hash.path.1]!=</if>
				<then>
					<button onclick="Hash.set('view/[hash.path.1]')">[button_cancel]</button>
				</then>
				<else>
					<button onclick="Hash.set('settings')">[button_cancel]</button>
				</else>
			</condition>
		</div>
	</div>
</div>
<div _table="list" wide_ style="--columns: repeat(6, minmax(96px, auto));">
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
				<!--
				<div>
					<div>Включений</div>
					<div>1</div>
				</div>
				-->
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
		<div>[string_i_see_as]</div>
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