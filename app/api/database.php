<?
	function openConnection() {
		$domain = 'localhost';
		$user = 'root';
		$password = '';
		$title = 'ex';
		$connection = new mysqli($domain, $user, $password, $title) or die('Connect failed: %s\n'.$connection -> error);

		return $connection;
	}

	function closeConnection($connection) {
		$connection -> close();
	}
?>