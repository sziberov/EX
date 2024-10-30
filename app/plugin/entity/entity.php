<?
	class Entity {
		public static function generateAlias($entity) {
			$parts = explode('_', $entity);
			$alias = '';

			foreach($parts as $part) {
				$alias .= $part[0];
			}

			return $alias;
		}

		public static function search($entity, $class, $fields = null, $condition = null, $limit = null, $offset = null) {
			if(!preg_match('/^([a-zA-Z0-9_]+)(?:\s+([a-zA-Z0-9_]+))?$/', $entity, $matches) || !class_exists($class)) {
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

			$entity = $matches[1];
			$alias = isset($matches[2]) ? $matches[2] : self::generateAlias($entity);
			$fields = !empty($fields) ? $fields : "$alias.*";
			$condition ??= '';
			$limit = isset($limit) ? " LIMIT $limit" : '';
			$offset_ = $offset > 0 ? " OFFSET $offset" : '';
			$sql = "SELECT $fields FROM $entity AS $alias $condition $limit $offset_";
			$query = $connection->query($sql);
			$entities = [];

			while($row = $query->fetch_object($class)) {
				$entities[$offset++] = $row;
			}

			$condition = preg_replace('/ORDER\sBY[\s\w,.()-+]*?$/is', '', $condition);
			$sql = "SELECT DISTINCT COUNT(*) OVER () FROM $entity AS $alias $condition";
			$query = $connection->query($sql);
			$count = 0;

			if($query->num_rows > 0) {
				$count = $query->fetch_column();
			}

			return ['entities' => $entities, 'count' => $count];
		}

		public static function get($entity, $class, $fields = null, $condition = null) {
			if(!preg_match('/^([a-zA-Z0-9_]+)(?:\s+([a-zA-Z0-9_]+))?$/', $entity, $matches) || !class_exists($class)) {
				throw new InvalidArgumentException('Invalid entity/class title');
			}

			global $connection;

			$entity = $matches[1];
			$alias = isset($matches[2]) ? $matches[2] : self::generateAlias($entity);
			$fields = !empty($fields) ? $fields : "$alias.*";
			$condition ??= '';
			$sql = "SELECT $fields FROM $entity AS $alias $condition";
			$query = $connection->query($sql);
			$entities = [];
			$offset = 0;

			while($row = $query->fetch_object($class)) {
				$entities[$offset++] = $row;
			}

			$count = $query->num_rows;

			return ['entities' => $entities, 'count' => $count];
		}

		protected $fields_ = [];

		public function __set($name, $value) {
			$this->fields_[$name] = $value;
		}

		public function __get($name) {
			if(isset($this->fields_[$name])) {
				return $this->fields_[$name];
			}

			$getter = '_get'.str_replace('_', '', ucwords($name, '_'));

			if(method_exists($this, $getter)) {
				return $this->$getter();

				/*
				$reflection_method = new ReflectionMethod($this, $getter);

				if(!$reflection_method->isStatic()) {
					return $this->$getter();
				}
				*/
			}
		}

		public function __isset($name) {
			$getter = '_get'.str_replace('_', '', ucwords($name, '_'));

			return isset($this->fields_[$name]) || isset($this->$name) || method_exists($this, $getter) && !is_null($this->$getter());

			/*
			if(method_exists($this, $getter)) {
				$reflection_method = new ReflectionMethod($this, $getter);

				return !$reflection_method->isStatic() && !is_null($this->$getter());
			}

			return isset($this->fields_[$name]) || isset($this->$name);
			*/
		}
	}
?>