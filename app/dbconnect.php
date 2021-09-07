<?php
namespace App;
class dbconnect {
	private $pdo;
	public function connect() {
		if ($this->pdo == null)
			$this->pdo = new \PDO ("sqlite:" . config::SQLITE_FILE);
		return $this->pdo;
	}
}
?>