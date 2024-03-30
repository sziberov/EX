<?
	include 'database.php';

	$connection = openConnection();
	$object_id = $_GET['id'] ?? '';
	$action_id = $_GET['action_id'] ?? 0;

	if($action_id == 0) { // view
		$sql = "SELECT objects.*, COUNT(inclusion_links.id) AS inclusions_count, COUNT(objects_files.id) AS files_count, COUNT(comment_links.id) AS comments_count FROM objects
				LEFT JOIN links AS inclusion_links ON inclusion_links.to_id = objects.id AND inclusion_links.type_id = 4
				LEFT JOIN links AS comment_links ON comment_links.to_id = objects.id AND comment_links.type_id = 7
				LEFT JOIN objects_files ON objects_files.object_id = objects.id
				WHERE objects.id = '$object_id'
				GROUP BY objects.id";
		$query = mysqli_query($connection, $sql);
		$object = (object)[];

		if(mysqli_num_rows($query) === 1) {
			$object = mysqli_fetch_assoc($query);

			if(isset($object['settings'])) {
				$object['settings'] = json_decode($object['settings']);
			}
		}

		echo json_encode($object, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}
	if($action_id == 1) {} // create
	if($action_id == 2) {} // edit
	if($action_id == 3) {} // destroy

	closeConnection($connection);
?>