<?php
class db{
	private $servername = "localhost";
	private $username = "n0bita";
	private $password = "trietsuper";
	private $db = "classroom";

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
