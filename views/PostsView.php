<?php



class PostsView extends View {
	public static $required = ['UserClassesDatabaseModel', 'LoginModel', 'CSRFTokenModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	
	public function render($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Posts' ]);

?>
<ul>
<?php

		global $mvcConfig;
		foreach ($args['posts'] as $post) {
?>
<div class='post'>
	<a href="<?php echo htmlentities($mvcConfig['pathBase'] . 'user/' . $post->creatorid); ?>">poster</a>
	<p><?php echo htmlentities($post->text); ?></p>
</div>
<?php
		}

?>
</ul>
<?php
		// if the user is privileged, show him the create_post form
		$user = $this->LoginModel->getCurrentUser();
		if ($user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('create_post')) {
?>
<p>Reply:</p>
<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'thread/' . $args['threadid']); ?>' method='POST'>
	<input type='textarea' name='post' placeholder='text' />
	<input type='hidden' name='action' value='create_post' />
	<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
	<button>submit</button>
</form>
<?php
		}


		$this->renderView('PopsicleFooterView');
	}
}
