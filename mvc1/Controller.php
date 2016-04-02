<?php



/**
* Controllers interpret user input and performs logic operations
* Controllers should perform data modification through Models and present data via Views
*/
abstract class Controller {
	private $models;
	/**
	* any Model classes specified with $required will be given to this Controller when it is loaded
	* additionally any Models that required Models require, will also be loaded
	*/
	public static $required = [];
	/**
	* any View classes specified with $inherited will have their $required Models inherited by this Controller in order to pass them on when rendering
	* any Views that this class could render should be in $inherited
	*/
	public static $inherited = [];

	public function __construct() {
		$this->models = [];
		foreach ($this::$required as $model) {
			$this->requireModel($model);
		}
		foreach ($this::$inherited as $view) {
			$this->requireInheritedModels($view);
		}
	}

	public function requireInheritedModels($class) {
		require_once "views/${class}.php";
		foreach ($class::$required as $model) {
			$this->requireModel($model);
		}
	}

	public function requireModel($class) {
		if (! isset($this->models[$class])) { // prevent double require-ing and require loops
			$model = require_once "models/${class}.php";
			$this->models[$class] = new $class();
			foreach ($class::$required as $name) {
				$this->requireModel($name);
			}
		}
	}

	public function loadModel($model) {
		$models = [];
		foreach ($model::$required as $name) {
			$models[$name] = $this->getModel($name);
		}
		$model->load($models);
	}

	public function getModel($name) {
		$model = $this->models[$name];
		if ($model === NULL) {
			die ("model not found: ". $name);
		}
		if (! $model->isLoaded()) {
			$this->loadModel($model);
		}
		return $model;
	}

	public function loadInheritedModels ($class, &$models) {
		require_once "views/${class}.php";
		foreach ($class::$required as $name) {
			if (! isset($models[$name])) {
				$models[$name] = $this->getModel($name);
			}
		}
	}
	public function getView($class) {
		require_once "views/${class}.php";
		$models = [];
		foreach ($class::$required as $name) {
			$models[$name] = $this->getModel($name);
		}
		foreach ($class::$inherited as $name) {
			$this->loadInheritedModels($name, $models);
		}
		$instance = new $class();
		$instance->load($models);
		return $instance;
	}
	public function __get($name) {
		return $this->getModel($name);
	}

	public function renderView($name, $args=[]) {
		return $this->getView($name)->render($args);
	}

	public function redirect($location, $permanent=FALSE) {
		if ($permanent) {
			header('Location: ' . $location, TRUE, 301);
		} else {
			header('Location: ' . $location, TRUE, 303);
		}
		die();
	}
	public abstract function invoke($args);
}




