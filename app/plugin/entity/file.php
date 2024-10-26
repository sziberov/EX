<?
	class File extends Entity {
		protected $file_servers_;
		protected $upload_offsets_;

		public function __construct($id) {
			if(filter_var($id, FILTER_VALIDATE_INT) === false) {
				throw new Exception('0');
			}

			global $connection;

			$sql = "SELECT f.*, width, height, length, latitude, longitude, mime_type
					FROM files AS f
					LEFT JOIN meta AS m ON m.file_id = f.id
					WHERE f.id = $id";
			$query = $connection->query($sql);

			if($query->num_rows > 0) {
				$this->fields_ = $query->fetch_assoc();
			} else {
				throw new Exception('1');
			}
		}

		public function _getFileServers() {
			if(isset($this->file_servers_)) {
				return $this->file_servers_;
			}

			global $connection;

			$sql = "SELECT fs.*
					FROM fs
					JOIN fs_files AS ff ON ff.fs_id = fs.id AND ff.file_id = $this->id
					ORDER BY fs.id ASC";
			$query = $connection->query($sql);
			$this->file_servers_ = [];

			while($file_servers = $query->fetch_object('FileServer')) {
				$this->file_servers_[] = $file_servers;
			}

			return $this->file_servers_;
		}

		public function _getUploadOffsets() {
			if(isset($this->upload_offsets_)) {
				return $this->upload_offsets_;
			}

			global $connection;

			$sql = "SELECT fs_id, upload_offset FROM fs_files AS ff WHERE ff.file_id = $this->id ORDER BY fs_id ASC";
			$query = $connection->query($sql);
			$this->upload_offsets_ = array_column($query->fetch_all(MYSQLI_ASSOC), 'upload_offset', 'fs_id');

			return $this->upload_offsets_;
		}
	}
?>