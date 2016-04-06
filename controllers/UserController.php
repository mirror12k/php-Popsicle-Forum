<?php



class UserController extends Controller {
	public static $required = ['UserClassesDatabaseModel', 'UsersDatabaseModel', 'LoginModel'];
	public static $inherited = ['UserErrorView', 'UserView'];
	public function invoke($args) {
		if (isset($_POST['action']) and isset($args['page'])) {
			$this->invokeAction($args);
		} else {
			$this->invokePage($args);
		}
	}

	public function invokeAction($args) {
		$this->renderView('UserErrorView', ['invalid page']);
	}

	public function invokePage($args) {
		if ($args['page'] === 'user' and isset($args['id'])) {
			$target = $this->UsersDatabaseModel->getUserById($args['id']);
			if ($target === NULL) {
				$this->renderView('UserErrorView', ['invalid page']);
			} else {
				$viewargs = ['user' => $target];

				// check our credentials to see if we should offer to mute/ban
				$targetClass = $this->UserClassesDatabaseModel->getUserClassByUser($target);
				$user = $this->LoginModel->getCurrentUser();
				$userClass = $this->UserClassesDatabaseModel->getUserClassByUser($user);

				$viewargs['showMuteUser'] = ($user !== NULL and $userClass->can('mute_user') and $targetClass->level < $userClass->level);
				$viewargs['showBanUser'] = ($user !== NULL and $userClass->can('ban_user') and $targetClass->level < $userClass->level);

				$this->renderView('UserView', $viewargs);
			}
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}
