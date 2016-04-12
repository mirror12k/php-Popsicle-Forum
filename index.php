<?php
/**
 * index page which starts mvc and fires the router
 * 
 * should not be edited, all config and routing should go into config.php and routes.php, respecfully
 */


require 'mvc1/mvc1.php';
require 'config.php';
require 'routes.php';

// ensure that the session is started even if automatic session starting isn't set in php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}



// parse out the path from the request uri
$path = explode('?', $_SERVER["REQUEST_URI"], 2)[0];
if (strpos($path, $mvcConfig['pathBase']) !== 0) {
	die('invalid path');
} else {
	$path = substr($path, strlen($mvcConfig['pathBase']));
}

// invoke the router with the parsed out path
// it will then go through a figure out which controller to invoke
$router->invoke($path);
