<?php


class LoginModel extends Model {
	public static $required = ['UsersDatabaseModel'];

	private $currentUser;

	public function loginUser($username, $password) {
		if ($this->UsersDatabaseModel->verifyLogin($username, $password)) {
			$user = $this->UsersDatabaseModel->getUserByUsername($username);
			if ($user->banned) {
				return 'user is banned';
			} else {
				$this->setLoggedInUser($user);
				return TRUE;
			}
		} else {
			return FALSE;
		}
	}

	public function logoutUser() {
		unset($_SESSION['PopsicleLoginModel__current_user']);
		$this->currentUser = NULL;
	}

	public function getCurrentUser() {
		if (! isset($this->currentUser)) {
			$this->loadCurrentUser();
		}
		return $this->currentUser;
	}

	private function loadCurrentUser() {
		if (isset($_SESSION['PopsicleLoginModel__current_user']) and $_SESSION['PopsicleLoginModel__current_user'] !== NULL) {
			$this->currentUser = $this->UsersDatabaseModel->getUserById($_SESSION['PopsicleLoginModel__current_user']);
			// kick the user if they were banned while being logged in
			if ($this->currentUser->banned) {
				$this->logoutUser();
			}
		} else {
			$this->currentUser = NULL;
		}
	}

	private function setLoggedInUser($user) {
		$_SESSION['PopsicleLoginModel__current_user'] = $user->id;
		$this->currentUser = $user;
	}
}
