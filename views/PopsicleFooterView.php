<?php


class PopsicleFooterView extends View {
	public function render ($args) {
		global $mvcConfig;
?>
<div class='footer'>
	<p><a href='<?php echo htmlentities($mvcConfig['pathBase'] . 'admin'); ?>'>admin</a></p>
	<p>Popsicle v0.1 - rendered in <?php echo number_format(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 3, '.', ''); ?> seconds</p>
</div>
</body>
</html>
<?php

	}
}

