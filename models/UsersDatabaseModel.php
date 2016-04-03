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


/**
* a read-only User object to reference
*/
class User {
	private $id;
	private $username;
	private $classid;

	public function __construct($data) {
		$this->id = (int)$data['id'];
		$this->classid = (int)$data['classid'];
		$this->username = (string)$data['username'];
	}
	public function __get($name) {
		if ($name === 'id') {
			return $this->id;
		} elseif ($name === 'classid') {
			return $this->classid;
		} elseif ($name === 'username') {
			return $this->username;
		}
	}
}


/**
* abstracts the users table
*/
class UsersDatabaseModel extends Model {
	public static $required = ['DatabaseModel'];

	/**
	* return a User object from a table row
	*/
	public function renderUser($data) {
		return new User($data);
	}

	/**
	* generates a secure 16 byte hex salt
	*/
	public function generateSalt() {
		$salt = openssl_random_pseudo_bytes(8);
		if ($salt === FALSE) {
			die('not enough entropy!');
		}
		return bin2hex($salt);
	}

	public function hashPassword($salt, $password) {
		$salt = (string)$salt;
		$password = (string)$password;
		return $salt . '/' . hash('sha256', $salt . $password);
	}

	/**
	* compares the given password to the stored password in the database for the given username
	* returns TRUE or FALSE depending on whether the user+pass is correct
	*/
	public function verifyLogin($username, $password) {
		$username = (string)$username;
		$password = (string)$password;

		// retrieve the salt/hash for the username
		$challenge = $this->getPasswordByUsername($username);
		if ($challenge === NULL) {
			return FALSE; // if the user doesn't exist
		} else {
			$salt = explode('/', $challenge)[0]; // get the salt
			// safely compare the hashes
			return hash_equals($this->hashPassword($salt, $password), $challenge);
		}
	}

	/**
	* private method to retrieve the salt/hash password
	*/
	private function getPasswordByUsername($username) {
		$username = $this->DatabaseModel->mysql_escape_string((string)$username);
		$result = $this->DatabaseModel->query("SELECT `password` FROM users WHERE `username`='${username}'");
		if (! is_object($result)) {
			return NULL;
		}
		if ($result->num_rows === 0) {
			$password = NULL;
		} else {
			$password = $result->fetch_assoc()['password'];
		}
		$result->free();
		return $password;
	}

	/**
	* returns a user object or null for a given id
	*/
	public function getUserById($id) {
		$id = (int)$id;
		$result = $this->DatabaseModel->query("SELECT `id`,`classid`, `username` FROM users WHERE `id`=${id}");
		if (! is_object($result)) {
			return NULL;
		}
		if ($result->num_rows === 0) {
			$user = NULL;
		} else {
			$user = $this->renderUser($result->fetch_assoc());
		}
		$result->free();
		return $user;
	}

	/**
	* returns a user object or null for a given username
	*/
	public function getUserByUsername($username) {
		$username = $this->DatabaseModel->mysql_escape_string($username);
		$result = $this->DatabaseModel->query("SELECT `id`,`classid`, `username` FROM users WHERE `username`='${username}'");
		if (! is_object($result)) {
			return NULL;
		}
		if ($result->num_rows === 0) {
			$user = NULL;
		} else {
			$user = $this->renderUser($result->fetch_assoc());
		}
		$result->free();
		return $user;
	}

	/**
	* creates a new user entry with the given username/password and classid
	* returns a user object for the new user if successful, otherwise null
	*/
	public function createUser($username, $password, $classid=3) {
		$username = $this->DatabaseModel->mysql_escape_string($username);
		$salt = $this->generateSalt();
		$password = $this->hashPassword($salt, (string)$password);
		$classid = (int)$classid;
		$result = $this->DatabaseModel->query("INSERT INTO `users` (`classid`, `username`, `password`) VALUES (${classid}, '${username}', '${password}')");
		if ($result === TRUE) {
			return $this->getUserById($this->DatabaseModel->insert_id);
		} else {
			return NULL;
		}
	}
}
