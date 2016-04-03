<?php


class PopsicleHeaderView extends View {
	public static $required = ['LoginModel'];
	public function render ($args) {
		global $mvcConfig; // need the base path
		echo "test";
		$user = $this->LoginModel->getCurrentUser();

?><!doctype html>
<html>
<head>
	<title><?php echo htmlentities($args['title']); ?></title>
	<link rel='stylesheet' type='text/css' href='<?php echo htmlentities($mvcConfig['pathBase']); ?>media/style.css' />
</head>
<body>
<div class='header'>
	<a href='<?php echo htmlentities($mvcConfig['pathBase']); ?>'>home</a>
<?php if ($user !== NULL) { ?>
	<p>welcome <?php echo htmlentities($user->username); ?>!</p>
	<a href='<?php echo htmlentities($mvcConfig['pathBase']); ?>logout'>logout</a>
<?php } else { ?>
	<a href='<?php echo htmlentities($mvcConfig['pathBase']); ?>login'>login</a>
	<a href='<?php echo htmlentities($mvcConfig['pathBase']); ?>register'>register</a>
<?php } ?>
</div>
<?php

	}
}

