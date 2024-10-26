<?
	class Object_ extends Entity {
		public static $settings_filters = [  // 0: Boolean, 1: Integer, 2: String
			1 => [  // Group
				'hide_from_search'					=> 0,
				'hide_default_referrer'				=> 0,
				'hide_author_and_times'				=> 0,
				'avatar_id'							=> 1,
				'poster_id'							=> 1,
				'hide_files_list'					=> 0
			],
			2 => [  // User
				'login'								=> 2,
				'password_hash'						=> 2,
				'email'								=> 2,
				'hide_from_search'					=> 0,
				'hide_default_referrer'				=> 0,
				'use_personal_menu'					=> 0,
				'group_id'							=> 1,
				'editor_id'							=> 1,
				'avatar_id'							=> 1,
				'max_upload_size'					=> 1,
				'notify_friends'					=> 0,
				'notify_inclusions'					=> 0,
				'notify_comments'					=> 0,
				'notify_recommendations'			=> 0,
				'notify_private_messages'			=> 0,
				'allow_any_upload_size'				=> 0,
				'allow_advanced_control'			=> 0,
				'allow_max_access_ignoring_groups'	=> 0
			],
			3 => [  // Plain
				'hide_from_search'					=> 0,
				'hide_default_referrer'				=> 0,
				'hide_title'						=> 0,
				'hide_author_and_times'				=> 0,
				'avatar_id'							=> 1,
				'poster_id'							=> 1,
				'display_search_bar'				=> 0,
				'display_amount'					=> 1,
				'display_mode_id'					=> 1,
				'sort_mode_id'						=> 1,
				'hide_files_list'					=> 0,
				'deny_nonbookmark_inclusion'		=> 0,
				'deny_claims'						=> 0,
				'allow_template_execution'			=> 0,
				'awaiting_save'						=> 0
			]
		];

		public static function createGroupID() {
			$user_id = Session::getUserID();
			$group_id = Session::getSetting('group_id');

			if(empty($user_id) || empty($group_id)) {
				return;
			}

			global $connection;

			$sql = "INSERT INTO objects (user_id, type_id) VALUES -- Group
										($user_id, 1);
					SET @group_id = LAST_INSERT_ID();
					INSERT INTO settings (object_id, `key`, value, user_id) VALUES
										 (@group_id, 'hide_from_search', 'true', $user_id),
										 (@group_id, 'hide_default_referrer', 'true', $user_id);

				--	/!\ Groups are private to their owners and members by default.

				--	INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from group of everyone to group
				--					  (1, @group_id, 2, 1);
				--	SET @link_id = LAST_INSERT_ID();
				--	INSERT INTO settings (link_id, `key`, value, user_id) VALUES
				--						 (@link_id, 'access_level_id', '2', 2);

					INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from group of user to group
									  ($group_id, @group_id, $user_id, 1);
					SET @link_id = LAST_INSERT_ID();
					INSERT INTO settings (link_id, `key`, value, user_id) VALUES
										 (@link_id, 'access_level_id', '5', $user_id);

					INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from group to group
									  (@group_id, @group_id, $user_id, 1);
					SET @link_id = LAST_INSERT_ID();
					INSERT INTO settings (link_id, `key`, value, user_id) VALUES
										 (@link_id, 'access_level_id', '2', $user_id);

					INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from user to group
									  ($user_id, @group_id, $user_id, 1);
					SET @link_id = LAST_INSERT_ID();
					INSERT INTO settings (link_id, `key`, value, user_id) VALUES
										 (@link_id, 'access_level_id', '5', $user_id),
										 (@link_id, 'allow_invites', 'true', $user_id),
										 (@link_id, 'allow_members_list_view', 'true', $user_id),
										 (@link_id, 'allow_higher_access_preference', 'true', $user_id);

					SELECT @group_id;";
			$query = $connection->multi_query($sql);
			$group_id = null;

			if($query) {
				while($connection->next_result()) {
					if($result = $connection->store_result()) {
						$row = mysqli_fetch_row($result);
						$group_id = $row[0];

						$result->free_result();
					}
				}
			}

			return $group_id;
		}

		public static function createUserID($login, $password, $email) {
			if(Object_::getUserID($login)) {
				return;
			}

			global $connection;

			$password_hash = Session::createPasswordHash($password);
			$sql = "INSERT INTO objects (type_id) VALUES -- User
										(2);
					SET @user_id = LAST_INSERT_ID();
					INSERT INTO settings (object_id, `key`, value, user_id) VALUES
										 (@user_id, 'login', '$login', @user_id),
										 (@user_id, 'password_hash', '$password_hash', @user_id),
										 (@user_id, 'email', '$email', @user_id),
										 (@user_id, 'hide_default_referrer', 'true', @user_id),
										 (@user_id, 'notify_friends', 'true', @user_id),
										 (@user_id, 'notify_inclusions', 'true', @user_id),
										 (@user_id, 'notify_comments', 'true', @user_id),
										 (@user_id, 'notify_recommendations', 'true', @user_id),
										 (@user_id, 'notify_private_messages', 'true', @user_id);

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
					INSERT INTO settings (object_id, `key`, value, user_id) VALUES
										 (@user_id, 'group_id', '$group_id', @user_id),
										 (@group_id, 'hide_from_search', 'true', @user_id),
										 (@group_id, 'hide_default_referrer', 'true', @user_id);

				--	/!\ Groups are private to their owners and members by default.

				--	INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from group of everyone to group of user
				--					  (1, @group_id, 2, 1);
				--	SET @link_id = LAST_INSERT_ID();
				--	INSERT INTO settings (link_id, `key`, value, user_id) VALUES
				--						 (@link_id, 'access_level_id', '2', 2);

					INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from group of user to group of user
									  (@group_id, @group_id, @user_id, 1);
					SET @link_id = LAST_INSERT_ID();
					INSERT INTO settings (link_id, `key`, value, user_id) VALUES
										 (@link_id, 'access_level_id', '2', @user_id);

					INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from group of user to user
									  (@group_id, @user_id, @user_id, 1);
					SET @link_id = LAST_INSERT_ID();
					INSERT INTO settings (link_id, `key`, value, user_id) VALUES
										 (@link_id, 'access_level_id', '3', @user_id);

					INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from user to group of user
									  (@user_id, @group_id, @user_id, 1);
					SET @link_id = LAST_INSERT_ID();
					INSERT INTO settings (link_id, `key`, value, user_id) VALUES
										 (@link_id, 'access_level_id', '5', @user_id),
										 (@link_id, 'allow_invites', 'true', @user_id),
										 (@link_id, 'allow_members_list_view', 'true', @user_id),
										 (@link_id, 'allow_higher_access_preference', 'true', @user_id);";

			$connection->multi_query($sql);
		}

		public static function createPlainID() {
			$user_id = Session::getUserID();
			$group_id = Session::getSetting('group_id');

			if(empty($user_id) || empty($group_id)) {
				return;
			}

			global $connection;

			$sql = "INSERT INTO objects (user_id, type_id) VALUES -- Plain
										($user_id, 3);
					SET @plain_id = LAST_INSERT_ID();
					INSERT INTO settings (object_id, `key`, value, user_id) VALUES
										 (@plain_id, 'awaiting_save', 'true', $user_id);

					INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from group of everyone to plain
									  (1, @plain_id, 2, 1);
					SET @link_id = LAST_INSERT_ID();
					INSERT INTO settings (link_id, `key`, value, user_id) VALUES
										 (@link_id, 'access_level_id', '2', 2);

					INSERT INTO links (from_id, to_id, user_id, type_id) VALUES -- Link from group of user to plain
									  ($group_id, @plain_id, $user_id, 1);
					SET @link_id = LAST_INSERT_ID();
					INSERT INTO settings (link_id, `key`, value, user_id) VALUES
										 (@link_id, 'access_level_id', '5', $user_id);

					SELECT @plain_id;";
			$query = $connection->multi_query($sql);
			$plain_id;

			if($query) {
				while($connection->next_result()) {
					if($result = $connection->store_result()) {
						$row = mysqli_fetch_row($result);
						$plain_id = $row[0];

						$result->free_result();
					}
				}
			}

			return $plain_id;
		}

		public static function createSharedID() {
			global $connection;

			$sql = "INSERT INTO objects (type_id) VALUES -- Shared
										(4);";
			$query = $connection->query($sql);

			if($query) {
				return $connection->insert_id;
			}
		}

		public static function saveID($object_id, $title = null, $description = null) {
			if(filter_var($object_id, FILTER_VALIDATE_INT) === false) {
				return;
			}

			global $connection;

			$title = isset($title) ? "'".$connection->real_escape_string($title)."'" : 'NULL';
			$description = isset($description) ? "'".$connection->real_escape_string($description)."'" : 'NULL';
			$sql = "UPDATE objects SET title = $title, description = $description WHERE id = $object_id";
			$query = $connection->query($sql);

			if($query) {
				return true;
			}
		}

		public static function getFilteredSetting($object, $key) {
			if(empty($object)) {
				foreach(self::$settings_filters as $sf) {
					$filter_id = $sf[$key] ?? null;

					if(isset($filter_id)) {
						break;
					}
				}
			} else {
				$filter_id = self::$settings_filters[$object->type_id][$key] ?? null;
			}

			if(!isset($filter_id)) {
				return;
			}

			$value = $object->settings[$key]->value ?? null;

			if($filter_id == 0) {
				return filter_var($value ?? false, FILTER_VALIDATE_BOOLEAN);
			}
			if($filter_id == 1) {
				return filter_var($value ?? 0, FILTER_VALIDATE_INT) ?? 0;
			}
			if($filter_id == 2) {
				return $value ?? '';
			}
		}

		public static function getFilteredSettings($object) {
			$filtered_settings = [];

			foreach(self::$settings_filters[$object->type_id] ?? [] as $key => $filter_id) {
				$filtered_settings[$key] = self::getFilteredSetting($object, $key);
			}

			return $filtered_settings;
		}

		public static function getUserID($login) {
			if(!isset($login)) {
				return;
			}

			global $connection;

			$sql = "SELECT object_id FROM settings WHERE `key` = 'login' AND value = '$login'";
			$query = $connection->query($sql);

			if($query->num_rows > 0) {
				return $query->fetch_column();
			}
		}

		public static function getGenericSearchCondition($query, $user_id = null, $section_id = null) {
			$words = preg_split('/\s+/', $query);
			$sql = "LEFT JOIN uploads AS u ON o.id = u.object_id
					LEFT JOIN files AS f ON u.file_id = f.id
					LEFT JOIN links AS l ON o.id = l.from_id
					WHERE o.type_id != 4 AND (";
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
						$conditions[] = "(o.title LIKE '%$word%' OR o.description LIKE '%$word%' OR u.title LIKE '%$word%')";
					} else {
						$conditions[] = "(IFNULL(o.title, '') NOT LIKE '%$word%' AND IFNULL(o.description, '') NOT LIKE '%$word%' AND IFNULL(u.title, '') NOT LIKE '%$word%')";
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

		public static function getMostSearchCondition($most_id) {
			$shared_where_group = 'WHERE o.type_id != 4
								   GROUP BY o.id';
			$shared_order = 'o.creation_time DESC, o.id DESC';

			if($most_id == 0)
				return "LEFT JOIN visits AS v ON v.object_id = o.id
						LEFT JOIN links AS c ON c.to_id = o.id AND c.type_id = 5
						LEFT JOIN links AS r ON r.from_id = o.id AND r.type_id = 6
						$shared_where_group
						HAVING COALESCE(COUNT(v.id), 0)+COALESCE(COUNT(c.id), 0)+COALESCE(COUNT(r.id), 0) > 0
						ORDER BY COALESCE(COUNT(v.id), 0)+COALESCE(COUNT(c.id), 0)+COALESCE(COUNT(r.id), 0) DESC, $shared_order";

			if($most_id == 1)
				return "LEFT JOIN visits AS v ON v.object_id = o.id
						$shared_where_group
						HAVING COUNT(v.id) > 0
						ORDER BY COUNT(v.id) DESC, $shared_order";

			if($most_id == 2)
				return "LEFT JOIN links AS l ON l.to_id = o.id AND l.type_id = 5
						$shared_where_group
						HAVING COUNT(l.id) > 0
						ORDER BY COUNT(l.id) DESC, $shared_order";

			if($most_id == 3)
				return "LEFT JOIN links AS l ON l.from_id = o.id AND l.type_id = 6
						$shared_where_group
						HAVING COUNT(l.id) > 0
						ORDER BY COUNT(l.id) DESC, $shared_order";
		}

		protected $user_;
		protected $alias_;
		protected $uploads_;
		protected $links_;
		protected $links_type_ids_;
		protected $settings_;
		protected $filtered_settings_;
		protected $friends_;
		protected $notifications_;
		protected $avatars_;
		protected $poster_;
		protected $avatar_;
		protected $counts_;
		protected $visits_stats_;
		protected $files_stats_;
		protected $files_size_;
		protected $files_length_;
		protected $file_servers_;
		protected $goa_links_;
		protected $uga_links_;
		protected $access_level_ids_;

		public function __construct($id_alias = null) {
			if(!isset($this->id)) {
				$id = filter_var($id_alias, FILTER_VALIDATE_INT) ? $id_alias : null;
				$alias = is_string($id_alias) && preg_match('/[a-z]/i', $id_alias) ? $id_alias : null;

				if(empty($id) && empty($alias)) {
					throw new Exception('0');
				}

				global $connection;

				if(empty($alias)) {
					$sql = "SELECT * FROM objects WHERE id = $id";
				} else {
					$sql = "SELECT o.* FROM objects AS o
							JOIN aliases AS a ON a.object_id = o.id AND a.url = '$alias'";
				}

				$query = $connection->query($sql);

				if($query->num_rows > 0) {
					$this->fields_ = $query->fetch_assoc();
				} else {
					throw new Exception('1');
				}
			}

			if(empty($this->title)) {
				$this->title = $this->type_id == 2 ? ($this->login ?? D['title_no_title']) : D['title_no_title'];
				$this->_title = '';
			} else {
				$this->_title = $this->title;
			}
		}

		public function _getUser() {
			if(!isset($this->user_id)) {
				return;
			}

			$this->user_ ??= new Object_($this->user_id);

			return $this->user_;
		}

		public function _getAlias() {
			if(isset($this->alias_)) {
				return $this->alias_;
			}

			global $connection;

			$sql = "SELECT url FROM aliases WHERE object_id = $this->id";
			$query = $connection->query($sql);

			if($query->num_rows > 0) {
				$this->alias_ = $query->fetch_column();
			}

			return $this->alias_;
		}

		public function _getUploads() {
			if(isset($this->uploads_)) {
				return $this->uploads_;
			}

			global $connection;

			$sql = "SELECT * FROM uploads AS u WHERE u.object_id = $this->id ORDER BY u.title ASC, u.id ASC";
			$query = $connection->query($sql);
			$this->uploads_ = [];

			while($upload = $query->fetch_object('Upload')) {
				$this->uploads_[] = $upload;
			}

			return $this->uploads_;
		}

		public function _getLinks() {
			if(isset($this->links_)) {
				return $this->links_;
			}

			global $connection;

			$sql = "SELECT * FROM links WHERE from_id = $this->id";
			$query = $connection->query($sql);
			$this->links_ = [];

			while($link = $query->fetch_object('Link')) {
				$this->links_[] = $link;
			}

			return $this->links_;
		}

		public function _getLinksTypeIds() {
			if(isset($this->links_type_ids_)) {
				return $this->links_type_ids_;
			}

			global $connection;

			$sql = "SELECT DISTINCT type_id FROM links WHERE from_id = $this->id";
			$query = $connection->query($sql);
			$this->links_type_ids_ = array_column($query->fetch_all(MYSQLI_ASSOC), 'type_id');

			return $this->links_type_ids_;
		}

		public function _getSettings() {
			if(isset($this->settings_)) {
				return $this->settings_;
			}

			global $connection;

			$sql = "SELECT * FROM settings WHERE object_id = $this->id";
			$query = $connection->query($sql);
			$this->settings_ = [];

			while($setting = $query->fetch_object('Setting')) {
				$this->settings_[$setting->key] = $setting;
			}

			return $this->settings_;
		}

		public function _getLogin() {
			return $this->getSetting('login');
		}

		public function _getFriends() {
			if(isset($this->friends_)) {
				return $this->friends_;
			}
			if($this->type_id != 2) {
				return;
			}

			global $connection;

			$sql = "SELECT o.*
					FROM objects AS o
					JOIN links AS l ON l.from_id = o.id AND l.to_id = $this->id AND l.type_id = 2";
			$query = $connection->query($sql);
			$this->friends_ = [];

			while($friend = $query->fetch_object('Object_')) {
				$this->friends_[] = $friend;
			}

			return $this->friends_;
		}

		public function _getNotifications() {
			if(isset($this->notifications_)) {
				return $this->notifications_;
			}
			if($this->type_id != 2) {
				return;
			}

			global $connection;

			$sql = "SELECT * FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 3";
			$query = $connection->query($sql);
			$this->notifications_ = [];

			while($notification = $query->fetch_object('Link')) {
				$this->notifications_[] = $notification;
			}

			return $this->notifications_;
		}

		public function _getAvatars() {
			if(isset($this->avatars_)) {
				return $this->avatars_;
			}
			if($this->type_id != 2) {
				return;
			}

			global $connection;

			$sql = "SELECT o.*
					FROM objects AS o
					JOIN links AS l ON l.from_id = o.id AND l.user_id = $this->id AND l.type_id = 7";
			$query = $connection->query($sql);
			$this->avatars_ = [];

			while($avatar = $query->fetch_object('Object_')) {
				$this->avatars_[] = $avatar;
			}

			return $this->avatars_;
		}

		public function _getPoster() {
			if(isset($this->poster_)) {
				return $this->poster_;
			}

			if(isset($this->settings['poster_id'])) {
				try {
					$poster = new Upload($this->settings['poster_id']->value);

					if(str_starts_with($poster->file->mime_type ?? '', 'image/')) {
						return $this->poster_ = $poster;
					}
				} catch(Exception $e) {}
			}

			foreach($this->uploads as $poster) {
				if(str_starts_with($poster->file->mime_type ?? '', 'image/')) {
					return $this->poster_ = $poster;
				}
			}
		}

		public function _getAvatar() {
			if(isset($this->avatar_)) {
				return $this->avatar_;
			}
			if(!isset($this->settings['avatar_id'])) {
				return;
			}

			try {
				$avatar = new Object_($this->settings['avatar_id']->value);
			} catch(Exception $e) {}

			if(isset($avatar->poster)) {
				return $this->avatar_ = $avatar;
			}
		}

		protected function getCount($type, $sql) {
			if(isset($this->counts_[$type])) {
				return $this->counts_[$type];
			}

			global $connection;

			$query = $connection->query($sql);
			$this->counts_ ??= [];
			$this->counts_[$type] = 0;

			if($query->num_rows > 0) {
				$this->counts_[$type] = $query->fetch_column();
			}

			return $this->counts_[$type];
		}

		public function _getFriendsCount() {
			return $this->getCount('friends', "SELECT COUNT(*) FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 2");
		}

		public function _getNotificationsCount() {
			return $this->getCount('notifications', "SELECT COUNT(*) FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 3");
		}

		public function _getInclusionsCount() {
			return $this->getCount('inclusions', "SELECT COUNT(*) FROM links AS l JOIN saved_objects AS o ON o.id = l.from_id WHERE l.to_id = $this->id AND l.type_id = 4");
		}

		public function _getSelfInclusionsCount() {
			return $this->getCount('self_inclusions', "SELECT COUNT(*) FROM links AS l WHERE l.from_id = $this->id AND l.type_id = 4");
		}

		public function _getFilesCount() {
			return $this->getCount('files', "SELECT COUNT(*) FROM uploads AS u WHERE u.object_id = $this->id");
		}

		public function _getMembersCount() {
			return $this->getCount('members', "SELECT COUNT(*) FROM links AS l JOIN objects AS o ON o.id = l.from_id AND o.type_id = 2 WHERE l.to_id = $this->id AND l.type_id = 1");
		}

		public function _getCommentsCount() {
		//	$sql = "SELECT COUNT(*) FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 5";

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
					SELECT COUNT(*) FROM link_chain";

			return $this->getCount('comments', $sql);
		}

		public function _getClaimsCount() {
			return $this->getCount('claims', "SELECT COUNT(*) FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 8");
		}

		public function _getArchiveCount() {
			return $this->getCount('user_archive', "SELECT COUNT(*) FROM saved_objects AS o WHERE o.user_id = $this->id");
		}

		public function _getUserCommentsCount() {
			return $this->getCount('user_comments', "SELECT COUNT(*) FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 5");
		}

		public function _getRecommendationsCount() {
			return $this->getCount('user_recommendations', "SELECT COUNT(*) FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 6");
		}

		public function _getAvatarsCount() {
			return $this->getCount('user_avatars', "SELECT COUNT(*) FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 7");
		}

		public function _getUserClaimsCount() {
			return $this->getCount('user_claims', "SELECT COUNT(*) FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 8");
		}

		public function _getTemplatesCount() {
			return $this->getCount('user_templates', "SELECT COUNT(*) FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 9");
		}

		public function _getDraftsCount() {
			return $this->getCount('user_drafts', "SELECT COUNT(*) FROM objects AS o JOIN settings AS s ON s.object_id = o.id WHERE o.user_id = $this->id AND s.key = 'awaiting_save' AND s.value = 'true'");
		}

		public function _getBookmarksCount() {
			return $this->getCount('user_bookmarks', "SELECT COUNT(*) FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 10");
		}

		public function _getGroupsCountAlien() {
			return $this->getCount('user_groups_alien', "SELECT COUNT(*) FROM links AS l WHERE l.from_id = $this->id AND l.user_id != $this->id AND l.type_id = 1");
		}

		public function _getGroupsCountOwn() {
			return $this->getCount('user_groups_own', "SELECT COUNT(*) FROM links AS l WHERE l.from_id = $this->id AND l.user_id = $this->id AND l.type_id = 1");
		}

		public function _getVisitsStats() {
			if(isset($this->visits_stats_)) {
				return $this->visits_stats_;
			}

			global $connection;

			$sql = "SELECT
						COUNT(*) AS hits_count,
						COUNT(DISTINCT CASE WHEN ip_address IS NOT NULL THEN ip_address END) AS hosts_count,
						COUNT(DISTINCT CASE WHEN referrer_url IS NOT NULL AND referrer_url NOT LIKE '/%' THEN COALESCE(ip_address, '') END) AS guests_count
					FROM visits
					WHERE object_id = $this->id";
			$query = $connection->query($sql);
			$this->visits_stats_ = [];

			if($query->num_rows > 0) {
				$this->visits_stats_ = $query->fetch_object();
			}

			return $this->visits_stats_;
		}

		public function _getHitsCount() {
			return $this->visits_stats->hits_count;
		}

		public function _getHostsCount() {
			return $this->visits_stats->hosts_count;
		}

		public function _getGuestsCount() {
			return $this->visits_stats->guests_count;
		}

		public function _getFilesStats() {
			if(isset($this->files_stats_)) {
				return $this->files_stats_;
			}
			if($this->type_id != 2) {
				return;
			}

			global $connection;

			$sql = "SELECT
						COALESCE(SUM(original), 0) AS originals_count,
						COALESCE(SUM(original*size), 0) AS originals_size,
						COALESCE(SUM(NOT original), 0) AS duplicates_count,
						COALESCE(SUM((NOT original)*size), 0) AS duplicates_size
					FROM (
						SELECT u_0.id = MIN(u_1.id) AS original, MAX(fs.upload_offset) AS size
						FROM uploads AS u_0
						JOIN uploads AS u_1 ON u_1.file_id = u_0.file_id
						JOIN fs_files AS fs ON fs.file_id = u_0.file_id
						JOIN objects AS o_0 ON o_0.id = u_0.object_id AND o_0.user_id = $this->id  -- Considering user's objects is the only way to identify uploads authorship, even if them done by another user
						JOIN objects AS o_1 ON o_1.id = u_1.object_id AND o_1.user_id IS NOT NULL  -- Do not consider anonymous objects as they don't participate in the statistics
						GROUP BY u_0.id
					) AS f";
			$query = $connection->query($sql);
			$this->files_stats_ = [];

			if($query->num_rows > 0) {
				$this->files_stats_ = $query->fetch_object();
			}

			return $this->files_stats_;
		}

		public function _getOriginalsCount() {
			return $this->files_stats->originals_count;
		}

		public function _getOriginalsSize() {
			return $this->files_stats->originals_size;
		}

		public function _getDuplicatesCount() {
			return $this->files_stats->duplicates_count;
		}

		public function _getDuplicatesSize() {
			return $this->files_stats->duplicates_size;
		}

		public function _getFilesSize() {
			if(isset($this->files_size_)) {
				return $this->files_size_;
			}

			global $connection;

			$sql = "SELECT SUM(size)
					FROM files AS f
					JOIN uploads AS u ON u.object_id = $this->id AND u.file_id = f.id";
			$query = $connection->query($sql);
			$this->files_size_ = 0;

			if($query->num_rows > 0) {
				$this->files_size_ = $query->fetch_column();
			}

			return $this->files_size_;
		}

		public function _getFilesLength() {
			if(isset($this->files_length_)) {
				return $this->files_length_;
			}

			global $connection;

			$sql = "SELECT SUM(length)
					FROM meta AS m
					JOIN uploads AS u ON u.object_id = $this->id AND u.file_id = m.file_id";
			$query = $connection->query($sql);
			$this->files_length_ = 0;

			if($query->num_rows > 0) {
				$this->files_length_ = $query->fetch_column();
			}

			return $this->files_length_;
		}

		public function _getFileServers() {
			if(isset($this->file_servers_)) {
				return $this->file_servers_;
			}

			global $connection;

			$sql = "SELECT fs.*
					FROM fs
					JOIN fs_files AS ff ON ff.fs_id = fs.id
					JOIN uploads AS u ON u.object_id = $this->id AND u.file_id = ff.file_id
					GROUP BY fs.id
					ORDER BY fs.id ASC";
			$query = $connection->query($sql);
			$this->file_servers_ = [];

			while($file_servers = $query->fetch_object('FileServer')) {
				$this->file_servers_[] = $file_servers;
			}

			return $this->file_servers_;
		}

		public function _getGroupObjectAccessLinks() {
			if(isset($this->goa_links_)) {
				return $this->goa_links_;
			}

			global $connection;

			$sql = "SELECT l.*
					FROM links AS l
					JOIN objects AS o ON o.id = l.from_id AND o.type_id = 1
					WHERE l.to_id = $this->id AND l.type_id = 1";
			$query = $connection->query($sql);
			$this->goa_links_ = [];

			while($goa_link = $query->fetch_object('Link')) {
				$this->goa_links_[] = $goa_link;
			}

			return $this->goa_links_;
		}

		public function _getUserGroupAccessLinks() {
			if(isset($this->uga_links_)) {
				return $this->uga_links_;
			}

			global $connection;

			$direction = $this->type_id == 1 ? 'to' : 'from';
			$sql = "SELECT l.*
					FROM links AS l
					JOIN objects AS o_0 ON o_0.id = l.from_id AND o_0.type_id = 2
					JOIN objects AS o_1 ON o_1.id = l.to_id AND o_1.type_id = 1
					WHERE l.{$direction}_id = $this->id AND l.type_id = 1";
			$query = $connection->query($sql);
			$this->uga_links_ = [];

			while($uga_link = $query->fetch_object('Link')) {
				$this->uga_links_[] = $uga_link;
			}

			return $this->uga_links_;
		}

		public function _getAccessLevelId() {
			if($this->type_id == 4) {
				return 5;
			}
			if(isset($this->access_level_id_)) {
				return $this->access_level_id_;
			}

			global $connection;

			$user_id = Session::getUserID();
			/*
			$redis = new Redis();
			$redis->connect('127.0.0.1', 6379);
			$cache_key = "object_{$this->id}_access_level_id_{$user_id}";
			$access_level_id = $redis->get($cache_key);

			if($access_level_id !== false) {
				return $access_level_id;
			}
			*/

			if((empty($user_id) || $user_id != $this->user_id) && $this->getSetting('awaiting_save')) {
				$this->access_level_id_ = 0;
			//	$redis->set($cache_key, $this->access_level_id_, 3600);
				return $this->access_level_id_;
			}
			if(!empty($user_id) && Session::getSetting('allow_max_access_ignoring_groups')) {
				$this->access_level_id_ = 5;
			//	$redis->set($cache_key, $this->access_level_id_, 3600);
				return $this->access_level_id_;
			}

			$user_id ??= -1;
			$sql = "SELECT l.from_id, l.to_id, s_0.value AS access_level_id, s_1.value AS allow_higher_access_preference
					FROM links AS l
					JOIN objects AS o_0 ON o_0.id = l.from_id
					JOIN objects AS o_1 ON o_1.id = l.to_id
					JOIN settings AS s_0 ON s_0.link_id = l.id AND s_0.key = 'access_level_id'
			   LEFT JOIN settings AS s_1 ON s_1.link_id = l.id AND s_1.key = 'allow_higher_access_preference'
					WHERE (l.from_id = $user_id AND o_0.type_id = 2 AND o_1.type_id = 1
					   OR  l.to_id = $this->id  AND o_0.type_id = 1)
					  AND l.type_id = 1
					ORDER BY l.from_id = $user_id DESC,
							 l.to_id = $this->id  DESC";
			$query = $connection->query($sql);
			$uga_links = [
				1 => ['access_level_id' => 2]
			];
			$access_level_ids = [0];

			foreach($query->fetch_all(MYSQLI_ASSOC) as $row) {
				if($row['from_id'] == $user_id) {
					$uga_links[$row['to_id']] = $row;  // user_group_access
				} else
				if($row['to_id'] == $this->id) {
					$goa = $row;  // group_object_access
					$uga = $uga_links[$goa['from_id']] ?? null;

					if(!empty($uga)) {
						$access_level_ids[] = filter_var($uga['allow_higher_access_preference'] ?? null, FILTER_VALIDATE_BOOLEAN)
											? max($uga['access_level_id'], $goa['access_level_id'])
											: min($uga['access_level_id'], $goa['access_level_id']);
					}
				}
			}

			$this->access_level_id_ = max($access_level_ids);
		//	$redis->set($cache_key, $this->access_level_id_, 3600);
			return $this->access_level_id_;
		}

		public function areFriendOf($user_id) {
			if(!isset($user_id)) {
				return false;
			}

			global $connection;

			$sql = "SELECT id FROM links WHERE from_id = $this->id AND to_id = $user_id AND type_id = 2";
			$query = $connection->query($sql);

			return $query->num_rows > 0;
		}

		public function getSetting($key) {
			return $this->filtered_settings_[$key] ??= self::getFilteredSetting($this, $key);
		}

		public function getValidReferrerID($referrer_id = null, $fallback = true) {
			global $connection;

			$sql_0 = "SELECT l.to_id
					  FROM links AS l
					  JOIN objects AS o ON o.id = l.to_id
					  WHERE l.from_id = $this->id AND l.type_id = 4 AND o.type_id = 3";
			$sql_1 = 'LIMIT 1';

			if(isset($referrer_id)) {
				$sql = "$sql_0 AND l.to_id = $referrer_id $sql_1";
				$query = $connection->query($sql);

				if($query->num_rows > 0) {
					return $referrer_id;
				}
			}
			if($fallback) {
				$sql = "$sql_0 $sql_1";
				$query = $connection->query($sql);

				if($query->num_rows > 0) {
					$row = $query->fetch_assoc();

					return $row['to_id'];
				}
			}
		}

		public function createVisitID() {
			$ip_address = $_SERVER['REMOTE_ADDR'];
			$referrer_url = $_SERVER['HTTP_REFERER'] ?? '';
			$parts = parse_url($referrer_url);

			if(isset($parts['host']) && $parts['host'] == $_SERVER['SERVER_NAME']) {
				$referrer_url = $parts['path'].(!empty($parts['query']) ? '?'.$parts['query'] : '').(!empty($parts['fragment']) ? '#'.$parts['fragment'] : '');
			}

			$ip_address = $ip_address != '127.0.0.1' && $ip_address != '::1' ? "'$ip_address'" : 'NULL';
			$referrer_url = !empty($referrer_url) ? "'$referrer_url'" : 'NULL';

			global $connection;

			$sql = "INSERT INTO visits (object_id, ip_address, referrer_url)
								VALUES ($this->id, $ip_address, $referrer_url)";
			$query = $connection->query($sql);

			if($query) {
				return $connection->insert_id;
			}
		}
	}
?>