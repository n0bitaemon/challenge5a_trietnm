<?php
class QuizService{
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function getAllQuizzes($published = null){
        $query = "SELECT * FROM quiz";
        if($published != null){
            $query = $query." WHERE published=:published";
        }
        $query = $query." ORDER BY create_date DESC";
        $stmt = $this->conn->prepare($query);
        if($published != null)
            $stmt->execute(["published"=>$published]);
        else
            $stmt->execute();
        return $stmt;
    }

    public function getQuizFromId($id, $published = null){
        $query = "SELECT * FROM quiz WHERE id=:id";
        if($published != null){
            $query = $query." AND published=:published";
        }
        $query = $query." ORDER BY create_date DESC";
        $stmt = $this->conn->prepare($query);
        if($published != null)
            $stmt->execute(["id"=>$id, "published"=>$published]);
        else
            $stmt->execute(["id"=>$id]);

        return $stmt->fetch();
    }

    public function createQuiz($title, $desc, $hint, $file, $creator, $end_date, $published){
        $query = "INSERT INTO quiz(title, description, hint, file, creator, end_date, published) VALUES (:title, :desc, :hint, :file, :creator, :end_date, :published)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["title"=>$title, "desc"=>$desc, "hint"=>$hint, "file"=>$file, "creator"=>$creator, "end_date"=>$end_date, "published"=>$published]);

        return $stmt;
    }

    public function updateQuiz($id, $title, $desc, $hint, $file, $end_date, $published){
        $query = "UPDATE quiz SET title=:title, description=:desc, hint=:hint, file=:file, end_date=:end_date, published=:published WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["id"=>$id, "title"=>$title, "desc"=>$desc, "hint"=>$hint, "file"=>$file, "end_date"=>$end_date, "published"=>$published]);

        return $stmt;
    }

    public function deleteQuiz($id){
        $query = "DELETE FROM quiz WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["id"=>$id]);

        return $stmt;
    }

    //Quiz answer section
    
    public function getAnswerFromUser($user_id, $quiz_id){
        $query = "SELECT * FROM quiz_ans WHERE user_id=:user_id AND quiz_id=:quiz_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["user_id"=>$user_id, "quiz_id"=>$quiz_id]);

        return $stmt->fetch();
    }

    public function getAllAnswersFromUser($userId){
        $query = "SELECT * FROM quiz_ans WHERE user_id=:userId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["userId"=>$userId]);

        return $stmt;
    }

    public function getAllAnswers($quiz_id){
        $query = "SELECT * FROM quiz_ans WHERE quiz_id=:quiz_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["quiz_id"=>$quiz_id]);

        return $stmt;
    }

    public function createAnswer($user_id, $quiz_id, $answer){
        $query = "INSERT INTO quiz_ans(user_id, quiz_id, answer) VALUES(:user_id, :quiz_id, :answer)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["user_id"=>$user_id, "quiz_id"=>$quiz_id, "answer"=>$answer]);

        return $stmt;
    }

    public function deleteAnswer($id){
        $query = "DELETE FROM quiz_ans WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["id"=>$id]);

        return $stmt;
    }
}
?>