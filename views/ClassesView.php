<?php



class ClassesView extends View {
	public static $required = ['CSRFTokenModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render($args) {
		global $mvcConfig;

		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Administration - Classes' ]);

?>
<div class='classes_list'>
	<?php if (isset($args['message'])) { echo "<p class='message'>" . htmlentities($args['message']) . "</p>"; } ?>
	<?php if (isset($args['error'])) { echo "<p class='error_message'>" . htmlentities($args['error']) . "</p>"; } ?>
<?php

		foreach ($args['classes'] as $class) {
?>
<div class='class'>
<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'admin/classes'); ?>' method='POST'>
<input type='hidden' name='action' value='edit_class' />
<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
<input type='hidden' name='classid' value='<?php echo htmlentities($class->id); ?>' />

<div class='class_title'>
	Class name: <input type='text' name='name' value='<?php echo htmlentities($class->name); ?>' /><br />
	Class level: <input type='text' name='level' value='<?php echo htmlentities($class->level); ?>' /><br />
	Class color: <input type='text' name='color' value='<?php echo htmlentities($class->color); ?>' />
</div>
<?php

			foreach ($class->getAllPermissions() as $key => $val) {
?>
<span class='class_privilege'>
<?php echo htmlentities($key); ?> :
	<input type='checkbox' name='<?php echo htmlentities($key); ?>' <?php echo htmlentities($val ? 'checked' : ''); ?> />
</span>
<?php
			}
?>
<button>Save</button>
</form>
</div>
<?php
		}
?>
</div>
<?php

		$this->renderView('PopsicleFooterView');
	}
}
