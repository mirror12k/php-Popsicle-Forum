<?php



class test_controller extends Controller {
	public static $inherited = ['test_view'];

	public function invoke($args) {
		$this->renderView('test_view', [(int)$args['key']]);
	}
}

