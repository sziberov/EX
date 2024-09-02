<?
	// User session

	function session_createPasswordHash($password) {
		return password_hash($password, PASSWORD_DEFAULT);
	}

	function session_verifyPasswordHash($password, $hash) {
		return password_verify($password, $hash);
	}

	function session($action = null, $login = '', $password = '') {
		if(in_array($action, ['login', 'logout'])) {
			global $connection;

			if($action == 'login') {
				$sql = "SELECT o.id, s_1.value
						FROM objects AS o
						JOIN settings AS s_0 ON s_0.object_id = o.id AND s_0.key = 'login' AND s_0.value = '$login'
						JOIN settings AS s_1 ON s_1.object_id = o.id AND s_1.key = 'password_hash'";
				$query = mysqli_query($connection, $sql);

				if(mysqli_num_rows($query) > 0) {
					$row = mysqli_fetch_assoc($query);
					$hash = $row['value'];

					if(session_verifyPasswordHash($password, $hash)) {
						$_SESSION['user_id'] = $row['id'];
					}
				}
			}
			if($action == 'logout') {
				session_unset();
			}
		}

		// TODO: Logout if user has been deleted

		return isset($_SESSION['user_id']);
	}

	function session_getUserID() {
		return $_SESSION['user_id'] ?? null;
	}

	function session_getNotificationsCount() {
		if(!session()) {
			return 0;
		}

		global $connection;

		$user_id = $_SESSION['user_id'];
		$sql = "SELECT COUNT(*) AS count FROM links AS l WHERE l.to_id = $user_id AND l.type_id = 3";
		$query = mysqli_query($connection, $sql);
		$notifications_count = 0;

		if(mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_assoc($query);
			$notifications_count = $row['count'];
		}

		return $notifications_count;
	}

	function session_getSettings() {
		return object_getSettings(session_getUserID());
	}
?>