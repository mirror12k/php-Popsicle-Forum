<?php



class PostsView extends View {
	public static $required = ['CSRFTokenModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	
	public function render($args) {
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Posts' ]);

?>
<div class='posts_list'>
<p>viewing posts #<?php echo htmlentities($args['currentIndexStart']); ?> to #<?php echo htmlentities($args['currentIndexEnd']); ?></p>
<?php

		global $mvcConfig;
		foreach ($args['posts'] as $post) {
?>
<div class='post'>
	<a href="<?php echo htmlentities($mvcConfig['pathBase'] . 'user/' . $post->creatorid); ?>">poster</a>
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

		if ($args['showCreatePost']) {
?>
<p>Reply:</p>
<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'thread/' . $args['threadid']); ?>' method='POST'>
	<input type='textarea' name='post' placeholder='text' />
	<input type='hidden' name='action' value='create_post' />
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
