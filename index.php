<?
	include 'app/main.php';

	db_openConnection();
	template_render();
	db_closeConnection();
?>