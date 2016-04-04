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
								$this->redirect('forum/' . $forum->id);
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
								$this->redirect('../thread/' . $thread->id);
							}
						}
					} else {
						$this->renderView('UserErrorView', ['invalid action']);
					}
				} else {
					$this->renderView('UserErrorView', ['invalid action']);
				}
			}
		} elseif ($args['page'] === 'forums') {
			$this->renderView('ForumsView');
		} elseif ($args['page'] === 'forum' and isset($args['id'])) {
			$forum = $this->ForumsDatabaseModel->getForumById($args['id']);
			if ($forum === NULL) {
				$this->renderView('UserErrorView', ['invalid forum id']);
			} else {
				$threads = $this->ThreadsDatabaseModel->listThreadsByForumId($forum->id);
				$this->renderView('ThreadsView', ['threads' => $threads, 'forumid' => $forum->id]);
			}
		} elseif ($args['page'] === 'thread' and isset($args['id'])) {
			$thread = $this->ThreadsDatabaseModel->getThreadById($args['id']);
			if ($thread === NULL) {
				$this->renderView('UserErrorView', ['invalid thread id']);
			} else {
				$posts = $this->ThreadsDatabaseModel->listPostsByThreadId($thread->id);
				$this->renderView('PostsView', ['posts' => $posts, 'threadid' => $thread->id]);
			}
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}
