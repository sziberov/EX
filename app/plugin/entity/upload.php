<?
	class Upload extends Entity {
		protected $object_;
		protected $file_;
		protected $fs_domain_;

		public function __construct($id = null) {
			if(isset($this->id)) {
				return;
			}
			if(filter_var($id, FILTER_VALIDATE_INT) === false) {
				throw new Exception('0');
			}

			global $connection;

			$sql = "SELECT * FROM uploads WHERE id = $id";
			$query = $connection->query($sql);

			if($query->num_rows > 0) {
				$this->fields_ = $query->fetch_assoc();
			} else {
				throw new Exception('1');
			}
		}

		public function _getObject() {
			return $this->object_ ??= new Object_($this->object_id);
		}

		public function _getFile() {
			return $this->file_ ??= new File($this->file_id);
		}

		public function _getFsDomain() {
			if(isset($this->fs_domain_)) {
				return $this->fs_domain_;
			}

			global $connection;

			$sql = "SELECT fs.id, fs.domain
					FROM fs
					JOIN uploads AS u ON u.id = $this->id
					JOIN files AS f ON f.id = u.file_id
					JOIN fs_files AS ff ON ff.fs_id = fs.id AND ff.file_id = f.id AND ff.upload_offset >= f.size";
			$query = $connection->query($sql);

			while($row = $query->fetch_assoc()) {
				foreach(['https', 'http'] as $protocol) {
					$url = "$protocol://{$row['domain']}";
					$headers = @get_headers($url.'/robots.txt');

					if($headers && str_contains($headers[0], '200')) {
						return $this->fs_domain_ = $url;
					}
				}
			}
		}
	}
?>