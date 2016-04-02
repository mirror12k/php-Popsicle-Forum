<?php
/**
 * global route creation script
 * 
 * avoid placing logic in here, logic is the controller's job
 */


// add routes to the router
$router->routeRedirect('/^$/', $mvcConfig['pathBase'] . '3'); // redirect empty path to the hello world greeting
$router->routeRedirect('/^[^\d]/', $mvcConfig['pathBase'] . '0'); // redirect invalid path
$router->route('/^(?<key>\d+)$/', 'test_controller'); // show the test controller

