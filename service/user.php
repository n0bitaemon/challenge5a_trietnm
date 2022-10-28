<?php
class UserService{
	private $conn;

	public function __construct($db){
		$this->conn = $db;
	}

	public function getAllUsers(){
		$query = "SELECT * FROM account ORDER BY is_teacher DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		return $stmt;
	}

	public function getUserFromId($id){
		$query = "SELECT * FROM account WHERE id=:id LIMIT 1";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["id"=>$id]);

		return $stmt->fetch();
	}

	public function getUserWithCredentials($username, $password){
		$query = "SELECT * FROM account WHERE username=:username AND password=:password LIMIT 1";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["username"=>$username, "password"=>$password]);

		return $stmt->fetch();
	}

	public function create($username, $password, $fullname, $email, $phone){
		$query = "INSERT INTO account(username, password, fullname, email, phone) VALUES(:username, :password, :fullname, :email, :phone)";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["username"=>$username, "password"=>$password, "fullname"=>$fullname, "email"=>$email, "phone"=>$phone]);

		return $stmt;
	}

	public function delete($id){
		$query = "DELETE FROM account WHERE id=:id";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["id"=>$id]);

		return $stmt;
	}

	public function update($id, $email, $phone, $avatar){
		$query = "UPDATE account SET email=:email, phone=:phone, avatar=:avatar WHERE id=:id";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["email"=>$email, "phone"=>$phone, "avatar"=>$avatar, "id"=>$id]);

		return $stmt;
	}

	public function changePassword($id, $newPass){
		$query = "UPDATE account SET password=:pwd WHERE id=:id";
		$stmt = $this->conn->prepare($query);
		$stmt->execute(["pwd"=>$newPass, "id"=>$id]);

		return $stmt;
	}

}
?>
