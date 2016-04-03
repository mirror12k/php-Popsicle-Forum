<?php



/**
* a read-only user class object to reference
*/
class UserClass {
	private $id;
	private $name;
	private $level;
	public $permissions;

	public function __construct($data) {
		$this->id = (int)$data['id'];
		$this->name = (string)$data['name'];
		$this->level = (int)$data['level'];
		$this->permissions = [];
		$this->permissions['create_forum'] = (bool)$data['permission_create_forum'];
		$this->permissions['delete_forum'] = (bool)$data['permission_delete_forum'];
		$this->permissions['create_thread'] = (bool)$data['permission_create_thread'];
		$this->permissions['delete_thread'] = (bool)$data['permission_delete_thread'];
		$this->permissions['create_post'] = (bool)$data['permission_create_post'];
		$this->permissions['delete_post'] = (bool)$data['permission_delete_post'];
		$this->permissions['edit_post'] = (bool)$data['permission_edit_post'];
		$this->permissions['edit_lower_class'] = (bool)$data['permission_edit_lower_class'];
	}
	public function __get($name) {
		if ($name === 'id') {
			return $this->id;
		} elseif ($name === 'name') {
			return $this->name;
		} elseif ($name === 'level') {
			return $this->level;
		}
	}
	public function can($action) {
		if (isset($this->permissions[$action])) {
			return $this->permissions[$action];
		} else {
			die ("invalid permission: " . $action);
		}
	}
}


/**
* interfaces with the classes table
*/
class UserClassesDatabaseModel extends Model {
	public static $required = ['DatabaseModel'];

	/**
	* returns a new UserClass object from the given table row
	*/
	public function renderUserClass($data) {
		return new UserClass($data);
	}

	public function getUserClassByUser($user) {
		return $this->getUserClassById($user->classid);
	}

	/**
	* returns a UserClass object for the given id or NULL if no such class exists
	*/
	public function getUserClassById($id) {
		$id = (int)$id;
		$result = $this->DatabaseModel->query("SELECT * FROM `classes` WHERE `id`=${id}");
		if (! is_object($result)) {
			return NULL;
		}
		if ($result->num_rows === 0) {
			$class = NULL;
		} else {
			$class = $this->renderUserClass($result->fetch_assoc());
		}
		$result->free();
		return $class;
	}

	/**
	* returns a UserClass object for the given name or NULL if no such class exists
	*/
	public function getUserClassByName($name) {
		$name = $this->DatabaseModel->mysql_escape_string($name);
		$result = $this->DatabaseModel->query("SELECT * FROM `classes` WHERE `name`='${name}'");
		if (! is_object($result)) {
			return NULL;
		}
		if ($result->num_rows === 0) {
			$class = NULL;
		} else {
			$class = $this->renderUserClass($result->fetch_assoc());
		}
		$result->free();
		return $class;
	}

	/**
	* creates a new class entry
	* returns a UserClass object if successful, null otherwise
	*/
	public function createClass($name, $level) {
		$name = $this->DatabaseModel->mysql_escape_string($name);
		$level = (int)$level;
		$result = $this->DatabaseModel->query("INSERT INTO `classes` (`name`, `level`) VALUES ('${name}', ${level})");
		if ($result === TRUE) {
			return $this->getUserClassById($this->DatabaseModel->insert_id);
		} else {
			return NULL;
		}
	}

	/**
	* sets a permission for a given class
	*/
	public function setPermission($class, $permission, $value) {
		if ($class === NULL) {
			die('attempt to set permission to NULL class');
		}
		$id = (int)$class->id;
		$class->can($permission); // UserClass will die unless $permission is a valid permission name
		$value = (int)$value;
		$result = $this->DatabaseModel->query("UPDATE `classes` SET `permission_${permission}` = $value WHERE `id`=${id}");
		return $result;
	}

}
