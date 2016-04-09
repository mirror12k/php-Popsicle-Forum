<?php
/**
 * global config setting script
 * 
 * this is where all your webroot configuration and server usernames/passwords
 * should generally go, as well as your application specific configuration
 */


// model-view-controller configuration
global $mvcConfig;
$mvcConfig = [
	'pathBase' => '/popsicle/', // the root of this mvc instance
];

// database configuration
global $databaseConfig;
$databaseConfig = [
	'host' => 'localhost',
	'user' => 'root',
	'password' => 'dminpassword',
	'database' => 'popsicle',
];

// popsicle forum configuration
global $popsicleConfig;
$popsicleConfig = [
	// the user class assigned by default to freshly registered users
	'defaultUserClass' => 1,
	// the maximum number of threads to be displayed on a forum page
	'threadsPerPage' => 15,
	// the maximum number of posts to be displayed on a thread page
	'postsPerPage' => 10,
	// the maximum number of users to be displayed on a user list page
	'usersPerPage' => 50,
	// the sql file to run when running intial setup
	'setupDatabaseFile' => 'setup_database.sql',
];


