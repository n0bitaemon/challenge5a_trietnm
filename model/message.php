<?php
class Message{
	private $conn;

	public $id;
	public $from_id;
	public $to_id;
	public $content;
	public $is_seen;
	public $create_date;

	public function __construct($db){
		$this->conn = $db;
	}

	public function getMessageFromId(){
		$query = "SELECT * FROM message WHERE from_id=:from_id AND to_id=:to_id";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		
		return $stmt;
	}
}
?>
