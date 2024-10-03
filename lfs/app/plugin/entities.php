<?
	// Entities

	function fs_getDomain() {
		$ip = $_SERVER['SERVER_ADDR'];  // $_SERVER['HTTP_HOST'];
		$request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$request_file = rtrim($request_path, $_SERVER['PATH_INFO'] ?? '');
		$request_folder = dirname($request_file);
		$domain = $ip.$request_folder;

		return $domain;
	}

	function fs_get() {
		global $connection;

		$sql = "SELECT * FROM fs WHERE domain = '".fs_getDomain()."'";
		$query = mysqli_query($connection, $sql);

		if(mysqli_num_rows($query) > 0) {
			return mysqli_fetch_object($query);
		}
	}

	function fs_setSize($fs_id, $used_size, $free_size) {
		global $connection;

		if(
			filter_var($fs_id, FILTER_VALIDATE_INT) === false ||
			filter_var($used_size, FILTER_VALIDATE_INT) === false ||
			filter_var($free_size, FILTER_VALIDATE_INT) === false
		) {
			return;
		}

		$sql = "UPDATE fs SET used_size = $used_size, free_size = $free_size WHERE id = $fs_id";

		return mysqli_query($connection, $sql);
	}

	function file_getByID($file_id) {
		global $connection;

		if(filter_var($file_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "SELECT * FROM files WHERE id = $file_id";
		$query = mysqli_query($connection, $sql);

		if(mysqli_num_rows($query) > 0) {
			return mysqli_fetch_object($query);
		}
	}

	function file_getByMD5($md5) {
		global $connection;

		$sql = "SELECT * FROM files WHERE md5 = '$md5'";
		$query = mysqli_query($connection, $sql);

		if(mysqli_num_rows($query) > 0) {
			return mysqli_fetch_object($query);
		}
	}

	function file_setMD5($file_id, $md5) {
		global $connection;

		if(filter_var($file_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "UPDATE files SET md5 = '$md5' WHERE id = $file_id";

		return mysqli_query($connection, $sql);
	}

	function file_createID($size, $edit_time) {
		global $connection;

		if(
			filter_var($size, FILTER_VALIDATE_INT) === false ||
			filter_var($edit_time, FILTER_VALIDATE_INT) === false
		) {
			return;
		}

		$sql = "INSERT INTO files (size, edit_time) VALUES ($size, from_unixtime($edit_time))";
		$query = mysqli_query($connection, $sql);

		if($query) {
			return mysqli_insert_id($connection);
		}
	}

	function file_destroyID($file_id) {
		global $connection;

		if(filter_var($file_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "DELETE FROM files WHERE id = $file_id";

		return mysqli_query($connection, $sql);
	}

	function meta_get($file_id) {
		global $connection;

		if(filter_var($file_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "SELECT width, height, length, latitude, longitude, mime_type FROM meta WHERE file_id = $file_id";
		$query = mysqli_query($connection, $sql);

		if(mysqli_num_rows($query) > 0) {
			return mysqli_fetch_object($query);
		}
	}

	function fs_file_createID($fs_id, $file_id, $offset = null) {
		global $connection;

		if(
			filter_var($fs_id, FILTER_VALIDATE_INT) === false ||
			filter_var($file_id, FILTER_VALIDATE_INT) === false ||
			!empty($offset) && filter_var($offset, FILTER_VALIDATE_INT) === false
		) {
			return;
		}

		$sql = "INSERT INTO fs_files (fs_id, file_id".(!empty($offset) ? ', offset' : '').") VALUES ($fs_id, $file_id".(!empty($offset) ? ", $offset" : '').")";
		$query = mysqli_query($connection, $sql);

		if($query) {
			return mysqli_insert_id($connection);
		}
	}

	function fs_file_setFileID($fs_id, $file_id, $file_id_) {
		global $connection;

		if(
			filter_var($fs_id, FILTER_VALIDATE_INT) === false ||
			filter_var($file_id, FILTER_VALIDATE_INT) === false ||
			filter_var($file_id_, FILTER_VALIDATE_INT) === false
		) {
			return;
		}

		$sql = "UPDATE fs_files SET file_id = $file_id_ WHERE fs_id = $fs_id AND file_id = $file_id";

		return mysqli_query($connection, $sql);
	}

	function fs_file_setGreaterOffset($fs_id, $file_id, $offset) {
		global $connection;

		if(
			filter_var($fs_id, FILTER_VALIDATE_INT) === false ||
			filter_var($file_id, FILTER_VALIDATE_INT) === false ||
			filter_var($offset, FILTER_VALIDATE_INT) === false
		) {
			return;
		}

		$sql = "UPDATE fs_files SET upload_offset = $offset WHERE fs_id = $fs_id AND file_id = $file_id AND upload_offset < $offset";

		return mysqli_query($connection, $sql);
	}

	function fs_file_destroy($fs_id, $file_id) {
		global $connection;

		if(
			filter_var($fs_id, FILTER_VALIDATE_INT) === false ||
			filter_var($file_id, FILTER_VALIDATE_INT) === false
		) {
			return;
		}

		$sql = "DELETE FROM fs_files WHERE fs_id = $fs_id AND file_id = $file_id";

		return mysqli_query($connection, $sql);
	}

	function fs_file_get($fs_id, $file_id) {
		global $connection;

		if(
			filter_var($fs_id, FILTER_VALIDATE_INT) === false ||
			filter_var($file_id, FILTER_VALIDATE_INT) === false
		) {
			return;
		}

		$sql = "SELECT * FROM fs_files WHERE fs_id = $fs_id AND file_id = $file_id";
		$query = mysqli_query($connection, $sql);

		if(mysqli_num_rows($query) > 0) {
			return mysqli_fetch_object($query);
		}
	}

	function upload_get($object_id, $file_id, $title) {
		global $connection;

		if(
			filter_var($object_id, FILTER_VALIDATE_INT) === false ||
			filter_var($file_id, FILTER_VALIDATE_INT) === false
		) {
			return;
		}

		$sql = "SELECT * FROM uploads WHERE object_id = $object_id AND file_id = $file_id AND title = '$title'";
		$query = mysqli_query($connection, $sql);

		if(mysqli_num_rows($query) > 0) {
			return mysqli_fetch_object($query);
		}
	}

	function upload_find($object_id, $file_size, $file_edit_time, $title) {
		global $connection;

		if(
			filter_var($object_id, FILTER_VALIDATE_INT) === false ||
			filter_var($file_size, FILTER_VALIDATE_INT) === false ||
			filter_var($file_edit_time, FILTER_VALIDATE_INT) === false
		) {
			return;
		}

		$sql = "SELECT u.* FROM uploads AS u
				JOIN files AS f ON f.id = u.file_id AND f.size = $file_size AND f.edit_time = from_unixtime($file_edit_time)
				WHERE u.object_id = $object_id AND u.title = '$title'";
		$query = mysqli_query($connection, $sql);

		if(mysqli_num_rows($query) > 0) {
			return mysqli_fetch_object($query);
		}
	}

	function upload_getByID($upload_id) {
		global $connection;

		if(filter_var($upload_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "SELECT * FROM uploads WHERE id = $upload_id";
		$query = mysqli_query($connection, $sql);

		if(mysqli_num_rows($query) > 0) {
			return mysqli_fetch_object($query);
		}
	}

	function upload_createID($object_id, $file_id, $title) {
		global $connection;

		if(
			filter_var($object_id, FILTER_VALIDATE_INT) === false ||
			filter_var($file_id, FILTER_VALIDATE_INT) === false
		) {
			return;
		}

		$sql = "INSERT INTO uploads (object_id, file_id, title) VALUES ($object_id, $file_id, '$title')";
		$query = mysqli_query($connection, $sql);

		if($query) {
			return mysqli_insert_id($connection);
		}
	}

	function upload_destroyID($upload_id) {
		global $connection;

		if(filter_var($upload_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "DELETE FROM uploads WHERE id = $upload_id";

		return mysqli_query($connection, $sql);
	}

	function upload_setFileID($upload_id, $new_file_id) {
		global $connection;

		if(
			filter_var($upload_id, FILTER_VALIDATE_INT) === false ||
			filter_var($new_file_id, FILTER_VALIDATE_INT) === false
		) {
			return;
		}

		$sql = "UPDATE uploads SET file_id = $new_file_id WHERE id = $upload_id";

		return mysqli_query($connection, $sql);
	}

	function upload_appendCounter($upload_id) {
		global $connection;

		if(filter_var($upload_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "UPDATE uploads SET downloads_count = downloads_count+1 WHERE id = $upload_id";

		return mysqli_query($connection, $sql);
	}
?>