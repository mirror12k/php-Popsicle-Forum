<?php



class UserController extends Controller {
	public static $required = ['UsersDatabaseModel'];
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
			$user = $this->UsersDatabaseModel->getUserById($args['id']);
			if ($user === NULL) {
				$this->renderView('UserErrorView', ['invalid page']);
			} else {
				$this->renderView('UserView', ['user' => $user]);
			}
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}
