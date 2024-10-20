<?
	// Entities

	function object_get($object_id) {
		global $connection;

		if(filter_var($object_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "SELECT * FROM objects WHERE id = $object_id";
		$query = $connection->query($sql);

		if($query->num_rows > 0) {
			return $query->fetch_object();
		}
	}

	function object_getAccessLevelID($object_id, $user_id = -1) {
		global $connection;

		if(
			filter_var($object_id, FILTER_VALIDATE_INT) === false ||
			filter_var($user_id, FILTER_VALIDATE_INT) === false
		) {
			return;
		}

		$sql = "SELECT o_0.user_id, o_0.type_id, o_1.id IS NOT NULL AS user_exists, s_0.value AS awaiting_save, s_1.value AS allow_max_access_ignoring_groups
				FROM objects AS o_0
		   LEFT JOIN objects AS o_1 ON o_1.id = $user_id
		   LEFT JOIN settings AS s_0 ON s_0.object_id = o_0.id AND s_0.`key` = 'awaiting_save' AND s_0.value = 'true'
		   LEFT JOIN settings AS s_1 ON s_1.user_id = o_1.id AND s_1.`key` = 'allow_max_access_ignoring_groups' AND s_1.value = 'true'
				WHERE o_0.id = $object_id";
		$query = $connection->query($sql);

		if($query->num_rows == 0) {
			return 0;
		}

		$result = $query->fetch_object();

		if($result->type_id == 4 || $result->allow_max_access_ignoring_groups == 'true') {
			return 5;
		}
		if((!$result->user_exists || $user_id != $result->user_id) && $result->awaiting_save == 'true') {
			return 0;
		}

		$sql = "SELECT l.from_id, l.to_id, s_0.value AS access_level_id, s_1.value AS allow_higher_access_preference
				FROM links AS l
				JOIN objects AS o_0 ON o_0.id = l.from_id
				JOIN objects AS o_1 ON o_1.id = l.to_id
				JOIN settings AS s_0 ON s_0.link_id = l.id AND s_0.key = 'access_level_id'
		   LEFT JOIN settings AS s_1 ON s_1.link_id = l.id AND s_1.key = 'allow_higher_access_preference'
				WHERE (l.from_id = $user_id AND o_0.type_id = 2 AND o_1.type_id = 1
				   OR  l.to_id = $object_id AND o_0.type_id = 1)
				  AND l.type_id = 1
				ORDER BY l.from_id = $user_id DESC,
						 l.to_id = $object_id DESC";
		$query = $connection->query($sql);
		$uga_links = [
			1 => ['access_level_id' => 2]
		];
		$access_level_ids = [0];

		foreach($query->fetch_all(MYSQLI_ASSOC) as $row) {
			if($row['from_id'] == $user_id) {
				$uga_links[$row['to_id']] = $row;  // user_group_access
			} else
			if($row['to_id'] == $object_id) {
				$goa = $row;  // group_object_access
				$uga = $uga_links[$goa['from_id']] ?? null;

				if(!empty($uga)) {
					$access_level_ids[] = filter_var($uga['allow_higher_access_preference'] ?? null, FILTER_VALIDATE_BOOLEAN)
										? max($uga['access_level_id'], $goa['access_level_id'])
										: min($uga['access_level_id'], $goa['access_level_id']);
				}
			}
		}

		return max($access_level_ids);
	}

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
		$query = $connection->query($sql);

		if($query->num_rows > 0) {
			return $query->fetch_object();
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

		return $connection->query($sql);
	}

	function file_getByID($file_id) {
		global $connection;

		if(filter_var($file_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "SELECT * FROM files WHERE id = $file_id";
		$query = $connection->query($sql);

		if($query->num_rows > 0) {
			return $query->fetch_object();
		}
	}

	function file_getByMD5($md5) {
		global $connection;

		$sql = "SELECT * FROM files WHERE md5 = '$md5'";
		$query = $connection->query($sql);

		if($query->num_rows > 0) {
			return $query->fetch_object();
		}
	}

	function file_setMD5($file_id, $md5) {
		global $connection;

		if(filter_var($file_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "UPDATE files SET md5 = '$md5' WHERE id = $file_id";

		return $connection->query($sql);
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
		$query = $connection->query($sql);

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

		return $connection->query($sql);
	}

	function meta_get($file_id) {
		global $connection;

		if(filter_var($file_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "SELECT width, height, length, latitude, longitude, mime_type FROM meta WHERE file_id = $file_id";
		$query = $connection->query($sql);

		if($query->num_rows > 0) {
			return $query->fetch_object();
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
		$query = $connection->query($sql);

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

		return $connection->query($sql);
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

		return $connection->query($sql);
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

		return $connection->query($sql);
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
		$query = $connection->query($sql);

		if($query->num_rows > 0) {
			return $query->fetch_object();
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
		$query = $connection->query($sql);

		if($query->num_rows > 0) {
			return $query->fetch_object();
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
		$query = $connection->query($sql);

		if($query->num_rows > 0) {
			return $query->fetch_object();
		}
	}

	function upload_getByID($upload_id) {
		global $connection;

		if(filter_var($upload_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "SELECT * FROM uploads WHERE id = $upload_id";
		$query = $connection->query($sql);

		if($query->num_rows > 0) {
			return $query->fetch_object();
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
		$query = $connection->query($sql);

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

		return $connection->query($sql);
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

		return $connection->query($sql);
	}

	function upload_appendCounter($upload_id) {
		global $connection;

		if(filter_var($upload_id, FILTER_VALIDATE_INT) === false) {
			return;
		}

		$sql = "UPDATE uploads SET downloads_count = downloads_count+1 WHERE id = $upload_id";

		return $connection->query($sql);
	}
?>