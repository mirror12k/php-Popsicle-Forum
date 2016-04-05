<?php



class AdminstrationController extends Controller {
	public static $required = ['LoginModel', 'UserClassesDatabaseModel'];
	public static $inherited = ['UserErrorView', 'ClassesView'];
	public function invoke($args) {
		if (! isset($args['page'])) {
			$this->renderView('UserErrorView', ['invalid page']);
		} elseif ($args['page'] === 'classes') {
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
