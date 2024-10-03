<?
	if (isAuthenticated()) {
	    $upload_id = $_POST['upload_id'];

	    $stmt = $mysqli->prepare("
	        SELECT o.id, f.md5_hash
	        FROM upload u
	        JOIN object o ON u.object_id = o.id
	        JOIN file f ON o.file_id = f.id
	        WHERE u.id = ?
	    ");
	    $stmt->bind_param('i', $upload_id);
	    $stmt->execute();
	    $stmt->bind_result($object_id, $md5_hash);

	    if ($stmt->fetch()) {
	        // Удаление записи из object
	        $stmt = $mysqli->prepare("DELETE FROM object WHERE id = ?");
	        $stmt->bind_param('i', $object_id);
	        $stmt->execute();

	        // Проверка, не используется ли этот файл другими объектами
	        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM object WHERE file_id = ?");
	        $stmt->bind_param('i', $file_id);
	        $stmt->execute();
	        $stmt->bind_result($count);
	        if ($stmt->fetch() && $count == 0) {
	            // Удаление файла с сервера, если он больше не нужен
	            unlink("/storage/{$md5_hash}");

	            // Удаление записи о файле
	            $stmt = $mysqli->prepare("DELETE FROM file WHERE id = ?");
	            $stmt->bind_param('i', $file_id);
	            $stmt->execute();
	        }

	        // Удаление записи о загрузке
	        $stmt = $mysqli->prepare("DELETE FROM upload WHERE id = ?");
	        $stmt->bind_param('i', $upload_id);
	        $stmt->execute();

	        echo json_encode(['status' => 'deleted']);
	    }
	}
?>