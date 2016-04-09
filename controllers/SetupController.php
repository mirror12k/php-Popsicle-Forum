<?php



class SetupController extends Controller {
	public static $required = ['UserClassesDatabaseModel', 'UsersDatabaseModel', 'DatabaseModel'];
	public static $inherited = ['UserErrorView'];

	public function invoke($args) {
		if ($this->DatabaseModel->isSetup() === FALSE) {
			echo "starting popsicle setup\n";

			$this->execute_setup_database();
			echo "executed database setup\n";

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



	public function execute_setup_database() {
		// credits to an answer on https://stackoverflow.com/questions/4027769/running-mysql-sql-files-in-php
		// with modifications
		// load file
		global $popsicleConfig;
		$text = file_get_contents($popsicleConfig['setupDatabaseFile']);

		// delete comments
		$lines = explode("\n",$text);
		$filteredText = '';
		foreach ($lines as $line){ // ensure success
			$line = trim($line);
			if ($line and (! preg_match('/^--.*$/', $line))){ // skip comments and empty lines
				$filteredText .= $line . "\n";
			}
		}

		// convert to array
		$commands = explode(";", $filteredText);

		// run commands
		foreach ($commands as $command){
			if (trim($command)) { // skip empty lines
				$result = $this->DatabaseModel->query($command);
				if ($result !== TRUE) { // ensure success
					die ("query failed: '${command}'");
				}
			}
		}
	}
}
