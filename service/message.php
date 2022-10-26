<?php
class MessageService{
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

	public function getAllMessagesToUser($to_id){
		$query = "SELECT * FROM message WHERE to_id=:to_id ORDER BY create_date DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["to_id"=>$to_id]);
		
		return $stmt;
	}

	public function getAllMessagesFromUser($from_id){
		$query = "SELECT * FROM message WHERE from_id=:from_id ORDER BY create_date DESC";	
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["from_id"=>$from_id]);

		return $stmt;
	}

	public function getAllMessagesFromUserToUser($from_id, $to_id){
		$query = "SELECT * FROM message WHERE from_id=:from_id AND to_id=:to_id ORDER BY create_date DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["from_id"=>$from_id, "to_id"=>$to_id]);

		return $stmt;
	}
	
	public function getMessageFromId($id){
		$query = "SELECT * FROM message WHERE id=:id ORDER BY create_date DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["id"=>$id]);

		return $stmt->fetch();
	}

	public function sendMessage($from_id, $to_id, $content){
		$query = "INSERT INTO message(from_id, to_id, content) VALUES (:from_id, :to_id, :content)";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["from_id"=>$from_id, "to_id"=>$to_id, "content"=>$content]);

		return $stmt;
	}

	public function updateMessage($id, $content){
		$query = "UPDATE message SET content=:content WHERE id=:id";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["content"=>$content, "id"=>$id]);

		return $stmt;
	}

	public function deleteMessage($id){
		$query = "DELETE FROM message WHERE id=:id";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["id"=>$id]);

		return $stmt;
	}

}
?>
