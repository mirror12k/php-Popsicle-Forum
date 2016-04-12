<?php



class UserView extends View {
	public static $required = ['UserClassesDatabaseModel', 'CSRFTokenModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render($args) {
		global $mvcConfig;
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - User' ]);
		$user = $args['user'];
		$class = $this->UserClassesDatabaseModel->getUserClassByUser($user);

?>
<div class='user_desc'>
	User: <b><?php echo htmlentities($user->username); ?></b><br />
	Class: <?php echo htmlentities($class->name); ?><br />
	<a href='<?php echo htmlentities($mvcConfig['pathBase'] . 'userposts/' . $user->id); ?>'>view user's posts</a>
	<?php echo $user->banned ? '<div class="error_message">User is banned</div>' : ''; ?><br />
	<?php echo $user->muted ? '<div class="error_message">User is muted</div>' : ''; ?><br />

<?php
		if ($args['showMuteUser']) {
			if ($user->muted) {
?>
<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'user/' . $user->id); ?>' method='POST'>
	<input type='hidden' name='action' value='unmute_user' />
	<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
	<button>unmute user</button>
</form>
<?php
			} else {
?>
<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'user/' . $user->id); ?>' method='POST'>
	<input type='hidden' name='action' value='mute_user' />
	<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
	<button>mute user</button>
</form>
<?php
			}
		}

		if ($args['showBanUser']) {
			if ($user->banned) {
?>
<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'user/' . $user->id); ?>' method='POST'>
	<input type='hidden' name='action' value='unban_user' />
	<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
	<button>unban user</button>
</form>
<?php
			} else {
?>
<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'user/' . $user->id); ?>' method='POST'>
	<input type='hidden' name='action' value='ban_user' />
	<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
	<button>ban user</button>
</form>
<?php
			}
		}

		if ($args['showChangeClass']) {
?>
<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'user/' . $user->id); ?>' method='POST'>
	<input type='hidden' name='action' value='change_class' />
	<select name='class'>
<?php
	foreach ($args['classesAvailable'] as $class) {
		echo "<option value='" . $class->id . "'>" . htmlentities($class->name) . "</option>";
	}
?>
	</select>
	<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
	<button>set user class</button>
</form>
<?php
		}
?>
</div>
<?php

		$this->renderView('PopsicleFooterView');
	}
}
