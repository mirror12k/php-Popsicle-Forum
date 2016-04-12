<?php



class SetupController extends Controller {
	public static $required = ['UserClassesDatabaseModel', 'UsersDatabaseModel', 'DatabaseModel'];
	public static $inherited = ['UserErrorView', 'PopsicleHeaderView', 'PopsicleFooterView'];

	public function invoke($args) {
		if ($this->DatabaseModel->isSetup() === FALSE) {
?><!doctype html>
<html>
<head>
	<title>Popsicle - Setup</title>
</head>
<body>
<p>starting popsicle setup</p>
<?php

			$this->execute_setup_database();
			echo "<p>executed database setup</p>";

			$userClass = $this->UserClassesDatabaseModel->createClass('user', 10);
			if ($userClass === NULL) {
				die ('failed to create user class');
			}
			$this->UserClassesDatabaseModel->updateUserClassPermissions($userClass, [
				'create_thread' => 1,
				'create_post' => 1,
			]);
			echo "<p>created 'user' class</p>";
	
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
			echo "<p>created 'moderator' class</p>";
	
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
			echo "<p>created 'admin' class</p>";

			$adminUser = $this->UsersDatabaseModel->createUser('admin', 'password', $adminClass->id);
			if ($adminUser === NULL) {
				die ('failed to create admin user');
			}
			echo "<p>created 'admin' user with password 'password'</p>";

?>
<p>done! now head over to the <a href='login'>login</a></p>
</body>
</html>
<?php
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
