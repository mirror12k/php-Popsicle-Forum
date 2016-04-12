<?php


class AdminPanelView extends View {
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render ($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Admin Panel' ]);
		global $mvcConfig;
		
?>
<div class='admin_panel'>
	<p><a href='<?php echo htmlentities($mvcConfig['pathBase'] . 'admin/classes'); ?>'>edit classes</a></p>
</div>
<?php

		$this->renderView('PopsicleFooterView');
	}
}

