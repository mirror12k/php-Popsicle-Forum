<?php
/**
 * global route creation script
 * 
 * avoid placing logic in here, logic is the controller's job
 */


// add routes to the router
// $router->routeRedirect('/^[^\d]/', $mvcConfig['pathBase'] . '0'); // redirect invalid path
$router->route('/^$/', 'test_controller'); // show the test controller

