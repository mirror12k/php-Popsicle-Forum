<?php



class ClassesView extends View {
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Administration - Classes' ]);

?>
<div class='classes_list'>
<?php

		foreach ($args['classes'] as $class) {
?>
<div class='class'>
<?php
			echo htmlentities($class->name . ' : ' . $class->level) . "<br />";

			foreach ($class->getAllPermissions() as $key => $val) {
?>
<span class='class_privilege'>
<?php echo htmlentities($key); ?> :
	<input type='checkbox' name='<?php echo htmlentities($key); ?>' <?php echo htmlentities($val ? 'checked' : ''); ?> />
</span>
<?php
			}
?>
</div>
<?php
		}
?>
</div>
<?php

		$this->renderView('PopsicleFooterView');
	}
}
