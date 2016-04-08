<?php



class UsersView extends View {
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView', 'FancyUsernameView'];
	public function render($args) {
		global $mvcConfig;
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Users List' ]);

?>
<div class='users_list'>
	<?php foreach ($args['users'] as $user) { ?>
	<div class='user_entry'><?php $this->renderView('FancyUsernameView', [$user]); ?></div>
	<?php } ?>
<?php
		if (isset($args['prevPage'])) {
			?><b><a href="<?php echo '?index=' . htmlentities($args['prevPage']); ?>">&lt;</a></b><?php
		}
		if (isset($args['thisPage'])) {
			?><b> <?php echo htmlentities($args['thisPage']); ?> </b><?php
		}
		if (isset($args['nextPage'])) {
			?><b><a href="<?php echo '?index=' . htmlentities($args['nextPage']); ?>">&gt;</a></b><?php
		}
?>
</div>
<?php


		$this->renderView('PopsicleFooterView');
	}
}
