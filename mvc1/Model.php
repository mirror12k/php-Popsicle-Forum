<?php

/**
* Models represent and control data
*/
abstract class Model {
	private $models;
	private $modelLoaded = FALSE;
	/**
	* any Model classes specified with $required will be given to this Model when it is loaded
	*/
	public static $required = [];

	public final function load($models) {
		$this->models = $models;
		$this->modelLoaded = TRUE;
	}
	public final function isLoaded() {
		return $this->modelLoaded;
	}
	public function __get($name) {
		return $this->models[$name];
	}
}


