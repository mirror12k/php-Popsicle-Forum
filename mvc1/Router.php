<?php


class Router {
	private $routes;
	private $redirects;
	private $mediaPaths;

	public function __construct() {
		$this->routes = [];
		$this->redirects = [];
		$this->mediaPaths = [];
	}
	public function invokeController($name, $args) {
		require "controllers/${name}.php";
		$controller = new $name();
		return $controller->invoke($args);
	}
	public function redirect($location, $permanent=FALSE) {
		if ($permanent) {
			header('Location: ' . $location, TRUE, 301);
		} else {
			header('Location: ' . $location, TRUE, 303);
		}
		die();
	}
	public function route($regex, $controller) {
		$this->routes[] = ['regex' => $regex, 'controller' => $controller];
	}
	public function routeRedirect($regex, $location) {
		$this->redirects[] = ['regex' => $regex, 'location' => $location];
	}
	public function routeMedia($matchPath, $path=NULL) {
		if ($path === NULL) {
			$path = $matchPath;
		}
		$matchPath = quotemeta($matchPath); // prevent metacharacters from messing up the regex
		$matchPath = preg_replace('/\//', '\/', $matchPath); // good work copying perl
		// honestly, why must people insist on the regex // syntax if they don't actually use them
		$this->mediaPaths[] = ['regex' => '/^' . $matchPath . '(?<filepath>.*)$/', 'path' => $path];
	}
	public function invoke($path) {
		// run through all possible media paths
		foreach ($this->mediaPaths as $route) {
			$result = preg_match($route['regex'], $path, $matches);
			if ($result === FALSE) {
				die("regex error in '" . $route['regex'] . "' !");
			} elseif ($result === 1) {
				$location = $route['path'] . $matches['filepath'];
				$location = preg_replace('/\.\./', '', $location); // delete any double dots to prevent file traversal
				if (file_exists($location)) {
					// output the file
					readfile($location);
				} else {
					die("file does not exist");
				}
				return;
			}
		}
		// run through all possible redirect paths
		foreach ($this->redirects as $route) {
			$result = preg_match($route['regex'], $path, $matches);
			if ($result === FALSE) {
				die("regex error in '" . $route['regex'] . "' !");
			} elseif ($result === 1) {
				$location = $route['location'];
				// substitute in matches
				foreach ($matches as $key => $val) {
					$location = str_replace("\${" . $key . "}", $val, $location);
				}
				$this->redirect($location, TRUE);
			}
		}
		// run through all possible route paths
		foreach ($this->routes as $route) {
			$result = preg_match($route['regex'], $path, $matches);
			if ($result === FALSE) {
				die("regex error in '" . $route['regex'] . "' !");
			} elseif ($result === 1) {
				$continue = $this->invokeController($route['controller'], $matches);
				// route controllers can return TRUE to pass control onto another route
				if ($continue !== TRUE) {
					return;
				}
			}
		}
		// if no redirect or route found
		die("no route found for path ${path}");
	}
}



