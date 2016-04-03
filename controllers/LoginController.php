<?php



class LoginController extends Controller {
	public static $required = ['CSRFTokenModel', 'UsersDatabaseModel'];
	public static $inherited = ['LoginView', 'RegisterView', 'UserErrorView'];

	public function invoke($args) {
		if ($args['page'] === 'login') {
			if (isset($_POST['username']) and isset($_POST['password']) and isset($_POST['csrf_token'])) {
				if (! $this->CSRFTokenModel->verify((string)$_POST['csrf_token'])) {
					$this->renderView('UserErrorView', ['invalid csrf token']);
				} else {
					$username = (string)$_POST['username'];
					$password = (string)$_POST['password'];
					if (! $this->UsersDatabaseModel->verifyLogin($username, $password)) {
						// TODO: render the login view with a message instead of user error
						$this->renderView('UserErrorView', ['invalid login']);
					} else {
						echo "login success!";
					}
				}
			} else {
				$this->renderView('LoginView');
			}
		} elseif ($args['page'] === 'register') {
			if (isset($_POST['username']) and isset($_POST['password']) and isset($_POST['repeat_password']) and isset($_POST['csrf_token'])) {
				if (! $this->CSRFTokenModel->verify((string)$_POST['csrf_token'])) {
					$this->renderView('UserErrorView', ['invalid csrf token']);
				} elseif ($_POST['repeat_password'] !== $_POST['password']) {
					// TODO: render the register view with a message instead of user error
					$this->renderView('UserErrorView', ['repeat password doesnt match']);
				} else {
					$username = (string)$_POST['username'];
					$password = (string)$_POST['password'];
					if (preg_match('/[^a-zA-Z0-9]/', $username)) {
						$this->renderView('UserErrorView', ['username may only contain alphanumeric characters']);
					} elseif (strlen($password) < 8) {
						$this->renderView('UserErrorView', ['password must be at least 8 characters']);
					} elseif ($this->UsersDatabaseModel->getUserByUsername($username) !== NULL) {
						$this->renderView('UserErrorView', ['username already taken']);
					} else {
						$user = $this->UsersDatabaseModel->createUser($username, $password);
						if ($user === NULL) {
							$this->renderView('UserErrorView', ['failed to create user']);
						} else {
							echo "register success!";
						}
					}
				}
			} else {
				$this->renderView('RegisterView');
			}
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}

