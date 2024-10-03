<?
	$object_id = pathinfo($path[1] ?? '', PATHINFO_FILENAME);
	$extension = pathinfo($path[1] ?? '', PATHINFO_EXTENSION);

	if($extension != 'urls') {
		error_404:
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	try {
		$object = new Object_($object_id);
	} catch(Exception $e) {
		goto error_404;
	}

	if($object->access_level_id == 0) {
		$error = D['error_page_forbidden'];
		http_response_code(403);
		return include 'plugin/error.php';
	}
	if($object->type_id == 2 || $object->files_count == 0 || $object->getSetting('hide_files_list')) {
		goto error_404;
	}

	$edit_time = gmdate('D, d M Y H:i:s', strtotime($object->edit_time)).' GMT';
	$content = '';

	foreach($object->uploads as $upload) {
		$content .= DOMAIN_ROOT.'/get/'.$upload->id.PHP_EOL;
	}

	header('Cache-Control: max-age=900, immutable, public');
	header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename="'.$path[1].'"');
	header('Content-Length: '.strlen($content));
	header('Content-Type: application/x-url-list');
	header('Expires: '.gmdate('D, d M Y H:i:s', time()+900).' GMT');
	header('Last-Modified: '.$edit_time);
	header('Pragma: public');

	exit($content);
?>