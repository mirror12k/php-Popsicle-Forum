<?php


class ForumsView extends View {
	public static $required = ['CSRFTokenModel', 'UsersDatabaseModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView', 'FancyUsernameView'];
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
	: created by <?php echo $this->renderView('FancyUsernameView', [$this->UsersDatabaseModel->getUserById($forum->creatorid)]); ?>
	: <?php echo $forum->threadcount; ?> threads
	<span class='post_time'> : last posted: <?php echo htmlentities($forum->timeposted); ?> :
	time created: <?php echo htmlentities($forum->timecreated); ?></span>
<?php
				if ($args['showLockForum']) {
					if ($forum->locked) {
?>
	<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'forums'); ?>' method='POST'>
		<input type='hidden' name='action' value='unlock_forum' />
		<input type='hidden' name='forumid' value='<?php echo htmlentities($forum->id); ?>' />
		<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
		<button>unlock forum</button>
	</form>
<?php
					} else {
?>
	<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'forums'); ?>' method='POST'>
		<input type='hidden' name='action' value='lock_forum' />
		<input type='hidden' name='forumid' value='<?php echo htmlentities($forum->id); ?>' />
		<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
		<button>lock forum</button>
	</form>
<?php
					}
				}
?>
</div>
<?php

			}
		}

		if ($args['showCreateForum']) {
?>
<p>Create forum:</p>
<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'forums'); ?>' method='POST'>
	Title: <input type='text' name='title' placeholder='title' />
	<input type='hidden' name='action' value='create_forum' />
	<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
	<button>submit</button>
</form>
<?php

		} elseif ($args['showMuted']) {
?>
<div class='message'>User is Muted!</div>
<?php
		}

?>
</div>
<?php

		$this->renderView('PopsicleFooterView');
	}
}

