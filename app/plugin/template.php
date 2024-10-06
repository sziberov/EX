<?
	// Template

	class Template {
		protected $file_title;
		protected $namespace;

		public function __construct($file_title) {
			$this->file_title = $file_title;
		}

		public function __get($name) {
			if(isset($this->namespace[$name])) {
				return $this->namespace[$name];
			}

			return false;
		}

		public function __set($name, $value) {
			$this->namespace[$name] = $value;
		}

		public function __isset($name) {
			return isset($this->namespace[$name]);
		}

		public function __toString() {
			return $this->render();
		}

		public function render($print = false) {
			ob_start();

			$languages = $GLOBALS['languages'];
			$language = $GLOBALS['language'];
			$path = $GLOBALS['path'];
			$page = $GLOBALS['page'];

			include "plugin/$this->file_title.php";

			$rendered = ob_get_clean();

			if($print) {
				echo $rendered;

				return;
			}

			return $rendered;
		}
	}

	function template_parseBB($string) {
		$codes = [
			[
				'pattern' => '/\n/',
				'replacement' => '<br>'
			],
			[
				'pattern' => '/\t/',
				'replacement' => '&emsp;'
			],
			[
				'pattern' => '/\[b\](.*?)\[\/b\]/s',
				'replacement' => '<b>$1</b>'
			],
			[
				'pattern' => '/\[i\](.*?)\[\/i\]/s',
				'replacement' => '<i>$1</i>'
			],
			[
				'pattern' => '/\[u\](.*?)\[\/u\]/s',
				'replacement' => '<u>$1</u>'
			],
			[
				'pattern' => '/\[s\](.*?)\[\/s\]/s',
				'replacement' => '<s>$1</s>'
			],
			[
				'pattern' => '/\[sup\](.*?)\[\/sup\]/s',
				'replacement' => '<sup>$1</sup>'
			],
			[
				'pattern' => '/\[sub\](.*?)\[\/sub\]/s',
				'replacement' => '<sub>$1</sub>'
			],
			[
				'pattern' => '/\[url=(.*?)\](.*?)\[\/url\]/s',
				'replacement' => '<a href="$1">$2</a>'
			],
			[
				'pattern' => '/\[color=(.*?)\](.*?)\[\/color\]/s',
				'replacement' => '<span style="color: $1;">$2</span>'
			],
			/*
			[
				'pattern' => '/\[lang=(.*?)\](.*?)\[\/lang\]/s',
				'replacement' => '$2'
			],
			*/
			[
				'pattern' => '/\[code\](.*?)\[\/code\]/s',
				'replacement' => '<pre>$1</pre>'
			],
			[
				'pattern' => '/\[left\](.*?)\[\/left\]/s',
				'replacement' => '<div align="left">$1</div>'
			],
			[
				'pattern' => '/\[center\](.*?)\[\/center\]/s',
				'replacement' => '<div align="center">$1</div>'
			],
			[
				'pattern' => '/\[right\](.*?)\[\/right\]/s',
				'replacement' => '<div align="right">$1</div>'
			],
			[
				'pattern' => '/\[just\](.*?)\[\/just\]/s',
				'replacement' => '<div align="justify">$1</div>'
			]
		];

		global $language;

		$string = preg_replace_callback('/\[lang=(.*?)\](.*?)\[\/lang\]/s', function($matches) use ($language) {
			$languages = explode('|', $matches[1]);
			$text = $matches[2];

			return in_array($language, $languages) ? $text : '';
		}, $string);

		foreach($codes as $code) {
			$string = preg_replace($code['pattern'], $code['replacement'], $string);
		}

		return $string;
	}

	function template_clearBB($string) {
		$codes = [
			'/\n/',
			'/\t/',
			'/\[b\](.*?)\[\/b\]/s',
			'/\[i\](.*?)\[\/i\]/s',
			'/\[u\](.*?)\[\/u\]/s',
			'/\[s\](.*?)\[\/s\]/s',
			'/\[sup\](.*?)\[\/sup\]/s',
			'/\[sub\](.*?)\[\/sub\]/s',
			'/\[url=(?:.*?)\](.*?)\[\/url\]/s',
			'/\[color=(?:.*?)\](.*?)\[\/color\]/s',
			'/\[code\](.*?)\[\/code\]/s',
			'/\[left\](.*?)\[\/left\]/s',
			'/\[center\](.*?)\[\/center\]/s',
			'/\[right\](.*?)\[\/right\]/s',
			'/\[just\](.*?)\[\/just\]/s'
		];

		global $language;

		$string = preg_replace_callback('/\[lang=(.*?)\](.*?)\[\/lang\]/s', function($matches) use ($language) {
			$languages = explode('|', $matches[1]);
			$text = $matches[2];

			return in_array($language, $languages) ? $text : '';
		}, $string);

		foreach($codes as $pattern) {
			$string = preg_replace($pattern, '$1', $string);
		}

		return $string;
	}

	function template_formatTime($time, $date_only = false) {
		$date = date_create($time ?? '0');

		return (!$date_only ? $date->format('G:i').' ' : '').$date->format('j').' '.D['string_month_'.$date->format('n')-1].' '.$date->format('Y');
	}

	function template_formatLength($seconds) {
		$from = new DateTime('@0');
		$to = new DateTime("@$seconds");
		$difference = $from->diff($to)->format('%D:%H:%I:%S');
		$difference = preg_replace('/^(00:){1,2}/', '', $difference);
		$difference = preg_replace('/^0(\d:)/', '$1', $difference);

		return $difference;
	}

	function template_formatSize($size) {
		return number_format($size ?? 0);
	}

	/*
	1. Загружаем шаблон по умолчанию и спрашиваем у него, какой номер объекта он видит в данный момент.
	2. Проверяем этот номер: если его нет, оставляет текущий шаблон (по умолчанию). Если он есть, смотрим какой шаблон привязан к объекту.
	3. Если шаблон отличается от текущего (по умолчанию) и не был загружен ранее, загружаем его и спрашиваем у него, какой номер объекта он видит в данный момент.
	4. Проверяем этот номер: если его нет или он был проверен ранее, оставляем текущий шаблон (кастомный #1). Если он есть, смотрим какой шаблон привязан к объекту.
	5. Если шаблон отличается от текущего (кастомного #1) и не был загружен ранее, загружаем его и спрашиваем у него, какой номер объекта он видит в данный момент.
	6. Проверяем этот номер: если его нет или он был проверен ранее, оставляем текущий шаблон (кастомный #2). Если он есть, смотрим какой шаблон привязан к объекту.
	   И т.д.
	*/
	function template_getID() {
		$template_ids = [-1];
		$object_ids = [];

		while(true) {
			$template_id = end($template_ids);
			$object_id = end($object_ids);
			$template = template_getByID($template_id);
			$new_object_id = $template->route_getViewObjectID();

			if(empty($new_object_id) || in_array($new_object_id, $object_ids)) {
				return $template_id;
			}

			$object_ids[] = $new_object_id;
			$new_template_id = template_getIDByObjectID($new_object_id);

			if(empty($new_template_id) || in_array($new_template_id, $template_ids)) {
				return $template_id;
			}

			$template_ids[] = $new_template_id;
		}
	}

	function e($string) {
		return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
	}

	function template_escape($string) {
		return e($string);
	}

	function template_render() {
		global $languages,
			   $language,
			   $path,
			   $page;

		set_include_path(ROOT.'/app/interface');
		include 'plugin/page.php';
		set_include_path(ROOT);
	}
?>