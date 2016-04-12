<?php



class LoginController extends Controller {
	public static $required = ['CSRFTokenModel', 'UsersDatabaseModel', 'LoginModel'];
	public static $inherited = ['LoginView', 'RegisterView', 'UserErrorView', 'EditPasswordView'];

	public function invoke($args) {
		$user = $this->LoginModel->getCurrentUser($username, $password);
		if ($args['page'] === 'login') {
			if ($user !== NULL) {
				$this->renderView('UserErrorView', ['cant login while logged in']);
			} else {
				if (isset($_POST['username']) and isset($_POST['password']) and isset($_POST['csrf_token'])) {
					if (! $this->CSRFTokenModel->verify((string)$_POST['csrf_token'])) {
						$this->renderView('LoginView', ['error' => 'invalid csrf token']);
					} else {
						$username = (string)$_POST['username'];
						$password = (string)$_POST['password'];
						$status = $this->LoginModel->loginUser($username, $password);
						if ($status === FALSE) { // failed login
							$this->renderView('LoginView', ['error' => 'invalid login']);
						} elseif ($status === TRUE) { // successful login
							$this->redirect('forums');
							// echo "login success!";
						} else { // else likely a ban message
							$this->renderView('LoginView', ['error' => $status]);
						}
					}
				} else {
					$this->renderView('LoginView');
				}
			}
		} elseif ($args['page'] === 'register') {
			if ($user !== NULL) {
				$this->renderView('UserErrorView', ['cant register while logged in']);
			} else {
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
								$this->redirect('login');
								// echo "register success!";
							}
						}
					}
				} else {
					$this->renderView('RegisterView');
				}
			}
		} elseif ($args['page'] === 'edit_password') {
			if ($user === NULL) {
				$this->renderView('UserErrorView', ['not logged in']);
			} else {
				if (isset($_POST['password']) and isset($_POST['repeat_password']) and isset($_POST['csrf_token'])) {
					if (! $this->CSRFTokenModel->verify((string)$_POST['csrf_token'])) {
						$this->renderView('EditPasswordView', ['error' => 'invalid csrf token']);
					} elseif ($_POST['repeat_password'] !== $_POST['password']) {
						$this->renderView('EditPasswordView', ['error' => 'repeat password doesnt match']);
					} else {
						$password = (string)$_POST['password'];
						if (strlen($password) < 8) {
							$this->renderView('EditPasswordView', ['error' => 'password must be at least 8 characters']);
						} else {
							$this->UsersDatabaseModel->setUserPassword($user, $password);
							echo "success!";
						}
					}
				} else {
					$this->renderView('EditPasswordView');
				}
			}
		} elseif ($args['page'] === 'logout') {
			if ($user === NULL) {
				$this->renderView('UserErrorView', ['not logged in']);
			} else {
				$this->LoginModel->logoutUser();
				$this->redirect('forums');
			}
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}

