<?
	// Dictionary

	$languages = ['ru', 'en'];
	$language = $_COOKIE['language'] ?? null;
	$strings;

	function dictionary_parseDefaultLanguage($http_accept, $default = 'ru') {
	    if(!isset($http_accept) || strlen($http_accept) <= 1) {
	        return strtolower($default);
	    }

	    $languages = array_reduce(
	        explode(',', $http_accept),
	        function($carry, $lang) {
	            $parts = explode(';q=', $lang);
	            $carry[$parts[0]] = isset($parts[1]) ? (float)$parts[1] : 1.0;

	            return $carry;
	        },
	        []
	    );

	    arsort($languages, SORT_NUMERIC);

	    return strtolower(key($languages));
	}

	function dictionary_getDefaultLanguage() {
		return dictionary_parseDefaultLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null);
	}

	function dictionary_setLanguage($language) {
		global $strings;

	//	$strings = json_decode(request_get(DOMAIN_ROOT.'/app/language/'.$language.'.json'), true);
		$strings = json_decode(file_get_contents(ROOT.'/app/language/'.$language.'.json'), true);
	}

	function dictionary_setDefaultLanguage() {
		global $languages,
			   $language;

		if(in_array($language, $languages)) {
			return;
		}

		$default = strstr(dictionary_parseDefaultLanguage(dictionary_getDefaultLanguage(), $languages[0]), '-', true);

		if(!empty($default) && in_array($default, $languages)) {
			$language = $default;
		} else {
			$language = $languages[0];
		}
	}

	function dictionary_getString($string) {
		global $strings;

		return $strings[$string];
	}

	function dictionary_getPageTitle($title) {
		global $strings;

		$app_title = $strings['string_app_title'];

		return strlen($title) > 0 ? $title.' @ '.$app_title : $app_title;
	}

	function dictionary_getPageDescription($description) {
		global $strings;

		$app_description = $strings['string_app_description'];

		return strlen($description) > 0 ? $description : $app_description;
	}

	dictionary_setDefaultLanguage();
	dictionary_setLanguage($language);

	define('D', $strings);
?>