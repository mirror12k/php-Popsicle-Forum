<?php


class UserErrorView extends View {
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render ($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'hello index!' ]);

?>
<div class='user_error'>
	<p class='error_message'>Error: <?php echo htmlentities($args[0]); ?></p>
</div>
<?php

		$this->renderView('PopsicleFooterView');
	}
}

