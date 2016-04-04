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
		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}
