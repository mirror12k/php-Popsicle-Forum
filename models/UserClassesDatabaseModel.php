<?php



/**
* a read-only user class object to reference
*/
class UserClass {
	private $id;
	private $name;
	private $level;
	private $color;
	public $permissions;

	public function __construct($data) {
		$this->id = (int)$data['id'];
		$this->name = (string)$data['name'];
		$this->level = (int)$data['level'];
		$this->color = (string)$data['color'];
		$this->permissions = [];
		$this->permissions['create_forum'] = (bool)$data['permission_create_forum'];
		$this->permissions['lock_forum'] = (bool)$data['permission_lock_forum'];
		$this->permissions['delete_forum'] = (bool)$data['permission_delete_forum'];

		$this->permissions['create_thread'] = (bool)$data['permission_create_thread'];
		$this->permissions['lock_thread'] = (bool)$data['permission_lock_thread'];
		$this->permissions['delete_thread'] = (bool)$data['permission_delete_thread'];

		$this->permissions['create_post'] = (bool)$data['permission_create_post'];
		$this->permissions['edit_post'] = (bool)$data['permission_edit_post'];
		$this->permissions['delete_post'] = (bool)$data['permission_delete_post'];

		$this->permissions['edit_lower_class'] = (bool)$data['permission_edit_lower_class'];
		$this->permissions['ban_user'] = (bool)$data['permission_ban_user'];
		$this->permissions['mute_user'] = (bool)$data['permission_mute_user'];
	}
	public function __get($name) {
		if ($name === 'id') {
			return $this->id;
		} elseif ($name === 'name') {
			return $this->name;
		} elseif ($name === 'level') {
			return $this->level;
		} elseif ($name === 'color') {
			return $this->color;
		}
	}
	public function can($action) {
		if (isset($this->permissions[$action])) {
			return $this->permissions[$action];
		} else {
			die ("invalid permission: " . $action);
		}
	}
	public function getAllPermissions() {
		// safely shallow copy the permissions array to prevent any accidental changes
		$all = [];
		foreach ($this->permissions as $key => $val) {
			$all[$key] = $val;
		}
		return $all;
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

	public function listUserClasses() {
		$result = $this->DatabaseModel->query("SELECT * FROM `classes`");
		if (! is_object($result)) {
			return [];
		}

		$classes = [];
		while ($row = $result->fetch_assoc()) {
			array_push($classes, $this->renderUserClass($row));
		}
		$result->free();
		return $classes;
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
	* shortcut to updating multiple permissions
	*/
	public function updateUserClassPermissions($class, $permissions) {
		foreach ($permissions as $key => $val) {
			$this->setPermission($class, $key, $val);
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
		$result = $this->DatabaseModel->query("UPDATE `classes` SET `permission_${permission}`=$value WHERE `id`=${id}");
		return $result;
	}

	/**
	* sets a user class's name
	*/
	public function setUserClassName($class, $value) {
		if ($class === NULL) {
			die('attempt to set name to NULL class');
		}
		$id = (int)$class->id;
		$value = (string)$value;
		if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9 ]{0,63}$/', $value)) {
			die('invalid name for class: ' . $value);
		}

		$result = $this->DatabaseModel->query("UPDATE `classes` SET `name`='${value}' WHERE `id`=${id}");
		return $result;
	}

	/**
	* sets a user class's level
	*/
	public function setUserClassLevel($class, $value) {
		if ($class === NULL) {
			die('attempt to set level to NULL class');
		}
		$id = (int)$class->id;
		$value = (int)$value;

		$result = $this->DatabaseModel->query("UPDATE `classes` SET `level`=${value} WHERE `id`=${id}");
		return $result;
	}

	/**
	* sets a user class's highlight color
	*/
	public function setUserClassColor($class, $value) {
		if ($class === NULL) {
			die('attempt to set color to NULL class');
		}
		$id = (int)$class->id;
		$value = (string)$value;
		if (! preg_match('/^[a-fA-F0-9]{6}$/', $value)) {
			die('invalid color for class: ' . $value);
		}
		$result = $this->DatabaseModel->query("UPDATE `classes` SET `color`='${value}' WHERE `id`=${id}");

		$this->writeUserClassesStylesheet();
		return $result;
	}

	/**
	* writes all the color rules from the classes table to the 'media/userclasses.css' file
	*/
	public function writeUserClassesStylesheet() {
		$file = fopen('media/userclasses.css', 'w');
		if ($file === FALSE) {
			die("failed to open media/userclasses.css!");
		}
		$classes = $this->listUserClasses();
		fwrite($file,
"/*
* this file is automatically written by popsicle
* any changes will be over written
*/\n\n");
		foreach ($classes as $class) {
			fwrite($file, ".userclass_" . $class->id . " {\n");
			fwrite($file, "\tcolor: #" . $class->color . ";\n");
			fwrite($file, "}\n\n");
		}
		fclose($file);
	}
}
