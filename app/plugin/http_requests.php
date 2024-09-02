<?
	// HTTP Requests

	// json_decode(request_get(DOMAIN_ROOT.'/app/api/objects.php?'.$filters.($navigation ? '&index='.$navigation_index.'&per='.$navigation_per : '')), true)
	function request_get($url) {
		if(!headers_sent()) session_write_close(); // Preserve session
		$curl = curl_init($url);
		if(!headers_sent()) curl_setopt($curl, CURLOPT_COOKIE, session_name().'='.session_id()); // Preserve session
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // For HTTPS
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // For HTTPS
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);
		if(!headers_sent()) session_start(); // Preserve session

		return $response;
	}

	function request_post($url, $data) {
		if(!headers_sent()) session_write_close(); // Preserve session
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		if(!headers_sent()) curl_setopt($curl, CURLOPT_COOKIE, session_name().'='.session_id()); // Preserve session
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // For HTTPS
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // For HTTPS
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);
		if(!headers_sent()) session_start(); // Preserve session

		return $response;
	}
?>