<?php


class LoginModel extends Model {
	public static $required = ['UsersDatabaseModel'];

	private $currentUser;

	public function loginUser($username, $password) {
		if ($this->UsersDatabaseModel->verifyLogin($username, $password)) {
			$this->setLoggedInUser($this->UsersDatabaseModel->getUserByUsername($username));
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getCurrentUser() {
		if (! isset($this->currentUser)) {
			$this->loadCurrentUser();
		}
		return $this->currentUser;
	}

	private function loadCurrentUser() {
		if (isset($_SESSION['PopsicleLoginModel__current_user'])) {
			$this->currentUser = $this->UsersDatabaseModel->getUserById($_SESSION['PopsicleLoginModel__current_user']);
		} else {
			$this->currentUser = NULL;
		}
	}

	private function setLoggedInUser($user) {
		$_SESSION['PopsicleLoginModel__current_user'] = $user->id;
		$this->currentUser = $user;
	}

	public function logoutUser() {
		unset($_SESSION['PopsicleLoginModel__current_user']);
		$this->currentUser = NULL;
	}
}
