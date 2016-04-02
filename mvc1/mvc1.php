<?php

if (!isset($_SESSION)) {
	session_start();
}


require 'Model.php';
require 'View.php';
require 'Controller.php';
require 'Router.php';



global $router;
$router = new Router();

