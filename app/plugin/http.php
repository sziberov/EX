<?
	// HTTP

	$query = $_GET;
	uksort($query, function($a, $b) {
		return strlen($b)-strlen($a);
	});
	$short_parameters = [];

	// json_decode(http_requestGet(DOMAIN_ROOT.'/app/api/objects.php?'.$filters.($navigation ? '&index='.$navigation_index.'&per='.$navigation_per : '')), true)
	function http_requestGet($url) {
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

	function http_requestPost($url, $data) {
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

	function http_getArgument($long_parameter) {
		global $query;

		foreach($query as $short_parameter => $value) {
			if(str_starts_with($long_parameter, $short_parameter)) {
				return $value;
			}
		}
	}

	function http_getShortParameter($long_parameter) {
		global $short_parameters;

		if(isset($short_parameters[$long_parameter])) {
			return $short_parameters[$long_parameter];
		}

		$short_parameter = '';

		for($i = 0; $i < strlen($long_parameter); $i++) {
			$short_parameter .= $long_parameter[$i];

			if(!in_array($short_parameter, $short_parameters)) {
				return $short_parameters[$long_parameter] = $short_parameter;
			}
		}

		return $long_parameter;
	}

	function http_getShortParameterQuery($long_parameter, $value) {
		return http_build_query(array_merge($_GET, [
			http_getShortParameter($long_parameter) => $value
		]));
	}
?>