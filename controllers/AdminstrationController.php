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
			$this->renderView('UserErrorView', ['invalid csrf token']);
		} else {
			if ($_POST['action'] === 'edit_class'
					and $args['page'] === 'classes'
					and isset($_POST['classid'])
					and isset($_POST['name'])
					and isset($_POST['color'])) {
				$class = $this->UserClassesDatabaseModel->getUserClassById((int)$_POST['classid']);
				$user = $this->LoginModel->getCurrentUser();
				$userclass = $this->UserClassesDatabaseModel->getUserClassByUser($user);
				if ($user === NULL or (! $userclass->can('edit_lower_class')) or $class === NULL) {
					$this->renderView('UserErrorView', ['invalid action']);
				} elseif ($class->level >= $userclass->level) {
					$this->renderView('UserErrorView', ['cannot edit classes of higher or equal user class level']);
				} else {
					$name = (string)$_POST['name'];
					if ($name !== $class->name) {
						if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9 ]{0,63}$/', $name)) {
							$this->renderView('UserErrorView', ['name must be exactly be alphanumeric, max 64 characters']);
							return;
						} else {
							$this->UserClassesDatabaseModel->setUserClassName($class, $name);
						}
					}

					$level = (int)$_POST['level'];
					if ($level !== $class->level) {
						if ($level < 0) {
							$this->renderView('UserErrorView', ['level must be at least 0']);
							return;
						} elseif ($level >= $userclass->level) {
							$this->renderView('UserErrorView', ['level must be below your own level']);
							return;
						} else {
							$this->UserClassesDatabaseModel->setUserClassLevel($class, $level);
						}
					}

					$color = (string)$_POST['color'];
					if ($color !== $class->color) {
						if (! preg_match('/^[a-fA-F0-9]{6}$/', $color)) {
							$this->renderView('UserErrorView', ['color must be exactly 6 hexidecimal characters']);
							return;
						} else {
							$this->UserClassesDatabaseModel->setUserClassColor($class, $color);
						}
					}

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
		if ($args['page'] === 'admin') {
			$user = $this->LoginModel->getCurrentUser();
			if ($user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('edit_lower_class')) {
				$classes = $this->UserClassesDatabaseModel->listUserClasses();
				$this->renderView('AdminPanelView');
			} else {
				$this->renderView('UserErrorView', ['must be an admin to view the admin panel']);
			}
		} elseif ($args['page'] === 'classes') {
			$user = $this->LoginModel->getCurrentUser();
			if ($user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('edit_lower_class')) {
				$classes = $this->UserClassesDatabaseModel->listUserClasses();
				$this->renderView('ClassesView', ['classes' => $classes]);
			} else {
				$this->renderView('UserErrorView', ['invalid page']);
			}
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}
