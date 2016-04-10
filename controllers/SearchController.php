<?php



class SearchController extends Controller {
	public static $required = ['ThreadsDatabaseModel'];
	public static $inherited = ['UserErrorView', 'SearchView', 'PostsView'];

	public function invoke($args) {
		if (isset($_POST['action']) and isset($_POST['csrf_token'])) {
			// verify csrf token
			if (! $this->CSRFTokenModel->verify((string)$_POST['csrf_token'])) {
				$this->renderView('UserErrorView', ['invalid csrf token']);
			} else {
				$this->invokeAction($args);
			}
		} elseif (isset($args['page'])) {
			$this->invokePage($args);
		}
	}

	public function invokeAction($args) {
		if ($_POST['action'] === 'search_posts' and isset($_POST['terms'])) {
			$terms = trim((string)$_POST['terms']);
			$terms = preg_split("/\s+/", $terms);

			global $popsicleConfig;
			$count = $popsicleConfig['postsPerPage'];

			$posts = $this->ThreadsDatabaseModel->searchPosts($terms, $count);

			$viewargs = ['posts' => $posts, 'thisPage' => 0];

			$viewargs['currentIndexStart'] = 0;
			$viewargs['currentIndexEnd'] = count($posts);
			$viewargs['lastIndex'] = count($posts);

			$this->renderView('PostsView', $viewargs);
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}

	public function invokePage($args) {
		if ($args['page'] === 'search') {
			$this->renderView('SearchView');
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}
