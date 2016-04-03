<?php


class PopsicleFooterView extends View {
	public function render ($args) {

?>
<div class='footer'>
	<p>Popsicle v0.1 - rendered in <?php echo number_format(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 3, '.', ''); ?> seconds</p>
</div>
</body>
</html>
<?php

	}
}

