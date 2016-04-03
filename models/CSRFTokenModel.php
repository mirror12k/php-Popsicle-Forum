<?php


// php 5.5 doesn't support hash_equals
// taken from https://secure.php.net/hash_equals
if(!function_exists('hash_equals')) {
	function hash_equals($str1, $str2) {
		if(strlen($str1) != strlen($str2)) {
			return FALSE;
		} else {
			$res = $str1 ^ $str2;
			$ret = 0;
			for($i = strlen($res) - 1; $i >= 0; $i--) {
				$ret |= ord($res[$i]);
			}
			return !$ret;
		}
	}
}





class CSRFTokenModel extends Model {
	private $gotten = FALSE;

	/**
	* assigns a new securely generated random seed and a new counter
	* should not be called directly, instead call get() or generate()
	* can die if there is not enough entropy
	* 
	* strength should be larger than the attackers power
	* resistance := 2^(8*$strength)
	*/ 
	public function init($strength=32) {
		$seed = openssl_random_pseudo_bytes($strength);
		if ($seed === FALSE) {
			die('Not enough entropy');
		}
		$seed = bin2hex($seed);
		$_SESSION['CSRFToken__seed'] = $seed;
		$_SESSION['CSRFToken__ctr'] = 0;
	}

	private function getToken() {
		return hash('sha256', $_SESSION['CSRFToken__seed'] . ((string)$_SESSION['CSRFToken__ctr']));
	}

	/**
	* invalidates the last csrf token and generates a new securely random token
	* if you need a token in multiple places, better call get() instead
	*/
	public function generate() {
		if (! isset($_SESSION['CSRFToken__seed'])) {
			$this->init();
		}
		// prevent value wrap aroung
		if ($_SESSION['CSRFToken__ctr'] >= PHP_INT_SIZE) {
			$this->init();
		}

		$_SESSION['CSRFToken__ctr'] = $_SESSION['CSRFToken__ctr'] + 1;
		$this->gotten = TRUE;
		return $this->getToken();
	}

	/**
	* if generate() has not been called yet during this instance, calls generate() and returns the new token
	* otherwise, returns the last token generated by generate()
	* generally, this is the function to use for getting your tokens
	*/
	public function get() {
		if ($this->gotten) {
			return $this->getToken();
		} else {
			return $this->generate();
		}
	}

	/**
	* verifies that the given token is indeed the last token issued
	* this should be done before any calls to generate() or get() the next token because they invalidate the last token upon being called
	* after the token is checked, generate() is called to invalidate the previous token in order to prevent token reuse
	*/
	public function verify($token) {
		if (! isset($_SESSION['CSRFToken__seed'])) {
			return FALSE;
		}
		$result = hash_equals($token, $this->getToken());
		// invalidate the token after checking it, this prevents token reuse
		$this->generate();
		return $result;
	}
}





