<?
	session_start();

	// Paths

	define('ROOT', $_SERVER['DOCUMENT_ROOT']);

	if(
		isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
		isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
	) {
		define('PROTOCOL', 'https://');
	} else {
		define('PROTOCOL', 'http://');
	}

	define('SERVER_NAME', $_SERVER['SERVER_NAME']);
	define('DOMAIN_ROOT', PROTOCOL.SERVER_NAME);

	// Database

	$connection;

	function db_openConnection() {
		global $connection;

		$domain = 'localhost';
		$user = 'root';
		$password = '';
		$title = 'ex';
		$connection = new mysqli($domain, $user, $password, $title) or die('Connect failed: %s\n'.$connection->error);

		return $connection;
	}

	function db_closeConnection() {
		global $connection;

		$connection->close();
	}

	// HTTP Requests

	// json_decode(request_get(DOMAIN_ROOT.'/app/api/objects.php?'.$filters.($navigation ? '&index='.$navigation_index.'&per='.$navigation_per : '')), true)
	function request_get($url) {
		if(!headers_sent()) session_write_close(); // Preserve session
		$curl = curl_init($url);
		if(!headers_sent()) curl_setopt($curl, CURLOPT_COOKIE, session_name().'='.session_id()); // Preserve session
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // For HTTPS
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // For HTTPS
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);
		if(!headers_sent()) session_start(); // Preserve session

		return $response;
	}

	function request_post($url, $data) {
		if(!headers_sent()) session_write_close(); // Preserve session
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		if(!headers_sent()) curl_setopt($curl, CURLOPT_COOKIE, session_name().'='.session_id()); // Preserve session
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // For HTTPS
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // For HTTPS
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);
		if(!headers_sent()) session_start(); // Preserve session

		return $response;
	}

	// Dictionary

	$languages = [
		'ru' => 'russian',
		'ng' => 'nizkagorian',
		'en' => 'english'
	];

	$language = $_COOKIE['language'];

	$strings;

	function dictionary_parseDefaultLanguage($http_accept, $default = 'ru') {
	   if(isset($http_accept) && strlen($http_accept) > 1)  {
			$x = explode(",", $http_accept);
			foreach($x as $val) {
				if(preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i",$val,$matches)) {
					$lang[$matches[1]] = (float)$matches[2];
				} else {
					$lang[$val] = 1.0;
				}
			}

			$qval = 0.0;
			foreach($lang as $key => $value) {
				if($value > $qval) {
					$qval = (float)$value;
					$default = $key;
				}
			}
	   }

	   return strtolower($default);
	}

	function dictionary_getDefaultLanguage() {
		return dictionary_parseDefaultLanguage($_SERVER["HTTP_ACCEPT_LANGUAGE"] ?? NULL);
	}

	function dictionary_setLanguage($language) {
		global $strings;

		$strings = json_decode(request_get(DOMAIN_ROOT.'/app/template/language_'.$language.'.json'), true);
	}

	function dictionary_setDefaultLanguage() {
		global $language, $languages;

		if(array_key_exists($language, $languages)) {
			return;
		}

		$default = strstr(dictionary_parseDefaultLanguage(dictionary_getDefaultLanguage(), array_key_first($languages)), '-', true);

		if(!empty($default) && array_key_exists($default, $languages)) {
			$language = $default;
		} else {
			$language = array_key_first($languages);
		}
	}

	function dictionary_getString($string) {
		global $strings;

		return $strings[$string];
	}

	function dictionary_getPageTitle($title) {
		global $strings;

		$appTitle = $strings['string_app_title'];

		if(strlen($title) > 0) {
			return $title.' @ '.$appTitle;
		} else {
			return $appTitle;
		}
	}

	dictionary_setDefaultLanguage();
	dictionary_setLanguage($language);

	define('D', $strings);

	// Entities

	class Entity {
		protected $fields_ = [];

		public function __set($name, $value) {
			$this->fields_[$name] = $value;
		}

		public function __get($name) {
			if(isset($this->fields_[$name])) {
				return $this->fields_[$name];
			}

			$getter = 'get'.str_replace('_', '', ucwords($name, '_'));

			if(method_exists($this, $getter)) {
				return $this->$getter();
			}
		}

		public function __isset($name) {
			$getter = 'get'.str_replace('_', '', ucwords($name, '_'));

			return method_exists($this, $getter) ? !is_null($this->$getter()) : isset($this->fields_[$name]) || isset($this->$name);
		}
	}

	class Object_ extends Entity {
		protected $user_;
		protected $files_;
		protected $links_;
		protected $settings_;
		protected $friends_;
		protected $poster_;
		protected $avatar_;
		protected $counts_;
		protected $goa_links_;
		protected $uga_links_;

		public function __construct($id) {
			if(!isset($id)) {
				throw new Exception('0');
			}

			global $connection;

			$sql = "SELECT * FROM objects WHERE id = $id";
			$query = mysqli_query($connection, $sql);

			if(mysqli_num_rows($query) > 0) {
				$this->fields_ = mysqli_fetch_assoc($query);
			} else {
				throw new Exception('1');
			}

			if(empty($this->title)) {
				$this->title = $this->type_id == 2 ? ($this->login ?? D['title_no_title']) : D['title_no_title'];
			}
		}

		public function getUser() {
			if(!isset($this->user_id)) {
				return;
			}

			$this->user_ ??= new Object_($this->user_id);

			return $this->user_;
		}

		public function getFiles() {
			if(isset($this->files_)) {
				return $this->files_;
			}

			global $connection;

			$sql = "SELECT f.id, `of`.title
					FROM files AS f
					JOIN objects_files AS `of` ON `of`.object_id = $this->id AND `of`.file_id = f.id";
			$query = mysqli_query($connection, $sql);
			$this->files_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->files_[$row['title']] = new File($row['id']);
			}

			return $this->files_;
		}

		public function getLinks() {
			if(isset($this->links_)) {
				return $this->links_;
			}

			global $connection;

			$sql = "SELECT id FROM links WHERE from_id = $this->id";
			$query = mysqli_query($connection, $sql);
			$this->links_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->links_[] = new Link($row['id']);
			}

			return $this->links_;
		}

		public function getSettings() {
			if(isset($this->settings_)) {
				return $this->settings_;
			}

			global $connection;

			$sql = "SELECT id, `key` FROM settings WHERE object_id = $this->id";
			$query = mysqli_query($connection, $sql);
			$this->settings_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->settings_[$row['key']] = new Setting($row['id']);
			}

			return $this->settings_;
		}

		public function getLogin() {
			return $this->settings['login']->value ?? null;
		}

		public function getFriends() {
			if(isset($this->friends_)) {
				return $this->friends_;
			}
			if($this->type_id != 2) {
				return;
			}

			global $connection;

			$sql = "SELECT from_id FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 2";
			$query = mysqli_query($connection, $sql);
			$this->friends_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->friends_[] = new Object_($row['from_id']);
			}

			return $this->friends_;
		}

		public function getPoster() {
			if(isset($this->poster_)) {
				return $this->poster_;
			}
			if(!isset($this->settings['poster_id'])) {
				return;
			}

			$poster = new Object_($this->settings['poster_id']->value);

			if(!isset($poster)) {
				return;
			}

			$file_title = array_key_first($poster->files);
			$file = $poster->files[$file_title];

			$this->poster_ = [
				'title' => $poster->title,
				'width' => $file->settings['width'],
				'height' => $file->settings['height'],
				'url' => '/storage/'.$file_title
			];

			/*
			$file = $poster->files[array_key_first($poster->files)];

			if(!isset($file) {
				return;
			}
			*/

			return $this->poster_;
		}

		public function getAvatar() {
			if(isset($this->avatar_)) {
				return $this->avatar_;
			}
			if(!isset($this->settings['avatar_id'])) {
				return;
			}

			$avatar = new Object_($this->settings['avatar_id']->value);

			if(!isset($avatar)) {
				return;
			}

			$this->avatar_ = [
				'title' => $avatar->title,
				'url' => '/storage/'.array_key_first($avatar->files)
			];

			/*
			$file = $avatar->files[array_key_first($avatar->files)];

			if(!isset($file) {
				return;
			}
			*/

			return $this->avatar_;
		}

		protected function getLinksCount($type) {
			/*
			if(!in_array($type, ['inclusions', 'files', 'comments'])) {
				return;
			}
			*/
			if(isset($this->counts_[$type])) {
				return $this->counts_[$type];
			}
			/*
			if(in_array($type, ['inclusions', 'comments'] && $this->type_id != 4) {
				return;
			}
			*/

			global $connection;

			$sql;

			if($type == 'inclusions')			$sql = "SELECT COUNT(*) AS count FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 4";
			if($type == 'self_inclusions')		$sql = "SELECT COUNT(*) AS count FROM links AS l WHERE l.from_id = $this->id AND l.type_id = 4";
			if($type == 'files')				$sql = "SELECT COUNT(*) AS count FROM objects_files AS `of` WHERE `of`.object_id = $this->id";
			if($type == 'comments')				$sql = "SELECT COUNT(*) AS count FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 5";
			if($type == 'user_archive')			$sql = "SELECT COUNT(*) AS count FROM objects AS o WHERE o.user_id = $this->id AND o.type_id = 3";
			if($type == 'user_comments')		$sql = "SELECT COUNT(*) AS count FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 5";
			if($type == 'user_recommendations')	$sql = "SELECT COUNT(*) AS count FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 6";
			if($type == 'user_avatars')			$sql = "SELECT COUNT(*) AS count FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 7";
			if($type == 'user_claims')			$sql = "SELECT COUNT(*) AS count FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 8";
			if($type == 'user_templates')		$sql = "SELECT COUNT(*) AS count FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 9";
			if($type == 'user_drafts')			$sql = "SELECT COUNT(*) AS count FROM objects AS o WHERE o.user_id = $this->id
														JOIN settings AS s ON s.object_id = o.id AND 'awaiting_save' = 'true'";
			if($type == 'user_bookmarks')		$sql = "SELECT COUNT(*) AS count FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 10";
			if($type == 'user_groups_alien')	$sql = "SELECT COUNT(*) AS count FROM links AS l WHERE l.from_id = $this->id AND l.user_id != $this->id AND l.type_id = 1";
			if($type == 'user_groups_own')		$sql = "SELECT COUNT(*) AS count FROM links AS l WHERE l.from_id = $this->id AND l.user_id = $this->id AND l.type_id = 1";

			$query = mysqli_query($connection, $sql);
			$this->counts_ ??= [];

			if(mysqli_num_rows($query) > 0) {
				$row = mysqli_fetch_assoc($query);

				$this->counts_[$type] = $row['count'];
			}

			return $this->counts_[$type];
		}

		public function getInclusionsCount() {
			return $this->getLinksCount('inclusions');
		}

		public function getSelfInclusionsCount() {
			return $this->getLinksCount('self_inclusions');
		}

		public function getFilesCount() {
			return $this->getLinksCount('files');
		}

		public function getCommentsCount() {
			if(isset($this->counts_['comments'])) {
				return $this->counts_['comments'];
			}

			global $connection;

			$sql = "WITH RECURSIVE link_chain AS (
						SELECT from_id, to_id
						FROM links
						WHERE to_id = $this->id AND type_id = 5
						UNION ALL
						SELECT l.from_id, l.to_id
						FROM links AS l
						INNER JOIN link_chain AS lc ON lc.from_id = l.to_id
						WHERE l.type_id = 5
					)
					SELECT COUNT(*) AS count FROM link_chain";

			$query = mysqli_query($connection, $sql);
			$this->counts_ ??= [];

			if(mysqli_num_rows($query) > 0) {
				$row = mysqli_fetch_assoc($query);

				$this->counts_['comments'] = $row['count'];
			}

			return $this->counts_['comments'];
		}

		public function getArchiveCount() {
			return $this->getLinksCount('user_archive');
		}

		public function getAvatarsCount() {
			return $this->getLinksCount('user_avatars');
		}

		public function getGroupsCountAlien() {
			return $this->getLinksCount('user_groups_alien');
		}

		public function getGroupsCountOwn() {
			return $this->getLinksCount('user_groups_own');
		}

		public function getFilesSize() {
			$size = 0;

			foreach($this->files as $file) {
				$size += $file->size;
			}

			return $size;
		}

		public function getFilesLength() {
			$length = 0;

			foreach($this->files as $file) {
				$length += $file->settings['length']->value ?? 0;
			}

			return $length;
		}

		public function getGroupObjectAccessLinks() {
			if(isset($this->goa_links_)) {
				return $this->goa_links_;
			}

			global $connection;

			$sql = "SELECT l.id
					FROM links AS l
					JOIN objects AS o ON o.id = l.from_id AND o.type_id = 1
					WHERE l.to_id = $this->id AND l.type_id = 1";
			$query = mysqli_query($connection, $sql);
			$this->goa_links_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->goa_links_[] = new Link($row['id']);
			}

			return $this->goa_links_;
		}

		public function getUserGroupAccessLinks() {
			if(isset($this->uga_links_)) {
				return $this->uga_links_;
			}

			global $connection;

			$direction = $this->type_id == 1 ? 'to' : 'from';
			$sql = "SELECT l.id
					FROM links AS l
					JOIN objects AS o_0 ON o_0.id = l.from_id AND o_0.type_id = 2
					JOIN objects AS o_1 ON o_1.id = l.to_id AND o_1.type_id = 1
					WHERE l.{$direction}_id = $this->id AND l.type_id = 1";
			$query = mysqli_query($connection, $sql);
			$this->uga_links_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->uga_links_[] = new Link($row['id']);
			}

			return $this->uga_links_;
		}
	}

	class File extends Entity {
		protected $settings_;
		protected $file_systems_;

		public function __construct($id) {
			if(!isset($id)) {
				throw new Exception('0');
			}

			global $connection;

			$sql = "SELECT * FROM files WHERE id = $id";
			$query = mysqli_query($connection, $sql);

			if(mysqli_num_rows($query) > 0) {
				$this->fields_ = mysqli_fetch_assoc($query);
			} else {
				throw new Exception('1');
			}
		}

		public function getSettings() {
			if(isset($this->settings_)) {
				return $this->settings_;
			}

			global $connection;

			$sql = "SELECT id, `key` FROM settings WHERE file_id = $this->id";
			$query = mysqli_query($connection, $sql);
			$this->settings_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->settings_[$row['key']] = new Setting($row['id']);
			}

			return $this->settings_;
		}

		public function getFileSystems() {
			if(isset($this->file_systems_)) {
				return $this->file_systems_;
			}

			global $connection;

			$sql = "SELECT fs.id AS id
					FROM fs
					JOIN fs_files AS ff ON ff.fs_id = fs.id AND ff.file_id = $this->id";
			$query = mysqli_query($connection, $sql);
			$this->file_systems_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->file_systems_[] = new FileSystem($row['id']);
			}

			return $this->file_systems_;
		}
	}

	class FileSystem extends Entity {
		public function __construct($id) {
			if(!isset($id)) {
				throw new Exception('0');
			}

			global $connection;

			$sql = "SELECT * FROM fs WHERE id = $id";
			$query = mysqli_query($connection, $sql);

			if(mysqli_num_rows($query) > 0) {
				$this->fields_ = mysqli_fetch_assoc($query);
			} else {
				throw new Exception('1');
			}
		}
	}

	class Link extends Entity {
		protected $from_;
		protected $to_;
		protected $user_;
		protected $settings_;

		public function __construct($id) {
			if(!isset($id)) {
				throw new Exception('0');
			}

			global $connection;

			$sql = "SELECT * FROM links WHERE id = $id";
			$query = mysqli_query($connection, $sql);

			if(mysqli_num_rows($query) > 0) {
				$this->fields_ = mysqli_fetch_assoc($query);
			} else {
				throw new Exception('1');
			}
		}

		public function getFrom() {
			$this->from_ ??= new Object_($this->from_id);

			return $this->from_;
		}

		public function getTo() {
			$this->to_ ??= new Object_($this->to_id);

			return $this->to_;
		}

		public function getUser() {
			$this->user_ ??= new Object_($this->user_id);

			return $this->user_;
		}

		public function getSettings() {
			if(isset($this->settings_)) {
				return $this->settings_;
			}

			global $connection;

			$sql = "SELECT id, `key` FROM settings WHERE link_id = $this->id";
			$query = mysqli_query($connection, $sql);
			$this->settings_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->settings_[$row['key']] = new Setting($row['id']);
			}

			return $this->settings_;
		}
	}

	class Setting extends Entity {
		protected $user_;

		public function __construct($id) {
			if(!isset($id)) {
				throw new Exception('0');
			}

			global $connection;

			$sql = "SELECT * FROM settings WHERE id = $id";
			$query = mysqli_query($connection, $sql);

			if(mysqli_num_rows($query) > 0) {
				$this->fields_ = mysqli_fetch_assoc($query);
			} else {
				throw new Exception('1');
			}
		}

		public function getUser() {
			$this->user_ ??= new Object_($this->user_id);

			return $this->user_;
		}
	}

	function object_createID($title, $description, $type_id) {
		$user_id = session_getUserID();

		if(!isset($type_id) || $type_id < 1 || $type_id > 4) {
			return;
		}

		global $connection;

		$title = !empty($title) ? "'$title'" : 'NULL';
		$description = !empty($description) ? "'$description'" : 'NULL';
		$user_id = $type_id == 2 ? "IDENT_CURRENT('objects')" :
				  ($type_id == 4 ? 'NULL' :
				   $user_id ?? 'NULL');
		$sql = "INSERT INTO objects (title, description, user_id, creation_time, edit_time, type_id)
							 VALUES ($title, $description, $user_id, DEFAULT, DEFAULT, $type_id)";
		$query = mysqli_query($connection, $sql);

		if($query) {
			return mysqli_insert_id($connection);
		}
	}

	function link_createID($from_id, $to_id, $type_id) {
		$user_id = session_getUserID();

		if(!isset($from_id) || !isset($type_id) || $type_id < 1 || $type_id > 11) {
			return;
		}
		if(empty($user_id)) {
			return;
		}

		global $connection;

		$to_id ??= 'NULL';
		$user_id ??= 'NULL';
		$sql = "INSERT INTO links (from_id, to_id, user_id, type_id) VALUES
								  ($from_id, $to_id, $user_id, $type_id)";
		$query = mysqli_query($connection, $sql);

		if($query) {
			return mysqli_insert_id($connection);
		}
	}

	function object_createUserID($login, $password, $email) {
		if(object_getUserID($login)) {
			return;
		}

		global $connection;

		$sql = "INSERT INTO objects (type_id) VALUES -- User
									(2);
				SET @user_id = LAST_INSERT_ID();
				INSERT INTO settings (object_id, `key`, value, user_id) VALUES
									 (@user_id, 'login', '$login', @user_id),
									 (@user_id, 'password_hash', '$password_hash', @user_id),
									 (@user_id, 'email', '$email', @user_id);

				INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from group of everyone to user
								  (1, @user_id, 2, 1);
				SET @link_id = LAST_INSERT_ID();
				INSERT INTO settings (link_id, `key`, value, user_id) VALUES
									 (@link_id, 'access_level_id', '2', 2);

			--  /!\ Group of everyone should not be polluted with real members links.
			--      These can be substituted with virtual ones (see session_getAccessLevelID()),
			--      while manual overrides can be used for moderation purposes.

			--	INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from user to group of everyone
			--					  (@user_id, 1, 2, 1);
			--	SET @link_id = LAST_INSERT_ID();
			--	INSERT INTO settings (link_id, `key`, value, user_id) VALUES
			--						 (@link_id, 'access_level_id', '2', 2);

				INSERT INTO objects (title, user_id, type_id) VALUES -- Group of user
									('Group_$login', @user_id, 1);
				SET @group_id = LAST_INSERT_ID();

				INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from group of everyone to group of user
								  (1, @group_id, 2, 1);
				SET @link_id = LAST_INSERT_ID();
				INSERT INTO settings (link_id, `key`, value, user_id) VALUES
									 (@link_id, 'access_level_id', '2', 2);

				INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from group of user to group of user
								  (@group_id, @group_id, @user_id, 1);
				SET @link_id = LAST_INSERT_ID();
				INSERT INTO settings (link_id, `key`, value, user_id) VALUES
									 (@link_id, 'access_level_id', '2', @user_id);

				INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from group of user to user
								  (@group_id, @user_id, @user_id, 1);
				SET @link_id = LAST_INSERT_ID();
				INSERT INTO settings (link_id, `key`, value, user_id) VALUES
									 (@link_id, 'access_level_id', '5', @user_id);

				INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from user to group of user
								  (@user_id, @group_id, @user_id, 1);
				SET @link_id = LAST_INSERT_ID();
				INSERT INTO settings (link_id, `key`, value, user_id) VALUES
									 (@link_id, 'access_level_id', '5', @user_id),
									 (@link_id, 'allow_invites', 'true', @user_id),
									 (@link_id, 'allow_members_list_view', 'true', @user_id),
									 (@link_id, 'allow_higher_access_preference', 'true', @user_id);";

		mysqli_multi_query($connection, $sql);
	}

	function object_getSettings($id) {
		$settings = [];

		if(!isset($id)) {
			return $settings;
		}

		global $connection;

		$sql = "SELECT `key`, value FROM settings WHERE object_id = $id";
		$query = mysqli_query($connection, $sql);

		while($row = mysqli_fetch_assoc($query)) {
			$settings[$row['key']] = $row['value'];
		}

		return $settings;
	}

	function object_getUserID($login) {
		if(!isset($login)) {
			return;
		}

		global $connection;

		$sql = "SELECT o.id
				FROM objects AS o
				JOIN settings AS s ON s.object_id = o.id AND s.key = 'login' AND s.value = '$login'";
		$query = mysqli_query($connection, $sql);

		if(mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_assoc($query);

			return $row['id'];
		}
	}

	function object_areMutualFriends($user_id, $user_id_) {
		if(!isset($user_id) || !isset($user_id_)) {
			return false;
		}

		global $connection;

		$sql = "SELECT id
				FROM links AS l
				WHERE (l.from_id = $user_id AND l.to_id = $user_id_ OR l.from_id = $user_id_ AND l.to_id = $user_id) AND l.type_id = 2";
		$query = mysqli_query($connection, $sql);

		if(mysqli_num_rows($query) >= 2) {
			return true;
		}

		return false;
	}

	function object_search($condition = null, $limit = null, $offset = null) {
		global $connection;

		$sql = "SELECT o.id AS o_id FROM objects AS o";

		if(isset($condition)) {
			$sql .= " $condition";
		}
		if(isset($limit)) {
			$sql .= " LIMIT $limit";
		}
		if(isset($offset)) {
			$sql .= " OFFSET $offset";
		}

		$query = mysqli_query($connection, $sql);
		$objects = [];

		while($row = mysqli_fetch_assoc($query)) {
			$objects[] = new Object_($row['o_id']);
		}

		$sql = "SELECT DISTINCT COUNT(*) OVER () AS count FROM objects AS o";

		if(isset($condition)) {
			$sql .= " $condition";
			$sql = preg_replace('/(ORDER BY .*)$/i', '', $sql);
		}

		$query = mysqli_query($connection, $sql);
		$count = 0;

		if(mysqli_num_rows($query) > 0) {
			$count = mysqli_fetch_assoc($query)['count'];
		}

		return ['objects' => $objects, 'count' => $count];
	}

	function object_getGenericSearchCondition($query, $user_id = null, $section_id = null) {
		$words = preg_split('/\s+/', $query);
		$sql = "LEFT JOIN objects_files AS `of` ON o.id = `of`.object_id
				LEFT JOIN files AS f ON `of`.file_id = f.id
				LEFT JOIN links AS l ON o.id = l.from_id
				WHERE (";
		$conditions = [];

		foreach($words as $word) {
			$exclude = false;
			$md5 = false;

			if(str_starts_with($word, '-')) {
				$exclude = true;
				$word = substr($word, 1);
			}
			if(str_starts_with($word, 'md5:')) {
				$md5 = true;
				$word = substr($word, 4);
			}

			if($md5) {
				if(!$exclude) {
					$conditions[] = "f.md5 = '$word'";
				} else {
					$conditions[] = "IFNULL(f.md5, '') != '$word'";
				}
			} else {
				if(!$exclude) {
					$conditions[] = "(o.title LIKE '%$word%' OR o.description LIKE '%$word%' OR `of`.title LIKE '%$word%')";
				} else {
					$conditions[] = "(IFNULL(o.title, '') NOT LIKE '%$word%' AND IFNULL(o.description, '') NOT LIKE '%$word%' AND IFNULL(`of`.title, '') NOT LIKE '%$word%')";
				}
			}
		}

		if($user_id) {
			$conditions[] = "o.user_id = $user_id";
		}
		if($section_id) {
			$conditions[] = "(l.to_id = $section_id AND l.type_id = 4)";
		}

		$sql .= implode(' AND ', $conditions).') GROUP BY o.id';

		return $sql;
	}

	function object_getValidReferrerID($object_id, $referrer_id = null, $fallback = true) {
		global $connection;

		$sql_0 = "SELECT l.to_id
				  FROM links AS l
				  JOIN objects AS o ON o.id = l.to_id
				  WHERE l.from_id = $object_id AND l.type_id = 4 AND o.type_id = 3";
		$sql_1 = 'LIMIT 1';

		if(isset($referrer_id)) {
			$sql = "$sql_0 AND l.to_id = $referrer_id $sql_1";
			$query = mysqli_query($connection, $sql);

			if(mysqli_num_rows($query) > 0) {
				return $referrer_id;
			}
		}
		if($fallback) {
			$sql = "$sql_0 $sql_1";
			$query = mysqli_query($connection, $sql);

			if(mysqli_num_rows($query) > 0) {
				$row = mysqli_fetch_assoc($query);

				return $row['to_id'];
			}
		}
	}

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

	function session_getAccessLevelID($object_id) { // TODO: Shared objects should be always 5
		global $connection;

		$user_id = session_getUserID();
		$user_group_access = [
			1 => ['access_level_id' => 2]
		];
		/*$redis = new Redis();
		$redis->connect('127.0.0.1', 6379);
		$cache_key = "user_access_level_{$user_id}_$object_id";*/

		if($user_id != null) {
			/*$access_level = $redis->get($cache_key);

			if($access_level !== false) {
				return $access_level;
			}*/

			$sql = "SELECT s.value
					FROM objects AS o
					JOIN settings AS s ON s.object_id = o.id AND s.key = 'allow_max_access_ignoring_groups' AND s.value = 'true'
					WHERE o.id = $user_id";
			$query = mysqli_query($connection, $sql);

			if(mysqli_num_rows($query) > 0) {
				return 5;
			}

			$sql = "SELECT l.to_id AS group_id, s_0.value AS access_level_id, s_1.value AS allow_higher_access_preference
					FROM links AS l
					JOIN objects AS o_0 ON o_0.id = l.from_id AND o_0.type_id = 2
					JOIN objects AS o_1 ON o_1.id = l.to_id AND o_1.type_id = 1
					JOIN settings AS s_0 ON s_0.link_id = l.id AND s_0.key = 'access_level_id'
			   LEFT JOIN settings AS s_1 ON s_1.link_id = l.id AND s_1.key = 'allow_higher_access_preference'
					WHERE l.from_id = $user_id AND l.type_id = 1";
			$query = mysqli_query($connection, $sql);

			foreach(mysqli_fetch_all($query, MYSQLI_ASSOC) as $uga/*buga*/) {
				$user_group_access[$uga['group_id']] = $uga;
			}
		}

		$sql = "SELECT l.from_id AS group_id, s.value AS access_level_id
				FROM links AS l
				JOIN objects AS o ON o.id = l.from_id AND o.type_id = 1
				JOIN settings AS s ON s.link_id = l.id AND s.key = 'access_level_id'
				WHERE l.to_id = $object_id AND l.type_id = 1";
		$query = mysqli_query($connection, $sql);
		$group_object_access = mysqli_fetch_all($query, MYSQLI_ASSOC);
		$access_levels = [0];

		foreach($group_object_access as $goa) {
			$uga = $user_group_access[$goa['group_id']] ?? null;

			if(!empty($uga)) {
				$access_levels[] = filter_var($uga['allow_higher_access_preference'] ?? null, FILTER_VALIDATE_BOOLEAN) ?
								   max($uga['access_level_id'], $goa['access_level_id']) :
								   min($uga['access_level_id'], $goa['access_level_id']);
			}
		}

		$access_level = max($access_levels);

		/*if($user_id != null) {
			$redis->set($cache_key, $access_level, 3600);
		}*/

		return $access_level;
	}

	// Template

	class Template {
		protected $file_title;
		protected $namespace;

		public function __construct($file_title) {
			$this->file_title = $file_title;
		}

		public function __get($name) {
			if(isset($this->namespace[$name])) {
				return $this->namespace[$name];
			}

			return false;
		}

		public function __set($name, $value) {
			$this->namespace[$name] = $value;
		}

		public function __isset($name) {
			return isset($this->namespace[$name]);
		}

		public function __toString() {
			return $this->render();
		}

		public function render($print = false) {
			ob_start();

			include 'app/template/'.$this->file_title;

			$rendered = ob_get_clean();

			if($print) {
				echo $rendered;

				return;
			 }

			return $rendered;
		}
	}

	function template_parseBB($string) {
		$codes = [
			'newline' => [
				'pattern' => '/\n/',
				'replacement' => '<br>'
			],
			'tab' => [
				'pattern' => '/\t/',
				'replacement' => '&emsp;'
			],
			'b' => [
				'pattern' => '/\[b\](.*?)\[\/b\]/s',
				'replacement' => '<b>$1</b>'
			],
			'i' => [
				'pattern' => '/\[i\](.*?)\[\/i\]/s',
				'replacement' => '<i>$1</i>'
			],
			'u' => [
				'pattern' => '/\[u\](.*?)\[\/u\]/s',
				'replacement' => '<u>$1</u>'
			],
			's' => [
				'pattern' => '/\[s\](.*?)\[\/s\]/s',
				'replacement' => '<s>$1</s>'
			],
			'sup' => [
				'pattern' => '/\[sup\](.*?)\[\/sup\]/s',
				'replacement' => '<sup>$1</sup>'
			],
			'sub' => [
				'pattern' => '/\[sub\](.*?)\[\/sub\]/s',
				'replacement' => '<sub>$1</sub>'
			],
			'url' => [
				'pattern' => '/\[url=(.*?)\](.*?)\[\/url\]/s',
				'replacement' => '<a href="$1">$2</a>'
			],
			'color' => [
				'pattern' => '/\[color=(.*?)\](.*?)\[\/color\]/s',
				'replacement' => '<span style="color: $1;">$2</span>'
			],
			'lang' => [
				'pattern' => '/\[lang=(.*?)\](.*?)\[\/lang\]/s',
				'replacement' => '$2'
			]
		];

		global $language;

		$string = preg_replace_callback('/\[lang=(.*?)\](.*?)\[\/lang\]/s', function($matches) use ($language) {
			$languages = explode('|', $matches[1]);
			$text = $matches[2];

			return in_array($language, $languages) ? $text : '';
		}, $string);

		foreach($codes as $code => $replacement) {
			$string = preg_replace($replacement['pattern'], $replacement['replacement'], $string);
		}

		return $string;
	}

	function template_formatTime($time) {
		$date = date_create($time ?? '0');
		$months = [
			D['string_january'],
			D['string_february'],
			D['string_march'],
			D['string_april'],
			D['string_may'],
			D['string_june'],
			D['string_july'],
			D['string_august'],
			D['string_september'],
			D['string_october'],
			D['string_november'],
			D['string_december']
		];

		return $date->format('G:i j').' '.$months[$date->format('n')-1].' '.$date->format('Y');
	}

	function template_formatLength($seconds) {
		$from = new DateTime('@0');
		$to = new DateTime("@$seconds");
		$difference = $from->diff($to)->format('%D:%H:%I:%S');
		$difference = preg_replace('/^(00:){1,2}/', '', $difference);
		$difference = preg_replace('/^0(\d:)/', '$1', $difference);

		return $difference;
	}

	function template_formatSize($size) {
		return number_format($size ?? 0);
	}

	function template_render() {
		global $languages;

		include 'app/template/head';
		include 'app/template/body';
	}
?>