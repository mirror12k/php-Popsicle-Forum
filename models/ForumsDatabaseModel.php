<?php



/**
* a read-only Forum object to reference
*/
class Forum {
	private $id;
	private $creatorid;
	private $title;
	private $threadcount;

	public function __construct($data) {
		$this->id = (int)$data['id'];
		$this->creatorid = (int)$data['creatorid'];
		$this->title = (string)$data['title'];
		$this->threadcount = (int)$data['threadcount'];
	}
	public function __get($name) {
		if ($name === 'id') {
			return $this->id;
		} elseif ($name === 'creatorid') {
			return $this->creatorid;
		} elseif ($name === 'threadcount') {
			return $this->threadcount;
		} elseif ($name === 'title') {
			return $this->title;
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
	* creates a new forum with the given title and creatorid
	*/
	public function createForum($creatorid, $title) {
		$creatorid = (int)$creatorid;
		$title = $this->DatabaseModel->mysql_escape_string($title);
		$result = $this->DatabaseModel->query("INSERT INTO `forums` (`creatorid`, `title`) VALUES (${creatorid}, '${title}')");
		if ($result === TRUE) {
			return $this->getForumById($this->DatabaseModel->insert_id);
		} else {
			return NULL;
		}
	}
}