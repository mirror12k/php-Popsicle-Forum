<?php



class LoginController extends Controller {
	public static $required = ['CSRFTokenModel', 'UsersDatabaseModel', 'LoginModel'];
	public static $inherited = ['LoginView', 'RegisterView', 'UserErrorView'];

	public function invoke($args) {
		if ($args['page'] === 'login') {
			if (isset($_POST['username']) and isset($_POST['password']) and isset($_POST['csrf_token'])) {
				if (! $this->CSRFTokenModel->verify((string)$_POST['csrf_token'])) {
					$this->renderView('LoginView', ['error' => 'invalid csrf token']);
				} else {
					$username = (string)$_POST['username'];
					$password = (string)$_POST['password'];
					if (! $this->LoginModel->loginUser($username, $password)) {
						$this->renderView('LoginView', ['error' => 'invalid login']);
					} else {
						$this->redirect('forums');
						// echo "login success!";
					}
				}
			} else {
				$this->renderView('LoginView');
			}
		} elseif ($args['page'] === 'register') {
			if (isset($_POST['username']) and isset($_POST['password']) and isset($_POST['repeat_password']) and isset($_POST['csrf_token'])) {
				if (! $this->CSRFTokenModel->verify((string)$_POST['csrf_token'])) {
					$this->renderView('RegisterView', ['error' => 'invalid csrf token']);
				} elseif ($_POST['repeat_password'] !== $_POST['password']) {
					$this->renderView('RegisterView', ['password_error' => 'repeat password doesnt match']);
				} else {
					$username = (string)$_POST['username'];
					$password = (string)$_POST['password'];
					if (preg_match('/[^a-zA-Z0-9]/', $username)) {
						$this->renderView('RegisterView', ['username_error' => 'username may only contain alphanumeric characters']);
					} elseif (strlen($password) < 8) {
						$this->renderView('RegisterView', ['password_error' => 'password must be at least 8 characters']);
					} elseif ($this->UsersDatabaseModel->getUserByUsername($username) !== NULL) {
						$this->renderView('RegisterView', ['username_error' => 'username already taken']);
					} else {
						$user = $this->UsersDatabaseModel->createUser($username, $password);
						if ($user === NULL) {
							$this->renderView('RegisterView', ['error' => 'failed to create user']);
						} else {
							echo "register success!";
						}
					}
				}
			} else {
				$this->renderView('RegisterView');
			}
		} elseif ($args['page'] === 'logout') {
			if ($this->LoginModel->getCurrentUser() === NULL) {
				$this->renderView('UserErrorView', ['not logged in']);
			} else {
				$this->LoginModel->logoutUser();
				$this->redirect('forums');
				// echo "logged out";
			}
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}

