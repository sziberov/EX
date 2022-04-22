window.Page = class {
	static title(a) {
		document.title = Dictionary.getPageTitle(a);
	}

	static switch() {
		let hash = Hash.get(),
			hashPath = hash.path.join('/'),
			replaceGeneric = 'view',
			replaceList = ['_ID_', 'video', 'audio', 'images', 'texts', 'apps', 'games', 'about']

		Header.menu(hashPath);
		Header.expanded(hash.path[0]?.length === 0);
		Footer.menu(hashPath);
		Wrapper.load(true, () => {
			if(hashPath === '') {
				hash.path = ['']
			} else
			if(hashPath !== '' && Number.isInteger(+hashPath) || replaceList.includes(hashPath)) {
				hash.path = [replaceGeneric, hashPath]
			}
			this.render(hash);
			Wrapper.load(false);
		});
	}

	static composite(hash, inner) {
		let templates,
			title,
			model,
			actions;

		if(inner !== undefined) {
			templates = {
				section_navigation: () => {
					model = `
						<div _flex="h">
							<button icon_="to_first" disabled_></button>
							<!--<div fallback_>← Ctrl</div>-->
							<button icon_="to_back" disabled_></button>
							<b fallback_>1..24</b>
							<button icon_="to_forward"></button>
							<div fallback_>Ctrl →</div>
							<a href="?p=1">25..48</a>
							<button icon_="to_last"></button>
							<select fallback_>
								<option>4</option>
								<option>6</option>
								<option>8</option>
								<option>12</option>
								<option>16</option>
								<option selected>24</option>
								<option>32</option>
								<option>48</option>
								<option>64</option>
								<option>96</option>
								<option>128</option>
								<option>192</option>
								<option>256</option>
							</select>
							<a fallback_ href="#rss/_ID_">RSS</a>
						</div>
					`;
				},
				section_list: () => {
					model = `
						<ul _list>
							<li>
								<div _grid="v">
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
									<!--
									<div _grid="h">
										<button onclick="Hash.set('edit/_ID_')">Редактировать</button>
										<button>Удалить</button>
									</div>
									-->
								</div>
								<img _image src="/storage/example.jpg">
							</li>
						</ul>
					`;
				},
				section_cells: () => {
					model = `
						<ul _cells>
							<li>
								<img _image="poster" src="/storage/example.jpg">
								<div _grid="v stacked centered">
									<b><a href="#_ID_">Без названия</a></b>
									<div _user>
										<div _avatar="small" onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
											<img src="/app/image/background.png">
										</div>
										<a href="#user/_USER_TITLE_">DIES</a>
										<div>00:00, 01 января 2019</div>
									</div>
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
							</li>
						</ul>
					`;
				}
			}

			templates[inner]?.();

			return model ?? '';
		} else {
			templates = {
				'': () => {
					title = 'Файлы';
					model = `
						<div _grid="padded">
							<div _grid="v">
								<div _title="small">Крайние объекты</div>
								${ this.composite(hash, 'section_list') }
							</div>
							<div _grid="v">
								<div _grid="list">
									<div _title="small">Самое популярное</div>
									<a href="#video">Видео</a>
									<a href="#_ID_">Без названия</a>
									<b><a href="#most_popular">Полный список</a></b>
								</div>
								<div _grid="list">
									<div _title="small">Самое посещаемое</div>
									<a href="#video">Видео</a>
									<a href="#_ID_">Без названия</a>
									<b><a href="#most_visited">Полный список</a></b>
								</div>
								<div _grid="list">
									<div _title="small">Самое обсуждаемое</div>
									<a href="#video">Видео</a>
									<a href="#_ID_">Без названия</a>
									<b><a href="#most_commented">Полный список</a></b>
								</div>
								<div _grid="list">
									<div _title="small">Самое рекомендуемое</div>
									<a href="#video">Видео</a>
									<a href="#_ID_">Без названия</a>
									<b><a href="#most_recommended">Полный список</a></b>
								</div>
							</div>
						</div>
					`;
				},
				login: () => {
					title = 'Вход';
					model = `
						<div _table style="--columns: repeat(3, minmax(0, max-content));">
							<div>
								<div></div>
								<div _title centered_>Вход</div>
								<div></div>
							</div>
							<!--
							<div>
								<div></div>
								<div>
									<div _icon="error"></div>
									<div _title="small">Действие невозможно в анонимном режиме</div>
								</div>
								<div></div>
							</div>
							-->
							<div>
								<div>Логин</div>
								<div>
									<input name="login" type="text">
								</div>
								<div centered_>
									<button onclick="Hash.set('registration')">Регистрация</button>
								</div>
							</div>
							<div>
								<div>Пароль</div>
								<div>
									<input name="password" type="password">
								</div>
								<div centered_>
									<button onclick="Hash.set('password')">Восстановление пароля</button>
								</div>
							</div>
							<div>
								<div>Тип</div>
								<div _flex="v stacked left">
									<div _check name="permanent">
										<div></div>
										<div>Постоянный</div>
									</div>
									<div _check name="ip_independent">
										<div></div>
										<div>Независимый от IP</div>
									</div>
								</div>
								<div></div>
							</div>
							<div>
								<div></div>
								<div>
									<button primary_>Войти</button>
								</div>
								<div></div>
							</div>
						</div>
					`;
				},
				registration: () => {
					title = 'Регистрация';
					model = `
						<div _table style="--columns: repeat(3, minmax(0, max-content));">
							<div>
								<div></div>
								<div _title centered_>Регистрация</div>
								<div></div>
							</div>
							<div>
								<div>Логин</div>
								<div>
									<input name="login" type="text">
									<button>Проверить</button>
								</div>
								<div></div>
							</div>
							<div>
								<div>Пароль</div>
								<div>
									<input name="password" type="password">
								</div>
								<div></div>
							</div>
							<div>
								<div>Пароль (повтор)</div>
								<div>
									<input name="passwordRepeat" type="password">
								</div>
								<div></div>
							</div>
							<div>
								<div>Почта</div>
								<div>
									<input size_="big" name="email" type="text">
								</div>
								<div>
									<button>Проверить</button>
								</div>
							</div>
							<div>
								<div>Каптча</div>
								<div _flex="v left">
									<div _captcha="ABCdef123"></div>
									<input size_="big" name="captcha" type="text">
								</div>
								<div></div>
							</div>
							<div>
								<div>Соглашение</div>
								<div style="max-width: 256px;">Регистрируясь, вы даёте согласие не использовать ресурс для совершения действий, противоречащих законам РФ</div>
								<div></div>
							</div>
							<div>
								<div></div>
								<div>
									<button primary_>Зарегистрироваться</button>
								</div>
								<div></div>
							</div>
						</div>
					`;
				},
				password: () => {
					title = 'Восстановление пароля';
					model = `
						<div _table style="--columns: repeat(2, minmax(0, max-content));">
							<div>
								<div></div>
								<div _title centered_>Восстановление пароля</div>
							</div>
							<div>
								<div>Почта</div>
								<div>
									<input size_="big" name="email" type="text">
								</div>
							</div>
							<div>
								<div>Каптча</div>
								<div _flex="v left">
									<div _captcha="ABCdef123"></div>
									<input size_="big" name="captcha" type="text">
								</div>
							</div>
							<div>
								<div></div>
								<div>
									<button primary_>Запросить</button>
								</div>
							</div>
						</div>
					`;
				},
				user: () => {
					title = ['DIES', 'Страница пользователя']
					model = `
						<div _grid="h spaced">
							<div _grid="h">
								<div _avatar onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
									<img src="/storage/ex.svg">
								</div>
								<div _title>Страница пользователя <b>DIES</b></div>
								<input size_="medium" name="search" type="text">
								<button>Поиск</button>
							</div>
							<div _flex="h right wrap">
								<button onclick="Hash.set('friends_comments/_USER_TITLE_')">Комментарии друзей</button>
								<button onclick="Hash.set('friends_recommendations/_USER_TITLE_')">Рекомендации друзей</button>
								<button onclick="Hash.set('user_comments/_USER_TITLE_')">Комментарии пользователя</button>
								<button onclick="Hash.set('user_recommendations/_USER_TITLE_')">Рекомендации пользователя</button>
							</div>
						</div>
						<div _grid="v stacked">
							<b><a href="#friends">Друзья</a></b>
							<div _flex="h wrap">
								<div _user fallback_>
									<div _avatar="small" onclick="Hash.set('user/Offline')" title="Offline">
										<img src="/app/image/background.png">
									</div>
									<a href="#user/Offline">Offline</a>
								</div>
								<div _user fallback_>
									<div _avatar="small" onclick="Hash.set('user/Online')" title="Online">
										<img src="/app/image/background.png">
									</div>
									<u><a href="#user/Online">Online</a></u>
								</div>
								<div _user>
									<div _avatar="small" onclick="Hash.set('user/Offline_Mutual')" title="Offline_Mutual">
										<img src="/app/image/background.png">
									</div>
									<a href="#user/Offline_Mutual">Offline_Mutual</a>
								</div>
								<div _user>
									<div _avatar="small" onclick="Hash.set('user/Online_Mutual')" title="Online_Mutual">
										<img src="/app/image/background.png">
									</div>
									<u><a href="#user/Online_Mutual">Online_Mutual</a></u>
								</div>
							</div>
						</div>
						<div _grid="v stacked">
							<b><a href="#notifications">Уведомления</a></b>
							<div _flex="h wrap">
								<div _properties>
									<div>
										<div>Приглашений в группу</div>
										<div>1</div>
									</div>
									<div>
										<div>Друзей</div>
										<div>1</div>
									</div>
									<div>
										<div>Рекомендаций</div>
										<div>1</div>
									</div>
									<div>
										<div>Комментариев</div>
										<div>1</div>
									</div>
									<div>
										<div>Личных сообщений</div>
										<div>1</div>
									</div>
								</div>
							</div>
						</div>
						${ this.composite(hash, 'section_list') }
						<div _grid="h">
							<button onclick="Hash.set('edit/_USER_ID_?type=plain')">Создать</button>
							<button onclick="Hash.set('archive')" badge_="1">Архив</button>
							<button onclick="Hash.set('comments')">Комментарии</button>
							<button onclick="Hash.set('recommendations')">Рекомендации</button>
							<button onclick="Hash.set('avatars')" badge_="1">Аватары</button>
							<button onclick="Hash.set('templates')">Шаблоны</button>
							<button onclick="Hash.set('drafts')">Черновики</button>
							<button onclick="Hash.set('bookmarks')">Закладки</button>
							<button onclick="Hash.set('groups')" badge_="2 / 1" title="Чужие / Свои">Группы</button>
						</div>
						<div _grid="h">
							<button onclick="Hash.set('edit/_USER_ID_?type=comment')">Комментировать</button>
							<button onclick="Button.toggle(this, Request.recommend('_USER_ID_'))">Рекомендовать</button>
							<!--<button onclick="Hash.set('edit/_USER_ID_?type=pm')">Сообщить</button>-->
							<button onclick="Hash.set('pm/in')">Входящие</button>
							<button onclick="Hash.set('pm/out')">Исходящие</button>
						</div>
						<div _grid="h">
							<button onclick="Hash.set('object_stats')">Статистика по объектам</button>
							<!--<button onclick="Button.toggle(this, Request.friend('_USER_TITLE_'))">В друзья</button>-->
						</div>
						<div>&nbsp;</div>
						<div _grid="h">
							<!--<button onclick="Claim.toggle()">Жалоба</button>-->
							<button onclick="Hash.set('template/_USER_ID_')">Шаблон</button>
						</div>
						<div>&nbsp;</div>
						<div>Вы на <b>1</b>'ом месте по размеру оригинального контента</div>
						<div>&nbsp;</div>
					`;
					if(hash.path[1] !== 'DIES') {
					//	return templates[404]();
					}
				},
				user_comments: () => {
					title = ['DIES', 'Комментарии пользователя']
					model = `
						<div _grid="h spaced">
							<div _grid="h">
								<div _avatar onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
									<img src="/app/image/background.png">
								</div>
								<div _title>Комментарии пользователя <b>DIES</b></div>
							</div>
							<button onclick="Hash.set('user/_USER_TITLE_')">Страница пользователя</button>
						</div>
					`;
				},
				user_recommendations: () => {
					title = ['DIES', 'Рекомендации пользователя']
					model = `
						<div _grid="h spaced">
							<div _grid="h">
								<div _avatar onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
									<img src="/app/image/background.png">
								</div>
								<div _title>Рекомендации пользователя <b>DIES</b></div>
							</div>
							<button onclick="Hash.set('user/_USER_TITLE_')">Страница пользователя</button>
						</div>
					`;
				},
				friends_comments: () => {
					title = ['DIES', 'Комментарии друзей']
					model = `
						<div _grid="h spaced">
							<div _grid="h">
								<div _avatar onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
									<img src="/app/image/background.png">
								</div>
								<div _title>Комментарии друзей <b>DIES</b></div>
							</div>
							<button onclick="Hash.set('user/_USER_TITLE_')">Страница пользователя</button>
						</div>
					`;
				},
				friends_recommendations: () => {
					title = ['DIES', 'Рекомендации друзей']
					model = `
						<div _grid="h spaced">
							<div _grid="h">
								<div _avatar onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
									<img src="/app/image/background.png">
								</div>
								<div _title>Рекомендации друзей <b>DIES</b></div>
							</div>
							<button onclick="Hash.set('user/_USER_TITLE_')">Страница пользователя</button>
						</div>
					`;
				},
				archive: () => {
					title = 'Архив';
					model = `
						<div _title>Архив</div>
						${ this.composite(hash, 'section_navigation') }
						${ this.composite(hash, 'section_list') }
						${ this.composite(hash, 'section_navigation') }
						<div _grid="h">
							<button>Создать</button>
						</div>
					`;
				},
				comments: () => {
					title = 'Комментарии';
					model = `
						<div _title>Комментарии</div>
						<div _grid="h">
							<button>Удалить все</button>
						</div>
					`;
				},
				recommendations: () => {
					title = 'Рекомендации';
					model = `
						<div _title>Рекомендации</div>
						<div _grid="h">
							<button>Убрать все</button>
						</div>
					`;
				},
				avatars: () => {
					title = 'Аватары';
					model = `
						<div _title>Аватары</div>
						<div _grid="h">
							<button>Создать</button>
							<button>Убрать все</button>
						</div>
					`;
				},
				templates: () => {
					title = 'Шаблоны';
					model = `
						<div _title>Шаблоны</div>
						<div _grid="h">
							<button>Создать</button>
							<button>Убрать все</button>
						</div>
					`;
				},
				drafts: () => {
					title = 'Черновики';
					model = `
						<div _title>Черновики</div>
						<div _grid="h">
							<button>Удалить все</button>
							<button>Удалить пустые</button>
						</div>
					`;
				},
				bookmarks: () => {
					title = 'Закладки';
					model = `
						<div _title>Закладки</div>
						<div _grid="h">
							<button>Убрать все</button>
						</div>
					`;
				},
				groups: () => {
					title = 'Группы';
					model = `
						<div _title>Группы</div>
						<div _table="list" wide_ style="--columns: repeat(5, minmax(96px, auto));">
							<div header_>
								<div>Группа</div>
								<div>Доступ</div>
								<div>Дано</div>
								<div>Когда</div>
								<div>Действия</div>
							</div>
							<!--
							<div data-group-id="_GROUP_ID_">
								<div>
									<div _description="short straight">Все</div>
								</div>
								<div>
									<div>Полный</div>
									<div _flex="v stacked left">
										<div _check disabled_>
											<div></div>
											<div>Предпочтение высшего доступа</div>
										</div>
										<div _check disabled_>
											<div></div>
											<div>Возможность приглашать участников</div>
										</div>
									</div>
								</div>
								<div>
									<a _description="short straight" href="#user/_USER_TITLE_">system</a>
								</div>
								<div>00:00, 01 января 2019</div>
								<div></div>
							</div>
							-->
							<div data-group-id="_GROUP_ID_">
								<div>
									<a _description="short straight" href="#group/_GROUP_ID_">Group_DIES</a>
								</div>
								<div>
									<div>Полный, основная группа</div>
									<div _flex="v stacked left">
										<div _check="active" disabled_>
											<div></div>
											<div>Предпочтение высшего доступа</div>
										</div>
										<div _check="active" disabled_>
											<div></div>
											<div>Возможность приглашать участников</div>
										</div>
									</div>
								</div>
								<div>
									<a _description="short straight" href="#user/_USER_TITLE_">system</a>
								</div>
								<div>00:00, 01 января 2019</div>
								<div>
									<button>Пригласить</button>
								</div>
							</div>
							<div data-group-id="_GROUP_ID_">
								<div>
									<a _description="short straight" href="#group/_GROUP_ID_">Group_user</a>
								</div>
								<div>
									<div>Чтение + Комментирование</div>
									<div _flex="v stacked left">
										<div _check disabled_>
											<div></div>
											<div>Предпочтение высшего доступа</div>
										</div>
										<div _check="active" disabled_>
											<div></div>
											<div>Возможность приглашать участников</div>
										</div>
									</div>
								</div>
								<div>
									<a _description="short straight" href="#user/_USER_TITLE_">user</a>
								</div>
								<div>12:00, 01 января 2019</div>
								<div>
									<button>Пригласить</button>
									<button>Убрать</button>
								</div>
							</div>
							<div data-group-id="_GROUP_ID_">
								<div>
									<a _description="short straight" href="#group/_GROUP_ID_">Без названия</a>
								</div>
								<div>
									<div>Полный</div>
									<div _flex="v stacked left">
										<div _check="active" disabled_>
											<div></div>
											<div>Предпочтение высшего доступа</div>
										</div>
										<div _check="active" disabled_>
											<div></div>
											<div>Возможность приглашать участников</div>
										</div>
									</div>
								</div>
								<div>
									<a _description="short straight" href="#user/_USER_TITLE_">DIES</a>
								</div>
								<div>12:00, 01 января 2019</div>
								<div>
									<button>Пригласить</button>
									<button>Редактировать</button>
									<button>Удалить</button>
								</div>
							</div>
						</div>
						<div fallback_>Группы служат для управления доступом к объектам</div>
						<div _grid="h">
							<button>Создать</button>
						</div>
					`;
				},
				friends: () => {
					title = 'Друзья';
					model = `
						<div _title>Друзья</div>
						<div _table="list" wide_ style="--columns: repeat(5, minmax(96px, auto));">
							<div header_>
								<div>Пользователь</div>
								<div>Добавлен</div>
								<div>Добавил</div>
								<div>В сети</div>
								<div>Действия</div>
							</div>
							<div data-user-id="_USER_ID_">
								<div>
									<a _description="short straight" href="#user/_USER_TITLE_">Offline</a>
								</div>
								<div></div>
								<div>00:00, 01 января 2019</div>
								<div>00:00, 01 января 2019</div>
								<div>
									<button>Убрать</button>
								</div>
							</div>
							<div data-user-id="_USER_ID_">
								<div>
									<a _description="short straight" href="#user/_USER_TITLE_">Online</a>
								</div>
								<div></div>
								<div>00:00, 01 января 2019</div>
								<div>00:00, 01 января 2019</div>
								<div>
									<button>Убрать</button>
								</div>
							</div>
							<div data-user-id="_USER_ID_">
								<div>
									<a _description="short straight" href="#user/_USER_TITLE_">Offline_Mutual</a>
								</div>
								<div>00:00, 01 января 2019</div>
								<div>00:00, 01 января 2019</div>
								<div>00:00, 01 января 2019</div>
								<div>
									<button>Убрать</button>
								</div>
							</div>
							<div data-user-id="_USER_ID_">
								<div>
									<a _description="short straight" href="#user/_USER_TITLE_">Online_Mutual</a>
								</div>
								<div>00:00, 01 января 2019</div>
								<div>00:00, 01 января 2019</div>
								<div>00:00, 01 января 2019</div>
								<div>
									<button>Убрать</button>
								</div>
							</div>
							<div data-user-id>
								<div>
									<input size_="big" type="text">
								</div>
								<div></div>
								<div></div>
								<div></div>
								<div>
									<button>Добавить</button>
								</div>
							</div>
						</div>
						<div _grid="h">
							<button>Убрать всех</button>
						</div>
					`;
				},
				notifications: () => {
					title = 'Уведомления';
					model = `
						<div _title>Уведомления</div>
						<div _table="list" wide_ style="--columns: repeat(3, minmax(96px, auto));">
							<div header_>
								<div>Событие</div>
								<div>Произошло</div>
								<div>Действия</div>
							</div>
							<div data-user-id="_USER_ID_">
								<div>
									<div _description>Пользователь <a href="#user/_USER_TITLE_">user</a> написал вам личное сообщение <a href="#view/_ID_">Без названия</a></div>
								</div>
								<div>00:04, 01 января 2019</div>
								<div>
									<button onclick="Hash.set('view/_ID_')">Смотреть</button>
									<button>Убрать</button>
								</div>
							</div>
							<div data-user-id="_USER_ID_">
								<div>
									<div _description>Пользователь <a href="#user/_USER_TITLE_">user</a> оставил комментарий к вашему объекту <a href="#view/_ID_">Без названия</a></div>
								</div>
								<div>00:03, 01 января 2019</div>
								<div>
									<button onclick="Hash.set('view_comments/_ID_')">Смотреть</button>
									<button>Убрать</button>
								</div>
							</div>
							<div data-user-id="_USER_ID_">
								<div>
									<div _description>Пользователь <a href="#user/_USER_TITLE_">user</a> порекомендовал ваш объект <a href="#view/_ID_">Без названия</a></div>
								</div>
								<div>00:02, 01 января 2019</div>
								<div>
									<button>Убрать</button>
								</div>
							</div>
							<div data-user-id="_USER_ID_">
								<div>
									<div _description>Пользователь <a href="#user/_USER_TITLE_">user</a> пригласил вас в группу User_user</div>
								</div>
								<div>00:01, 01 января 2019</div>
								<div>
									<button>Принять</button>
									<button>Убрать</button>
								</div>
							</div>
							<div data-user-id="_USER_ID_">
								<div>
									<div _description>Пользователь <a href="#user/_USER_TITLE_">Online</a> добавил вас в друзья</div>
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
					`;
				},
				settings: () => {
					title = 'Настройки';
					model = `
						<div _table style="--columns: repeat(3, minmax(0, max-content));">
							<div>
								<div></div>
								<div _title centered_>Настройки</div>
								<div></div>
							</div>
							<div>
								<div>Меню</div>
								<div>
									<div _check>
										<div></div>
										<div>Использовать <a href="#menu_items">персональное меню</a></div>
									</div>
								</div>
								<div></div>
							</div>
							<div>
								<div>Редактор</div>
								<div>
									<input name="editor" type="text" placeholder="Номер файла">
								</div>
								<div></div>
							</div>
							<div>
								<div>Аватар по умолчанию</div>
								<div>
									<select name="avatar">
										<option>Нет</option>
										<option selected>DIES</option>
									</select>
								</div>
								<div></div>
							</div>
							<div>
								<div>Шаблоны</div>
								<div _flex="v left">
									<button onclick="Hash.set('template')">Выбрать шаблон</button>
									<div _check>
										<div></div>
										<div>Выполнять чужой JavaScript</div>
									</div>
								</div>
								<div></div>
							</div>
							<div>
								<div>Уведомления</div>
								<div _flex="v stacked left">
									<div _check="active">
										<div></div>
										<div>Дружба</div>
									</div>
									<div _check="active">
										<div></div>
										<div>Рекомендации</div>
									</div>
									<div _check="active">
										<div></div>
										<div>Комментарии</div>
									</div>
									<div _check="active">
										<div></div>
										<div>Личные сообщения</div>
									</div>
								</div>
								<div></div>
							</div>
							<div>
								<div>Пароль</div>
								<div>
									<input name="password" type="password">
								</div>
								<div></div>
							</div>
							<div>
								<div>Пароль (повтор)</div>
								<div>
									<input name="passwordRepeat" type="password">
								</div>
								<div></div>
							</div>
							<div>
								<div>Почта</div>
								<div>
									<input size_="big" name="email" type="text">
								</div>
								<div>
									<button>Проверить</button>
								</div>
							</div>
							<div>
								<div></div>
								<div>
									<button onclick="Hash.set('view/_ID_')">Сохранить</button>
									<button onclick="Hash.set('view/_ID_')">Отменить</button>
								</div>
								<div></div>
							</div>
						</div>
					`;
				},
				menu_items: () => {
					title = 'Персональное меню';
					model = `
						<div _title>Персональное меню</div>
						<div _table="list" wide_ style="--columns: repeat(3, minmax(96px, 1fr));">
							<div header_>
								<div>Пункт</div>
								<div>URL</div>
								<div>Действия</div>
							</div>
							<div data-id="0">
								<div>
									<a _description="short straight" href="#_URL_">Видео</a>
								</div>
								<div>
									<div _description="short straight">video</div>
								</div>
								<div>
									<button>↓</button>
									<button>Убрать</button>
								</div>
							</div>
							<div data-id="1">
								<div>
									<a _description="short straight" href="#_URL_">Аудио</a>
								</div>
								<div>
									<div _description="short straight">audio</div>
								</div>
								<div>
									<button>↑</button>
									<button>↓</button>
									<button>Убрать</button>
								</div>
							</div>
							<div data-id="2">
								<div>
									<a _description="short straight" href="#_URL_">Изображения</a>
								</div>
								<div>
									<div _description="short straight">images</div>
								</div>
								<div>
									<button>↑</button>
									<button>Убрать</button>
								</div>
							</div>
							<div data-id>
								<div>
									<input size_="big" type="text" value="Объект">
								</div>
								<div>
									<input size_="big" type="text" value="123456">
								</div>
								<div>
									<button>Добавить</button>
								</div>
							</div>
						</div>
					`;
				},
				edit: () => {
					title = ['Без названия', 'Редактирование']
					model = `
						<!--
						<div>&nbsp;</div>
						<div _flex="h">
							<div _icon="error"></div>
							<div _title="small">Доступ ограничен из-за несоблюдения правил использования сервиса</div>
						</div>
						<div>&nbsp;</div>
						<div _grid="h">
							<button>Вернуться</button>
						</div>
						-->
						<div _flex="h wrap" _title="small">
							<!--
							<div fallback_>Создание статьи на странице пользователя</div>
							<div fallback_>Создание аватара</div>
							<div fallback_>Создание группы</div>
							<div fallback_>Включение в раздел</div>
							<div fallback_>Комментарий к</div>
							-->
							<a href="#view/_ID_">Видео</a>
						</div>
						<div _table style="--columns: minmax(96px, max-content) minmax(96px, auto) minmax(96px, max-content);">
							<div>
								<div></div>
								<div _title centered_>Редактирование</div>
								<div></div>
							</div>
							<!--
							<div>
								<div>Ключ доступа</div>
								<div _flex="v stacked left">
									<div>Номер <b>_ID_</b> или ссылка <b>_DOMAIN_/_ID_</b></div>
									<div fallback_>Сохраните эти данные, доступ к объекту возможен только по ним</div>
								</div>
								<div></div>
							</div>
							-->
							<div>
								<div>Название</div>
								<div>
									<input size_="max" name="title" type="text">
								</div>
								<div></div>
							</div>
							<div>
								<div>Описание</div>
								<div _flex="v left">
									<textarea name="description"></textarea>
									<div _grid="h">
										<button><b>Жирный</b></button>
										<button><i>Наклонный</i></button>
										<button><u>Подчёркнутый</u></button>
										<button><s>Зачёркнутый</s></button>
										<button>Ссылка</button>
										<button>Цвет</button>
										<button>Язык</button>
									</div>
								</div>
								<div></div>
							</div>
							<div>
								<div>Аватар</div>
								<div>
									<select>
										<option>Нет</option>
										<option selected>По умолчанию</option>
										<option>DIES</option>
									</select>
									<div _avatar onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
										<img src="/app/image/background.png">
									</div>
								</div>
								<div></div>
							</div>
							<div>
								<div>Доступ для &#34;Всех&#34;</div>
								<div>
									<select>
										<option>Нет</option>
										<option>Чтение</option>
										<option selected>Чтение + Комментирование</option>
										<option>Чтение + Комментирование + Включение</option>
										<option>Редактирование</option>
										<option>Полный</option>
									</select>
								</div>
								<div></div>
							</div>
							<div>
								<div></div>
								<div>
									<button onclick="Hash.set('view/_ID_')">Смотреть</button>
									<button onclick="Request.save('_ID_')">Сохранить</button>
									<button onclick="Request.delete('_ID_')">Удалить</button>
									<button onclick="Hash.set('edit_settings/_ID_')">Настройки</button>
									<button onclick="Hash.set('edit_access/_ID_')">Доступ</button>
								</div>
								<div></div>
							</div>
						</div>
						<div _table="list" wide_ style="--columns: repeat(4, minmax(96px, auto));">
							<!--
							<div header_>
								<div>Файл</div>
								<div>Предпросмотр</div>
								<div>Размер</div>
								<div>Действия</div>
							</div>
							-->
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
									<img _image src="/storage/example.jpg">
								</div>
								<div>2,558,625</div>
								<div>
									<button>✓</button>
									<button>↺</button>
									<button>↻</button>
									<button onclick="Hash.set('copy/_ID_/_FILE_ID_')">Копировать</button>
									<button>Удалить</button>
								</div>
							</div>
							<div data-file-id="_FILE_ID_">
								<div>
									<div _description="short straight">video.mp4</div>
								</div>
								<div upload_="finish">Загружено</div>
								<div>386,537,472</div>
								<div>
									<button onclick="Hash.set('copy/_ID_/_FILE_ID_')">Копировать</button>
									<button>Удалить</button>
								</div>
							</div>
						</div>
						<div _grid="h spaced">
							<a><u>Загрузить</u></a>
							<div fallback_>Для загрузки нескольких файлов, используйте Ctrl и Shift при выделении</div>
						</div>
						<div>&nbsp;</div>
						<div _table="list" wide_ style="--columns: repeat(3, minmax(96px, auto));">
							<div header_>
								<div>Пользователь</div>
								<div>Доступ</div>
								<div>Действия</div>
							</div>
							<div data-user-id="_GROUP_ID_">
								<div>
									<a _description="short straight" href="#user/_USER_TITLE_">DIES</a>
								</div>
								<div>
									<div>Полный</div>
									<div _flex="v stacked left">
										<div _check="active" disabled_>
											<div></div>
											<div>Предпочтение высшего доступа</div>
										</div>
										<div _check="active" disabled_>
											<div></div>
											<div>Возможность приглашать участников</div>
										</div>
									</div>
								</div>
								<div></div>
							</div>
							<div data-user-id="_GROUP_ID_">
								<div>
									<a _description="short straight" href="#user/_USER_TITLE_">UserIn</a>
								</div>
								<div>
									<select>
										<option>Чтение</option>
										<option>Чтение + Комментирование</option>
										<option>Чтение + Комментирование + Включение</option>
										<option selected>Редактирование</option>
										<option>Полный</option>
									</select>
									<div _flex="v stacked left">
										<div _check>
											<div></div>
											<div>Предпочтение высшего доступа</div>
										</div>
										<div _check="active">
											<div></div>
											<div>Возможность приглашать участников</div>
										</div>
									</div>
								</div>
								<div>
									<button>Убрать</button>
								</div>
							</div>
							<div data-user-id="_GROUP_ID_">
								<div>
									<input size_="big" type="text" value="UserOut">
								</div>
								<div>
									<select>
										<option>Чтение</option>
										<option selected>Чтение + Комментирование</option>
										<option>Чтение + Комментирование + Включение</option>
										<option>Редактирование</option>
										<option>Полный</option>
									</select>
									<div _flex="v stacked left">
										<div _check>
											<div></div>
											<div>Предпочтение высшего доступа</div>
										</div>
										<div _check>
											<div></div>
											<div>Возможность приглашать участников</div>
										</div>
									</div>
								</div>
								<div>
									<button>Добавить</button>
								</div>
							</div>
						</div>
						<div _grid="h spaced">
							<div></div>
							<div fallback_>Пользователь получит доступ только после принятия приглашения</div>
						</div>
					`;
				},
				edit_settings: () => {
					title = ['Без названия', 'Редактирование настроек']
					model = `
						<div _title="small">
							<a href="#view/_ID_">Без названия</a>
						</div>
						<div _table style="--columns: repeat(2, minmax(0, max-content));">
							<div>
								<div></div>
								<div _title centered_>Редактирование настроек</div>
							</div>
							<div>
								<div>Поиск</div>
								<div _flex="v stacked left">
									<div _check>
										<div></div>
										<div>Убирать из поисковой выдачи</div>
									</div>
									<div _check>
										<div></div>
										<div>Отображать строку поиска по включениям</div>
									</div>
								</div>
							</div>
							<div>
								<div>Алиас</div>
								<div>
									<input type="text">
									<button>Проверить</button>
								</div>
							</div>
							<div>
								<div>Происхождение</div>
								<div _flex="v stacked left">
									<div _check>
										<div></div>
										<div>Скрывать реферер по умолчанию</div>
									</div>
									<div _check>
										<div></div>
										<div>Скрывать автора и даты</div>
									</div>
								</div>
							</div>
							<div>
								<div>Включения</div>
								<div>
									<select>
										<option selected>Ячейками</option>
										<option>Списком</option>
									</select>
									<select>
										<option selected>По дате включения</option>
										<option>По дате редактирования</option>
										<option>По дате создания</option>
										<option>По названию</option>
									</select>
									<select>
										<option>4</option>
										<option>6</option>
										<option>8</option>
										<option>12</option>
										<option>16</option>
										<option selected>24</option>
										<option>32</option>
										<option>48</option>
										<option>64</option>
										<option>96</option>
										<option>128</option>
										<option>192</option>
										<option>256</option>
									</select>
								</div>
							</div>
							<div>
								<div>Файлы</div>
								<div>
									<div _check>
										<div></div>
										<div>Скрывать список файлов</div>
									</div>
								</div>
							</div>
							<div>
								<div>Ограничения</div>
								<div _flex="v stacked left">
									<div _check>
										<div></div>
										<div>Запретить включение извне закладок</div>
									</div>
									<div _check>
										<div></div>
										<div>Запретить обжалование</div>
									</div>
								</div>
							</div>
							<div>
								<div></div>
								<div>
									<button onclick="Hash.set('edit/_ID_')">Сохранить</button>
									<button onclick="Hash.set('edit/_ID_')">Отменить</button>
								</div>
							</div>
						</div>
					`;
				},
				edit_access: () => {
					title = ['Без названия', 'Редактирование доступа']
					model = `
						<div _title="small">
							<a href="#view/_ID_">Без названия</a>
						</div>
						<div _title>Редактирование доступа</div>
						<div _table="list" wide_ style="--columns: repeat(3, minmax(96px, auto));">
							<div header_>
								<div>Группа</div>
								<div>Доступ</div>
								<div>Действия</div>
							</div>
							<div data-group-id="_GROUP_ID_">
								<div>
									<div _description="short straight">Все</div>
								</div>
								<div>
									<select>
										<option>Нет</option>
										<option>Чтение</option>
										<option selected>Чтение + Комментирование</option>
										<option>Чтение + Комментирование + Включение</option>
										<option>Редактирование</option>
										<option>Полный</option>
									</select>
								</div>
								<div></div>
							</div>
							<div data-group-id="_GROUP_ID_">
								<div>
									<a _description="short straight" href="#group/_GROUP_ID_">Group_DIES</a>
								</div>
								<div>
									<select>
										<option>Нет</option>
										<option>Чтение</option>
										<option>Чтение + Комментирование</option>
										<option>Чтение + Комментирование + Включение</option>
										<option>Редактирование</option>
										<option selected>Полный</option>
									</select>
								</div>
								<div>
									<button>Убрать</button>
								</div>
							</div>
							<div data-group-id="_GROUP_ID_">
								<div>
									<select>
										<option selected>-</option>
										<option>Group_user</option>
									</select>
								</div>
								<div>
									<select>
										<option>Нет</option>
										<option>Чтение</option>
										<option selected>Чтение + Комментирование</option>
										<option>Чтение + Комментирование + Включение</option>
										<option>Редактирование</option>
										<option>Полный</option>
									</select>
								</div>
								<div>
									<button>Добавить</button>
								</div>
							</div>
						</div>
						<div _grid="h spaced">
							<div></div>
							<div fallback_="">Редактирование связи со своей основной группой может привести к потери доступа</div>
						</div>
					`;
				},
				view: () => {
					title = ['Без названия', 'Видео']
					model = `
						<!--
						<div _title="small" fallback_>Объект временного хранения #101</div>
						<div _title="small" fallback_>Черновик</div>
						-->
						<div _title="small">
							<a href="#view/_ID_">Видео</a>
						</div>
						<div _flex="h" fallback_>
							<button icon_="to_back" disabled_></button>
							<button icon_="to_random" disabled_></button>
							<button icon_="to_forward" disabled_></button>
						</div>
						<div _grid="h spaced">
							<div _grid="v">
								<div _title>Без названия</div>
								<div _user>
									<div _avatar="small" onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
										<img src="/app/image/background.png">
									</div>
									<a href="#user/_USER_TITLE_">DIES</a>
									<div>00:00, 01 января 2019</div>
									<div>12:00, 01 января 2019</div>
								</div>
								<div _description>
									Идейные соображения высшего порядка, а также постоянное информационно-пропагандистское обеспечение нашей деятельности способствует подготовки и реализации дальнейших направлений развития. Идейные соображения высшего порядка, а также начало повседневной работы по формированию позиции представляет собой интересный эксперимент проверки систем массового участия. Равным образом дальнейшее развитие различных форм деятельности влечет за собой процесс внедрения и модернизации дальнейших направлений развития. Равным образом дальнейшее развитие различных форм деятельности обеспечивает широкому кругу (специалистов) участие в формировании направлений прогрессивного развития. С другой стороны дальнейшее развитие различных форм деятельности влечет за собой процесс внедрения и модернизации соответствующий условий активизации. Равным образом сложившаяся структура организации влечет за собой процесс внедрения и модернизации направлений прогрессивного развития.
									<br><br>
									Товарищи! реализация намеченных плановых заданий представляет собой интересный эксперимент проверки модели развития. Повседневная практика показывает, что начало повседневной работы по формированию позиции требуют от нас анализа направлений прогрессивного развития. Не следует, однако забывать, что постоянный количественный рост и сфера нашей активности представляет собой интересный эксперимент проверки форм развития.
								</div>
							</div>
							<div _flex="v right">
								<img _image="poster" src="/storage/example.jpg">
								<div fallback_>PNG: 2560x1440</div>
							</div>
						</div>
						<div _grid="h">
							<select>
								<option selected>Ячейками</option>
								<option>Списком</option>
							</select>
							<select>
								<option selected>По дате включения</option>
								<option>По дате редактирования</option>
								<option>По дате создания</option>
								<option>По названию</option>
							</select>
							<input size_="medium" name="search" type="text">
							<button>Поиск</button>
						</div>
						${ this.composite(hash, 'section_navigation') }
						${ this.composite(hash, 'section_cells') }
						${ this.composite(hash, 'section_navigation') }
						<div _table="list" wide_ style="--columns: minmax(192px, auto) repeat(2, minmax(96px, max-content));">
							<div header_>
								<div>
									<div _grid="v stacked">
										<b>Файлы</b>
										<div _properties>
											<div>
												<div>Количество</div>
												<div>2</div>
											</div>
											<div>
												<div>Суммарный размер</div>
												<div>2,558,625</div>
											</div>
											<a>Файл-лист</a>
											<a>Плей-лист</a>
										</div>
									</div>
								</div>
								<div></div>
								<div>
									<button onclick="Viewer.toggleClose()">Отобразить просмотрщик</button>
								</div>
							</div>
							<div>
								<div>
									<div _icon="floppy"></div>
									<div _grid="v stacked">
										<div fallback_>1</div>
										<a href="#get/_FILE_ID_">background.png</a>
									</div>
								</div>
								<div>
									<img _image src="/storage/example.jpg" onclick="Viewer.toggleClose('image', '1');" title="background.png - Без названия - Видео">
								</div>
								<div _flex="v right" fallback_>
									<b>2,558,625</b>
									<div _flex="v right stacked">
										<div>00:00, 01 января 2019</div>
										<a href="#search?s=md5:c43f10467775456d14013d25fe708b1e">c43f10467775456d14013d25fe708b1e</a>
										<div>PNG: 2560x1440</div>
									</div>
									<div _grid="h">
										<button>Загрузить</button>
										<button onclick="Hash.set('copy/_ID_/_FILE_ID_')">Копировать</button>
									</div>
									<!--
									<div _grid="h">
										<button>Смотреть на карте</button>
									</div>
									-->
									<div>FS1</div>
								</div>
							</div>
							<div>
								<div>
									<div _icon="floppy"></div>
									<div _grid="v stacked">
										<div fallback_>2</div>
										<a href="#get/2">video.mp4</a>
									</div>
								</div>
								<div>
									<button onclick="Viewer.toggleClose('video', '2')" badge_="22:24">Играть</button>
								</div>
								<div _flex="v right" fallback_>
									<b>386,537,472</b>
									<div _flex="v right stacked">
										<div>12:00, 01 января 2019</div>
										<a href="#search?s=md5:d98035045cd7aa0fc1def68dd19c33e6">d98035045cd7aa0fc1def68dd19c33e6</a>
										<div>44:56, MPEG4: 640x480</div>
									</div>
									<!--<div important_>Файл недоступен</div>-->
									<div _grid="h">
										<button>Загрузить</button>
										<!--<button>Торрент</button>-->
										<button onclick="Hash.set('copy/_ID_/2')">Копировать</button>
									</div>
									<div>FS1</div>
								</div>
							</div>
						</div>
						<div _grid="h">
							<button onclick="Hash.set('edit?type=plain&parent_id=_ID_')">Создать статью в разделе</button>
							<button onclick="Hash.set('upload/_ID_')">Загрузить файлы в раздел</button>
							<button onclick="Hash.set('include/_ID_')">Включить из закладок</button>
						</div>
						<div _grid="h">
							<button onclick="Hash.set('view_comments/_ID_')" badge_="2">Комментарии</button>
							<button onclick="Button.toggle(this, Request.recommend('_ID_'))">Рекомендовать</button>
						</div>
						<div _grid="h">
							<button onclick="Button.toggle(this, Request.my('_ID_', 'bookmarks'))">В закладки</button>
							<button onclick="Hash.set('include_my/_ID_')">В моё</button>
						</div>
						<div _grid="h">
							<button onclick="Hash.set('edit/_ID_')">Редактировать</button>
							<button onclick="Request.delete('_ID_')">Удалить</button>
						</div>
						<div>&nbsp;</div>
						<div _grid="h">
							<button onclick="Claim.toggle()">Жалоба</button>
							<button onclick="Hash.set('template/_ID_')">Шаблон</button>
						</div>
						<div>&nbsp;</div>
						<div _viewer="closed">
							<div _grid="h spaced" __panel>
								<div __title>video.mp4</div>
								<div _grid="h stacked">
									<button icon_="minimize" onclick="Viewer.toggleMinimize()"></button>
									<button icon_="close" onclick="Viewer.toggleClose()"></button>
								</div>
							</div>
							<div __content>
								<img src="/storage/example.jpg">
								<video src="/storage/example.jpg" nocontrols>
							</div>
							<div _grid="h spaced" __panel>
								<div __progress>
									<div __playback></div>
								</div>
								<div _grid="h">
									<button icon_="play"></button>
									<div>0:00 / 0:00</div>
								</div>
								<div _grid="h">
									<button icon_="volume"></button>
									<button icon_="maximize"></button>
									<button icon_="back" disabled_></button>
									<button icon_="forward" disabled_></button>
								</div>
								<button icon_="resize"></button>
							</div>
						</div>
					`;
				},
				view_comments: () => {
					title = ['Без названия', 'Комментарии']
					model = `
						<!--
						<div _grid="v stacked">
							<div _title="small">
								<a href="#view_comments/_ID_">Без названия</a>
							</div>
							<div>...</div>
							<div _title="small">
								<a href="#view_comments/_ID_">Без названия</a>
							</div>
						</div>
						-->
						<div _grid="h spaced">
							<div _grid="v">
								<div _title>
									<a href="#_ID_">Без названия</a>
								</div>
								<div _user>
									<div _avatar="small" onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
										<img src="/app/image/background.png">
									</div>
									<a href="#user/_USER_TITLE_">DIES</a>
									<div>00:00, 01 января 2019</div>
									<div>12:00, 01 января 2019</div>
								</div>
								<div _description="short straight">
									Идейные соображения высшего порядка, а также постоянное информационно-пропагандистское обеспечение нашей деятельности способствует подготовки и реализации дальнейших направлений развития. Идейные соображения высшего порядка, а также начало повседневной работы по формированию позиции представляет собой интересный эксперимент проверки систем массового участия. Равным образом дальнейшее развитие различных форм деятельности влечет за собой процесс внедрения и модернизации дальнейших направлений развития. Равным образом дальнейшее развитие различных форм деятельности обеспечивает широкому кругу (специалистов) участие в формировании направлений прогрессивного развития. С другой стороны дальнейшее развитие различных форм деятельности влечет за собой процесс внедрения и модернизации соответствующий условий активизации. Равным образом сложившаяся структура организации влечет за собой процесс внедрения и модернизации направлений прогрессивного развития.
									<br><br>
									Товарищи! реализация намеченных плановых заданий представляет собой интересный эксперимент проверки модели развития. Повседневная практика показывает, что начало повседневной работы по формированию позиции требуют от нас анализа направлений прогрессивного развития. Не следует, однако забывать, что постоянный количественный рост и сфера нашей активности представляет собой интересный эксперимент проверки форм развития.
								</div>
								<div _properties>
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
							<img _image src="/storage/example.jpg">
						</div>
						<b>Комментарии</b>
						<div _grid="h" navigation_>
							<button disabled_>1</button>
						</div>
						<div _table="list" wide_ style="--columns: minmax(192px, auto);">
							<div>
								<div _flex="v left">
									<!--
									<div _flex="h">
										<div _icon="comment"></div>
									</div>
									-->
									<div _grid="v stacked">
										<b><a href="#_ID_">Без названия</a></b>
										<div _user>
											<div _avatar="small" onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
												<img src="/app/image/background.png">
											</div>
											<a href="#user/_USER_TITLE_">DIES</a>
											<div>00:00, 01 января 2019</div>
											<div>12:00, 01 января 2019</div>
										</div>
									</div>
									<div _description="short">
										Идейные соображения высшего порядка, а также постоянное информационно-пропагандистское обеспечение нашей деятельности способствует подготовки и реализации дальнейших направлений развития. Идейные соображения высшего порядка, а также начало повседневной работы по формированию позиции представляет собой интересный эксперимент проверки систем массового участия. Равным образом дальнейшее развитие различных форм деятельности влечет за собой процесс внедрения и модернизации дальнейших направлений развития. Равным образом дальнейшее развитие различных форм деятельности обеспечивает широкому кругу (специалистов) участие в формировании направлений прогрессивного развития. С другой стороны дальнейшее развитие различных форм деятельности влечет за собой процесс внедрения и модернизации соответствующий условий активизации. Равным образом сложившаяся структура организации влечет за собой процесс внедрения и модернизации направлений прогрессивного развития.
										<br><br>
										Товарищи! реализация намеченных плановых заданий представляет собой интересный эксперимент проверки модели развития. Повседневная практика показывает, что начало повседневной работы по формированию позиции требуют от нас анализа направлений прогрессивного развития. Не следует, однако забывать, что постоянный количественный рост и сфера нашей активности представляет собой интересный эксперимент проверки форм развития.
									</div>
									<div _grid="h">
										<button>Ответить</button>
									</div>
								</div>
							</div>
							<div>
								<div _flex="v left" style="padding-left: 48px;">
									<div _grid="v stacked">
										<b><a href="#_ID_">Без названия</a></b>
										<div _user>
											<div _avatar="small" onclick="Hash.set('user/_USER_TITLE_')" title="DIES">
												<img src="/app/image/background.png">
											</div>
											<a href="#user/_USER_TITLE_">DIES</a>
											<div>00:00, 01 января 2019</div>
											<div>12:00, 01 января 2019</div>
										</div>
									</div>
									<div _description="short">
										Идейные соображения высшего порядка, а также постоянное информационно-пропагандистское обеспечение нашей деятельности способствует подготовки и реализации дальнейших направлений развития. Идейные соображения высшего порядка, а также начало повседневной работы по формированию позиции представляет собой интересный эксперимент проверки систем массового участия. Равным образом дальнейшее развитие различных форм деятельности влечет за собой процесс внедрения и модернизации дальнейших направлений развития. Равным образом дальнейшее развитие различных форм деятельности обеспечивает широкому кругу (специалистов) участие в формировании направлений прогрессивного развития. С другой стороны дальнейшее развитие различных форм деятельности влечет за собой процесс внедрения и модернизации соответствующий условий активизации. Равным образом сложившаяся структура организации влечет за собой процесс внедрения и модернизации направлений прогрессивного развития.
										<br><br>
										Товарищи! реализация намеченных плановых заданий представляет собой интересный эксперимент проверки модели развития. Повседневная практика показывает, что начало повседневной работы по формированию позиции требуют от нас анализа направлений прогрессивного развития. Не следует, однако забывать, что постоянный количественный рост и сфера нашей активности представляет собой интересный эксперимент проверки форм развития.
									</div>
									<div _grid="h">
										<button>Ответить</button>
										<button>Редактировать</button>
										<button>Удалить</button>
									</div>
								</div>
							</div>
							<div>
								<div _flex="v left" style="padding-left: 96px;">
									<div>Нет доступа к объекту 3</div>
									<div _properties>
										<div>
											<div>Комментариев</div>
											<div>1</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div _grid="h" navigation_>
							<button disabled_>1</button>
						</div>
						<div>&nbsp;</div>
						<div _grid="h">
							<button onclick="Hash.set('edit?type=comment&parent_id=_ID_')">Комментировать</button>
						</div>
						<div>&nbsp;</div>
					`;
				},
				copy: () => {
					title = 'Копирование файла';
					model = `
						<div _table style="--columns: repeat(2, minmax(0, max-content));">
							<div>
								<div></div>
								<div _title centered_>Копирование файла</div>
							</div>
							<div>
								<div>Из</div>
								<div>_ID_</div>
							</div>
							<div>
								<div>Название</div>
								<div>background.png</div>
							</div>
							<div>
								<div>Действие</div>
								<div _flex="v stacked left">
									<div _radio="active" name="action_id" value="1">
										<div></div>
										<div>Копирование</div>
									</div>
									<div _radio name="action_id" value="2">
										<div></div>
										<div>Перемещение</div>
									</div>
								</div>
							</div>
							<div>
								<div>В</div>
								<div>
									<input name="to" type="text" value="_ID_">
								</div>
							</div>
							<div>
								<div>Название</div>
								<div>
									<input size_="big" name="title" type="text" value="background.png">
								</div>
							</div>
							<div>
								<div></div>
								<div>
									<button onclick="Hash.set('view/_ID_')">Выполнить</button>
									<button onclick="Hash.set('view/_ID_')">Отменить</button>
								</div>
							</div>
						</div>
					`;
				},
				upload: () => {
					title = ['Без названия', 'Загрузка файлов в раздел']
					model = `
						<div _title="small">
							<a href="#view/_ID_">Без названия</a>
						</div>
						<div _title>Загрузка файлов в раздел</div>
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
									<img _image src="/storage/example.jpg">
								</div>
								<div>2,558,625</div>
								<div>
									<button>↺</button>
									<button>↻</button>
									<button onclick="Hash.set('view/_ID_')">Смотреть</button>
									<button onclick="Hash.set('copy/_ID_/_FILE_ID_')">Копировать</button>
									<button>Удалить</button>
								</div>
							</div>
							<div data-file-id="_FILE_ID_">
								<div>
									<div _description="short straight">video.mp4</div>
								</div>
								<div upload_="finish">Загружено</div>
								<div>386,537,472</div>
								<div>
									<button onclick="Hash.set('view/_ID_')">Смотреть</button>
									<button onclick="Hash.set('copy/_ID_/_FILE_ID_')">Копировать</button>
									<button>Удалить</button>
								</div>
							</div>
						</div>
						<div _grid="h spaced">
							<u><a>Загрузить</a></u>
							<div fallback_>Каждый файл отобразится в собственной статье</div>
						</div>
					`;
				},
				include: () => {
					title = ['Без названия', 'Включение в раздел из закладок']
					model = `
						<!--
						<div _title="small">
							<a href="#user/_USER_TITLE_">DIES</a>
						</div>
						-->
						<div _title="small">
							<a href="#view/_ID_">Без названия</a>
						</div>
						<div _title>Включение в раздел из закладок</div>
						${ this.composite(hash, 'section_list') }
						<div _grid="h">
							<button onclick="Hash.set('view/_ID_')">Сохранить</button>
							<button onclick="Hash.set('view/_ID_')">Отменить</button>
							<button wide_>Выбрать все</button>
							<button wide_>Очистить все</button>
						</div>
					`;
					actions = () => {
						console.log('1');
					}
				},
				include_my: () => {
					title = ['Без названия', 'Включение объекта в мои разделы']
					model = `
						<div _title="small">
							<a href="#view/_ID_">Без названия</a>
						</div>
						<div _title>Включение объекта в мои разделы</div>
						<div _grid="v stacked">
							<div _check>
								<div></div>
								<div>Страница</div>
							</div>
							<div _check>
								<div></div>
								<div>Рекомендации</div>
							</div>
							<div _check>
								<div></div>
								<div>Аватары</div>
							</div>
							<div _check>
								<div></div>
								<div>Шаблоны</div>
							</div>
							<div _check>
								<div></div>
								<div>Закладки</div>
							</div>
						</div>
						<div _grid="h">
							<button onclick="Hash.set('view/_ID_')">Сохранить</button>
							<button onclick="Hash.set('view/_ID_')">Отменить</button>
						</div>
						<!--
						<div _table="" style="--columns: repeat(2, minmax(0, max-content));">
							<div>
								<div></div>
								<div _title="" centered_="">Включение объекта в мои разделы</div>
							</div>
							<div>
								<div>Объект</div>
								<div>Без названия</div>
							</div>
							<div>
								<div>Действия</div>
								<div _flex="v stacked left">
									<div name="action_id" value="1" _check="">
										<div></div>
										<div>Добавить в быстрый доступ для гостей</div>
									</div>
									<div name="action_id" value="2" _check="">
										<div></div>
										<div>Рекомендовать объект</div>
									</div>
								<div name="action_id" value="2" _check="active">
										<div></div>
										<div>Использовать постер объекта как аватар</div>
									</div><div name="action_id" value="2" _check="">
										<div></div>
										<div>Использовать файлы объекта как ресурсы шаблона</div>
									</div><div name="action_id" value="2" _check="">
										<div></div>
										<div>Запомнить для последующего включения в разделы</div>
									</div></div>
							</div>
							<div>
								<div></div>
								<div>
									<button onclick="Hash.set('view/_ID_')">Выполнить</button>
									<button onclick="Hash.set('view/_ID_')">Отменить</button>
								</div>
							</div>
						</div>
						-->
					`;
				},
				template: () => {
					title = ['Без названия', 'Выбор шаблона']
					model = `
						<div _title="small" data-type-id="3">
							<a href="#view/_ID_">Без названия</a>
						</div>
						<div _table style="--columns: repeat(2, minmax(0, max-content));">
							<div>
								<div></div>
								<div _title centered_>Выбор шаблона</div>
							</div>
							<div data-type-id="2">
								<div>Я вижу всё как</div>
								<div>
									<select name="3">
										<option selected>Обычно</option>
										<option>InfoStore</option>
										<option>Modern</option>
									</select>
								</div>
							</div>
							<div data-type-id="2">
								<div>Все видят всё моё как</div>
								<div>
									<select name="4">
										<option selected>Обычно</option>
										<option>InfoStore</option>
										<option>Modern</option>
									</select>
								</div>
							</div>
							<div data-type-id="3">
								<div>Я вижу как</div>
								<div>
									<select name="1">
										<option selected>Обычно</option>
										<option>InfoStore</option>
										<option>Modern</option>
									</select>
								</div>
							</div>
							<div data-type-id="3">
								<div>Все видят как</div>
								<div>
									<select name="2">
										<option selected>Обычно</option>
										<option>InfoStore</option>
										<option>Modern</option>
									</select>
								</div>
							</div>
							<div>
								<div></div>
								<div>
									<button onclick="Hash.set('view/_ID_')">Сохранить</button>
									<button onclick="Hash.set('view/_ID_')">Отменить</button>
								</div>
							</div>
						</div>
					`;
					actions = () => {
						if(hash.path[1] === undefined) {
							$('[data-type-id="3"]').hide();
						} else {
							$('[data-type-id="2"]').hide();
						}
					}
				},
				search: () => {
					title = 'Поиск';
					model = `
						<div _title>Поиск</div>
						<div _grid="h">
							<input size_="large" name="search" type="text">
							<button primary_>Искать</button>
						</div>
						<div _properties>
							<div>
								<div>Пользователь</div>
								<a>DIES</a>
							</div>
							<div>
								<div>Раздел</div>
								<a>Видео</a>
							</div>
							<!--<div>Ничего не найдено</div>-->
						</div>
						${ this.composite(hash, 'section_navigation') }
						${ this.composite(hash, 'section_list') }
						${ this.composite(hash, 'section_navigation') }
						<div>
							<button Hash.set('search')>Искать везде</button>
						</div>
					`;
				},
				object_stats: () => {
					title = 'Статистика по объектам';
					model = `
						<div _title>Статистика по объектам</div>
						<div _table="list" wide_ style="--columns: repeat(5, minmax(96px, auto));">
							<div header_>
								<div>Объект</div>
								<div></div>
								<div>Хитов</div>
								<div>Хостов</div>
								<div>Гостей</div>
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
								<div>64</div>
								<div>48</div>
								<div>24</div>
							</div>
						</div>
					`;
				},
				user_stats: () => {
					title = 'Статистика по пользователям';
					model = `
						<div _title>Статистика по пользователям</div>
						<div _table="list" wide_ style="--columns: repeat(12, minmax(48px, auto));">
							<div header_>
								<div>Пользователь</div>
								<div>Друзей</div>
								<div>Объектов</div>
								<div>Оригиналов</div>
								<div>Суммарный размер</div>
								<div>Дубликатов</div>
								<div>Суммарный размер</div>
								<div>Зарегистрирован</div>
								<div>В сети</div>
								<div>Хитов</div>
								<div>Хостов</div>
								<div>Гостей</div>
							</div>
							<div>
								<div>
									<a _description="short straight" href="#user/_USER_TITLE_">User</a>
								</div>
								<div>768</div>
								<div>12</div>
								<div>11,569</div>
								<div>3,953,353,350,665</div>
								<div>1,093</div>
								<div>367,941,329,053</div>
								<div>00:00, 01 января 2019</div>
								<div>12:00, 01 января 2019</div>
								<div>64</div>
								<div>48</div>
								<div>24</div>
							</div>
						</div>
					`;
				},
				fs_stats: () => {
					title = 'Статистика по файловым серверам';
					model = `
						<div _title>Статистика по файловым серверам</div>
					`;
				},
				contact: () => {
					title = 'Контактная информация';
					model = `
						<div _title>Контактная информация</div>
						<div>Нарушения и вопросы правообладания - <a>abuse@ex.ua</a></div>
						<div>Сотрудничество - <a>info@ex.ua</a></div>
						<div>Поддержка пользователей и другие вопросы - <a>support@ex.ua</a></div>
					`;
				},
				copyright: () => {
					title = 'ПОЛИТИКА ЗАКРЫТИЯ КОНТЕНТА';
					model = `
						<div _title>ПОЛИТИКА ЗАКРЫТИЯ КОНТЕНТА</div>
					`;
				},
				404: () => {
					title = '';
					model = `
						<div _flex="v left">
							<div>&nbsp;</div>
							<div _flex="h">
								<div _icon="error"></div>
								<div _title="small">Страница не найдена</div>
							</div>
							<div>&nbsp;</div>
							<button primary_ onclick="Hash.set('')">На титул</button>
						</div>
					`;
				}
			}

			if(templates.hasOwnProperty(hash.path[0])) {
				templates[hash.path[0]]();
			} else {
				templates[404]();
			}

			return {
				title: title,
				model: model,
				actions: actions
			}
		}
	}

	static async templateLoad() {
		let document_ = document.open('text/html'),
			namespace = {
				page: Hash.get().path[0],
				session_active: true,
				user_notifications_count: 5,
				user_title: 'DIES',
				avatar_url: '/storage/ex.svg',
				avatar_title: 'EX',
				title: Dictionary.getPageTitle(Dictionary.getString('title_files')),
				description: Dictionary.getString('string_app_description'),
				navigation_index: 0,
				navigation_per: 24,
				...Dictionary.getString()
			},
			main = await Template.getModule(undefined, 'main', namespace);

		document_.write(main.documentElement.innerHTML);
		document_.close();
	}

	static render(hash) {
		let data = this.composite(hash);

		this.title(data.title);
		Content.clear();
		Content.add(data.model);
		data.actions?.();
	}
}