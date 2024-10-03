<div _table="list" wide_ switch_ id="uploads" style="--columns: repeat(4, minmax(96px, auto));" data-sync="files_list">
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
	<div fallback_>
		<?
			$file_servers = [];

			foreach($object->file_servers as $fs) {
				$file_servers[$fs->id] = 'FS'.$fs->id;
			}

			echo implode(', ', $file_servers);
		?>
	</div>
	<div fallback_>
		<? if($object->type_id == 3) { ?>
			Для создания фотоальбома используйте <a href="/upload/<?= $object->id; ?>"><u>загрузку файлов в раздел</u></a>
		<? } else { ?>
			Для загрузки нескольких файлов используйте Ctrl и Shift при выделении
		<? } ?>
	</div>
</div>
<script>
	let objectID = <?= $object->id; ?>,
		blockSize = 1024*1024*16, // 16MiB
		FSDomain;

	fetch('/r_upload')
	.then(response => response.text())
	.then(data => {
		FSDomain = data;
	});

	$('#upload').on('change', function() {
		for(let file of this.files) {
			let upload,
				uploadID,
				uploadOffset;

			function uploadBlock() {
				let formData = new FormData();

				if(uploadID == null) {
					formData.append('object_id', objectID);
					formData.append('file_size', file.size);
					formData.append('file_edit_time', Number.parseInt(file.lastModified/1000));
					formData.append('upload_title', file.name);
				} else
				if(uploadOffset == null) {
					formData.append('upload_id', uploadID);
				} else {
					let fileBlock = file.slice(uploadOffset, uploadOffset+blockSize);

					formData.append('upload_id', uploadID);
					formData.append('upload_offset', uploadOffset);
					formData.append('file_block', fileBlock);
				}

				fetch(FSDomain+'/upload', {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					uploadID = data.upload_id;

					if(uploadID != null) {
						upload ??= $('[data-upload-id='+uploadID+']');

						if(upload.length === 0) {
							upload = $(`
								<div data-upload-id="${uploadID}">
									<div>
										<div _description="short straight">${file.name}</div>
									</div>
									<div>
										<div upload_></div>
									</div>
									<div>${file.size.toLocaleString()}</div>
									<div>
										<button><?= D['button_cancel']; ?></button>
									</div>
								</div>
							`).appendTo($('#uploads'));
						}
					}

					if(data.status === 'progressing') {
						uploadOffset = data.upload_offset*1;

						upload?.find('[upload_]').attr('upload_', data.status).text(Math.round(uploadOffset/data.size*100)+'%');

						if(uploadOffset <= data.size) {
							uploadBlock();
						}
					} else
					if(data.status === 'finished') {
						upload?.find('[upload_]').attr('upload_', data.status).text('Загружено');
					} else
					if(data.status === 'failed') {
						upload?.find('[upload_]').attr('upload_', data.status).text('Ошибка: '+data.message);
					}
				})
				.catch(error => {
					upload?.find('[upload_]').attr('upload_', 'failed').text('Ошибка: '+error.message);
				});
			}

			uploadBlock();
		}
	});
</script>