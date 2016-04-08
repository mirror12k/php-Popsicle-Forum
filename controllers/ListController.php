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
				if ($user->muted) {
					$this->renderView('UserErrorView', ['user muted']);
				} elseif (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9 ]*$/', (string)$_POST['title'])) {
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

		} elseif (($_POST['action'] === 'unlock_forum' or $_POST['action'] === 'lock_forum') and isset($_POST['forumid'])) {
			// verify user credentials
			$user = $this->LoginModel->getCurrentUser();
			if ($user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('lock_forum')) {
				// verify target
				$forum = $this->ForumsDatabaseModel->getForumById($_POST['forumid']);
				if ($forum === NULL) {
					$this->renderView('UserErrorView', ['invalid action']);
				} else {
					$result = $this->ForumsDatabaseModel->setForumLockedStatus($forum, $_POST['action'] === 'lock_forum');
					if (! $result) {
						$this->renderView('UserErrorView', ['error setting locked status']);
					} else {
						$this->redirect($mvcConfig['pathBase'] . 'forums');
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
				if ($user->muted) {
					$this->renderView('UserErrorView', ['user muted']);
				} elseif ($forum->locked) {
					$this->renderView('UserErrorView', ['forum locked']);
				} elseif (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9 ]*$/', (string)$_POST['title'])) {
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

		} elseif (($_POST['action'] === 'unlock_thread' or $_POST['action'] === 'lock_thread') and isset($args['id']) and isset($_POST['threadid'])) {
			// verify user credentials
			$forum = $this->ForumsDatabaseModel->getForumById($args['id']);
			$user = $this->LoginModel->getCurrentUser();
			if ($forum !== NULL and $user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('lock_thread')) {
				// verify target
				$thread = $this->ThreadsDatabaseModel->getThreadById($_POST['threadid']);
				if ($thread === NULL) {
					$this->renderView('UserErrorView', ['invalid action']);
				} else {
					$result = $this->ThreadsDatabaseModel->setThreadLockedStatus($thread, $_POST['action'] === 'lock_thread');
					if (! $result) {
						$this->renderView('UserErrorView', ['error setting locked status']);
					} else {
						$this->redirect($mvcConfig['pathBase'] . 'forum/' . $forum->id);
					}
				}
			} else {
				$this->renderView('UserErrorView', ['invalid action']);
			}

		} elseif (($_POST['action'] === 'unsticky_thread' or $_POST['action'] === 'sticky_thread') and isset($args['id']) and isset($_POST['threadid'])) {
			// verify user credentials
			$forum = $this->ForumsDatabaseModel->getForumById($args['id']);
			$user = $this->LoginModel->getCurrentUser();
			if ($forum !== NULL and $user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('sticky_thread')) {
				// verify target
				$thread = $this->ThreadsDatabaseModel->getThreadById($_POST['threadid']);
				if ($thread === NULL) {
					$this->renderView('UserErrorView', ['invalid action']);
				} else {
					$result = $this->ThreadsDatabaseModel->setThreadStickiedStatus($thread, $_POST['action'] === 'sticky_thread');
					if (! $result) {
						$this->renderView('UserErrorView', ['error setting stickying status']);
					} else {
						$this->redirect($mvcConfig['pathBase'] . 'forum/' . $forum->id);
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
				if ($user->muted) {
					$this->renderView('UserErrorView', ['user muted']);
				} elseif ($thread->locked) {
					$this->renderView('UserErrorView', ['thread locked']);
				} elseif (strlen((string)$_POST['post']) < 1) {
					$this->renderView('UserErrorView', ['post must be at least one character long']);
				} else {
					$post = $this->ThreadsDatabaseModel->createPost($thread, $user->id, $_POST['post']);
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
			// get the forums to display
			$forums = $this->ForumsDatabaseModel->listForums();
			$viewargs = ['forums' => $forums];
			// if the user is privileged, show him the create_forum form
			$user = $this->LoginModel->getCurrentUser();
			$viewargs['showCreateForum'] = ($user !== NULL and (! $user->muted)
				and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('create_forum'));
			$viewargs['showMuted'] = ($user !== NULL and $user->muted);
			$viewargs['showLockForum'] = ($user !== NULL and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('lock_forum'));

			$this->renderView('ForumsView', $viewargs);

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

				// if the user is privileged, show him the create_thread form
				$user = $this->LoginModel->getCurrentUser();
				$userclass = $this->UserClassesDatabaseModel->getUserClassByUser($user);
				$viewargs['showCreateThread'] = ($user !== NULL and (! $user->muted) and $userclass->can('create_thread'));
				$viewargs['showMuted'] = ($user !== NULL and $user->muted);
				$viewargs['showLockThread'] = ($user !== NULL and $userclass->can('lock_thread'));
				$viewargs['showStickyThread'] = ($user !== NULL and $userclass->can('sticky_thread'));

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

				// if the user is privileged, show him the create_post form
				$user = $this->LoginModel->getCurrentUser();
				$viewargs['showCreatePost'] = ($user !== NULL and (! $user->muted)
					and $this->UserClassesDatabaseModel->getUserClassByUser($user)->can('create_post'));
				$viewargs['showMuted'] = ($user !== NULL and $user->muted);

				$this->renderView('PostsView', $viewargs);
			}
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}
