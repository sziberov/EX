<title>Блокировка</title>
<div _table style="--columns: repeat(2, minmax(0, max-content));">
	<div>
		<div></div>
		<div _title centered_>Блокировка</div>
	</div>
	<div>
		<div>Логин</div>
		<div>
			<input name="login" type="text" value="">
		</div>
	</div>
	<div>
		<div>IP-адрес</div>
		<div>
			<input name="ip" type="text" value="">
			<button>Подставить</button>
		</div>
	</div>
	<div>
		<div>Причина</div>
		<div>
			<input size_="big" name="reason" type="text" value="">
		</div>
	</div>
	<div>
		<div>Действие</div>
		<div _flex="v stacked left">
			<div _radio="active" name="action_id" value="1">
				<div></div>
				<div>Заблокировать</div>
			</div>
			<div _radio name="action_id" value="2">
				<div></div>
				<div>Разблокировать</div>
			</div>
		</div>
	</div>
	<div>
		<div></div>
		<div>
			<button>Выполнить</button>
		</div>
	</div>
</div>
<div _table="list" wide_ style="--columns: repeat(6, minmax(96px, auto));">
	<div header_>
		<div>Логин</div>
		<div>IP-адрес</div>
		<div>Причина</div>
		<div>Кто</div>
		<div>Когда</div>
		<div>Действия</div>
	</div>
	<div data-user-id="_USER_ID_">
		<div>Offline</div>
		<div>192.168.127.1</div>
		<div>
			<div _description="short"></div>
		</div>
		<div>
			<a _description="short straight" href="#user/_USER_TITLE_">admin</a>
		</div>
		<div>00:00, 01 января 2019</div>
		<div>
			<button>Убрать</button>
		</div>
	</div>
	<div data-user-id="_USER_ID_">
		<div>Online</div>
		<div>192.168.127.1</div>
		<div>
			<div _description="short"></div>
		</div>
		<div>
			<a _description="short straight" href="#user/_USER_TITLE_">admin</a>
		</div>
		<div>00:00, 01 января 2019</div>
		<div>
			<button>Убрать</button>
		</div>
	</div>
	<div data-user-id="_USER_ID_">
		<div>Offline_Mutual</div>
		<div>192.168.127.1</div>
		<div>
			<div _description="short"></div>
		</div>
		<div>
			<a _description="short straight" href="#user/_USER_TITLE_">admin</a>
		</div>
		<div>00:00, 01 января 2019</div>
		<div>
			<button>Убрать</button>
		</div>
	</div>
	<div data-user-id="_USER_ID_">
		<div>Online_Mutual</div>
		<div>192.168.127.1</div>
		<div>
			<div _description="short"></div>
		</div>
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