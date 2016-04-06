<?php
// specialized view for displaying a user's username in a fancy way


class FancyUsernameView extends View {
	public function render($args) {
		global $mvcConfig;
		$user = $args[0];
?>
<a href="<?php echo htmlentities($mvcConfig['pathBase'] . 'user/' . $user->id); ?>"><?php echo htmlentities($user->username); ?></a>
<?php
	}
}
