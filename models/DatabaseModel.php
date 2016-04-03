<?php

/**
* interfaces directly with the real database to control login and cache the connection between usage
*/
class DatabaseModel extends Model {

	private $configHost;
	private $configUser;
	private $configPassword;
	private $configDatabase;

	private $isConnected = FALSE;
	private $database;

	public function __construct() {
		global $databaseConfig;
		$this->configHost = $databaseConfig['host'];
		$this->configUser = $databaseConfig['user'];
		$this->configPassword = $databaseConfig['password'];
		$this->configDatabase = $databaseConfig['database'];
	}

	public function connect() {
		assert($this->isConnected === FALSE);
		$this->database = new mysqli($this->configHost, $this->configUser, $this->configPassword, $this->configDatabase);
		if ($this->database->connect_error) {
			die('Database Connect Error: (' . $this->database->connect_errno . ') ' . $this->database->connect_error);
		}
		$this->isConnected = TRUE;
	}

	public function query($query) {
		if ($this->isConnected !== TRUE) {
			$this->connect();
		}
		$result = $this->database->query($query);
		if ($this->database->error) {
			die('Database query error: ' . $this->database->error);
		}
		return $result;
	}
	public function mysql_escape_string($string) {
		if ($this->isConnected !== TRUE) {
			$this->connect();
		}
		return $this->database->real_escape_string($string);
	}

	public function __get($name) {
		if ($name === 'insert_id') {
			return $this->database->insert_id;
		} elseif ($name === 'affected_rows') {
			return $this->database->affected_rows;
		}
	}
}
