<?
	try {
		$upload = new Upload($path[1] ?? null);
	} catch(Exception $e) {
		error_404:
		$error = D['error_page_not_found'];
		http_response_code(404);
		return include 'plugin/error.php';
	}

	if(empty($upload->fs_domain)) {
		goto error_404;
	}

	header("Location: $upload->fs_domain/$page/$upload->id");
	header('Cache-Control: max-age=31536000, immutable, public');
	header('Expires: '.gmdate('D, d M Y H:i:s', time()+31536000).' GMT');
	header('Pragma: public');

	exit;
?>