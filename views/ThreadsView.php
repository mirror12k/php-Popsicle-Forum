<?php

class ThreadsView extends View {
	public static $required = ['CSRFTokenModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Threads' ]);

?>
<div class='threads_list'>
<?php

		global $mvcConfig;
		foreach ($args['threads'] as $thread) {
?>
<div class='thread'>
	<a href="<?php echo htmlentities($mvcConfig['pathBase'] . 'thread/' . $thread->id); ?>"><?php echo htmlentities($thread->title); ?></a>
	: <?php echo $thread->postcount; ?> posts :
	<span class='post_time'>last posted: <?php echo htmlentities($thread->timeposted); ?> :
	time created: <?php echo htmlentities($thread->timecreated); ?></span>
<?php
				if ($args['showLockThread']) {
					if ($thread->locked) {
?>
	<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'forum/' . $args['forumid']); ?>' method='POST'>
		<input type='hidden' name='action' value='unlock_thread' />
		<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
		<button>unlock thread</button>
	</form>
<?php
					} else {
?>
	<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'forum/' . $args['forumid']); ?>' method='POST'>
		<input type='hidden' name='action' value='lock_thread' />
		<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
		<button>lock thread</button>
	</form>
<?php
					}
				}
?>
</div>
<?php
		}

		if (isset($args['prevPage'])) {
			?><b><a href="<?php echo '?index=' . htmlentities($args['prevPage']); ?>">&lt;</a></b><?php
		}
		if (isset($args['thisPage'])) {
			?><b> <?php echo htmlentities($args['thisPage']); ?> </b><?php
		}
		if (isset($args['nextPage'])) {
			?><b><a href="<?php echo '?index=' . htmlentities($args['nextPage']); ?>">&gt;</a></b><?php
		}

		if ($args['showCreateThread']) {
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
