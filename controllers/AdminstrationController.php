<?php



class AdminstrationController extends Controller {
	public static $required = ['LoginModel', 'UserClassesDatabaseModel', 'CSRFTokenModel'];
	public static $inherited = ['UserErrorView', 'ClassesView'];
	public function invoke($args) {
		if (isset($_POST['action']) and isset($args['page'])) {
			$this->invokeAction($args);
		} elseif (isset($args['page'])) {
			$this->invokePage($args);
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}

	public function invokeAction($args) {
		if (! (isset($_POST['csrf_token']) and $this->CSRFTokenModel->verify((string)$_POST['csrf_token']))) {
			$this->renderView('UserErrorView', ['error' => 'invalid csrf token']);
		} else {
			if ($_POST['action'] === 'edit_class' and $args['page'] === 'classes' and isset($_POST['classid'])) {
				$class = $this->UserClassesDatabaseModel->getUserClassById((int)$_POST['classid']);
				$user = $this->LoginModel->getCurrentUser();
				$userclass = $this->UserClassesDatabaseModel->getUserClassByUser($user);
				if ($user === NULL or (! $userclass->can('edit_lower_class')) or $class === NULL) {
					$this->renderView('UserErrorView', ['invalid action']);
				} elseif ($class->level >= $userclass->level) {
					$this->renderView('UserErrorView', ['cannot edit classes of higher or equal user class level']);
				} else {
					$privileges = $class->getAllPermissions();
					foreach (array_keys($privileges) as $key) {
						if (isset($_POST[$key]) and $_POST[$key] === 'on') {
							if ((! $userclass->can($key)) and (! $class->can($key))) {
								$this->renderView('UserErrorView', ['cant give a class permission that you dont have:' . $key]);
								return;
							} else {
								$privileges[$key] = 1;
							}
						} else {
							if ((! $userclass->can($key)) and $class->can($key)) {
								$this->renderView('UserErrorView', ['cant take away a class permission that you dont have:' . $key]);
								return;
							} else {
								$privileges[$key] = 0;
							}
						}
					}
					$this->UserClassesDatabaseModel->updateUserClassPermissions($class, $privileges);
					echo "success";
				}
			} else {
				$this->renderView('UserErrorView', ['invalid page']);
			}
		}
	}

	public function invokePage($args) {
		if ($args['page'] === 'classes') {
			$user = $this->LoginModel->getCurrentUser();
			if ($user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('edit_lower_class')) {
				$classes = $this->UserClassesDatabaseModel->listUserClasses();
				$this->renderView('ClassesView', ['classes' => $classes]);
			} else {
				$this->renderView('UserErrorView', ['invalid action']);
			}
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}
