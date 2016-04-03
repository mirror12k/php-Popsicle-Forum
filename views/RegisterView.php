<?php


class RegisterView extends View {
	public static $required = ['CSRFTokenModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render ($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Register' ]);
		
?>
<?php if (isset($args['error'])) { echo "<p class='error_message'>" . htmlentities($args['error']) . "</p>"; } ?>
<p>Enter your desired username and password:</p>
<form action='#' method='POST'>
	<?php if (isset($args['username_error'])) { echo "<p class='error_message'>" . htmlentities($args['username_error']) . "</p><br />"; } ?>
	username: <input type='text' name='username' placeholder='username' /><br />
	<?php if (isset($args['password_error'])) { echo "<p class='error_message'>" . htmlentities($args['password_error']) . "</p><br />"; } ?>
	password: <input type='password' name='password' placeholder='password' /><br />
	repeat password: <input type='password' name='repeat_password' placeholder='password' /><br />
	<input type='hidden' name='csrf_token' value='<?php echo $this->CSRFTokenModel->get();?>' />
	<button>submit</button>
</form>
<?php

		$this->renderView('PopsicleFooterView');
	}
}

