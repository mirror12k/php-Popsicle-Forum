<?php


class EditPasswordView extends View {
	public static $required = ['CSRFTokenModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render ($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Change password' ]);
		global $mvcConfig;

?>
<div class='register_form'>
	<?php if (isset($args['message'])) { echo "<p class='message'>" . htmlentities($args['message']) . "</p>"; } ?>
	<?php if (isset($args['error'])) { echo "<p class='error_message'>" . htmlentities($args['error']) . "</p>"; } ?>
	<p>Enter your desired password:</p>
	<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'edit_password'); ?>' method='POST'>
		<?php if (isset($args['password_error'])) { echo "<p class='error_message'>" . htmlentities($args['password_error']) . "</p><br />"; } ?>
		old password: <input type='password' name='old_password' placeholder='old password' /><br />
		password: <input type='password' name='password' placeholder='password' /><br />
		repeat password: <input type='password' name='repeat_password' placeholder='password' /><br />
		<input type='hidden' name='csrf_token' value='<?php echo $this->CSRFTokenModel->get();?>' />
		<button>submit</button>
	</form>
</div>
<?php

		$this->renderView('PopsicleFooterView');
	}
}

