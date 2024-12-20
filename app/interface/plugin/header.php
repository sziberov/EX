<header>
	<section>
		<? include 'menus.php'; ?>
	</section>
	<? if($page == '') { ?>
		<section>
			<div _grid="padded">
				<div _grid="v">
					<div _title="small"><?= template_parseBB(D['string_shared_objects_title']); ?></div>
					<div><?= template_parseBB(D['string_shared_objects_description']); ?></div>
				</div>
				<div _flex="h" __get>
					<a _button primary_ href="/create?type_id=4"><?= D['button_create']; ?></a>
					<div _flex="stacked">
						<input size_="max" type="text" placeholder="<?= D['string_object_id']; ?>" oninput="Header.getInput(this)">
						<button primary_ onclick="Header.get(this)"><?= D['button_get']; ?></button>
					</div>
				</div>
			</div>
		</section>
		<? if(rand(0, 1)) { ?>
			<section role="ad" aria-label="advertisement">
				<a __image href="/ad/1">
					<img src="/app/image/header_image.webp">
				</a>
			</section>
		<? } ?>
	<? } ?>
</header>