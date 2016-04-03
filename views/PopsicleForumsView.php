<?php


class PopsicleForumsView extends View {
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render ($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'hello index!' ]);

?>
<p>hello world!</p>
<?php

		$this->renderView('PopsicleFooterView');
	}
}

