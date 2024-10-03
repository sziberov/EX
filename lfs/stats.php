<?
	include 'app/main.php';

	db_openConnection();

	$fs = fs_get();
	$response = [];

	if(!empty($fs)) {
		$response['fs_id'] = $fs->id;
	} else {
		$response['message'] = 'Server ('.fs_getDomain().') not found in DB';
	}

	$size = $response['size'] = disk_total_space('.');
	$free_size = $response['free_size'] = disk_free_space('.');
	$used_size = $response['used_size'] = $size-$free_size;

	if(!empty($fs)) {
		fs_setSize($fs->id, $used_size, $free_size);
	}

	echo json_encode($response);
?>