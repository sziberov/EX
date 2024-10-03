<?
	$object_id = pathinfo($path[1] ?? '', PATHINFO_FILENAME);
	$extension = pathinfo($path[1] ?? '', PATHINFO_EXTENSION);

	if($extension != 'm3u' && $extension != 'xspf') {
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
	if($object->type_id == 2 || $object->files_length == 0 || $object->getSetting('hide_files_list')) {
		goto error_404;
	}

	$edit_time = gmdate('D, d M Y H:i:s', strtotime($object->edit_time)).' GMT';
	$mime_type = $extension == 'm3u' ? 'audio/x-mpegurl' : 'application/xspf+xml';
	$content = '';

	foreach($object->uploads as $upload) {
		if(!empty($upload->file->mime_type) && !str_starts_with($upload->file->mime_type, 'image/')) {
			if($extension == 'm3u') {
				$content .= DOMAIN_ROOT.'/get/'.$upload->id.PHP_EOL;
			} else {
				$content .= '<track>'.PHP_EOL.
							'	<title>'.htmlspecialchars($upload->title, ENT_QUOTES | ENT_XML1).'</title>'.PHP_EOL.
							'	<location>'.DOMAIN_ROOT.'/get/'.$upload->id.'</location>'.PHP_EOL.
							'</track>'.PHP_EOL;
			}
		}
	}

	if(empty($content)) {
		goto error_404;
	}

	if($extension == 'xspf') {
		$content = '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL.
				   '<playlist version="1" xmlns="http://xspf.org/ns/0/">'.PHP_EOL.
				   '<title>'.htmlspecialchars($object->title, ENT_QUOTES | ENT_XML1).'</title>'.PHP_EOL.
				   '<location>'.DOMAIN_ROOT.'/'.$object->id.'</location>'.PHP_EOL.
				   '<trackList>'.PHP_EOL.
				   $content.
				   '</trackList>'.PHP_EOL.
				   '</playlist>'.PHP_EOL;
	}

	header('Cache-Control: max-age=900, immutable, public');
	header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename="'.$path[1].'"');
	header('Content-Length: '.strlen($content));
	header('Content-Type: '.$mime_type);
	header('Expires: '.gmdate('D, d M Y H:i:s', time()+900).' GMT');
	header('Last-Modified: '.$edit_time);
	header('Pragma: public');

	exit($content);
?>