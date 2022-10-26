<?php
class ExerciseService{
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function getAllExercises($published = null){
        $query = "SELECT * FROM exercise";
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

    public function getExerciseFromId($id, $published = null){
        $query = "SELECT * FROM exercise WHERE id=:id";
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

    public function createExercise($title, $desc, $file, $creator, $end_date, $published){
        $query = "INSERT INTO exercise(title, description, file, creator, end_date, published) VALUES (:title, :desc, :file, :creator, :end_date, :published)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["title"=>$title, "desc"=>$desc, "file"=>$file, "creator"=>$creator, "end_date"=>$end_date, "published"=>$published]);

        return $stmt;
    }

    public function updateExercise($id, $title, $desc, $file, $end_date, $published){
        $query = "UPDATE exercise SET title=:title, description=:desc, file=:file, end_date=:end_date, published=:published WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["id"=>$id, "title"=>$title, "desc"=>$desc, "file"=>$file, "end_date"=>$end_date, "published"=>$published]);

        return $stmt;
    }

    public function deleteExercise($id){
        $query = "DELETE FROM exercise WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["id"=>$id]);

        return $stmt;
    }

    //Answer API
    public function getAllAnswers($exId){
        $query = "SELECT * FROM exercise_ans WHERE exercise_id=:exId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["exId"=>$exId]);

        return $stmt;
    }

    public function getAnswerFromUser($userId, $exId){
        $query = "SELECT * FROM exercise_ans WHERE exercise_id=:exId AND user_id=:userId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["exId"=>$exId, "userId"=>$userId]);

        return $stmt->fetch();
    }

    public function getAllAnswersFromUser($userId){
        $query = "SELECT * FROM exercise_ans WHERE user_id=:userId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["userId"=>$userId]);

        return $stmt;
    }

    public function deleteAnswer($id){
        $query = "DELETE FROM exercise_ans WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["id"=>$id]);

        return $stmt;
    }

    public function createAnswer($userId, $exId, $file, $isDone){
        $query = "INSERT INTO exercise_ans(user_id, exercise_id, ans_file, is_done) VALUE (:userId, :exId, :file, :isDone)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["userId"=>$userId, "exId"=>$exId, "file"=>$file, "isDone"=>$isDone]);

        return $stmt;
    }

    public function updateAnswer($userId, $exId, $file, $isDone){
        $query = "UPDATE exercise_ans SET is_done=:isDone, ans_file=:file WHERE user_id=:userId AND exercise_id=:exId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["isDone"=>$isDone, "file"=>$file, "userId"=>$userId, "exId"=>$exId]);

        return $stmt;
    }
}
?>