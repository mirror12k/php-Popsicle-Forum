<?php



class PopsicleBBCodeView extends View {
	public function render($args) {
		$text = $args[0];


		$textBuffer = '';
		$tagStack = [];
		$bufferStack = [];
		$offset = 0;
		while (preg_match('/\[(\/(?:b|i|u|s|code|img|url|quote|size|color)|(?:b|i|u|s|code|img|url|url=[^\]]+|quote|quote=[^\]]+|size=\d{1,2}|color=#[a-fA-F0-9]{3}|color=#[a-fA-F0-9]{6}))\]|(.+?(?=\[|$))/',
				$text, $matches, PREG_OFFSET_CAPTURE, $offset)) {
			$offset = $matches[0][1] + strlen($matches[0][0]);
			// var_dump($matches);
			if ($matches[1][1] > 0) {
				// $result .= '|tag: ' . $matches[1][0];
				$tag = $matches[1][0];
				if (substr($tag, 0, 1) === '/') {
					$lastTag = array_pop($tagStack);
					if (strpos($lastTag, '=') === FALSE) {
						$lastType = $lastTag;
					} else {
						$lastType = substr($lastTag, 0, strpos($lastTag, '='));
					}
					if ('/' . $lastType === $tag) {
						$rendered = $this->renderTag($lastTag, $textBuffer);
						$textBuffer = array_pop($bufferStack);
						$textBuffer .= $rendered;
					} else {
						// invalid tag
						// $textBuffer = array_pop($bufferStack);
						// echo 'invalid tag: ';
					}
				} else {
					array_push($tagStack, $tag);
					array_push($bufferStack, $textBuffer);
					$textBuffer = '';
				}
			} else {
				$textBuffer .= htmlentities($matches[2][0], ENT_QUOTES);
			}
		}

		$result = '';
		foreach ($bufferStack as $lostBuffer) {
			$result .= $lostBuffer;
		}
		$result .= $textBuffer;

		echo $result;
	}

	private function renderTag($tag, $text) {
		if ($tag === 'b') {
			return '<b>' . $text . '</b>';
		} elseif ($tag === 'i') {
			return '<i>' . $text . '</i>';
		} elseif ($tag === 'u') {
			return '<u>' . $text . '</u>';
		} elseif ($tag === 's') {
			return '<s>' . $text . '</s>';
		} elseif ($tag === 'code') {
			return '<pre>' . $text . '</pre>';
		} elseif ($tag === 'quote') {
			return '<blockquote class="post_quote"><p>quote</p><p>' . $text . '</p></blockquote>';
		} elseif (strpos($tag, 'quote=') === 0) {
			$author = substr($tag, strlen('quote='));
			return '<blockquote class="post_quote"><p>quote by ' . htmlentities($author) . '</p><p>' . $text . '</p></blockquote>';
		} elseif (strpos($tag, 'size=') === 0) {
			$size = substr($tag, strlen('size='));
			return '<span style="font-size:' . htmlentities($size) . 'px">' . $text . '</span>';
		} elseif (strpos($tag, 'color=') === 0) {
			$color = substr($tag, strlen('color='));
			return '<span style="color:' . htmlentities($color) . '">' . $text . '</span>';

		} elseif ($tag === 'img') {
			$filtered = str_replace(['"', '\'', '<', '>', '\\'], '', $text);
			if ((strpos($text, 'http://') === 0 or strpos($text, 'https://') === 0) and filter_var($filtered, FILTER_VALIDATE_URL)) {
				return '<img src="' . $filtered . '" />';
			} else {
				return $text;
			}
		} elseif ($tag === 'url') {
			$filtered = str_replace(['"', '\'', '<', '>', '\\'], '', $text);
			if ((strpos($text, 'http://') === 0 or strpos($text, 'https://') === 0) and filter_var($filtered, FILTER_VALIDATE_URL)) {
				return '<a href="' . $filtered . '">' . $text . '</a>';
			} else {
				return $text;
			}
		} elseif (strpos($tag, 'url=') === 0) {
			$link = substr($tag, strlen('url='));
			$filtered = str_replace(['"', '\'', '<', '>', '\\'], '', $link);
			if ((strpos($link, 'http://') === 0 or strpos($link, 'https://') === 0) and filter_var($link, FILTER_VALIDATE_URL)) {
				return '<a href="' . $link . '">' . $text . '</a>';
			} else {
				return $text;
			}
		} else {
			die ("invalid tag: " . $tag);
		}
	}
}
