<?php



class SearchController extends Controller {
	public static $required = ['UserClassesDatabaseModel', 'UsersDatabaseModel', 'DatabaseModel'];
	public static $inherited = ['UserErrorView', 'SearchView'];

	public function invoke($args) {
		if (isset($_POST['action']) and isset($_POST['csrf_token'])) {
			// verify csrf token
			if (! $this->CSRFTokenModel->verify((string)$_POST['csrf_token'])) {
				$this->renderView('UserErrorView', ['invalid csrf token']);
			} else {
				$this->invokeAction($args);
			}
		} elseif (isset($args['page'])) {
			$this->invokePage($args);
		}
	}

	public function invokeAction($args) {
		if ($_POST['action'] === 'search_posts' and isset($_POST['terms'])) {
			die ('yep');
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}

	public function invokePage($args) {
		if ($args['page'] === 'search') {
			$this->renderView('SearchView');
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}
