<?
	include 'app/main.php';

	if(
		$_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0 ||
		isset($_SERVER['CONTENT_LENGTH']) && (int)$_SERVER['CONTENT_LENGTH'] > (1024*1024*(int) ini_get('post_max_size'))
	) {
		exit; //(json_encode(['upload_id' => random_int(-16384, -1), 'status' => 'failed', 'message' => 'File size too big']));
	}

	db_openConnection();

	// TODO: Session, session close garbage collect, shared objects

	$user_id = session_getUserID();
	$fs = fs_get();

	if(empty($fs)) {
		exit(json_encode(['upload_id' => random_int(-16384, -1), 'status' => 'failed', 'Server ('.fs_getDomain().') not found in DB']));
	}

	$upload_id = $_POST['upload_id'] ?? null;
	$upload_offset = $_POST['upload_offset'] ?? null;
	$file_block = $_FILES['file_block'] ?? null;

	if(empty($upload_id)) {
		$object_id = $_POST['object_id'] ?? null;
		$file_size = $_POST['file_size'] ?? null;
		$file_edit_time = $_POST['file_edit_time'] ?? null;
		$file_md5 = $_POST['file_md5'] ?? null;
		$upload_title = $_POST['upload_title'] ?? null;

		if(empty($object_id) || empty($file_size) || empty($file_edit_time) || empty($upload_title)) {
			exit(json_encode(['upload_id' => random_int(-16384, -1), 'status' => 'failed', 'message' => 'Missing required parameters']));
		}

		/*
		try {
			$object = new Object_($object_id);
		} catch(Exception $e) {
			exit(json_encode(['upload_id' => random_int(-16384, -1), 'status' => 'failed', 'message' => 'Object not found']));
		}
		*/

		$existing_file = $file_md5 ? file_getByMD5($file_md5) : null;													// Аналогичный файл [не] существует
		$existing_upload = $existing_file ? upload_get($object_id, $existing_file->id, $upload_title)						// Аналогичная загрузка (аналогичного файла) [не] существует
										  : upload_find($object_id, $file_size, $file_edit_time, $upload_title);			// Похожая загрузка (похожего файла) [не] существует

		$file_id = $existing_file->file_id ?? $existing_upload->file_id ?? file_createID($file_size, $file_edit_time);	// Использовать аналогичный, похожий или (создать) новый файл
		$upload_id = $existing_upload->id ?? upload_createID($object_id, $file_id, $upload_title);						// Использовать аналогичную, похожую или (создать) новую загрузку

		if(!fs_file_get($fs->id, $file_id)) {																			// Связь аналогичного файла с текущим сервером не существует
			fs_file_createID($fs->id, $file_id);																			// Создать связь аналогичного файла с текущим сервером
		}
	}

	$upload = upload_getByID($upload_id);

	if(empty($upload)) {
		exit(json_encode(['upload_id' => $upload_id, 'status' => 'failed', 'message' => 'Upload not found']));
	}

	$fs_file = fs_file_get($fs->id, $upload->file_id);
	$file = file_getByID($upload->file_id);

	if(empty($fs_file)) {
		exit(json_encode(['upload_id' => $upload->id, 'status' => 'failed', 'message' => 'File not found on this server']));
	}
	if(empty($file)) {
		exit(json_encode(['upload_id' => $upload->id, 'status' => 'failed', 'message' => 'File not found']));  // Should not occur
	}
	if($fs_file->upload_offset >= $file->size && !empty($file->md5)) {
		exit(json_encode(['upload_id' => $upload->id, 'status' => 'finished']));
	}
	if(empty($file_block)) {
		exit(json_encode(['upload_id' => $upload->id, 'status' => 'progressing', 'upload_id' => $upload->id, 'size' => $file->size, 'upload_offset' => $fs_file->upload_offset]));
	}

	$file_path = "storage/$file->id";
	$file_handle = fopen($file_path, 'c');
	$upload_offset ??= $fs_file->upload_offset;
	fseek($file_handle, $upload_offset);
	$file_block_max_size = max(0, $file->size-$fs_file->upload_offset);
	$file_block_data = substr(file_get_contents($file_block['tmp_name']), 0, $file_block_max_size);
	$file_block_size = fwrite($file_handle, $file_block_data);
	fclose($file_handle);
	$fs_file->upload_offset = max($fs_file->upload_offset, $upload_offset+$file_block_size);
	fs_file_setGreaterOffset($fs->id, $file->id, $fs_file->upload_offset);

	if($fs_file->upload_offset < $file->size) {
		exit(json_encode(['upload_id' => $upload->id, 'status' => 'progressing', 'upload_id' => $upload->id, 'size' => $file->size, 'upload_offset' => $fs_file->upload_offset]));
	}

	set_time_limit(240);
	$file->md5 = md5_file($file_path);

	if(($existing_file = file_getByMD5($file->md5)) && $existing_file->id != $file->id) {			// Аналогичный файл существует и он не текущий
		if($existing_upload = upload_get($upload->object_id, $existing_file->id, $upload->title)) {		// Аналогичная загрузка (аналогичного файла) существует
			if($existing_upload->id != $upload->id) {														// Аналогичная загрузка (аналогичного файла) не текущая
				upload_destroyID($upload->id);																	// Удалить текущую загрузку (текущего файла)
			}
		} else {																						// Аналогичная загрузка (аналогичного файла) не существует
			upload_setFileID($upload->id, $existing_file->id);												// Изменить текущую загрузку (текущего файла) (привязать аналогичный файл)
		}

		if($existing_fs_file = fs_file_get($fs->id, $existing_file->id)) {								// Связь аналогичного файла с текущим сервером существует
			if($existing_fs_file->id != $fs_file->id) {														// Связь аналогичного файла с текущим сервером не текущая
			    fs_file_destroy($fs->id, $file->id);															// Удалить текущую связь (текущего файла) с текущим сервером

				if($existing_fs_file->upload_offset >= $existing_file->size) {									// Аналогичный файл полностью загружен на текущем сервере
					unlink($file_path);																				// Удалить текущий файл с текущего сервера (физически)
				} else {																						// Аналогичный файл не полностью загружен на текущем сервере
				    rename($file_path, "storage/$existing_file->id");												// Переименовать текущий файл на текущем сервере в аналогичный (перезаписать аналогичный файл)
					fs_file_setGreaterOffset($fs->id, $existing_file->id, $existing_file->size);					// Изменить связь аналогичного файла с текущим сервером (установить смещение загрузки в конец)
				}
			}
		} else {																						// Связь аналогичного файла с текущим сервером не существует
			rename($file_path, "storage/$existing_file->id");												// Переименовать текущий файл на текущем сервере в аналогичный
			fs_file_setFileID($fs->id, $file->id, $existing_file->id);										// Изменить текущую связь (текущего файла) с текущим сервером (привязать аналогичный файл)
		}

		file_destroyID($file->id);																		// Удалить текущий файл
	} else {																						// Аналогичный файл не существует или он текущий
		file_setMD5($file->id, $file->md5);																// Записать хеш текущего файла

		// TODO: Meta
	}

	echo json_encode(['upload_id' => $upload->id, 'status' => 'finished']);
?>