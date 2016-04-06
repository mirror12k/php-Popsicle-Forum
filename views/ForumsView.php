<?php


class ForumsView extends View {
	public static $required = ['LoginModel', 'UserClassesDatabaseModel', 'CSRFTokenModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render ($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Forums' ]);

?>
<div class='forums_list'>
<?php
		global $mvcConfig; // need the base path
		if (count($args['forums']) === 0) {
?>
<div class='message'>No forums have been created yet!</div>
<?php

		} else {
			foreach ($args['forums'] as $forum) {

?>
<div class='forum'>
	<a href="<?php echo htmlentities($mvcConfig['pathBase'] . 'forum/' . $forum->id); ?>"><?php echo htmlentities($forum->title); ?></a>
	: <?php echo $forum->threadcount; ?> threads
</div>
<?php

			}
		}

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

?>
</div>
<?php

		$this->renderView('PopsicleFooterView');
	}
}

