<?php



class UserView extends View {
	public static $required = ['UserClassesDatabaseModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - User' ]);
		$user = $args['user'];
		$class = $this->UserClassesDatabaseModel->getUserClassByUser($user);

?>
<div class='user_desc'>
	User: <b><?php echo htmlentities($user->username); ?></b><br />
	Class: <?php echo htmlentities($class->name); ?><br />
	<?php echo $user->banned ? '<div class="error_message">User is banned</div>' : ''; ?><br />
	<?php echo $user->muted ? '<div class="error_message">User is muted</div>' : ''; ?><br />
</div>
<?php

		$this->renderView('PopsicleFooterView');
	}
}
