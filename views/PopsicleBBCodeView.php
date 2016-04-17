<?php



class PopsicleBBCodeView extends View {
	public function render($args) {
		$text = $args[0];


		$textBuffer = '';
		$tagStack = [];
		$bufferStack = [];
		$offset = 0;
		while (preg_match('/\[(\/?(?:b|i|u|s|url|img|code))\]|(.+?(?=\[|$))/', $text, $matches, PREG_OFFSET_CAPTURE, $offset)) {
			$offset = $matches[0][1] + strlen($matches[0][0]);
			// var_dump($matches);
			if ($matches[1][1] > 0) {
				// $result .= '|tag: ' . $matches[1][0];
				$tag = $matches[1][0];
				if (substr($tag, 0, 1) === '/') {
					$lastTag = array_pop($tagStack);
					if ('/' . $lastTag === $tag) {
						$rendered = $this->renderTag($lastTag, $textBuffer);
						$textBuffer = array_pop($bufferStack);
						$textBuffer .= $rendered;
					} else {
						$textBuffer = array_pop($bufferStack);
						// invalid tag
						echo 'invalid tag';
					}
				} else {
					array_push($tagStack, $tag);
					array_push($bufferStack, $textBuffer);
					$textBuffer = '';
				}
			} else {
				$textBuffer .= htmlentities($matches[2][0]);
			}
		}

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
		// } elseif ($tag === 'url') {
		// 	return '<a href="' . htmlentities($text, ENT_QUOTES) . '">' . $text . '</a>';
		} else {
			die ("invalid tag: " . $tag);
		}
	}
}
