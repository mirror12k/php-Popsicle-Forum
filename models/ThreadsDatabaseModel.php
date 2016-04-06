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
	private $timecreated;
	private $timeposted;
	private $locked;

	public function __construct($data) {
		$this->id = (int)$data['id'];
		$this->forumid = (int)$data['forumid'];
		$this->creatorid = (int)$data['creatorid'];
		$this->title = (string)$data['title'];
		$this->postcount = (int)$data['postcount'];
		$this->timecreated = (string)$data['timecreated'];
		$this->timeposted = (string)$data['timeposted'];
		$this->locked = (bool)$data['locked'];
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
		} elseif ($name === 'timecreated') {
			return $this->timecreated;
		} elseif ($name === 'timeposted') {
			return $this->timeposted;
		} elseif ($name === 'locked') {
			return $this->locked;
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
	private $timeposted;

	public function __construct($data) {
		$this->id = (int)$data['id'];
		$this->threadid = (int)$data['threadid'];
		$this->creatorid = (int)$data['creatorid'];
		$this->text = (string)$data['text'];
		$this->timeposted = (string)$data['timeposted'];
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
		} elseif ($name === 'timeposted') {
			return $this->timeposted;
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
	* updates the timeposted on a thread to the current time
	*/
	public function updateThreadTimePosted($id) {
		$id = (int)$id;
		$result = $this->DatabaseModel->query("UPDATE `threads` SET `timeposted`=UTC_TIMESTAMP() WHERE `id`=${id}");
		return $result;
	}

	/**
	* returns an array of thread entries (maximum of $count) in a given forum as Thread objects (starting at index $index)
	*/
	public function listThreadsByForumId($id, $index=0, $count=15) {
		$id = (int)$id;
		$index = (int)$index;
		$count = (int)$count;
		$result = $this->DatabaseModel->query("SELECT * FROM `threads` WHERE `forumid`=${id} ORDER BY `timeposted` DESC LIMIT ${index}, ${count}");
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
		$result = $this->DatabaseModel->query("INSERT INTO `threads` (`forumid`, `creatorid`, `title`, `timecreated`, `timeposted`)
				VALUES (${forumid}, ${creatorid}, '${title}', UTC_TIMESTAMP(), UTC_TIMESTAMP())");
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
	public function listPostsByThreadId($id, $index=0, $count=10) {
		$id = (int)$id;
		$index = (int)$index;
		$count = (int)$count;
		$result = $this->DatabaseModel->query("SELECT * FROM `posts` WHERE `threadid`=${id} ORDER BY `id` LIMIT ${index}, ${count}");
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
		$result = $this->DatabaseModel->query(
			"INSERT INTO `posts` (`threadid`, `creatorid`, `text`, `timeposted`) VALUES (${threadid}, ${creatorid}, '${text}', UTC_TIMESTAMP())"
		);
		if ($result === TRUE) {
			$post = $this->getPostById($this->DatabaseModel->insert_id);
			$this->incrementThreadPostCount($threadid);
			$this->updateThreadTimePosted($threadid);
			return $post;
		} else {
			return NULL;
		}
	}

	/**
	* changes the thread's lock status (0/1)
	*/
	public function setThreadLockedStatus($thread, $status) {
		if ($thread === NULL) {
			die('attempt to lock a NULL thread');
		}
		$id = (int)$thread->id;
		$status = (bool)$status;
		$result = $this->DatabaseModel->query("UPDATE `threads` SET `locked`=${status} WHERE `id`=${id}");
		return $result;
	}
}
