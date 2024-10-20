<?
	// User session

	class Session {
		protected static $user;
		protected static $title;
		protected static $notifications_count;
		protected static $settings;
		protected static $menu_items;

		public static function createPasswordHash($password) {
			return password_hash($password, PASSWORD_DEFAULT);
		}

		public static function verifyPasswordHash($password, $hash) {
			return password_verify($password, $hash);
		}

		public static function login($login = '', $password = '') {
			global $connection;

			$sql = "SELECT o.id, s_1.value
					FROM objects AS o
					JOIN settings AS s_0 ON s_0.object_id = o.id AND s_0.key = 'login' AND s_0.value = '$login'
					JOIN settings AS s_1 ON s_1.object_id = o.id AND s_1.key = 'password_hash'";
			$query = mysqli_query($connection, $sql);

			if($query->num_rows > 0) {
				$row = $query->fetch_assoc();
				$hash = $row['value'];

				if(self::verifyPasswordHash($password, $hash)) {
					$_SESSION['user_id'] = $row['id'];

					return true;
				}
			}

			return false;
		}

		public static function logout() {
			session_unset();
		}

		public static function set() {
			// TODO: Logout if user has been deleted

			return isset($_SESSION['user_id']);
		}

		public static function getUserID() {
			return $_SESSION['user_id'] ?? null;
		}

		public static function getUser() {
			if(!empty(self::$user)) {
				return self::$user;
			}
			if(!self::set()) {
				return;
			}

			try {
				self::$user = new Object_(self::getUserID());
			} catch(Exception $e) {}

			return self::$user;
		}

		public static function getTitle() {
			if(!empty(self::$title)) {
				return self::$title;
			}
			if(!self::set()) {
				return '';
			}

			global $connection;

			$user_id = $_SESSION['user_id'];
			$sql = "SELECT title FROM objects WHERE id = $user_id";
			$query = mysqli_query($connection, $sql);
			self::$title = '';

			if($query->num_rows > 0) {
				self::$title = $query->fetch_column();
			}

			return self::$title;
		}

		public static function getNotificationsCount() {
			if(!empty(self::$notifications_count)) {
				return self::$notifications_count;
			}
			if(!self::set()) {
				return 0;
			}

			global $connection;

			$user_id = $_SESSION['user_id'];
			$sql = "SELECT COUNT(*) FROM links WHERE to_id = $user_id AND type_id = 3";
			$query = mysqli_query($connection, $sql);
			self::$notifications_count = 0;

			if($query->num_rows > 0) {
				self::$notifications_count = $query->fetch_column();
			}

			return self::$notifications_count;
		}

		public static function getSettings() {
			return Object_::getFilteredSettings(self::getUser());
		}

		public static function getSetting($key) {
			return Object_::getFilteredSetting(self::getUser(), $key);
		}

		public static function getMenuItems() {
			if(!empty(self::$menu_items)) {
				return self::$menu_items;
			}
			if(!self::set()) {
				return [];
			}

			global $connection;

			$user_id = $_SESSION['user_id'];
			$sql = "SELECT url, title FROM menu_items WHERE user_id = $user_id";
			$query = mysqli_query($connection, $sql);
			self::$menu_items = [];

			while($row = $query->fetch_assoc()) {
				self::$menu_items[] = $row;
			}

			return self::$menu_items;
		}
	}
?>