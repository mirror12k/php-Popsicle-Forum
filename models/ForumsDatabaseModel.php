<?php



/**
* a read-only Forum object to reference
*/
class Forum {
	private $id;
	private $creatorid;
	private $title;
	private $threadcount;
	private $timecreated;
	private $timeposted;
	private $locked;

	public function __construct($data) {
		$this->id = (int)$data['id'];
		$this->creatorid = (int)$data['creatorid'];
		$this->title = (string)$data['title'];
		$this->threadcount = (int)$data['threadcount'];
		$this->timecreated = (string)$data['timecreated'];
		$this->timeposted = (string)$data['timeposted'];
		$this->locked = (bool)$data['locked'];
	}
	public function __get($name) {
		if ($name === 'id') {
			return $this->id;
		} elseif ($name === 'creatorid') {
			return $this->creatorid;
		} elseif ($name === 'title') {
			return $this->title;
		} elseif ($name === 'threadcount') {
			return $this->threadcount;
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
* abstracts the forums table
*/
class ForumsDatabaseModel extends Model {
	public static $required = ['DatabaseModel'];

	/**
	* return a Forum object from a table row
	*/
	public function renderForum($data) {
		return new Forum($data);
	}

	/**
	* returns a Forum object or null for a given id
	*/
	public function getForumById($id) {
		$id = (int)$id;
		$result = $this->DatabaseModel->query("SELECT * FROM `forums` WHERE `id`=${id}");
		if (! is_object($result)) {
			return NULL;
		}
		if ($result->num_rows === 0) {
			$forum = NULL;
		} else {
			$forum = $this->renderForum($result->fetch_assoc());
		}
		$result->free();
		return $forum;
	}

	/**
	* changes the threadcount of a thread by the given delta (defaults to 1)
	*/
	public function incrementForumThreadCount($id, $delta=1) {
		$id = (int)$id;
		$delta = (int)$delta;
		$result = $this->DatabaseModel->query("UPDATE `forums` SET `threadcount`=`threadcount`+${delta} WHERE `id`=${id}");
		return $result;
	}


	/**
	* returns an array of all forums entries as Forum objects
	*/
	public function listForums() {
		$result = $this->DatabaseModel->query("SELECT * FROM `forums`");
		if (! is_object($result)) {
			return [];
		}
		$forums = [];
		while ($row = $result->fetch_assoc()) {
			array_push($forums, $this->renderForum($row));
		}
		$result->free();
		return $forums;
	}

	/**
	* updates the timeposted on a forum to the current time
	*/
	public function updateForumTimePosted($forum) {
		$id = (int)$forum->id;
		$result = $this->DatabaseModel->query("UPDATE `forums` SET `timeposted`=UTC_TIMESTAMP() WHERE `id`=${id}");
		return $result;
	}

	/**
	* creates a new forum with the given title and creatorid
	*/
	public function createForum($creatorid, $title) {
		$creatorid = (int)$creatorid;
		$title = $this->DatabaseModel->mysql_escape_string($title);
		$result = $this->DatabaseModel->query("INSERT INTO `forums` (`creatorid`, `title`, `timecreated`, `timeposted`)
			VALUES (${creatorid}, '${title}', UTC_TIMESTAMP(), UTC_TIMESTAMP())");
		if ($result === TRUE) {
			return $this->getForumById($this->DatabaseModel->insert_id);
		} else {
			return NULL;
		}
	}

	/**
	* changes the forum's lock status (0/1)
	*/
	public function setForumLockedStatus($forum, $status) {
		if ($forum === NULL) {
			die('attempt to lock a NULL forum');
		}
		$id = (int)$forum->id;
		$status = (int)$status;
		$result = $this->DatabaseModel->query("UPDATE `forums` SET `locked`=${status} WHERE `id`=${id}");
		return $result;
	}
}
