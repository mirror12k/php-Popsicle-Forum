<?php


class test_view extends View {
	public static $required = ['test_model'];
	public function render ($args) {
		echo "hello: " . $this->test_model->get($args[0]) . "\n";
	}
}

