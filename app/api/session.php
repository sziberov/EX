<?
	include 'database.php';

	$action = $_GET['action'] ?? '';

	if(in_array($action, ['login', 'logout'])) {
		$connection = openConnection();

		session_start();

		if($action == 'login') {
			$login = $_POST['login'] ?? '';
			$password = $_POST['password'] ?? '';
			$sql = "SELECT settings FROM objects WHERE
					JSON_CONTAINS(settings, '\"$login\"', '$.login') AND
					JSON_CONTAINS(settings, '\"$password\"', '$.password_hash')";
			$query = mysqli_query($connection, $sql);

			if(mysqli_num_rows($query) === 1) {
				$row = mysqli_fetch_assoc($query);
				$_SESSION['user_id'] = $row['id'];
			}
		}
		if($action == 'logout') {
			unset($_SESSION['user_id']);
		}

		closeConnection($connection);
	}

	echo json_encode(['logged_in' => isset($_SESSION['user_id'])]);
?>