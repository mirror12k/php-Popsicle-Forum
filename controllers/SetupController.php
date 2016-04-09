<?php



class SetupController extends Controller {
	public static $required = ['UserClassesDatabaseModel', 'UsersDatabaseModel'];
	public static $inherited = ['UserErrorView'];
	public function invoke($args) {
		if (count($this->UserClassesDatabaseModel->listUserClasses()) === 0) {
			$userClass = $this->UserClassesDatabaseModel->createClass('user', 10);
			if ($userClass === NULL) {
				die ('failed to create user class');
			}
			$this->UserClassesDatabaseModel->updateUserClassPermissions($userClass, [
				'create_thread' => 1,
				'create_post' => 1,
			]);
			echo "created 'user' class\n";
	
			$moderatorClass = $this->UserClassesDatabaseModel->createClass('moderator', 1000);
			if ($moderatorClass === NULL) {
				die ('failed to create moderator class');
			}
			$this->UserClassesDatabaseModel->updateUserClassPermissions($moderatorClass, [
				'create_thread' => 1,
				'lock_thread' => 1,
				'sticky_thread' => 1,
				'create_post' => 1,
				'edit_post' => 1,
				'mute_user' => 1,
			]);
			$this->UserClassesDatabaseModel->setUserClassColor($moderatorClass, '0080C0');
			echo "created 'moderator' class\n";
	
			$adminClass = $this->UserClassesDatabaseModel->createClass('admin', 1000000);
			if ($adminClass === NULL) {
				die ('failed to create admin class');
			}
			$this->UserClassesDatabaseModel->updateUserClassPermissions($adminClass, [
				'create_forum' => 1,
				'lock_forum' => 1,
				'delete_forum' => 1,
				'create_thread' => 1,
				'lock_thread' => 1,
				'sticky_thread' => 1,
				'delete_thread' => 1,
				'create_post' => 1,
				'delete_post' => 1,
				'edit_post' => 1,
				'edit_lower_class' => 1,
				'mute_user' => 1,
				'ban_user' => 1,
			]);
			$this->UserClassesDatabaseModel->setUserClassColor($adminClass, 'F00000');
			echo "created 'admin' class\n";

			$adminUser = $this->UsersDatabaseModel->createUser('admin', 'password', $adminClass->id);
			if ($adminUser === NULL) {
				die ('failed to create admin user');
			}
			echo "created 'admin' user with password 'password'\n";

		} else {
			$this->renderView('UserErrorView', ['invalid page']);
		}
	}
}
