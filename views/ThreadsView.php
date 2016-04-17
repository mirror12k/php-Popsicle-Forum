<?php

class ThreadsView extends View {
	public static $required = ['CSRFTokenModel', 'UsersDatabaseModel', 'ThreadsDatabaseModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView', 'FancyUsernameView'];
	public function render($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Threads' ]);

?>
<div class='threads_list'>
<?php

		global $mvcConfig;
		foreach ($args['threads'] as $thread) {
			$lastpost = $this->ThreadsDatabaseModel->getPostById($thread->lastpostid);
			$latestPoster = $this->UsersDatabaseModel->getUserById($lastpost->creatorid);

			$latestPage = (int)(($thread->postcount - 1) / 10);

?>
<div class='thread'>
	<a href="<?php echo htmlentities($mvcConfig['pathBase'] . 'thread/' . $thread->id); ?>"><?php echo htmlentities($thread->title); ?></a>
	: created by <?php echo $this->renderView('FancyUsernameView', [$this->UsersDatabaseModel->getUserById($thread->creatorid)]); ?>
	: <?php echo $thread->postcount; ?> posts
	<?php echo $thread->stickied ? ' : stickied ' : ''; ?>
	<?php echo $thread->locked ? ' : locked ' : ''; ?>
	: <a href="<?php echo htmlentities($mvcConfig['pathBase'] . 'thread/' . $thread->id . '?index=' . $latestPage); ?>">latest post</a>
		by <?php echo $this->renderView('FancyUsernameView', [$latestPoster]); ?>
	<span class='post_time'>
		: last posted: <?php echo htmlentities($thread->timeposted); ?>
		: time created: <?php echo htmlentities($thread->timecreated); ?>
	</span>
<?php
				if ($args['showLockThread']) {
					if ($thread->locked) {
?>
	<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'forum/' . $args['forumid']); ?>' method='POST'>
		<input type='hidden' name='action' value='unlock_thread' />
		<input type='hidden' name='threadid' value='<?php echo htmlentities($thread->id); ?>' />
		<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
		<button>unlock thread</button>
	</form>
<?php
					} else {
?>
	<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'forum/' . $args['forumid']); ?>' method='POST'>
		<input type='hidden' name='action' value='lock_thread' />
		<input type='hidden' name='threadid' value='<?php echo htmlentities($thread->id); ?>' />
		<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
		<button>lock thread</button>
	</form>
<?php
					}
				}

				if ($args['showStickyThread']) {
					if ($thread->stickied) {
?>
	<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'forum/' . $args['forumid']); ?>' method='POST'>
		<input type='hidden' name='action' value='unsticky_thread' />
		<input type='hidden' name='threadid' value='<?php echo htmlentities($thread->id); ?>' />
		<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
		<button>unsticky thread</button>
	</form>
<?php
					} else {
?>
	<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'forum/' . $args['forumid']); ?>' method='POST'>
		<input type='hidden' name='action' value='sticky_thread' />
		<input type='hidden' name='threadid' value='<?php echo htmlentities($thread->id); ?>' />
		<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
		<button>sticky thread</button>
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
	Post: <textarea name='post' cols=50 rows=10 ></textarea>
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
