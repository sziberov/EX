<?
	$page_title = D['title_menu_items'];
?>
<div _title><?= D['title_menu_items']; ?></div>
<div _table="list" wide_ style="--columns: repeat(3, minmax(96px, auto));">
	<div header_>
		<div>Пункт</div>
		<div>URL</div>
		<div></div>
	</div>
	<div>
		<div>
			<div _description="short straight">Видео</div>
		</div>
		<div>
			<div _description="short straight">video</div>
		</div>
		<div>
			<button>↓</button>
			<button><?= D['button_remove']; ?></button>
		</div>
	</div>
	<div>
		<div>
			<div _description="short straight">Аудио</div>
		</div>
		<div>
			<div _description="short straight">audio</div>
		</div>
		<div>
			<button>↑</button>
			<button>↓</button>
			<button><?= D['button_remove']; ?></button>
		</div>
	</div>
	<div>
		<div>
			<div _description="short straight">Изображения</div>
		</div>
		<div>
			<div _description="short straight">images</div>
		</div>
		<div>
			<button>↑</button>
			<button><?= D['button_remove']; ?></button>
		</div>
	</div>
	<div footer_ switch_="current" data-switch="menu">
		<div>
			<a data-switch-ref="menu"><u><?= D['link_add']; ?></u></a>
		</div>
		<div></div>
		<div></div>
	</div>
	<div footer_ switch_ data-switch="menu">
		<div>
			<input size_="big" type="text" placeholder="<?= D['string_title']; ?>">
		</div>
		<div>
			<input size_="large" type="text" placeholder="123456">
		</div>
		<div>
			<button><?= D['button_save']; ?></button>
			<button data-switch-ref="menu"><?= D['button_cancel']; ?></button>
		</div>
	</div>
</div>