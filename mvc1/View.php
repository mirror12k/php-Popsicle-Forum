<?php


/**
* Views present data given to them
*/
abstract class View {
	private $models;
	/**
	* any Model classes specified with $required will be given to this View when it is rendered
	*/
	public static $required = [];
	/**
	* any View classes specified with $inherited will have their $required Models inherited by this View in order to pass them on when rendering
	* any Views that this class could render should be in $inherited
	*/
	public static $inherited = [];

	public function requiredModels () {
		return $this->required;
	}
	public final function load($models) {
		$this->models = $models;
	}
	public function __get($name) {
		return $this->models[$name];
	}

	public function getView($name) {
		require_once "views/${name}.php";
		$instance = new $name();
		$models = [];
		foreach ($instance::$required as $name) {
			$models[$name] = $this->models[$name];
		}
		$instance->load($models);
		return $instance;
	}
	public function renderView($name, $args=[]) {
		return $this->getView($name)->render($args);
	}

	public abstract function render($args);
}

