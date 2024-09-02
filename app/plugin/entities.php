<?
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
		protected $alias_;
		protected $uploads_;
		protected $links_;
		protected $links_type_ids_;
		protected $settings_;
		protected $flat_settings_;
		protected $friends_;
		protected $notifications_;
		protected $poster_;
		protected $avatar_;
		protected $counts_;
		protected $file_servers_;
		protected $goa_links_;
		protected $uga_links_;
		protected $access_level_ids_;

		public function __construct($id_alias) {
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

		public function getAlias() {
			if(isset($this->alias_)) {
				return $this->alias_;
			}

			global $connection;

			$sql = "SELECT url FROM aliases WHERE object_id = $this->id";
			$query = mysqli_query($connection, $sql);

			if(mysqli_num_rows($query) > 0) {
				$this->alias_ = mysqli_fetch_column($query);
			}

			return $this->alias_;
		}

		public function getUploads() {
			if(isset($this->uploads_)) {
				return $this->uploads_;
			}

			global $connection;

			$sql = "SELECT id FROM uploads AS u WHERE u.object_id = $this->id";
			$query = mysqli_query($connection, $sql);
			$this->uploads_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->uploads_[] = new Upload($row['id']);
			}

			return $this->uploads_;
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

		public function getLinksTypeIds() {
			if(isset($this->links_type_ids_)) {
				return $this->links_type_ids_;
			}

			global $connection;

			$sql = "SELECT DISTINCT type_id FROM links WHERE from_id = $this->id";
			$query = mysqli_query($connection, $sql);
			$this->links_type_ids_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->links_type_ids_[] = $row['type_id'];
			}

			return $this->links_type_ids_;
		}

		public function getSettings($flat = false) {
			if(!$flat) {
				if(isset($this->settings_)) {
					return $this->settings_;
				}
			} else {
				if(isset($this->flat_settings_)) {
					return $this->flat_settings_;
				}
			}

			global $connection;

			$sql = "SELECT id, `key`, value FROM settings WHERE object_id = $this->id";
			$query = mysqli_query($connection, $sql);
			$this->settings_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				if(!$flat) {
					$this->settings_[$row['key']] = new Setting($row['id']);
				} else {
					$this->flat_settings_[$row['key']] = $row['value'];
				}
			}

			if(!$flat) {
				return $this->settings_;
			} else {
				return $this->flat_settings_;
			}
		}

		public function getFlatSettings() {
			return $this->getSettings(true);
		}

		public function getLogin() {
			return $this->flat_settings_['login'] ?? $this->settings['login']->value ?? null;
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

		public function getNotifications() {
			if(isset($this->notifications_)) {
				return $this->notifications_;
			}
			if($this->type_id != 2) {
				return;
			}

			global $connection;

			$sql = "SELECT id FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 3";
			$query = mysqli_query($connection, $sql);
			$this->notifications_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->notifications_[] = new Link($row['id']);
			}

			return $this->notifications_;
		}

		public function getPoster() {
			if(isset($this->poster_)) {
				return $this->poster_;
			}

			if(isset($this->settings['poster_id'])) {
				try {
					$poster = new Upload($this->settings['poster_id']->value);

					image:
					if(str_starts_with($poster->file->mime_type ?? '', 'image/')) {
						return $this->poster_ = $poster;
					}
				} catch(Exception $e) {}
			}

			foreach($this->uploads as $poster) {
				goto image;
			}
		}

		public function getAvatar() {
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

			$query = mysqli_query($connection, $sql);
			$this->counts_ ??= [];
			$this->counts_[$type] = 0;

			if(mysqli_num_rows($query) > 0) {
				$this->counts_[$type] = mysqli_fetch_column($query);
			}

			return $this->counts_[$type];
		}

		public function getFriendsCount() {
			return $this->getCount('friends', "SELECT COUNT(*) FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 2");
		}

		public function getNotificationsCount() {
			return $this->getCount('notifications', "SELECT COUNT(*) FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 3");
		}

		public function getInclusionsCount() {
			return $this->getCount('inclusions', "SELECT COUNT(*) FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 4");
		}

		public function getSelfInclusionsCount() {
			return $this->getCount('self_inclusions', "SELECT COUNT(*) FROM links AS l WHERE l.from_id = $this->id AND l.type_id = 4");
		}

		public function getFilesCount() {
			return $this->getCount('files', "SELECT COUNT(*) FROM uploads AS u WHERE u.object_id = $this->id");
		}

		public function getMembersCount() {
			return $this->getCount('members', "SELECT COUNT(*) FROM links AS l JOIN objects AS o ON o.id = l.from_id AND o.type_id = 2 WHERE l.to_id = $this->id AND l.type_id = 1");
		}

		public function getCommentsCount() {
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

		public function getClaimsCount() {
			return $this->getCount('claims', "SELECT COUNT(*) FROM links AS l WHERE l.to_id = $this->id AND l.type_id = 8");
		}

		public function getArchiveCount() {
			return $this->getCount('user_archive', "SELECT COUNT(*) FROM objects AS o WHERE o.user_id = $this->id AND o.type_id = 3");
		}

		public function getUserCommentsCount() {
			return $this->getCount('user_comments', "SELECT COUNT(*) FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 5");
		}

		public function getRecommendationsCount() {
			return $this->getCount('user_recommendations', "SELECT COUNT(*) FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 6");
		}

		public function getAvatarsCount() {
			return $this->getCount('user_avatars', "SELECT COUNT(*) FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 7");
		}

		public function getUserClaimsCount() {
			return $this->getCount('user_claims', "SELECT COUNT(*) FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 8");
		}

		public function getTemplatesCount() {
			return $this->getCount('user_templates', "SELECT COUNT(*) FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 9");
		}

		public function getDraftsCount() {
			return $this->getCount('user_drafts', "SELECT COUNT(*) FROM objects AS o JOIN settings AS s ON s.object_id = o.id WHERE o.user_id = $this->id AND s.key = 'awaiting_save' AND s.value = 'true'");
		}

		public function getBookmarksCount() {
			return $this->getCount('user_bookmarks', "SELECT COUNT(*) FROM links AS l WHERE l.user_id = $this->id AND l.type_id = 10");
		}

		public function getGroupsCountAlien() {
			return $this->getCount('user_groups_alien', "SELECT COUNT(*) FROM links AS l WHERE l.from_id = $this->id AND l.user_id != $this->id AND l.type_id = 1");
		}

		public function getGroupsCountOwn() {
			return $this->getCount('user_groups_own', "SELECT COUNT(*) FROM links AS l WHERE l.from_id = $this->id AND l.user_id = $this->id AND l.type_id = 1");
		}

		public function getHitsCount() {
			return $this->getCount('hits', "SELECT COUNT(*) FROM visits WHERE object_id = $this->id");
		}

		public function getHostsCount() {
			return $this->getCount('hosts', "SELECT DISTINCT COUNT(*) OVER () FROM visits WHERE object_id = $this->id AND ip_address IS NOT NULL GROUP BY ip_address");
		}

		public function getGuestsCount() {
			return $this->getCount('guests', "SELECT DISTINCT COUNT(*) OVER () FROM visits WHERE object_id = $this->id AND referrer_url IS NOT NULL AND referrer_url NOT LIKE '/%' GROUP BY ip_address");
		}

		public function getFilesSize() {
			$size = 0;

			foreach($this->uploads as $upload) {
				$size += $upload->file->size;
			}

			return $size;
		}

		public function getFilesLength() {
			$length = 0;

			foreach($this->uploads as $upload) {
				$length += $upload->file->length ?? 0;
			}

			return $length;
		}

		public function getFileServers() {
			if(isset($this->file_servers_)) {
				return $this->file_servers_;
			}

			global $connection;

			$sql = "SELECT DISTINCT ff.fs_id
					FROM uploads AS u
					JOIN files AS f ON f.id = u.file_id
					JOIN fs_files AS ff ON ff.file_id = f.id
					WHERE u.object_id = $this->id";
			$query = mysqli_query($connection, $sql);
			$this->file_servers_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->file_servers_[] = new FileServer($row['fs_id']);
			}

			return $this->file_servers_;
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

		public function getAccessLevelId($user_id = null) {
			if($this->type_id == 4) {
				return 5;
			}

			$this->access_level_ids_ ??= [];
			$user_id ??= session_getUserID();

			if(isset($this->access_level_ids_[$user_id])) {
				return $this->access_level_ids_[$user_id];
			}

			global $connection;

			$user_group_access = [
				1 => ['access_level_id' => 2]
			];
			/*$redis = new Redis();
			$redis->connect('127.0.0.1', 6379);
			$cache_key = "user_access_level_{$user_id}_{$this->id}";*/

			if($user_id != null) {
				/*$access_level_id = $redis->get($cache_key);

				if($access_level_id !== false) {
					return $access_level_id;
				}*/

				$sql = "SELECT s.value
						FROM objects AS o
						JOIN settings AS s ON s.object_id = o.id AND s.key = 'allow_max_access_ignoring_groups' AND s.value = 'true'
						WHERE o.id = $user_id";
				$query = mysqli_query($connection, $sql);

				if(mysqli_num_rows($query) > 0) {
					$this->access_level_ids_[$user_id] = 5;

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
					WHERE l.to_id = $this->id AND l.type_id = 1";
			$query = mysqli_query($connection, $sql);
			$group_object_access = mysqli_fetch_all($query, MYSQLI_ASSOC);
			$access_level_ids = [0];

			foreach($group_object_access as $goa) {
				$uga = $user_group_access[$goa['group_id']] ?? null;

				if(!empty($uga)) {
					$access_level_ids[] = filter_var($uga['allow_higher_access_preference'] ?? null, FILTER_VALIDATE_BOOLEAN)
										? max($uga['access_level_id'], $goa['access_level_id'])
										: min($uga['access_level_id'], $goa['access_level_id']);
				}
			}

			$access_level_id = max($access_level_ids);

			/*if($user_id != null) {
				$redis->set($cache_key, $access_level_id, 3600);
			}*/

			$this->access_level_ids_[$user_id] = $access_level_id;

			return $this->access_level_ids_[$user_id];
		}
	}

	class Upload extends Entity {
		protected $object_;
		protected $file_;

		public function __construct($id) {
			if(filter_var($id, FILTER_VALIDATE_INT) === false) {
				throw new Exception('0');
			}

			global $connection;

			$sql = "SELECT * FROM uploads WHERE id = $id";
			$query = mysqli_query($connection, $sql);

			if(mysqli_num_rows($query) > 0) {
				$this->fields_ = mysqli_fetch_assoc($query);
			} else {
				throw new Exception('1');
			}
		}

		public function getObject() {
			return $this->object_ ??= new Object_($this->object_id);
		}

		public function getFile() {
			return $this->file_ ??= new File($this->file_id);
		}
	}

	class File extends Entity {
		protected $file_servers_;
		protected $meta_;
		protected $url_;

		public function __construct($id) {
			if(filter_var($id, FILTER_VALIDATE_INT) === false) {
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

		public function getFileServers() {
			if(isset($this->file_servers_)) {
				return $this->file_servers_;
			}

			global $connection;

			$sql = "SELECT fs.id AS id
					FROM fs
					JOIN fs_files AS ff ON ff.fs_id = fs.id AND ff.file_id = $this->id";
			$query = mysqli_query($connection, $sql);
			$this->file_servers_ = [];

			while($row = mysqli_fetch_assoc($query)) {
				$this->file_servers_[] = new FileServer($row['id']);
			}

			return $this->file_servers_;
		}

		public function getMeta() {
			if(isset($this->meta_)) {
				return $this->meta_;
			}

			global $connection;

			$sql = "SELECT width, height, length, latitude, longitude, mime_type FROM meta WHERE file_id = $this->id";
			$query = mysqli_query($connection, $sql);
			$this->meta_ = mysqli_fetch_assoc($query) ?? [];

			return $this->meta_;
		}

		public function getWidth() {
			return $this->meta['width'] ?? null;
		}

		public function getHeight() {
			return $this->meta['height'] ?? null;
		}

		public function getLength() {
			return $this->meta['length'] ?? null;
		}

		public function getLatitude() {
			return $this->meta['latitude'] ?? null;
		}

		public function getLongitude() {
			return $this->meta['longitude'] ?? null;
		}

		public function getMimeType() {
			return $this->meta['mime_type'] ?? null;
		}

		public function getUrl() {
			if(isset($this->url_)) {
				return $this->url_;
			}

			foreach($this->file_servers as $file_server) {
				foreach(['https', 'http'] as $protocol) {
					$url = "$protocol://$file_server->domain/storage/$this->md5";
					$headers = @get_headers($url);

					if($headers && str_contains($headers[0], '200')) {
						return $this->url_ = $url;
					}
				}
			}
		}
	}

	class FileServer extends Entity {
		public function __construct($id) {
			if(filter_var($id, FILTER_VALIDATE_INT) === false) {
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
			if(filter_var($id, FILTER_VALIDATE_INT) === false) {
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
		protected $link_;
		protected $object_;
		protected $user_;

		public function __construct($id) {
			if(filter_var($id, FILTER_VALIDATE_INT) === false) {
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

		public function getLink() {
			$this->link_ ??= new Object_($this->link_id);

			return $this->link_;
		}

		public function getObject() {
			$this->object_ ??= new Object_($this->object_id);

			return $this->object_;
		}

		public function getUser() {
			$this->user_ ??= new Object_($this->user_id);

			return $this->user_;
		}
	}

	class Visit extends Entity {
		protected $object_;

		public function __construct($id, $count = null) {
			if(filter_var($id, FILTER_VALIDATE_INT) === false) {
				throw new Exception('0');
			}

			global $connection;

			$sql = "SELECT * FROM visits WHERE id = $id";
			$query = mysqli_query($connection, $sql);

			if(mysqli_num_rows($query) > 0) {
				$this->fields_ = mysqli_fetch_assoc($query);
			} else {
				throw new Exception('1');
			}

			$this->count = $count ?? null;
		}

		public function getObject() {
			$this->object_ ??= new Object_($this->object_id);

			return $this->object_;
		}
	}

	function entity_generateAlias($entity) {
		$parts = explode('_', $entity);
		$alias = '';

		foreach($parts as $part) {
			$alias .= $part[0];
		}

		return $alias;
	}

	function entity_search($entity, $class, $fields = null, $condition = null, $limit = null, $offset = null) {
		if(!preg_match('/^[a-zA-Z0-9_]+$/', $entity) || !class_exists($class)) {
			throw new InvalidArgumentException('Invalid entity/class title');
		}
		if(filter_var($limit, FILTER_VALIDATE_INT) === false) {
			$limit = null;
		} else
		if($limit < 1) {
			$limit = 1;
		}
		if(filter_var($offset, FILTER_VALIDATE_INT) === false || $offset < 0) {
			$offset = 0;
		}

		global $connection;

		$alias = entity_generateAlias($entity);
		$fields = !empty($fields) ? $fields : "$alias.id";
		$condition ??= '';
		$limit = isset($limit) ? " LIMIT $limit" : '';
		$offset_ = $offset > 0 ? " OFFSET $offset" : '';
		$sql = "SELECT $fields FROM $entity AS $alias $condition $limit $offset_";
		$query = mysqli_query($connection, $sql);
		$entities = [];

		while($row = mysqli_fetch_assoc($query)) {
			$entities[$offset++] = new $class(...array_values($row));
		}

		$condition = preg_replace('/(ORDER BY .*)$/i', '', $condition);
		$sql = "SELECT DISTINCT COUNT(*) OVER () FROM $entity AS $alias $condition";
		$query = mysqli_query($connection, $sql);
		$count = 0;

		if(mysqli_num_rows($query) > 0) {
			$count = mysqli_fetch_column($query);
		}

		return ['entities' => $entities, 'count' => $count];
	}

	function entity_get($entity, $class, $condition = null) {
		if(!preg_match('/^[a-zA-Z0-9_]+$/', $entity) || !class_exists($class)) {
			throw new InvalidArgumentException('Invalid entity/class title');
		}

		global $connection;

		$alias = entity_generateAlias($entity);
		$condition ??= '';
		$sql = "SELECT $alias.id FROM $entity AS $alias $condition";
		$query = mysqli_query($connection, $sql);
		$entities = [];

		while($row = mysqli_fetch_assoc($query)) {
			$entities[] = new $class($row['id']);
		}

		return $entities;
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
		$sql = "INSERT INTO objects (title, description, user_id, type_id)
							 VALUES ($title, $description, $user_id, $type_id)";
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

	function link_getAncestorsIDs($from_id, $to_id, $type_id) {
		global $connection;

		$where = implode(' AND ', array_filter([
			isset($from_id) ? "from_id = $from_id" : null,
			isset($to_id) ? "to_id = $to_id" : null,
			"type_id = $type_id"
		]));
		$sql = "WITH RECURSIVE link_chain AS (
					SELECT from_id, to_id, CAST(from_id AS CHAR(255)) AS path
					FROM links
					WHERE $where
					UNION ALL
					SELECT l.from_id, l.to_id, CONCAT(lc.path, ',', l.from_id)
					FROM links l
					INNER JOIN link_chain AS lc ON lc.to_id = l.from_id
					WHERE l.type_id = $type_id AND FIND_IN_SET(l.from_id, lc.path) = 0
				)
				SELECT to_id FROM link_chain";
		$query = mysqli_query($connection, $sql);
		$ancestors_ids = [];

		while($row = mysqli_fetch_assoc($query)) {
			$ancestors_ids[] = $row['to_id'];
		}

		return $ancestors_ids;
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

	/*
	function object_createUserID($login, $password, $email) {
		if(object_getUserID($login)) {
			return;
		}

		global $connection;

		$password_hash = password_hash($password, PASSWORD_BCRYPT);
		$everyone_group_id = 1;
		$system_user_id = 2;

		$sql_user = "INSERT INTO objects (type_id) VALUES (2);
					 SET @user_id = LAST_INSERT_ID();";

		$sql_user_setting = "INSERT INTO settings (object_id, `key`, value, user_id) VALUES
												  (@user_id, ?, ?, @user_id);";

		$sql_link = "INSERT INTO links (from_id, to_id, user_id, type_id) VALUES
									   (?, ?, ?, 1);
					 SET @link_id = LAST_INSERT_ID();";

		$sql_link_setting = "INSERT INTO settings (link_id, `key`, value, user_id) VALUES
												  (@link_id, ?, ?, ?);";

		$sql_group = "INSERT INTO objects (title, user_id, type_id) VALUES
										  (?, @user_id, 1);
					  SET @group_id = LAST_INSERT_ID();";

		$query = $connection->prepare("$sql_user $sql_user_setting $sql_user_setting $sql_user_setting
									   $sql_link $sql_link_setting
									   $sql_group
									   $sql_link $sql_link_setting
									   $sql_link $sql_link_setting
									   $sql_link $sql_link_setting
									   $sql_link $sql_link_setting $sql_link_setting $sql_link_setting $sql_link_setting");

		$parameters = [
			// User
			'login', $login,
			'password_hash', $password_hash,
			'email', $email,

			// Link from group of everyone to user
			$everyone_group_id, '@user_id', $system_user_id,
			'access_level_id', '2', $system_user_id,

			// Group of user
			"Group_$login",

			// Link from group of everyone to group of user
			$everyone_group_id, '@group_id', $system_user_id,
			'access_level_id', '2', $system_user_id,

			// Link from group of user to group of user
			'@group_id', '@group_id', '@user_id',
			'access_level_id', '2', '@user_id',

			// Link from group of user to user
			'@group_id', '@user_id', '@user_id',
			'access_level_id', '5', '@user_id',

			// Link from user to group of user
			'@user_id', '@group_id', '@user_id',
			'access_level_id', '5', '@user_id',
			'allow_invites', 'true', '@user_id',
			'allow_members_list_view', 'true', '@user_id',
			'allow_higher_access_preference', 'true', '@user_id'
		];

		$query->bind_param(str_repeat('s', count($parameters)), ...$parameters);
		$query->execute();
		$query->close();
	}
	*/

	function object_getSettings($object_id) {
		$settings = [];

		if(filter_var($object_id, FILTER_VALIDATE_INT) === false) {
			return $settings;
		}

		global $connection;

		$sql = "SELECT `key`, value FROM settings WHERE object_id = $object_id";
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

	function object_getGenericSearchCondition($query, $user_id = null, $section_id = null) {
		$words = preg_split('/\s+/', $query);
		$sql = "LEFT JOIN uploads AS u ON o.id = u.object_id
				LEFT JOIN files AS f ON u.file_id = f.id
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

	function object_getMostSearchCondition($most_id) {
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

	function object_createVisitID($object_id) {
		$ip_address = $_SERVER['REMOTE_ADDR'];
		$referrer_url = $_SERVER['HTTP_REFERER'] ?? '';
		$parts = parse_url($referrer_url);

		if(isset($parts['host']) && $parts['host'] == $_SERVER['SERVER_NAME']) {
			$referrer_url = $parts['path'].(!empty($parts['query']) ? '?'.$parts['query'] : '').(!empty($parts['fragment']) ? '#'.$parts['fragment'] : '');
		}

		$ip_address = $ip_address != '127.0.0.1' ? "'$ip_address'" : 'NULL';
		$referrer_url = !empty($referrer_url) ? "'$referrer_url'" : 'NULL';

		global $connection;

		$sql = "INSERT INTO visits (object_id, ip_address, referrer_url)
							VALUES ($object_id, $ip_address, $referrer_url)";
		$query = mysqli_query($connection, $sql);

		if($query) {
			return mysqli_insert_id($connection);
		}
	}
?>