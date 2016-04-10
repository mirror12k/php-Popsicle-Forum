<?php



class SearchView extends View {
	public static $required = ['CSRFTokenModel'];
	public static $inherited = ['PopsicleHeaderView', 'PopsicleFooterView'];
	public function render($args) {
		global $mvcConfig;
		$this->renderView('PopsicleHeaderView', [ 'title' => 'Popsicle - Search' ]);

?>
<div class='search_form'>
	<p>Search posts:</p>
	<form action='<?php echo htmlentities($mvcConfig['pathBase'] . 'search'); ?>' method='POST'>
		<input type='text' name='terms' placeholder='search text' />
		<input type='hidden' name='action' value='search_posts' />
		<input type='hidden' name='csrf_token' value='<?php echo htmlentities($this->CSRFTokenModel->get()); ?>' />
		<button>submit</button>
	</form>
</div>
<?php

		$this->renderView('PopsicleFooterView');
	}
}