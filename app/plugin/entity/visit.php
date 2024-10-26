<?
	class Visit extends Entity {
		protected $object_;

		public function __construct($id = null) {
			if(isset($this->id)) {
				return;
			}
			if(filter_var($id, FILTER_VALIDATE_INT) === false) {
				throw new Exception('0');
			}

			global $connection;

			$sql = "SELECT * FROM visits WHERE id = $id";
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
	}
?>