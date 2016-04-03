<?php



class test_controller extends Controller {
	public static $inherited = ['PopsicleForumsView'];

	public function invoke($args) {
		$this->renderView('PopsicleForumsView');
	}
}

