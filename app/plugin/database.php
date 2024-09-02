<?
	// Database

	$connection;

	function db_openConnection() {
		global $connection;

		$domain = 'localhost';
		$user = 'root';
		$password = '';
		$title = 'ex';
		$connection = new mysqli($domain, $user, $password, $title) or die('Connection failed: %s\n'.$connection->error);

		return $connection;
	}

	function db_closeConnection() {
		global $connection;

		$connection->close();
	}
?>