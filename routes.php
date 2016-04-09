<?php
/**
 * global route creation script
 * 
 * avoid placing logic in here, logic is the controller's job
 */


// add routes to the router
$router->routeMedia('media/'); // media folder
$router->routeRedirect('/^$/', $mvcConfig['pathBase'] . 'forums'); // redirect index page to forums page
$router->route('/^(?<page>forums)$/', 'ListController');
$router->route('/^(?<page>forum|thread)\/(?<id>\d+)$/', 'ListController');
$router->route('/^(?<page>login|logout|register)$/', 'LoginController');
$router->route('/^(?<page>user|userposts)\/(?<id>\d+)$/', 'UserController');
$router->route('/^(?<page>users)$/', 'UserController');
$router->route('/^admin\/(?<page>classes)$/', 'AdminstrationController');

$router->route('/^(?<page>setup)$/', 'SetupController');

