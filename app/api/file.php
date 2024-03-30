<?
	$action_id = $_GET['action_id'] ?? 0;

	if($action_id == 0) {} // view
	if($action_id == 1) {} // create
	if($action_id == 2) {} // edit
	if($action_id == 3) {} // destroy

	if($_GET["_"] == 1) {
		echo '
			{
				"_": 1,
				"title": "background.png",
				"size": 2558625,
				"date": 1546300800,
				"md5": "c43f10467775456d14013d25fe708b1e",
				"length": "",
				"resolution": "2560|1440",
				"holder": 101
			}
		';
	}
	if($_GET["_"] == 2) {
		echo '
			{
				"_": 2,
				"title": "video.mp4",
				"size": 386537472,
				"date": 1546300800,
				"md5": "c43f10467775456d14013d25fe708b1e",
				"length": 2696,
				"resolution": "640|480",
				"holder": 101
			}
		';
	}
?>