<?php
define("DB_USR", "n0bita");
define("DB_PWD", "trietsuper");
define("DB_HOST", "localhost");
define("DB_NAME", "classroom");

class db{
	private $servername = DB_HOST;
	private $username = DB_USR;
	private $password = DB_PWD;
	private $db = DB_NAME;

	public function connect(){
		$this->conn = null;
		try{
			$this->conn = new PDO("mysql:host=".$this->servername.";dbname=".$this->db, $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			echo "Connection failed: ".$e->getMessage();
		}
		return $this->conn;
	}
}
?>
