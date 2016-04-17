<?php


class PopsicleHeaderView extends View {
	public static $required = ['LoginModel'];
	public static $inherited = ['FancyUsernameView'];
	public function render ($args) {
		global $mvcConfig; // need the base path
		$user = $this->LoginModel->getCurrentUser();

?><!doctype html>
<html>
<head>
	<title><?php echo htmlentities($args['title']); ?></title>
	<link rel='stylesheet' type='text/css' href='<?php echo htmlentities($mvcConfig['pathBase']); ?>media/style.css' />
	<link rel='stylesheet' type='text/css' href='<?php echo htmlentities($mvcConfig['pathBase']); ?>media/userclasses.css' />
	<script type='text/javascript' src='<?php echo htmlentities($mvcConfig['pathBase']); ?>media/global.js'></script>
</head>
<body>
<div class='header'>
	<a href='<?php echo htmlentities($mvcConfig['pathBase'] . 'forums'); ?>'>forums</a> | 
	<a href='<?php echo htmlentities($mvcConfig['pathBase'] . 'users'); ?>'>users</a> | 
	<a href='<?php echo htmlentities($mvcConfig['pathBase'] . 'latest'); ?>'>latest posts</a> | 
<?php if ($user !== NULL) { ?>
	<a href='<?php echo htmlentities($mvcConfig['pathBase']); ?>edit_password'>edit password</a> | 
	<a href='<?php echo htmlentities($mvcConfig['pathBase']); ?>logout'>logout</a> | 
	<span>Logged in as <?php $this->renderView('FancyUsernameView', [$user]); ?></span>
<?php } else { ?>
	<a href='<?php echo htmlentities($mvcConfig['pathBase']); ?>search'>search posts</a> | 
	<a href='<?php echo htmlentities($mvcConfig['pathBase']); ?>login'>login</a> | 
	<a href='<?php echo htmlentities($mvcConfig['pathBase']); ?>register'>register</a>
<?php } ?>
</div>
<?php

	}
}

