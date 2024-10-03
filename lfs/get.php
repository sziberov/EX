<?
	include 'app/main.php';

	db_openConnection();

	$path = explode('/', $_SERVER['PATH_INFO']);
	array_shift($path);
	$fs = fs_get()										?? exit(http_response_code(404));
	$upload = upload_getByID($path[0] ?? null)			?? exit(http_response_code(404));
	$fs_file = fs_file_get($fs->id, $upload->file_id)	?? exit(http_response_code(404));
	$file = file_getByID($upload->file_id)				?? exit(http_response_code(404));

	if($fs_file->upload_offset < $file->size || empty($file->md5)) {
		return http_response_code(403);
	}

	$file_path = 'storage/'.$file->id;
	$file_title = $path[1] ?? $upload->title;

	if(!file_exists($file_path)) {
		exit(http_response_code(404));
	}

	$meta = meta_get($file->id) ?? null;
	$mime_type = $meta->mime_type ?? 'application/octet-stream';

	header('Cache-Control: max-age=31536000, immutable, public');
	header('Content-Description: File Transfer');
	header('Content-Disposition: inline; filename="'.$file_title.'"');
	header('Content-Length: '.$file->size);
	header('Content-Type: '.$mime_type);
	header('Expires: '.gmdate('D, d M Y H:i:s', time()+31536000).' GMT');
	header('Pragma: public');

	readfile($file_path);
?>