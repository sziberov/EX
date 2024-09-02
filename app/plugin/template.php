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

			include 'app/template/'.$this->file_title;

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
			'newline' => [
				'pattern' => '/\n/',
				'replacement' => '<br>'
			],
			'tab' => [
				'pattern' => '/\t/',
				'replacement' => '&emsp;'
			],
			'b' => [
				'pattern' => '/\[b\](.*?)\[\/b\]/s',
				'replacement' => '<b>$1</b>'
			],
			'i' => [
				'pattern' => '/\[i\](.*?)\[\/i\]/s',
				'replacement' => '<i>$1</i>'
			],
			'u' => [
				'pattern' => '/\[u\](.*?)\[\/u\]/s',
				'replacement' => '<u>$1</u>'
			],
			's' => [
				'pattern' => '/\[s\](.*?)\[\/s\]/s',
				'replacement' => '<s>$1</s>'
			],
			'sup' => [
				'pattern' => '/\[sup\](.*?)\[\/sup\]/s',
				'replacement' => '<sup>$1</sup>'
			],
			'sub' => [
				'pattern' => '/\[sub\](.*?)\[\/sub\]/s',
				'replacement' => '<sub>$1</sub>'
			],
			'url' => [
				'pattern' => '/\[url=(.*?)\](.*?)\[\/url\]/s',
				'replacement' => '<a href="$1">$2</a>'
			],
			'color' => [
				'pattern' => '/\[color=(.*?)\](.*?)\[\/color\]/s',
				'replacement' => '<span style="color: $1;">$2</span>'
			],
			'lang' => [
				'pattern' => '/\[lang=(.*?)\](.*?)\[\/lang\]/s',
				'replacement' => '$2'
			],
			'left' => [
				'pattern' => '/\[left\](.*?)\[\/left\]/s',
				'replacement' => '<div align="left">$1</div>'
			],
			'center' => [
				'pattern' => '/\[center\](.*?)\[\/center\]/s',
				'replacement' => '<div align="center">$1</div>'
			],
			'right' => [
				'pattern' => '/\[right\](.*?)\[\/right\]/s',
				'replacement' => '<div align="right">$1</div>'
			],
			'just' => [
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

		foreach($codes as $code => $replacement) {
			$string = preg_replace($replacement['pattern'], $replacement['replacement'], $string);
		}

		return $string;
	}

	function template_formatTime($time, $date_only = false) {
		$date = date_create($time ?? '0');
		$months = [
			D['string_january'],
			D['string_february'],
			D['string_march'],
			D['string_april'],
			D['string_may'],
			D['string_june'],
			D['string_july'],
			D['string_august'],
			D['string_september'],
			D['string_october'],
			D['string_november'],
			D['string_december']
		];

		return (!$date_only ? $date->format('G:i').' ' : '').$date->format('j').' '.$months[$date->format('n')-1].' '.$date->format('Y');
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

	function template_render() {
		global $languages;

		include 'app/template/page';
	}
?>