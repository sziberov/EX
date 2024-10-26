<?
	class Link extends Entity {
		public static $settings_filters = [  // 0: Boolean, 1: Integer
			1 => [  // Access
				'access_level_id'					=> 1,
				'allow_invites'						=> 0,
				'allow_members_list_view'			=> 0,
				'allow_higher_access_preference'	=> 0,
				'awaiting_accept'					=> 0
			],
			3 => [  // Notification
				'event_id'							=> 1
			],
			4 => [  // Inclusion
				'awaiting_moderation'				=> 0
			],
			9 => [  // Template
				'display_mode_id'					=> 1
			]
		];

		public static function createID($from_id, $to_id, $type_id) {
			$user_id = Session::getUserID();

			if(empty($from_id) || empty($user_id) || empty($type_id)) {
				return;
			}

			global $connection;

			$to_id ??= 'NULL';
			$sql = "INSERT INTO links (from_id, to_id, user_id, type_id)
					SELECT $from_id, $to_id, $user_id, $type_id
					WHERE NOT EXISTS (SELECT id
									  FROM links
									  WHERE from_id = $from_id
										AND to_id ".($to_id == 'NULL' ? 'IS' : '=')." $to_id
										AND user_id = $user_id
										AND type_id = $type_id)";
			$query = $connection->query($sql);

			if($query) {
				return $connection->insert_id;
			}
		}

		public static function getID($from_id, $to_id, $user_id, $type_id) {
			if(empty($from_id) || empty($user_id) || empty($type_id)) {
				return;
			}

			global $connection;

			$to_id ??= 'NULL';
			$sql = "SELECT id FROM links WHERE from_id = $from_id
										   AND to_id ".($to_id == 'NULL' ? 'IS' : '=')." $to_id
										   AND user_id = $user_id
										   AND type_id = $type_id";
			$query = $connection->query($sql);

			if($query) {
				return $query->fetch_column();
			}
		}

		public static function getAncestorsIDs($from_id, $to_id, $type_id) {
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
			$query = $connection->query($sql);
			$ancestors_ids = array_column($query->fetch_all(MYSQLI_ASSOC), 'to_id');

			return $ancestors_ids;
		}

		public static function getSiblingsIDs($from_id, $to_id, $type_id) {
			global $connection;

			$from_id ??= 'NULL';
			$to_id ??= 'NULL';
			$sql = "WITH ordered_links AS (
						SELECT LAG(from_id) OVER (ORDER BY id ASC) AS previous_id,
							   from_id,
							   LEAD(from_id) OVER (ORDER BY id ASC) AS next_id
						FROM links
						WHERE to_id = $to_id AND type_id = $type_id
					)
					SELECT previous_id,
						   (SELECT from_id FROM ordered_links WHERE from_id != $from_id ORDER BY RAND() LIMIT 1) AS random_id,
						   next_id
					FROM ordered_links
					WHERE from_id = $from_id";
			$query = $connection->query($sql);

			if($query->num_rows > 0) {
				return $query->fetch_assoc();
			}

			return [];
		}

		public static function getFilteredSetting($link, $key) {
			if(empty($link)) {
				foreach(self::$settings_filters as $sf) {
					$filter_id = $sf[$key] ?? null;

					if(isset($filter_id)) {
						break;
					}
				}
			} else {
				$filter_id = self::$settings_filters[$link->type_id][$key] ?? null;
			}

			if(!isset($filter_id)) {
				return;
			}

			$value = $link->settings[$key]->value ?? null;

			if($filter_id == 0) {
				return filter_var($value ?? false, FILTER_VALIDATE_BOOLEAN);
			}
			if($filter_id == 1) {
				return filter_var($value ?? 0, FILTER_VALIDATE_INT) ?? 0;
			}
		}

		protected $from_;
		protected $to_;
		protected $user_;
		protected $settings_;
		protected $filtered_settings_;
		protected $privileges_;

		public function __construct($id = null) {
			if(isset($this->id)) {
				return;
			}
			if(filter_var($id, FILTER_VALIDATE_INT) === false) {
				throw new Exception('0');
			}

			global $connection;

			$sql = "SELECT * FROM links WHERE id = $id";
			$query = $connection->query($sql);

			if($query->num_rows > 0) {
				$this->fields_ = $query->fetch_assoc();
			} else {
				throw new Exception('1');
			}
		}

		public function _getFrom() {
			return $this->from_ ??= new Object_($this->from_id);
		}

		public function _getTo() {
			return $this->to_ ??= new Object_($this->to_id);
		}

		public function _getUser() {
			return $this->user_ ??= new Object_($this->user_id);
		}

		public function _getSettings() {
			if(isset($this->settings_)) {
				return $this->settings_;
			}

			global $connection;

			$sql = "SELECT * FROM settings WHERE link_id = $this->id";
			$query = $connection->query($sql);
			$this->settings_ = [];

			while($setting = $query->fetch_object('Setting')) {
				$this->settings_[$setting->key] = $setting;
			}

			return $this->settings_;
		}

		public function _getPrivileges() {
			if(isset($this->privileges_)) {
				return $this->privileges_;
			}
			if($this->type_id != 1) {
				return;
			}

			$this->privileges_ = array_filter(self::$settings_filters[1], fn($k) => str_starts_with($k, 'allow_'), ARRAY_FILTER_USE_KEY);

			foreach($this->privileges_ as $k => $v) {
				$this->privileges_[$k] = $this->getSetting($k);
			}

			return $this->privileges_;
		}

		public function _getAccessLevelId() {
			$types = [
				1,	// access
				2,	// friend
			//	3,	// notification
			//	4,	// inclusion
			//	5,	// comment
				6,	// recommendation
			//	7,	// avatar
			//	8,	// claim
			//	9,	// template
				10,	// bookmark
			//	11	// private message
			];

		//	if(!in_array($this->type_id, $types)) {
		//		return 0;
		//	}

			$user_id = Session::getUserID();
			$access_level_id = $user_id == $this->user_id || (
			//	$this->type_id == 4 && ($user_id == $this->from->user_id || $user_id == $this->to->user_id) ||
			//	$this->type_id == 5 && $user_id == $this->to->user_id
				false
			);

			return $access_level_id;
		}

		public function getSetting($key) {
			return $this->filtered_settings_[$key] ??= self::getFilteredSetting($this, $key);
		}

		public function destroy() {
			global $connection;

			$sql = "DELETE FROM links WHERE id = $this->id";

			return $connection->query($sql);
		}
	}
?>