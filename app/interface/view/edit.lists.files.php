<script>
	let objectID = <?= $object->id; ?>;
</script>
<script src="/app/plugin/uploads.js"></script>
<div _table="list" small_ switch_ id="uploads" style="--columns: repeat(4, minmax(96px, auto));" data-sync="files_list">
	<div header_>
		<div>
			<select data-sync-ref="list">
				<option value="files_list"><?= D['string_files']; ?></option>
			</select>
		</div>
	</div>
	<? foreach($object->uploads as $upload) {
		$file = $upload->file;

		if(!in_array($file->size, $file->upload_offsets)) {
			continue;
		}

		$mime_type = $file->mime_type ?? '';
	?>
		<div data-upload-id="<?= $upload->id; ?>">
			<div>
				<div _description="short straight"><?= e($upload->title); ?></div>
			</div>
			<div>
				<? if(str_starts_with($mime_type, 'image/')) { ?>
					<img _image src="/get/<?= $upload->id; ?>">
				<? } else { ?>
					<div upload_="finished">Загружено</div>
				<? } ?>
			</div>
			<div><?= template_formatSize($file->size); ?></div>
			<div>
				<? if(str_starts_with($mime_type, 'image/')) { ?>
					<? if($upload->id != $object->poster->id) { ?>
						<button>✓</button>
					<? } ?>
					<? if(!str_ends_with($mime_type, '/svg+xml')) { ?>
						<button>↺</button>
						<button>↻</button>
					<? } ?>
				<? } ?>
				<a _button href="/copy/<?= $upload->id; ?>"><?= D['button_copy']; ?></a>
				<button><?= D['button_delete']; ?></button>
			</div>
		</div>
	<? } ?>
</div>
<div _grid="h spaced" switch_ data-sync="files_list">
	<label _file>
		<input type="file" id="upload" name="files[]" multiple="multiple">
		<a><u><?= D['link_upload']; ?></u></a>
	</label>
	<small fallback_>
		<?
			$file_servers = [];

			foreach($object->file_servers as $fs) {
				$file_servers[$fs->id] = 'FS'.$fs->id;
			}

			echo implode(', ', $file_servers);
		?>
	</small>
	<small fallback_>
		<? if($object->type_id == 3) { ?>
			Для создания фотоальбома используйте <a href="/upload/<?= $object->id; ?>"><u>загрузку файлов в раздел</u></a>
		<? } else { ?>
			Для загрузки нескольких файлов используйте Ctrl и Shift при выделении
		<? } ?>
	</small>
</div>