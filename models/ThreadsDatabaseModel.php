<?php



/**
* a read-only Thread object to reference
*/
class Thread {
	private $id;
	private $forumid;
	private $creatorid;
	private $title;
	private $postcount;

	public function __construct($data) {
		$this->id = (int)$data['id'];
		$this->forumid = (int)$data['forumid'];
		$this->creatorid = (int)$data['creatorid'];
		$this->title = (string)$data['title'];
		$this->postcount = (int)$data['postcount'];
	}
	public function __get($name) {
		if ($name === 'id') {
			return $this->id;
		} elseif ($name === 'forumid') {
			return $this->forumid;
		} elseif ($name === 'creatorid') {
			return $this->creatorid;
		} elseif ($name === 'postcount') {
			return $this->postcount;
		} elseif ($name === 'title') {
			return $this->title;
		}
	}
}

/**
* a read-only Post object to reference
*/
class Post {
	private $id;
	private $threadid;
	private $creatorid;
	private $text;

	public function __construct($data) {
		$this->id = (int)$data['id'];
		$this->threadid = (int)$data['threadid'];
		$this->creatorid = (int)$data['creatorid'];
		$this->text = (string)$data['text'];
	}
	public function __get($name) {
		if ($name === 'id') {
			return $this->id;
		} elseif ($name === 'threadid') {
			return $this->threadid;
		} elseif ($name === 'creatorid') {
			return $this->creatorid;
		} elseif ($name === 'text') {
			return $this->text;
		}
	}
}


/**
* abstracts the threads table
*/
class ThreadsDatabaseModel extends Model {
	public static $required = ['DatabaseModel', 'ForumsDatabaseModel'];

	/**
	* return a Thread object from a table row
	*/
	public function renderThread($data) {
		return new Thread($data);
	}
	/**
	* return a Post object from a table row
	*/
	public function renderPost($data) {
		return new Post($data);
	}

	/**
	* returns a Thread object or null for a given id
	*/
	public function getThreadById($id) {
		$id = (int)$id;
		$result = $this->DatabaseModel->query("SELECT * FROM `threads` WHERE `id`=${id}");
		if (! is_object($result)) {
			return NULL;
		}
		if ($result->num_rows === 0) {
			$thread = NULL;
		} else {
			$thread = $this->renderThread($result->fetch_assoc());
		}
		$result->free();
		return $thread;
	}

	/**
	* changes the postcount of a thread by the given delta (defaults to 1)
	*/
	public function incrementThreadPostCount($id, $delta=1) {
		$id = (int)$id;
		$delta = (int)$delta;
		$result = $this->DatabaseModel->query("UPDATE `threads` SET `postcount`=`postcount`+${delta} WHERE `id`=${id}");
		return $result;
	}

	/**
	* returns an array of all threads entries in a given forum as Thread objects
	*/
	public function listThreadsByForumId($id) {
		$id = (int)$id;
		$result = $this->DatabaseModel->query("SELECT * FROM `threads` WHERE `forumid`=${id}");
		if (! is_object($result)) {
			return [];
		}
		$threads = [];
		while ($row = $result->fetch_assoc()) {
			array_push($threads, $this->renderThread($row));
		}
		$result->free();
		return $threads;
	}

	/**
	* creates a new Thread with the given data, and creates a single post in it from the $firstpost text
	*/
	public function createThread($forumid, $creatorid, $title, $firstpost) {
		$forumid = (int)$forumid;
		$creatorid = (int)$creatorid;
		$title = $this->DatabaseModel->mysql_escape_string($title);
		$result = $this->DatabaseModel->query("INSERT INTO `threads` (`forumid`, `creatorid`, `title`) VALUES (${forumid}, ${creatorid}, '${title}')");
		if ($result === TRUE) {
			$thread = $this->getThreadById($this->DatabaseModel->insert_id);
			$this->ForumsDatabaseModel->incrementForumThreadCount($forumid);
			$this->createPost($thread->id, $creatorid, $firstpost);
			return $thread;
		} else {
			return NULL;
		}
	}

	/**
	* returns a Post object or null for a given id
	*/
	public function getPostById($id) {
		$id = (int)$id;
		$result = $this->DatabaseModel->query("SELECT * FROM `posts` WHERE `id`=${id}");
		if (! is_object($result)) {
			return NULL;
		}
		if ($result->num_rows === 0) {
			$post = NULL;
		} else {
			$post = $this->renderPost($result->fetch_assoc());
		}
		$result->free();
		return $post;
	}

	/**
	* returns an array of all threads entries in a given forum as Thread objects
	*/
	public function listPostsByThreadId($id) {
		$id = (int)$id;
		$result = $this->DatabaseModel->query("SELECT * FROM `posts` WHERE `threadid`=${id}");
		if (! is_object($result)) {
			return [];
		}
		$posts = [];
		while ($row = $result->fetch_assoc()) {
			array_push($posts, $this->renderPost($row));
		}
		$result->free();
		return $posts;
	}

	/**
	* creates a new Post with the given data
	*/
	public function createPost($threadid, $creatorid, $text) {
		$threadid = (int)$threadid;
		$creatorid = (int)$creatorid;
		$text = $this->DatabaseModel->mysql_escape_string($text);
		$result = $this->DatabaseModel->query("INSERT INTO `posts` (`threadid`, `creatorid`, `text`) VALUES (${threadid}, ${creatorid}, '${text}')");
		if ($result === TRUE) {
			$post = $this->getPostById($this->DatabaseModel->insert_id);
			$this->incrementThreadPostCount($threadid);
			return $post;
		} else {
			return NULL;
		}
	}
}
