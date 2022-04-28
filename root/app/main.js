$(() => {
	window.Hash = class {
		static current;

		static load() {
			let hash = location.hash.substr(1),
				hashParts = hash.split(/\?(.+)/),
				hashPath = hashParts[0],
				hashParameters = hashParts[1],
				path = hashPath.split(/[\\/]/).filter(v => v !== ''),
				parameters = {}

			for(let hashParameter of hashParameters?.split('&') ?? []) {
				let entry = hashParameter.split('=');

				parameters[entry[0]] = entry[1]
			}

			this.current = {
				path: path,
				parameters: parameters
			}

			Page.switch();
		}

		static get() {
			return this.current;
		}

		static set(a, b) {
			if(b?.target.nodeName === 'A') {
				return;
			}

			location.hash = a;
			if(a === '') {
				history.pushState('', document.title, window.location.pathname+window.location.search);
			}
		}
	}

	window.Header = class {
		static selector = 'header';

		static expanded(a) {
			return {
				true: () => $(this.selector).attr('expanded_', ''),
				false: () => $(this.selector).removeAttr('expanded_')
			}[a]();
		}

		static menu(a) {
			$(this.selector+` [__menu] a`).removeAttr('current_');
			$(this.selector+` [__menu] a[href="#${a}"]`).attr('current_', '');
		}

		static get(a) {
			$(a).attr('disabled_', '').closest(this.selector+' [__get]').attr('__get', 'wrong');
			setTimeout(() =>
				$(a).removeAttr('disabled_').closest(this.selector+' [__get]').attr('__get', '')
			, 500);
		}

		static getInput(a) {
			a.value = a.value.replaceAll(/[^0-9]/g, '');
		}
	}

	window.Content = class {
		static selector = 'content';

		static set(elements) {
			$(this.selector).empty().append(elements);
		}
	}

	window.Footer = class {
		static selector = 'footer';

		static menu(a) {
			$(this.selector+` a`).removeAttr('current_');
			$(this.selector+` a[href="#${a}"]`).attr('current_', '');
		}
	}

	window.Request = class {
		static recommend() {}
		static my() {}
		static bookmarks() {}
		static friend() {}
	}

	window.Button = class {
		static toggle(a, b) {
			a.toggleAttribute('toggle_');
		}

		static checkClick(e) {
			let check = $(e.currentTarget);

			if(check.attr('_check') !== 'active') {
				check.attr('_check', 'active');
			} else {
				check.attr('_check', '');
			}
		}

		static radioClick(e) {
			let radio = $(e.currentTarget),
				siblings = radio.siblings('[_radio]');

			radio.attr('_radio', 'active');
			siblings.attr('_radio', '');
		}
	}

	window.Viewer = class {
		static attribute = '_viewer';
		static fileID;

		static toggleMinimize() {
			let state = $('['+this.attribute+']').attr(this.attribute).split(' ').filter(v => v !== '');

			if(state.includes('minimized')) {
				state = state.filter(v => v !== 'minimized');
			} else {
				state.push('minimized');
			}

			$('['+this.attribute+']').attr(this.attribute, state.join(' '));
		}

		static toggleClose(type, fileID) {
			let state = $('['+this.attribute+']').attr(this.attribute).split(' ').filter(v => v !== ''),
				closed = state.includes('closed'),
				fileIDChanged = fileID !== undefined && fileID !== this.fileID;

			if(closed || fileIDChanged) {
				if(closed) {
					state = state.filter(v => v !== 'closed' && v !== 'minimized');
				}
				if(type !== undefined) {
					state = state.filter(v => v !== 'image' && v !== 'video');
					state.push(type);
				}
				if(fileIDChanged) {
					this.fileID = fileID;
				}
			} else {
				state.push('closed');
			}

			$('['+this.attribute+']').attr(this.attribute, state.join(' '));
		}
	}

	window.Page = class {
		static headElements = []
		static firstLoad = true;

		static setTitle(a) {
			document.title = Dictionary.getPageTitle(a);
		}

		static setHead(elements) {
			for(let element of this.headElements) {
				$('head').remove(element);
			}
			$('head').append(elements);

			this.headElements = elements;
		}

		static setBody(elements) {
			$('body').html('').append(elements);
		}

		static setLoading(state) {
			return {
				true: () => {
					this.setTitle('. . .');
					$('body').attr('loading_', '')
				},
				false: () => $('body').removeAttr('loading_')
			}[state]();
		}

		static decodeURI(URI) {
			return decodeURI(URI ?? '');
		}

		static parseBB(string) {
			return string?.replaceAll('[br]', '<br>');
		}

		static formatTime(time) {
			let date = new Date(Date.parse(time ?? '0')),
				months = [
					Dictionary.getString('string_january'),
					Dictionary.getString('string_february'),
					Dictionary.getString('string_march'),
					Dictionary.getString('string_april'),
					Dictionary.getString('string_may'),
					Dictionary.getString('string_june'),
					Dictionary.getString('string_july'),
					Dictionary.getString('string_august'),
					Dictionary.getString('string_september'),
					Dictionary.getString('string_october'),
					Dictionary.getString('string_november'),
					Dictionary.getString('string_december')
				]

			return date.getHours()+':'+('0'+date.getMinutes()).slice(-2)+', '+date.getDay()+' '+months[date.getMonth()]+' '+date.getFullYear();
		}

		static formatSize(size) {
			return size?.toLocaleString('en-US') ?? '';
		}

		static getNamespace(hash) {
			return {
				...Dictionary.getString(),
				hash: hash,
				decodeURI: this.decodeURI,
				parseBB: this.parseBB,
				formatTime: this.formatTime,
				formatSize: this.formatSize,
				stringify: JSON.stringify,
				plus: (a, b) => a*1+b*1,
				minus: (a, b) => a*1-b*1,
				if: (a, b, c) => Template.getComparisonsResult(a) ? b : c
			}
		}

		static updateEdges(hash) {
			let hashPath = hash.path.join('/');

			Header.menu(hashPath);
			Header.expanded((hash.path[0] ?? '').length === 0);
			Footer.menu(hashPath);
		}

		static async switch() {
			let hash = Hash.get(),
				namespace = this.getNamespace(hash);

			this.setLoading(true);
			if(this.firstLoad) {
				let head = await Template.getModule(undefined, 'head', namespace),
					body = await Template.getModule(undefined, 'body', namespace);

				this.setTitle(body?.title ?? head?.title ?? '');
				this.setHead(head?.head.childNodes ?? []);
				this.setBody(body?.body.childNodes ?? []);
				this.updateEdges(hash);

				this.firstLoad = false;
			} else {
				this.updateEdges(hash);

				let content = await Template.getModule(undefined, 'content', namespace);

				this.setTitle(content?.title ?? '');
				Content.set(content?.body.childNodes ?? []);
			}
			this.setLoading(false);
		}
	}

	window.Dictionary = class {
		static current = 'ru';

		static strings = {
			ru: {
				button_archive: 'Архив',
				button_avatars: 'Аватары',
				button_bookmarks: 'Закладки',
				button_cancel: 'Отменить',
				button_check: 'Проверить',
				button_claim: 'Жалоба',
				button_comment: 'Комментировать',
				button_comments: 'Комментарии',
				button_copy: 'Копировать',
				button_create: 'Создать',
				button_create_article_in_section: 'Создать статью в разделе',
				button_delete: 'Удалить',
				button_delete_all: 'Удалить все',
				button_display_viewer: 'Отобразить просмотрщик',
				button_download: 'Загрузить',
				button_drafts: 'Черновики',
				button_edit: 'Редактировать',
				button_friends_comments: 'Комментарии друзей',
				button_friends_recommendations: 'Рекомендации друзей',
				button_get: 'Получить',
				button_groups: 'Группы',
				button_groups_tooltip: 'Чужие / Свои',
				button_inbox: 'Входящие',
				button_include_from_bookmarks: 'Включить из закладок',
				button_invite: 'Пригласить',
				button_login: 'Войти',
				button_most_discussed: 'Самое обсуждаемое',
				button_most_popular: 'Самое популярное',
				button_most_recommended: 'Самое рекомендуемое',
				button_most_visited: 'Самое посещаемое',
				button_objects_stats: 'Статистика по объектам',
				button_outbox: 'Исходящие',
				button_password_recovery: 'Восстановление пароля',
				button_private_message: 'Сообщить',
				button_recommend: 'Рекомендовать',
				button_recommendations: 'Рекомендации',
				button_register: 'Зарегистрироваться',
				button_registration: 'Регистрация',
				button_remove: 'Убрать',
				button_remove_all: 'Убрать все',
				button_request: 'Запросить',
				button_save: 'Сохранить',
				button_search: 'Искать',
				button_search_everywhere: 'Искать везде',
				button_select_template: 'Выбрать шаблон',
				button_template: 'Шаблон',
				button_templates: 'Шаблоны',
				button_to_bookmarks: 'В закладки',
				button_to_friends: 'В друзья',
				button_to_main: 'На титул',
				button_to_my: 'В моё',
				button_torrent: 'Торрент',
				button_upload_files_to_section: 'Загрузить файлы в раздел',
				button_user_comments: 'Комментарии пользователя',
				button_user_page: 'Страница пользователя',
				button_user_recommendations: 'Рекомендации пользователя',
				link_about: 'О сервисе',
				link_apps: 'Приложения',
				link_audio: 'Аудио',
				link_complete_list: 'Полный список',
				link_contacts: 'Контакты',
				link_copyright: 'Правообладателям',
				link_file_list: 'Файл-лист',
				link_files: 'Файлы',
				link_friends: 'Друзья',
				link_fs_stats: 'файловым серверам',
				link_games: 'Игры',
				link_images: 'Изображения',
				link_login: 'Вход',
				link_logout: 'Выход',
				link_notifications: 'Уведомления',
				link_page: 'Страница',
				link_personal_menu: 'персональное меню',
				link_play_list: 'Плей-лист',
				link_search: 'Поиск',
				link_settings: 'Настройки',
				link_texts: 'Тексты',
				link_users_stats: 'пользователям',
				link_video: 'Видео',
				select_items_per_page_tooltip: 'Элементов на страницу',
				string_access: 'Доступ',
				string_account_deletion: 'Вы можете удалить свою учётную запись[br]После удаления вы потеряете доступ ко всем своим объектам',
				string_account_deletion_confirmation: 'Удалить учётную запись?',
				string_actions: 'Действия',
				string_agreement: 'Соглашение',
				string_agreement_description: 'Регистрируясь, вы даёте согласие не использовать ресурс для совершения действий, противоречащих законам РФ',
				string_alias: 'Алиас',
				string_allow_advanced_control: 'Разрешить профессиональное управление',
				string_allow_any_upload_size: 'Разрешить любой размер загрузки',
				string_allow_max_access_ignoring_groups: 'Разрешить максимальный доступ, игнорируя группы',
				string_app_description: 'Хранение и обмен файлов.',
				string_app_title: 'EX',
				string_april: 'апреля',
				string_as_cells: 'Ячейками',
				string_as_list: 'Списком',
				string_august: 'августа',
				string_by_creation_time: 'По времени создания',
				string_by_edit_time: 'По времени редактирования',
				string_by_inclusion_time: 'По времени включения',
				string_by_title: 'По названию',
				string_captcha: 'Каптча',
				string_comments: 'Комментарии',
				string_comments_count: 'Комментариев',
				string_contact_abuse: 'Нарушения и вопросы правообладания',
				string_contact_info: 'Сотрудничество',
				string_contact_support: 'Поддержка пользователей и другие вопросы',
				string_content_rating_first: 'Вы на ',
				string_content_rating_second: '\'ом месте по размеру оригинального контента',
				string_count: 'Количество',
				string_december: 'декабря',
				string_default_avatar: 'Аватар по умолчанию',
				string_deny_claims: 'Запретить обжалование',
				string_deny_nonbookmark_inclusion: 'Запретить включение извне закладок',
				string_display_search_bar: 'Отображать строку поиска',
				string_duplicates_count: 'Дубликатов',
				string_editor: 'Редактор',
				string_email: 'Почта',
				string_english: 'English',
				string_everyone_see_as: 'Все видят как',
				string_everyone_see_everything_my_as: 'Все видят всё моё как',
				string_execute_alien_js: 'Исполнять чужой JavaScript',
				string_february: 'февраля',
				string_file_number: 'Номер файла',
				string_files: 'Файлы',
				string_files_count: 'Файлов',
				string_friends_count: 'Друзей',
				string_friendship: 'Дружба',
				string_given: 'Дано',
				string_group: 'Группа',
				string_group_invites_count: 'Приглашений в группу',
				string_groups_description: 'Группы служат для управления доступом к объектам',
				string_guests_count: 'Гостей',
				string_hide_author_and_times: 'Скрывать автора и времена',
				string_hide_default_referer: 'Скрывать реферер по умолчанию',
				string_hide_file_list: 'Скрывать список файлов',
				string_hide_from_search: 'Скрывать из поисковой выдачи',
				string_hide_from_search_inherition: '(включая принадлежащие объекты)',
				string_hits_count: 'Хитов',
				string_hosts_count: 'Хостов',
				string_i_see_as: 'Я вижу как',
				string_i_see_everything_as: 'Я вижу всё как',
				string_inclusions_count: 'Включений',
				string_ip_independent: 'Независимый от IP',
				string_january: 'января',
				string_july: 'июля',
				string_june: 'июня',
				string_login: 'Логин',
				string_march: 'марта',
				string_may: 'мая',
				string_menu: 'Меню',
				string_navigation: 'Навигация',
				string_nizkagorian: 'Низкагорский',
				string_nothing_found: 'Ничего не найдено',
				string_notifications: 'Уведомления',
				string_november: 'ноября',
				string_object_number: 'Номер объекта',
				string_objects_count: 'Объектов',
				string_objects_stats: 'Статистика по объектам',
				string_october: 'октября',
				string_online: 'В сети',
				string_origin: 'Происхождение',
				string_originals_count: 'Оригиналов',
				string_password: 'Пароль',
				string_password_repeat: 'Пароль (повтор)',
				string_permanent: 'Постоянный',
				string_poster: 'Постер',
				string_private_messages: 'Личные сообщения',
				string_private_messages_count: 'Личных сообщений',
				string_privileges: 'Привилегии',
				string_recommendations: 'Рекомендации',
				string_recommendations_count: 'Рекомендаций',
				string_registered: 'Зарегистрирован',
				string_restrictions: 'Ограничения',
				string_russian: 'Русский',
				string_search: 'Поиск',
				string_section: 'Раздел',
				string_september: 'сентября',
				string_shared_objects: 'Вы можете анонимно воспользоваться сервисом хранения и обмена файлов[br]В этом случае доступ к объекту будет возможен только по его номеру',
				string_stats_of: 'Статистика по',
				string_summary_size: 'Суммарный размер',
				string_templates: 'Шаблоны',
				string_type: 'Тип',
				string_use: 'Использовать',
				string_user: 'Пользователь',
				string_when: 'Когда',
				title_archive: 'Архив',
				title_avatars: 'Аватары',
				title_bookmarks: 'Закладки',
				title_comments: 'Комментарии',
				title_contacts: 'Контактная информация',
				title_copyright: 'ПОЛИТИКА ЗАКРЫТИЯ КОНТЕНТА',
				title_drafts: 'Черновики',
				title_edit_settings: 'Редактирование настроек',
				title_files: 'Файлы',
				title_friends: 'Друзья',
				title_friends_comments: 'Комментарии друзей',
				title_friends_recommendations: 'Рекомендации друзей',
				title_fs_stats: 'Статистика по файловым серверам',
				title_groups: 'Группы',
				title_groups: 'Группы',
				title_inbox: 'Входящие личные сообщения',
				title_last_objects: 'Крайние объекты',
				title_login: 'Вход',
				title_most_discussed: 'Самое обсуждаемое',
				title_most_popular: 'Самое популярное',
				title_most_recommended: 'Самое рекомендуемое',
				title_most_visited: 'Самое посещаемое',
				title_notifications: 'Уведомления',
				title_outbox: 'Исходящие личные сообщения',
				title_page_not_found: 'Страница не найдена',
				title_password: 'Восстановление пароля',
				title_recommendations: 'Рекомендации',
				title_registration: 'Регистрация',
				title_search: 'Поиск',
				title_session_required: 'Действие невозможно в анонимном режиме',
				title_settings: 'Настройки',
				title_template: 'Выбор шаблона',
				title_templates: 'Шаблоны',
				title_user_comments: 'Комментарии пользователя',
				title_user_page: 'Страница пользователя',
				title_user_recommendations: 'Рекомендации пользователя',
				title_users_stats: 'Статистика по пользователям'
			},
			ng: {
				title_last_objects: 'Крайние абъекты',
				title_most_popular: 'Самае папулярнае',
				title_most_visited: 'Самае пасешяемае',
				title_most_discussed: 'Самае абсуждаемае',
				title_most_recommended: 'Самае рекамендуемае',
				string_shared_objects: 'Вы можэте ананимна васпользавац\'а сэрвисам хранения и абмена файлаў.[br]В этам случяе доступ к абъекту будет вазможэн тока па ево номеру.',
				string_object_number: 'Номер абъекта',
				string_page_not_found: 'Страница не найдена',
				string_stats_of: 'Статистика па',
				string_app_title: 'EX',
				string_russian: 'Рус\'кий',
				string_nizkagorian: 'Низкагорский',
				string_english: 'Английский',
				button_create: 'Саздать',
				button_get: 'Палучить',
				button_to_main: 'На титул',
				link_page: 'Страница',
				link_settings: 'Настройки',
				link_login: 'Ўход',
				link_logout: 'Выхад',
				link_complete_list: 'Поўный списак',
				link_stats_of_users: 'пользавателям',
				link_stats_of_fs: 'файлавым серверам',
				link_contacts: 'Кантакты',
				link_copyright: 'Права\'бладателям'
			},
			en: {
				button_archive: 'Archive',
				button_avatars: 'Avatars',
				button_bookmarks: 'Bookmarks',
				button_cancel: 'Cancel',
				button_check: 'Check',
				button_claim: 'Claim',
				button_comment: 'Comment',
				button_comments: 'Comments',
				button_copy: 'Copy',
				button_create: 'Create',
				button_create_article_in_section: 'Create article in section',
				button_delete: 'Delete',
				button_delete_all: 'Delete all',
				button_display_viewer: 'Display viewer',
				button_download: 'Download',
				button_drafts: 'Drafts',
				button_edit: 'Edit',
				button_friends_comments: 'Friends comments',
				button_friends_recommendations: 'Friends recommendations',
				button_get: 'Get',
				button_groups: 'Groups',
				button_groups_tooltip: 'Alien / Yours',
				button_inbox: 'Inbox',
				button_include_from_bookmarks: 'Include from bookmarks',
				button_invite: 'Invite',
				button_login: 'Login',
				button_most_discussed: 'Most discussed',
				button_most_popular: 'Most popular',
				button_most_recommended: 'Most recommended',
				button_most_visited: 'Most visited',
				button_objects_stats: 'Stats of objects',
				button_outbox: 'Outbox',
				button_password_recovery: 'Password recovery',
				button_private_message: 'Message',
				button_recommend: 'Recommend',
				button_recommendations: 'Recommendations',
				button_register: 'Register',
				button_registration: 'Registration',
				button_remove: 'Remove',
				button_remove_all: 'Remove all',
				button_request: 'Request',
				button_save: 'Save',
				button_search: 'Search',
				button_search_everywhere: 'Search everywhere',
				button_select_template: 'Select template',
				button_template: 'Template',
				button_templates: 'Templates',
				button_to_bookmarks: 'To bookmarks',
				button_to_friends: 'To friends',
				button_to_main: 'To main',
				button_to_my: 'To my',
				button_torrent: 'Torrent',
				button_upload_files_to_section: 'Upload files to section',
				button_user_comments: 'User comments',
				button_user_page: 'User page',
				button_user_recommendations: 'User recommendations',
				link_about: 'About',
				link_apps: 'Apps',
				link_audio: 'Audio',
				link_complete_list: 'Complete list',
				link_contacts: 'Contacts',
				link_copyright: 'Copyright',
				link_file_list: 'File-list',
				link_files: 'Files',
				link_friends: 'Friends',
				link_fs_stats: 'file servers',
				link_games: 'Games',
				link_images: 'Images',
				link_login: 'Login',
				link_logout: 'Logout',
				link_notifications: 'Notifications',
				link_page: 'Page',
				link_personal_menu: 'personal menu',
				link_play_list: 'Play-list',
				link_search: 'Search',
				link_settings: 'Settings',
				link_texts: 'Texts',
				link_users_stats: 'users',
				link_video: 'Video',
				select_items_per_page_tooltip: 'Items per page',
				string_access: 'Access',
				string_account_deletion: 'You can delete your account[br]After deletion you will lose access to all your objects',
				string_account_deletion_confirmation: 'Delete account?',
				string_actions: 'Actions',
				string_agreement: 'Agreement',
				string_agreement_description: 'By registering, you agree not to use the service to perform actions contrary to the laws of the Russian Federation',
				string_alias: 'Alias',
				string_allow_advanced_control: 'Allow advanced control',
				string_allow_any_upload_size: 'Allow any upload size',
				string_allow_max_access_ignoring_groups: 'Allow max access ignoring groups',
				string_app_description: 'File storage and exchange.',
				string_app_title: 'EX',
				string_april: 'april',
				string_as_cells: 'As cells',
				string_as_list: 'As list',
				string_august: 'august',
				string_by_creation_time: 'By creation time',
				string_by_edit_time: 'By edit time',
				string_by_inclusion_time: 'By inclusion time',
				string_by_title: 'By title',
				string_captcha: 'Captcha',
				string_comments: 'Comments',
				string_comments_count: 'Comments',
				string_contact_abuse: 'Violations and ownership issues',
				string_contact_info: 'Cooperation',
				string_contact_support: 'User support and other questions',
				string_content_rating_first: 'You are at ',
				string_content_rating_second: ' place by rating of original content',
				string_count: 'Count',
				string_december: 'december',
				string_default_avatar: 'Default avatar',
				string_deny_claims: 'Deny claims',
				string_deny_nonbookmark_inclusion: 'Deny non-bookmark inclusion',
				string_display_search_bar: 'Display search bar',
				string_duplicates_count: 'Duplicates',
				string_editor: 'Editor',
				string_email: 'Email',
				string_english: 'English',
				string_everyone_see_as: 'Everyone see as',
				string_everyone_see_everything_my_as: 'Everyone see everything my as',
				string_execute_alien_js: 'Execute alien JavaScript',
				string_february: 'february',
				string_file_number: 'File number',
				string_files: 'Files',
				string_files_count: 'Files',
				string_friends_count: 'Friends',
				string_friendship: 'Friendship',
				string_given: 'Given',
				string_group: 'Group',
				string_group_invites_count: 'Group invites',
				string_groups_description: 'Groups are used to control access to objects',
				string_guests_count: 'Guests',
				string_hide_author_and_times: 'Hide author and times',
				string_hide_default_referer: 'Hide default referer',
				string_hide_file_list: 'Hide file list',
				string_hide_from_search: 'Hide from search results',
				string_hide_from_search_inherition: '(including owned objects)',
				string_hits_count: 'Hits',
				string_hosts_count: 'Hosts',
				string_i_see_as: 'I see as',
				string_i_see_everything_as: 'I see everything as',
				string_inclusions_count: 'Inclusions',
				string_ip_independent: 'IP independent',
				string_january: 'january',
				string_july: 'july',
				string_june: 'june',
				string_login: 'Login',
				string_march: 'march',
				string_may: 'may',
				string_menu: 'Menu',
				string_navigation: 'Navigation',
				string_nizkagorian: 'Низкагорский',
				string_nothing_found: 'Nothing found',
				string_notifications: 'Notifications',
				string_november: 'november',
				string_object_number: 'Object number',
				string_objects_count: 'Objects',
				string_objects_stats: 'Stats of objects',
				string_october: 'october',
				string_online: 'Online',
				string_origin: 'Origin',
				string_originals_count: 'Originals',
				string_password: 'Password',
				string_password_repeat: 'Password (repeat)',
				string_permanent: 'Permanent',
				string_poster: 'Poster',
				string_private_messages: 'Private messages',
				string_private_messages_count: 'Private messages',
				string_privileges: 'Privileges',
				string_recommendations: 'Recommendations',
				string_recommendations_count: 'Recommendations',
				string_registered: 'Registered',
				string_restrictions: 'Restrictions',
				string_russian: 'Русский',
				string_search: 'Search',
				string_section: 'Section',
				string_september: 'september',
				string_shared_objects: 'You can anonymously use the file storage and EXchange service[br]In this case access to an object will be possible only by its number',
				string_stats_of: 'Stats of',
				string_summary_size: 'Summary size',
				string_templates: 'Templates',
				string_type: 'Type',
				string_use: 'Use',
				string_user: 'User',
				string_when: 'When',
				title_archive: 'Archive',
				title_avatars: 'Avatars',
				title_bookmarks: 'Bookmarks',
				title_comments: 'Comments',
				title_contacts: 'Contact information',
				title_copyright: 'CONTENT CLOSING POLICY',
				title_drafts: 'Drafts',
				title_edit_settings: 'Edit of settings',
				title_files: 'Files',
				title_friends: 'Friends',
				title_friends_comments: 'Friends comments of',
				title_friends_recommendations: 'Friends recommendations of',
				title_fs_stats: 'Stats of file servers',
				title_groups: 'Groups',
				title_groups: 'Groups',
				title_inbox: 'Inbox private messages',
				title_last_objects: 'Last objects',
				title_login: 'Login',
				title_most_discussed: 'Most discussed',
				title_most_popular: 'Most popular',
				title_most_recommended: 'Most recommended',
				title_most_visited: 'Most visited',
				title_notifications: 'Notifications',
				title_outbox: 'Outbox private messages',
				title_page_not_found: 'Page not found',
				title_password: 'Password recovery',
				title_recommendations: 'Recommendations',
				title_registration: 'Registration',
				title_search: 'Search',
				title_session_required: 'Action isn\'t possible in anonymous mode',
				title_settings: 'Settings',
				title_template: 'Template selection',
				title_templates: 'Templates',
				title_user_comments: 'Comments of user',
				title_user_page: 'Page of user',
				title_user_recommendations: 'Recommendations of user',
				title_users_stats: 'Stats of users'
			}
		}

		static setLanguage(language) {
			if(this.strings[language] == null) {
				return;
			}

			this.current = language;
		}

		static getString(string) {
			if(string != null) {
				return this.strings[this.current][string]
			} else {
				return this.strings[this.current]
			}
		}

		static getPageTitle(title) {
			let appTitle = this.getString('string_app_title');

			if(title.length > 0) {
				return title+' @ '+appTitle;
			} else {
				return appTitle;
			}
		}
	}

	window.Link = class {
		static async getContents(URL) {
			return new Promise((resolve, reject) => {
				fetch(URL).then(response => {
					if(response.status === 200) {
						resolve(response.text());
					} else {
						reject(response.status);
					}
				}).catch(error => {
					reject(error);
				});
			});
		}
	}

	window.String.prototype.replaceAt = function(index, replacement) {
		let str = this;

		str = str.split('');
		str[index] = replacement;
		str = str.join('');

		return str;
	}

	Object.defineProperty(window.Array.prototype, 'equalsTo', {
		enumerable: false,
		configurable: false,
		writable: false,
		value: function(array) {
			if(this.length !== array.length) {
				return false;
			}

			for(let i = 0; i < this.length; i++) {
				if(this[i] !== array[i]) {
					return false;
				}
			}

			return true;
		}
	});

	window.Template = class {
		static baseURL = '/app/template/';

		static cache = []

		static findCached(key) {
			return this.cache.find(v => Array.isArray(v.key) && Array.isArray(key) ? v.key.equalsTo(key) : v.key === key);
		}

		static setCached(key, value) {
			let value_ = this.findCached(key);

			if(value_ != null) {
				value_.value = value;
			} else {
				this.cache.push({
					key: key,
					value: value
				});
				if(this.cache.length > 16) {
					this.cache.shift();
				}
			}

			return value;
		}

		static getCached(key) {
			return this.findCached(key)?.value;
		}

		static getVariable(namespace, path) {
			path = path?.split('.') ?? []

			let value;

			for(let k in path) {
				let variable = path[k]

				if(k == 0) {
					while(value == null && namespace != null) {
						value = namespace[variable]
						namespace = namespace._parent_;
					}
				} else
				if(value != null) {
					value = value[variable]
				} else {
					break;
				}
			}

			return value;
		}

		static async getModuleURL(objectId, title) {
			if(objectId == null) {
				return this.baseURL+title;
			}

			try {
				let files = await Link.getContents('/app/api/files.php?object_id='+objectId);

				if(files != null) {
					return '/get/'+files.find(v => v.title === title).id;
				}
			} catch {}
		}

		static getTopLevelElements(model, selector) {
		//	let cacheKey = [model, selector]

		//	return this.getCached(cacheKey) ?? this.setCached(cacheKey, model.querySelectorAll(selector+':not(module '+selector+')'));
			return model.querySelectorAll(selector+':not(module '+selector+')');
		}

		static getReplacementBlackzones(model) {
			let modelInner = model.documentElement.innerHTML,
				childModules = this.getTopLevelElements(model, 'module'),
				result = []

			for(let cm of childModules) {
				let cmOuter = cm.outerHTML,
					cmInner = cm.innerHTML,
					cmLeft = modelInner.indexOf(cmOuter, result.at(-1)?.[1] ?? 0)+cmOuter.lastIndexOf(cmInner)-1,
					cmRight = cmLeft+cmInner.length+1;

				result.push([
					cmLeft,
					cmRight
				]);
			}

			return result;
		}

		static replaceWithBlackzones(model, blackzones, entries) {
			let modelInner = model.documentElement.innerHTML;

			for(let entry of entries) {
				let key = entry[0],
					value = entry[1],
					left;

				while((left = modelInner.indexOf(key, left+1)) !== -1) {
					let right = left+key.length,
						blackzoned;

					for(let blackzone of blackzones) {
						if(left >= blackzone[0] && right <= blackzone[1]) {
							blackzoned = true;

							break;
						}
					}
					if(blackzoned) {
						continue;
					}

					let difference = key.length-value.length,
						before = modelInner.substring(0, left),
						after = modelInner.substring(right, modelInner.length);

					modelInner = before+value+after;
					for(let blackzone of blackzones) {
						if(right <= blackzone[0]) {
							blackzone[0] -= difference;
							blackzone[1] -= difference;
						}
					}
				}
			}

			model.documentElement.innerHTML = modelInner;
		}

		static escapeVariable(variable) {
			return variable?.replaceAll(/&(?!(amp|lt|gt|quot|#039);)/g, '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
		}

		static parseVariables(model, blackzones, namespace) {
			let modelInner = model.documentElement.innerHTML,
				variables = new Set(Array.from(modelInner.matchAll(/\[[\w.]+\]/g), v => v[0]).sort((a, b) => b.length-a.length)),
				entries = []

			for(let variable of variables) {
				let variable_normalized = variable.substring(1, variable.length-1),
					value = this.escapeVariable(this.getVariable(namespace, variable_normalized)?.toString()) ?? '';

				entries.push([
					variable,
					value
				]);
			}

			this.replaceWithBlackzones(model, blackzones, entries);
		}

		static getComparisonsResult(comparisons) {
			comparisons = comparisons.match(/([^;\\]|\\.)+/g) ?? []

			for(let comparison of comparisons) {
				comparison = comparison.match(/(.*)(==|!=|>=|<=|>|<)(.*)/);

				if(comparison?.length >= 4) {
					let lh = comparison[1],
						sign = comparison[2],
						rh = comparison[3]

					if(
						sign === '==' && lh !== rh ||
						sign === '!=' && lh === rh ||
						sign === '>=' && lh*1 < rh*1 ||
						sign === '<=' && lh*1 > rh*1 ||
						sign === '>' && lh*1 <= rh*1 ||
						sign === '<' && lh*1 >= rh*1
					) {
						return false;
					}
				}
			}

			return true;
		}

		static parseConditions(model) {
			let conditions = this.getTopLevelElements(model, 'condition');

			for(let k = conditions.length-1; k >= 0; k--) {
				let condition = conditions[k],
					conditionBlocks = condition.childNodes,
					expectedBlocks = ['IF']

				for(let cb of conditionBlocks) {
					if(!expectedBlocks.includes(cb.nodeName)) {
						continue;
					}

					if(cb.nodeName === 'IF' || cb.nodeName === 'ELSEIF') {
						if(this.getComparisonsResult(cb.innerText)) {
							expectedBlocks = ['THEN']
						} else {
							expectedBlocks = ['ELSEIF', 'ELSE']
						}
					}
					if(cb.nodeName === 'THEN' || cb.nodeName === 'ELSE') {
						condition.replaceWith(...cb.childNodes);

						expectedBlocks = []
					}
				}
				if(condition != null) {
					condition.remove();
				}
			}
		}

		static parseFunction(string, namespace) {
			let identifiers = string?.split(' ; ') ?? [],
				unescaped,
				linked;

			for(let k in identifiers) {
				if(k == 0 && identifiers[k].startsWith('!')) {
					unescaped = true;
					identifiers[k] = identifiers[k].substring(1);
				}
				if(k != 0) {
					if(identifiers[k].startsWith('&amp;')) {
						linked = true;
						identifiers[k] = identifiers[k].substring(5);
					}
					if(identifiers[k].startsWith('\\&amp;')) {
						identifiers[k] = identifiers[k].substring(1);
					}
				}
				if(k == 0 || linked) {
					linked = false;
					identifiers[k] = this.getVariable(namespace, identifiers[k]);
				}
			}
			if(typeof identifiers[0] === 'function') {
				let key = identifiers.shift(),
					value = key(...identifiers)?.toString() ?? '';

				if(!unescaped) {
					value = this.escapeVariable(value);
				}

				return value;
			}
		}

		static parseExpression(expression, namespace) {
			let result = expression,
				match,
				expectedSeparators = /{{/,
				parts = {}

			while((match = result.match(expectedSeparators)) != null) {
				let i = match.index,
					separator = match[0]

				if(separator === '{{') {
					parts.before = result.substring(0, i);
					if(parts.before.indexOf('}}') > -1) {
						break;
					}
					result = this.parseExpression(result.substring(i+separator.length), namespace);

					expectedSeparators = /{{|}}/;
				}
				if(separator === '}}') {
					parts.content = this.parseFunction(result.substring(0, i), namespace) ?? '';
					result = result.substring(i+separator.length);

					parts.after = result;
					result = parts.before+parts.content+parts.after;

					parts = {}
					expectedSeparators = /{{/;
				}
			}

			return result;
		}

		static parseExpressions(model, blackzones, namespace) {
			let modelInner = model.documentElement.innerHTML,
				match,
				expectedSeparators = /{{|}}/g,
				expressions = [],
				level = 0,
				entries = []

			while((match = expectedSeparators.exec(modelInner)) != null) {
				let i = match.index,
					separator = match[0]

				if(separator === '{{' && ++level === 1) {
					expressions.push({
						index: i
					});
				}
				if(separator === '}}' && level > 0 && --level === 0) {
					let expression = expressions.at(-1);

					expression.string = modelInner.substring(expression.index, i+separator.length);
				}

				expectedSeparators.lastIndex = i+separator.length;
			}
			expressions = expressions.filter(v => v.string != null).sort((a, b) => b.string.length-a.string.length);

			for(let expression of expressions) {
				entries.push([
					expression.string,
					this.parseExpression(expression.string, namespace)
				]);
			}

			this.replaceWithBlackzones(model, blackzones, entries);
		}

		static parseAttributes(model) {
			let elements = this.getTopLevelElements(model, '[attributes]');

			for(let element of elements) {
				let attributes = element.getAttribute('attributes');

				if(attributes != null) {
					attributes = attributes.match(/([^;\\]|\\.)+/g) ?? []

					for(let attribute of attributes) {
						attribute = attribute.match(/([^=\\]|\\.)+/g);

						if(attribute != null) {
							element.setAttribute(attribute[0], attribute[1]?.replaceAll('\\;', ';').replaceAll('\\=', '=').trim() ?? '');
						}
					}

					element.removeAttribute('attributes');
				}
			}
		}

		static async parseChildModules(objectId, model, namespace) {
			let childModules = this.getTopLevelElements(model, 'module');

			for(let k = childModules.length-1; k >= 0; k--) {
				let cm = childModules[k],
					cmTitle = cm.getAttribute('title'),
					cmVariables = cm.getAttribute('variables'),
					cmVariablesInstances = cm.getAttribute('variables-instances'),
					cmInner = cm.innerHTML,
					cmNamespace = {},
					cmModelTitle,
					cmModelBody = []

				if(cmTitle == null && cmInner === '') {
					continue;
				}

				if(cmVariables != null) {
					cmVariables = cmVariables.match(/([^;\\]|\\+.)+/g) ?? []

					for(let cmVariable of cmVariables) {
						cmVariable = cmVariable.match(/([^=\\]|\\+.)+/g);

						if(cmVariable != null) {
							let key = cmVariable[0],
								value = cmVariable[1]?.replaceAll('\\;', ';').replaceAll('\\=', '=');

							if(value?.startsWith('@')) {
								value = value.replaceAll('\\@', '@').substring(1);
								value = JSON.parse(await Link.getContents(value));
							} else
							if(key === '...') {
								value = this.getVariable(namespace, value);
							} else {
								value = this.escapeVariable(value);
							}

							if(key === '...') {
								if(value !== null && typeof value === 'object' && !Array.isArray(value)) {
									Object.assign(cmNamespace, value);
								}
							} else {
								cmNamespace[key] = value ?? '';
							}
						}
					}
				}
				cmNamespace._parent_ = namespace;

				if(cmVariablesInstances != null) {
					if(cmVariablesInstances.startsWith('@')) {
						cmVariablesInstances = JSON.parse(await Link.getContents(cmVariablesInstances.substring(1)));
					} else {
						cmVariablesInstances = this.getVariable(cmNamespace, cmVariablesInstances.replaceAll('\\@', '@'));
					}
					if(!Array.isArray(cmVariablesInstances)) {
						cmVariablesInstances = []
					}

					for(let k in cmVariablesInstances) {
						let cmVariablesInstance = cmVariablesInstances[k],
							cmNamespaceInstance = {
								...cmNamespace,
								...cmVariablesInstance,
								index: k,
								last_index: cmVariablesInstances.length-1
							},
							cmModelInstanceBody;

						if(cmTitle != null) {
							cmModelInstanceBody = (await this.getModule(objectId, cmTitle, cmNamespaceInstance))?.body.childNodes;
						} else {
							cmModelInstanceBody = (await this.parseModule(objectId, cmInner, cmNamespaceInstance))?.body.childNodes;
						}
						if(cmModelInstanceBody?.length > 0) {
							cmModelBody.push(...cmModelInstanceBody);
						}
					}
				} else {
					let cmModel;

					if(cmTitle != null) {
						cmModel = await this.getModule(objectId, cmTitle, cmNamespace);
					} else {
						cmModel = await this.parseModule(objectId, cmInner, cmNamespace);
					}

					cmModelTitle = cmModel?.title;
					cmModelBody = cmModel?.body.childNodes;
				}
				if(cmModelBody?.length > 0) {
					cm.replaceWith(...cmModelBody);
				} else {
					cm.remove();
				}
				if(cmModelTitle?.length > 0) {
					model.title = cmModelTitle;
				}
			}
		}

		static async parseModule(objectId, model, namespace) {
			try {
				model = new DOMParser().parseFromString(model, 'text/html');

				let blackzones = this.getReplacementBlackzones(model);

				this.parseVariables(model, blackzones, namespace);
				this.parseExpressions(model, blackzones, namespace);
				this.parseConditions(model);
				this.parseAttributes(model);
				await this.parseChildModules(objectId, model, namespace);

				return model;
			} catch(error) {
				console.log(error)
			}
		}

		static async getModule(objectId, title, namespace) {
			try {
				let cacheKey = 'object_id='+objectId+';title='+title,
					model = this.getCached(cacheKey) ?? this.setCached(cacheKey, await Link.getContents(await this.getModuleURL(objectId, title)));

				if(model != null) {
					return this.parseModule(objectId, model, namespace);
				}
			} catch(error) {
				console.log(error)
			}
		}
	}

	Hash.load();

	/*
	window.addEventListener('hashchange', () => Hash.load());
	document.addEventListener('click', (e) => {
		if(e.target.getAttribute('_check') != null) {
			Button.checkClick(e);
		}
		if(e.target.getAttribute('_radio') != null) {
			Button.radioClick(e);
		}
	});
	*/
	$(window).on('hashchange', () => Hash.load());
	$(document).on('click', '[_check]', Button.checkClick);
	$(document).on('click', '[_radio]', Button.radioClick);
	/*
	$(document).on('mousedown', '[onclick]', (e) => {
		if(e.button === 1) {
			return false;
		}
	});
	$(document).on('mouseup', '[onclick]', (e) => {
		let action = $(e.currentTarget).attr('onclick'),
			actionLink = action.match(/Hash.set\(\'(.*)\'.*\)/i);

		if(e.button === 1 && e.target.nodeName !== 'A' && actionLink) {
			window.open('/#'+actionLink[1], '_blank');
		}
	});
	*/
});