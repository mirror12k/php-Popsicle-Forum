<?php


class LoginView extends View {
	public static $required = ['CSRFTokenModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render ($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Login' ]);

?>
<?php if (isset($args['error'])) { echo "<p class='error_message'>" . htmlentities($args['error']) . "</p>"; } ?>
<p>Enter your username and password:</p>
<form action='#' method='POST'>
	username: <input type='text' name='username' placeholder='username' /><br />
	password: <input type='password' name='password' placeholder='password' /><br />
	<input type='hidden' name='csrf_token' value='<?php echo $this->CSRFTokenModel->get();?>' />
	<button>submit</button>
</form>
<?php

		$this->renderView('PopsicleFooterView');
	}
}

