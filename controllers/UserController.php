<?php



class UserController extends Controller {
	public static $required = ['UserClassesDatabaseModel', 'UsersDatabaseModel', 'LoginModel', 'CSRFTokenModel', 'ThreadsDatabaseModel'];
	public static $inherited = ['UserErrorView', 'UserView', 'UsersView'];
	public function invoke($args) {
		if (isset($args['page']) and isset($_POST['action']) and isset($_POST['csrf_token'])) {
			// verify csrf token
			if (! $this->CSRFTokenModel->verify((string)$_POST['csrf_token'])) {
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
		global $mvcConfig;

		if ($args['page'] === 'user' and ($_POST['action'] === 'ban_user' or $_POST['action'] === 'unban_user') and isset($args['id'])) {
			$target = $this->UsersDatabaseModel->getUserById($args['id']);
			$user = $this->LoginModel->getCurrentUser();
			if ($user === NULL or $target === NULL) {
				$this->renderView('UserErrorView', ['invalid action']);
			} else {
				$targetClass = $this->UserClassesDatabaseModel->getUserClassByUser($target);
				$userClass = $this->UserClassesDatabaseModel->getUserClassByUser($user);
				if (! $userClass->can('ban_user')) {
					$this->renderView('UserErrorView', ['invalid action']);
				} elseif ($targetClass->level >= $userClass->level) {
					$this->renderView('UserErrorView', ['invalid action']);
				} else {
					$result = $this->UsersDatabaseModel->setUserBannedStatus($target, $_POST['action'] === 'ban_user');
					if (! $result) {
						$this->renderView('UserErrorView', ['error setting banned status']);
					} else {
						$this->redirect($mvcConfig['pathBase'] . 'user/' . $target->id);
					}
				}
			}

		} elseif ($args['page'] === 'user' and ($_POST['action'] === 'mute_user' or $_POST['action'] === 'unmute_user') and isset($args['id'])) {
			$target = $this->UsersDatabaseModel->getUserById($args['id']);
			$user = $this->LoginModel->getCurrentUser();
			if ($user === NULL or $target === NULL) {
				$this->renderView('UserErrorView', ['invalid action']);
			} else {
				$targetClass = $this->UserClassesDatabaseModel->getUserClassByUser($target);
				$userClass = $this->UserClassesDatabaseModel->getUserClassByUser($user);
				if (! $userClass->can('mute_user')) {
					$this->renderView('UserErrorView', ['invalid action']);
				} elseif ($targetClass->level >= $userClass->level) {
					$this->renderView('UserErrorView', ['invalid action']);
				} else {
					$result = $this->UsersDatabaseModel->setUserMutedStatus($target, $_POST['action'] === 'mute_user');
					if (! $result) {
						$this->renderView('UserErrorView', ['error setting muted status']);
					} else {
						$this->redirect($mvcConfig['pathBase'] . 'user/' . $target->id);
					}
				}
			}

		} elseif ($args['page'] === 'user' and $_POST['action'] === 'change_class' and isset($_POST['class']) and isset($args['id'])) {
			$target = $this->UsersDatabaseModel->getUserById($args['id']);
			$user = $this->LoginModel->getCurrentUser();
			if ($user === NULL or $target === NULL) {
				$this->renderView('UserErrorView', ['invalid action']);
			} else {
				$targetClass = $this->UserClassesDatabaseModel->getUserClassByUser($target);
				$userClass = $this->UserClassesDatabaseModel->getUserClassByUser($user);
				$newClass = $this->UserClassesDatabaseModel->getUserClassById((int)$_POST['class']);
				if (! $userClass->can('edit_lower_class')) {
					$this->renderView('UserErrorView', ['invalid action']);
				} elseif ($targetClass->level >= $userClass->level) {
					$this->renderView('UserErrorView', ['invalid action']);
				} elseif ($newClass === NULL or $newClass->level >= $userClass->level) {
					$this->renderView('UserErrorView', ['invalid action']);
				} else {
					$result = $this->UsersDatabaseModel->setUserClassId($target, $newClass->id);
					if (! $result) {
						$this->renderView('UserErrorView', ['error changing user class']);
					} else {
						$this->redirect($mvcConfig['pathBase'] . 'user/' . $target->id);
					}
				}
			}

		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}

	public function invokePage($args) {
		global $popsicleConfig;
		if ($args['page'] === 'user' and isset($args['id'])) {
			$target = $this->UsersDatabaseModel->getUserById($args['id']);
			if ($target === NULL) {
				$this->renderView('UserErrorView', ['invalid page']);
			} else {
				$viewargs = ['user' => $target];

				// check our credentials to see if we should offer to mute/ban
				$targetClass = $this->UserClassesDatabaseModel->getUserClassByUser($target);
				$user = $this->LoginModel->getCurrentUser();
				if ($user !== NULL) {
					$userClass = $this->UserClassesDatabaseModel->getUserClassByUser($user);
					// decide which options to show
					$viewargs['showMuteUser'] = ($userClass->can('mute_user') and $targetClass->level < $userClass->level);
					$viewargs['showBanUser'] = ($userClass->can('ban_user') and $targetClass->level < $userClass->level);
					$viewargs['showChangeClass'] = ($userClass->can('edit_lower_class') and $targetClass->level < $userClass->level);
					if ($viewargs['showChangeClass']) {
						// add classes that we can set to this user
						$viewargs['classesAvailable'] = [];
						foreach ($this->UserClassesDatabaseModel->listUserClasses() as $class) {
							if ($class->level < $userClass->level) {
								array_push($viewargs['classesAvailable'], $class);
							}
						}
					}
				} else {
					$viewargs['showMuteUser'] = FALSE;
					$viewargs['showBanUser'] = FALSE;
				}

				$this->renderView('UserView', $viewargs);
			}
		} elseif ($args['page'] === 'userposts' and isset($args['id'])) {
			$user = $this->UsersDatabaseModel->getUserById($args['id']);
			if ($user === NULL) {
				$this->renderView('UserErrorView', ['invalid page']);
			} else {
				if (isset($_GET['index'])) {
					$index = (int)$_GET['index'];
					if ($index < 0) {
						$index = 0;
					}
				} else {
					$index = 0;
				}
				$count = $popsicleConfig['postsPerPage'];

				$postCount = $this->ThreadsDatabaseModel->countPostsByCreatorId($user->id);
				if ($postCount === NULL) {
					die("failed to get posts count");
				}

				$posts = $this->ThreadsDatabaseModel->listPostsByCreatorId($user->id, $index * $count, $count);
				$viewargs = ['posts' => $posts, 'thisPage' => $index];
				if ($index > 0) {
					$viewargs['prevPage'] = $index - 1;
				}
				if ($index + 1 < $postCount / $count) {
					$viewargs['nextPage'] = $index + 1;
				}

				$viewargs['currentIndexStart'] = $index * $count;
				if ($postCount > ($index + 1) * $count) {
					$viewargs['currentIndexEnd'] = ($index + 1) * $count - 1;
				} else {
					$viewargs['currentIndexEnd'] = $postCount - 1;
				}
				$viewargs['lastIndex'] = $postCount;

				$viewargs['linkThread'] = TRUE;

				$this->renderView('PostsView', $viewargs);
			}
		} elseif ($args['page'] === 'users') {
			if (isset($_GET['index'])) {
				$index = (int)$_GET['index'];
				if ($index < 0) {
					$index = 0;
				}
			} else {
				$index = 0;
			}
			$count = $popsicleConfig['usersPerPage'];

			$usercount = $this->UsersDatabaseModel->getUserCount();
			if ($usercount === NULL) {
				die("failed to get user count");
			}

			$users = $this->UsersDatabaseModel->listUsers($index * $count, $count);
			$viewargs = ['users' => $users, 'thisPage' => $index];
			if ($index > 0) {
				$viewargs['prevPage'] = $index - 1;
			}
			if ($index + 1 < $usercount / $count) {
				$viewargs['nextPage'] = $index + 1;
			}

			$this->renderView('UsersView', $viewargs);
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}
