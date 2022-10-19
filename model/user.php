<?php
class User{
	private $conn;

	public $id;
	public $username;
	public $password;
	public $fullname;
	public $avatar;
	public $email;
	public $phone;
	public $is_teacher;

	public function __construct($db){
		$this->conn = $db;
	}

	public function getAllUsers(){
		$query = "SELECT * FROM account ORDER BY is_teacher DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		return $stmt;
	}

	public function getUserFromId(){
		$query = "SELECT * FROM account WHERE id=:id LIMIT 1";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["id"=>$this->id]);

		return $stmt->fetch();
	}

	public function create(){
		$query = "INSERT INTO account(username, password, fullname, avatar, email, phone, is_teacher) VALUES(:username, :password, :fullname, :avatar, :email, :phone, :is_teacher)";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["username"=>$this->username, "password"=>$this->password, "fullname"=>$this->fullname, "avatar"=>$this->avatar, "email"=>$this->email, "phone"=>$this->phone, "is_teacher"=>$this->is_teacher]);

		return $stmt;
	}

	public function delete(){
		$query = "DELETE FROM account WHERE id=:id";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["id"=>$this->id]);

		return $stmt;
	}

	public function update(){
		$query = "UPDATE account SET email=:email, phone=:phone, avatar=:avatar WHERE id=:id";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["email"=>$this->email, "phone"=>$this->phone, "avatar"=>$this->avatar, "id"=>$this->id]);

		return $stmt;
	}

}
?>
