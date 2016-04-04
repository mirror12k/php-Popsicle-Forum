<?php


class ForumsView extends View {
	public static $required = ['ForumsDatabaseModel', 'LoginModel', 'UserClassesDatabaseModel', 'CSRFTokenModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render ($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Forums' ]);

?>
<ul>
<?php
		global $mvcConfig; // need the base path
		foreach ($this->ForumsDatabaseModel->listForums() as $forum) {

?>
<li>
	<a href="<?php echo htmlentities($mvcConfig['pathBase'] . 'forum/' . $forum->id); ?>"><?php echo htmlentities($forum->title); ?></a>
	: <?php echo $forum->threadcount; ?> threads
</li>
<?php

		}

?>
</ul>
<p>hello world!</p>

<?php
		// if the user is privileged, show him the create_forum form
		$user = $this->LoginModel->getCurrentUser();
		if ($user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('create_forum')) {
?>
<p>Create forum:</p>
<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'forums'); ?>' method='POST'>
	Title: <input type='text' name='title' placeholder='title' />
	<input type='hidden' name='action' value='create_forum' />
	<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
	<button>submit</button>
</form>
<?php
		}

		$this->renderView('PopsicleFooterView');
	}
}

