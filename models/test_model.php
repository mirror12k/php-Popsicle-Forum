<?php


class test_model extends Model {
	private $data;
	public function __construct() {
		$this->data = ['ha', 'yay', 'nope', 'world!'];
	}
	public function get($index) {
		return $this->data[$index];
	}
}


