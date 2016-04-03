<?php



class LoginController extends Controller {
	public static $inherited = ['LoginView', 'RegisterView', 'UserErrorView'];

	public function invoke($args) {
		if ($args['page'] === 'login') {
			$this->renderView('LoginView');
		} elseif ($args['page'] === 'register') {
			$this->renderView('RegisterView');
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}

