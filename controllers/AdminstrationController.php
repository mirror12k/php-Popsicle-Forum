<?php



class AdminstrationController extends Controller {
	public static $required = ['LoginModel', 'UserClassesDatabaseModel', 'CSRFTokenModel'];
	public static $inherited = ['UserErrorView', 'ClassesView'];
	public function invoke($args) {
		if (isset($_POST['action']) and isset($args['page'])) {
			if (! (isset($_POST['csrf_token']) and $this->CSRFTokenModel->verify((string)$_POST['csrf_token']))) {
				$this->renderView('UserErrorView', ['invalid csrf token']);
			} else {
				$this->invokeAction($args);
			}
		} elseif (isset($args['page'])) {
			$this->invokePage($args);
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}

	public function invokeAction($args) {
		$user = $this->LoginModel->getCurrentUser();
		if ($_POST['action'] === 'edit_class'
				and $args['page'] === 'classes'
				and isset($_POST['classid'])
				and isset($_POST['name'])
				and isset($_POST['color'])
				and $user !== NULL) {
			$class = $this->UserClassesDatabaseModel->getUserClassById((int)$_POST['classid']);
			$userclass = $this->UserClassesDatabaseModel->getUserClassByUser($user);
			if ((! $userclass->can('edit_lower_class')) or $class === NULL) {
				$this->renderView('UserErrorView', ['invalid action']);
			} elseif ($class->level >= $userclass->level) {
				$this->renderView('ClassesView', ['classes' => $this->UserClassesDatabaseModel->listUserClasses(),
					'error' => 'cannot edit classes of higher or equal user class level']);
			} else {
				$name = (string)$_POST['name'];
				if ($name !== $class->name) {
					if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9 ]{0,63}$/', $name)) {
						$this->renderView('ClassesView', ['classes' => $this->UserClassesDatabaseModel->listUserClasses(),
							'error' => 'name must be exactly be alphanumeric, max 64 characters']);
						return;
					} else {
						$this->UserClassesDatabaseModel->setUserClassName($class, $name);
					}
				}

				$level = (int)$_POST['level'];
				if ($level !== $class->level) {
					if ($level < 0) {
						$this->renderView('ClassesView', ['classes' => $this->UserClassesDatabaseModel->listUserClasses(),
							'error' => 'level must be at least 0']);
						return;
					} elseif ($level >= $userclass->level) {
						$this->renderView('ClassesView', ['classes' => $this->UserClassesDatabaseModel->listUserClasses(),
							'error' => 'level must be below your own level']);
						return;
					} else {
						$this->UserClassesDatabaseModel->setUserClassLevel($class, $level);
					}
				}

				$color = (string)$_POST['color'];
				if ($color !== $class->color) {
					if (! preg_match('/^[a-fA-F0-9]{6}$/', $color)) {
						$this->renderView('ClassesView', ['classes' => $this->UserClassesDatabaseModel->listUserClasses(),
							'error' => 'color must be exactly 6 hexidecimal characters']);
						return;
					} else {
						$this->UserClassesDatabaseModel->setUserClassColor($class, $color);
					}
				}

				$privileges = $class->getAllPermissions();
				foreach (array_keys($privileges) as $key) {
					if (isset($_POST[$key]) and $_POST[$key] === 'on') {
						if ((! $userclass->can($key)) and (! $class->can($key))) {
							$this->renderView('ClassesView', ['classes' => $this->UserClassesDatabaseModel->listUserClasses(),
								'error' => 'cant give a class permission that you dont have:' . $key]);
							return;
						} else {
							$privileges[$key] = 1;
						}
					} else {
						if ((! $userclass->can($key)) and $class->can($key)) {
							$this->renderView('ClassesView', ['classes' => $this->UserClassesDatabaseModel->listUserClasses(),
								'error' => 'cant take away a class permission that you dont have:' . $key]);
							return;
						} else {
							$privileges[$key] = 0;
						}
					}
				}
				$this->UserClassesDatabaseModel->updateUserClassPermissions($class, $privileges);

				$this->renderView('ClassesView', ['classes' => $this->UserClassesDatabaseModel->listUserClasses(),
					'message' => 'successfully changed class!']);
			}

		} elseif ($_POST['action'] === 'new_class') {
			$userclass = $this->UserClassesDatabaseModel->getUserClassByUser($user);
			$allclasses = $this->UserClassesDatabaseModel->listUserClasses();
			foreach ($allclasses as $class) {
				if ($class->level > $userclass->level) {
					$this->renderView('ClassesView', ['classes' => $this->UserClassesDatabaseModel->listUserClasses(),
						'error' => 'cant create a new class unless you are of the highest level class']);
					return;
				}
			}

			$new_class = $this->UserClassesDatabaseModel->createClass('new class', 1);
			if ($new_class === NULL) {
				$this->renderView('ClassesView', ['classes' => $this->UserClassesDatabaseModel->listUserClasses(),
					'error' => 'error creating new class']);
			} else {
				$this->renderView('ClassesView', ['classes' => $this->UserClassesDatabaseModel->listUserClasses(),
					'message' => 'successfully created new class!']);
			}

		} else {
			$this->renderView('UserErrorView', ['invalid page']);
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
