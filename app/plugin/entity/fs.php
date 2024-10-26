<?
	class FileServer extends Entity {
		protected $size_;

		public function __construct($id = null) {
			if(isset($this->id)) {
				return;
			}
			if(filter_var($id, FILTER_VALIDATE_INT) === false) {
				throw new Exception('0');
			}

			global $connection;

			$sql = "SELECT * FROM fs WHERE id = $id";
			$query = $connection->query($sql);

			if($query->num_rows > 0) {
				$this->fields_ = $query->fetch_assoc();
			} else {
				throw new Exception('1');
			}
		}

		public function _getSize() {
			return $this->size_ ??= $this->used_size+$this->free_size;
		}
	}
?>