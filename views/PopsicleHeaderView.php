<?php


class PopsicleHeaderView extends View {
	public function render ($args) {
		global $mvcConfig; // need the base path

?><!doctype html>
<html>
<head>
	<title><?php echo htmlentities($args['title']); ?></title>
	<link rel='stylesheet' type='text/css' href='<?php echo htmlentities($mvcConfig['pathBase']); ?>media/style.css' />
</head>
<body>
<div class='header'>
	<a href='<?php echo htmlentities($mvcConfig['pathBase']); ?>'>home</a>
	<a href='<?php echo htmlentities($mvcConfig['pathBase']); ?>login'>login</a>
	<a href='<?php echo htmlentities($mvcConfig['pathBase']); ?>register'>register</a>
</div>
<?php

	}
}

