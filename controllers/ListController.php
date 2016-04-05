<?php



class ListController extends Controller {
	public static $required = ['ForumsDatabaseModel', 'ThreadsDatabaseModel', 'LoginModel', 'UserClassesDatabaseModel', 'CSRFTokenModel'];
	public static $inherited = ['ForumsView', 'UserErrorView'];
	public function invoke($args) {
		if (isset($_POST['action']) and isset($_POST['csrf_token'])) {
			// verify csrf token
			if (! $this->CSRFTokenModel->verify((string)$_POST['csrf_token'])) {
				$this->renderView('UserErrorView', ['invalid csrf token']);
			} else {
				$this->invokeAction($args);
			}
		} else {
			$this->invokePage($args);
		}
	}

	public function invokeAction($args) {
		global $mvcConfig;

		// determine what action the user wants to take
		if ($_POST['action'] === 'create_forum' and isset($_POST['title'])) {
			// verify user credentials
			$user = $this->LoginModel->getCurrentUser();
			if ($user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('create_forum')) {
				if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9 ]*$/', (string)$_POST['title'])) {
					$this->renderView('UserErrorView', ['forum title must be all alphanumeric or spaces with at least one character']);
				} else {
					$forum = $this->ForumsDatabaseModel->createForum($user->id, $_POST['title']);
					if ($forum === NULL) {
						$this->renderView('UserErrorView', ['error creating forum']);
					} else {
						$this->redirect($mvcConfig['pathBase'] . 'forum/' . $forum->id);
					}	
				}
			} else {
				$this->renderView('UserErrorView', ['invalid action']);
			}

		} elseif ($_POST['action'] === 'create_thread' and isset($_POST['title']) and isset($_POST['post']) and isset($args['id'])) {
			// verify user credentials
			$forum = $this->ForumsDatabaseModel->getForumById($args['id']);
			$user = $this->LoginModel->getCurrentUser();
			if ($forum !== NULL and $user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('create_thread')) {
				if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9 ]*$/', (string)$_POST['title'])) {
					$this->renderView('UserErrorView', ['thread title must be all alphanumeric or spaces with at least one character']);
				} elseif (strlen((string)$_POST['post']) < 1) {
					$this->renderView('UserErrorView', ['thread post must be at least one character long']);
				} else {
					$thread = $this->ThreadsDatabaseModel->createThread($forum->id, $user->id, $_POST['title'], $_POST['post']);
					if ($thread === NULL) {
						$this->renderView('UserErrorView', ['error creating thread']);
					} else {
						$this->redirect($mvcConfig['pathBase'] . 'thread/' . $thread->id);
					}
				}
			} else {
				$this->renderView('UserErrorView', ['invalid action']);
			}

		} elseif ($_POST['action'] === 'create_post' and isset($_POST['post']) and isset($args['id'])) {
			// verify user credentials
			$thread = $this->ThreadsDatabaseModel->getThreadById($args['id']);
			$user = $this->LoginModel->getCurrentUser();
			if ($thread !== NULL and $user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('create_post')) {
				if (strlen((string)$_POST['post']) < 1) {
					$this->renderView('UserErrorView', ['post must be at least one character long']);
				} else {
					$post = $this->ThreadsDatabaseModel->createPost($thread->id, $user->id, $_POST['post']);
					if ($post === NULL) {
						$this->renderView('UserErrorView', ['error creating post']);
					} else {
						$this->redirect($mvcConfig['pathBase'] . 'thread/' . $thread->id);
					}
				}
			} else {
				$this->renderView('UserErrorView', ['invalid action']);
			}

		} else {
			$this->renderView('UserErrorView', ['invalid action']);
		}
	}

	public function invokePage($args) {
		if ($args['page'] === 'forums') {
			$this->renderView('ForumsView');

		} elseif ($args['page'] === 'forum' and isset($args['id'])) {
			$forum = $this->ForumsDatabaseModel->getForumById($args['id']);
			if ($forum === NULL) {
				$this->renderView('UserErrorView', ['invalid forum id']);
			} else {
				if (isset($_GET['index'])) {
					$index = (int)$_GET['index'];
					if ($index < 0) {
						$index = 0;
					}
				} else {
					$index = 0;
				}
				$count = 15;

				$threads = $this->ThreadsDatabaseModel->listThreadsByForumId($forum->id, $index * $count, $count);
				$viewargs = ['threads' => $threads, 'forumid' => $forum->id, 'thisPage' => $index];
				if ($index > 0) {
					$viewargs['prevPage'] = $index - 1;
				}
				if ($index + 1 < $forum->threadcount / $count) {
					$viewargs['nextPage'] = $index + 1;
				}
				$this->renderView('ThreadsView', $viewargs);
			}

		} elseif ($args['page'] === 'thread' and isset($args['id'])) {

			$thread = $this->ThreadsDatabaseModel->getThreadById($args['id']);
			if ($thread === NULL) {
				$this->renderView('UserErrorView', ['invalid thread id']);
			} else {
				if (isset($_GET['index'])) {
					$index = (int)$_GET['index'];
					if ($index < 0) {
						$index = 0;
					}
				} else {
					$index = 0;
				}
				$count = 10;

				$posts = $this->ThreadsDatabaseModel->listPostsByThreadId($thread->id, $index * $count, $count);
				$viewargs = ['posts' => $posts, 'threadid' => $thread->id, 'thisPage' => $index];
				if ($index > 0) {
					$viewargs['prevPage'] = $index - 1;
				}
				if ($index + 1 < $thread->postcount / $count) {
					$viewargs['nextPage'] = $index + 1;
				}

				$viewargs['currentIndexStart'] = $index * $count;
				if ($thread->postcount > ($index + 1) * $count) {
					$viewargs['currentIndexEnd'] = ($index + 1) * $count - 1;
				} else {
					$viewargs['currentIndexEnd'] = $thread->postcount - 1;
				}


				$this->renderView('PostsView', $viewargs);
			}
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}
