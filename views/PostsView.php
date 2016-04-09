<?php



class PostsView extends View {
	public static $required = ['CSRFTokenModel', 'UsersDatabaseModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView', 'FancyUsernameView'];
	
	public function render($args) {
		global $mvcConfig;
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Posts' ]);

?>
<div class='posts_list'>
<p>
	viewing posts #<?php echo htmlentities($args['currentIndexStart']); ?>
	to #<?php echo htmlentities($args['currentIndexEnd']); ?>
	of <?php echo htmlentities($args['lastIndex']); ?> posts
</p>
<?php

		foreach ($args['posts'] as $post) {
?>
<div class='post'>
	<?php $this->renderView('FancyUsernameView', [$this->UsersDatabaseModel->getUserById($post->creatorid)]) ?>
	<?php
		if (isset($args['linkThread']) and $args['linkThread']) {
			echo  " : <a href='" . htmlentities($mvcConfig['pathBase'] . 'thread/' . $post->threadid) . "'>source</a>";
		}
	?>
	<div class='post_time'><?php echo htmlentities($post->timeposted) ?></div>
	<div class='post_text'><p><?php echo htmlentities($post->text); ?></p></div>
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

		if (isset($args['showCreatePost']) and $args['showCreatePost']) {
?>
<p>Reply:</p>
<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'thread/' . $args['threadid']); ?>' method='POST'>
	<input type='textarea' name='post' placeholder='text' />
	<input type='hidden' name='action' value='create_post' />
	<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
	<button>submit</button>
</form>
<?php

		} elseif (isset($args['showMuted']) and $args['showMuted']) {
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
