<?php

class ThreadsView extends View {
	public static $required = ['UserClassesDatabaseModel', 'LoginModel', 'CSRFTokenModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Threads' ]);

?>
<ul>
<?php

		global $mvcConfig;
		foreach ($args['threads'] as $thread) {
?>
<li>
	<a href="<?php echo htmlentities($mvcConfig['pathBase'] . 'thread/' . $thread->id); ?>"><?php echo htmlentities($thread->title); ?></a>
	: <?php echo $thread->postcount; ?> posts
</li>
<?php
		}

?>
</ul>
<?php
		// if the user is privileged, show him the create_forum form
		$user = $this->LoginModel->getCurrentUser();
		if ($user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('create_thread')) {
?>
<p>Create Thread:</p>
<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'forum/' . $args['forumid']); ?>' method='POST'>
	Title: <input type='text' name='title' placeholder='title' /><br />
	Post: <input type='textarea' name='post' placeholder='text' />
	<input type='hidden' name='action' value='create_thread' />
	<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
	<button>submit</button>
</form>
<?php
		}


		$this->renderView('PopsicleFooterView');
	}
}