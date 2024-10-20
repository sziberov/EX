$(() => {
	let blockSize = 1024*1024*16, // 16MiB
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
});