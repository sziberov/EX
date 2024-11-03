<?
	$page_title = 'Алиасы';
?>
<div _title>Алиасы</div>
<div _table="list" small_ style="--columns: repeat(3, minmax(96px, 1fr));">
	<div header_>
		<div>Алиас</div>
		<div>Номер объекта</div>
		<div></div>
	</div>
	<div data-id="0">
		<div>
			<div _description="short straight">video</div>
		</div>
		<div>
			<div _description="short straight">2</div>
		</div>
		<div>
			<button>Удалить</button>
		</div>
	</div>
	<div data-id="1">
		<div>
			<div _description="short straight">video_1</div>
		</div>
		<div>
			<div _description="short straight">2</div>
		</div>
		<div>
			<button>По умолчанию</button>
			<button>Удалить</button>
		</div>
	</div>
	<div data-id="2">
		<div>
			<div _description="short straight">audio</div>
		</div>
		<div>
			<div _description="short straight">3</div>
		</div>
		<div>
			<button>Удалить</button>
		</div>
	</div>
	<div data-id="3">
		<div>
			<div _description="short straight">images</div>
		</div>
		<div>
			<div _description="short straight">4</div>
		</div>
		<div>
			<button>Удалить</button>
		</div>
	</div>
	<div footer_ id="addRow">
		<div>
			<a onclick="document.getElementById('addRow').style.display = 'none'; document.getElementById('editRow').removeAttribute('style');"><u>Создать</u></a>
		</div>
		<div></div>
		<div></div>
	</div>
	<div footer_ id="editRow" style="display: none;">
		<div>
			<input size_="big" type="text" placeholder="object">
		</div>
		<div>
			<input size_="big" type="text" placeholder="123456">
		</div>
		<div>
			<button>Сохранить</button>
			<button onclick="document.getElementById('editRow').style.display = 'none'; document.getElementById('addRow').removeAttribute('style');">Отменить</button>
		</div>
	</div>
</div>