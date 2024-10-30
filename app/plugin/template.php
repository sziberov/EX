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

	function template_parseBB($string, $clear = false) {
		$codes = [
			'/\n/' => '<br>',
			'/\t/' => '&emsp;',
			'/\[b\](.*?)\[\/b\]/s' => '<b>$1</b>',
			'/\[i\](.*?)\[\/i\]/s' => '<i>$1</i>',
			'/\[u\](.*?)\[\/u\]/s' => '<u>$1</u>',
			'/\[s\](.*?)\[\/s\]/s' => '<s>$1</s>',
			'/\[sup\](.*?)\[\/sup\]/s' => '<sup>$1</sup>',
			'/\[sub\](.*?)\[\/sub\]/s' => '<sub>$1</sub>',
			'/\[url=(.*?)\](.*?)\[\/url\]/s' => '<a href="$1">$2</a>',
			'/\[color=(.*?)\](.*?)\[\/color\]/s' => '<span style="color: $1;">$2</span>',
			'/\[left\](.*?)\[\/left\]/s' => '<div align="left">$1</div>',
			'/\[center\](.*?)\[\/center\]/s' => '<div align="center">$1</div>',
			'/\[right\](.*?)\[\/right\]/s' => '<div align="right">$1</div>',
			'/\[just\](.*?)\[\/just\]/s' => '<div align="justify">$1</div>'
		];

		$string = preg_replace(['/^(\[\w+(?:=[^\]]+)?\])[\r\n]+/m', '/[\r\n]+(\[\/\w+\][\r\n]*?)$/m'], '$1', $string);  // Trim line breaks after opening and before closing tags at start of lines

		$code_blocks = [];
		$string = preg_replace_callback('/\[code\](.*?)\[\/code\]/s', function($matches) use ($clear, &$code_blocks) {  // Hide code blocks from main replacer
			$code_marker = '<!--code_block_'.uniqid().'-->';
			$code_block = !$clear ? '<pre>'.$matches[1].'</pre>' : $matches[1];
			$code_blocks[$code_marker] = $code_block;

			return $code_marker;
		}, $string);

		global $language;
		$string = preg_replace_callback('/\[lang=(.*?)\](.*?)\[\/lang\]/s', function($matches) use ($language) {  // Use current language
			$languages = explode('|', $matches[1]);
			$text = in_array($language, $languages) ? $matches[2] : '';

			return $text;
		}, $string);

		$string = preg_replace(array_keys($codes), !$clear ? array_values($codes) : '$1', $string);  // Main replacer
		$string = strtr($string, $code_blocks);  // Show code blocks

		return $string;
	}

	function template_clearBB($string) {
		return template_parseBB($string, true);
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