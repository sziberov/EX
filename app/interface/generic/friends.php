<?
	if(!Session::set()) {
		return include 'generic/login.php';
	}
?>
<title>Друзья @ EX</title>
<div _title>Друзья</div>
<div _table="list" wide_ style="--columns: repeat(5, minmax(96px, auto));">
	<div header_>
		<div>Пользователь</div>
		<div>Добавлен</div>
		<div>Добавил</div>
		<div>В сети</div>
		<div></div>
	</div>
	<div data-user-id="_USER_ID_">
		<div>
			<a _description="short straight" href="#user/_USER_TITLE_">Offline</a>
		</div>
		<div>00:00, 01 января 2019</div>
		<div></div>
		<div>00:00, 01 января 2019</div>
		<div>
			<button>Убрать</button>
		</div>
	</div>
	<div data-user-id="_USER_ID_">
		<div>
			<a _description="short straight" href="#user/_USER_TITLE_">Online</a>
		</div>
		<div>00:00, 01 января 2019</div>
		<div></div>
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
	<div footer_ id="addRow">
		<div>
			<a onclick="document.getElementById('addRow').style.display = 'none'; document.getElementById('editRow').removeAttribute('style');"><u>Добавить</u></a>
		</div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
	</div>
	<div footer_ id="editRow" style="display: none;">
		<div>
			<input size_="big" type="text" placeholder="Логин">
		</div>
		<div></div>
		<div></div>
		<div></div>
		<div>
			<button>Сохранить</button>
			<button onclick="document.getElementById('editRow').style.display = 'none'; document.getElementById('addRow').removeAttribute('style');">Отменить</button>
		</div>
	</div>
</div>