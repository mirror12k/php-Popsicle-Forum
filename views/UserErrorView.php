<?php


class UserErrorView extends View {
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render ($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'hello index!' ]);

?>
<p>Error: <?php echo htmlentities($args[0]); ?></p>
<?php

		$this->renderView('PopsicleFooterView');
	}
}

