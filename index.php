<?php
/**
 * index page which starts mvc and fires the router
 * 
 * should not be edited, all config and routing should go into config.php and routes.php, respecfully
 */


require 'mvc1/mvc1.php';
require 'config.php';
require 'routes.php';


// echo "i got a pretty url: ", $_SERVER['REQUEST_URI'], "\n";



// parse out the path from the request uri
$path = explode('?', $_SERVER["REQUEST_URI"], 2)[0];
if (strpos($path, $mvcConfig['pathBase']) !== 0) {
	die('invalid path');
} else {
	$path = substr($path, strlen($mvcConfig['pathBase']));
}

// invoke the controller with the parsed out path
$router->invoke($path);
