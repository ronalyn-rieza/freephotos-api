<?php
class Like{
  //db stuff
  private $conn;
  private $table = 'likes';

  //image properties
  public $like_id;
  public $user_id;
  public $image_id;

  //constructor with DB
  public function __construct($db){
    $this->conn = $db;
  }

  //get images that user liked
  public function check_if_user_liked_image(){
    //create query
    $query = "SELECT user_id, image_id FROM ". $this->table ." WHERE user_id = :user_id";
    //Prepare statment
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $user_id = abs((int)$this->user_id);
    //bind id
    $stmt->bindParam(':user_id', $user_id);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }

  //check if user liked single image
  public function check_if_user_liked_an_image(){
    //create query
    $query = "SELECT user_id, image_id FROM ". $this->table ." WHERE user_id = :user_id AND image_id = :image_id";
    //Prepare statment
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $user_id = abs((int)$this->user_id);
    $image_id = abs((int)$this->image_id);
    //bind id
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':image_id', $image_id);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }

  //function for creating new user on database
  public function save_image_liked_on_likes_table(){
    $query = "INSERT INTO ". $this->table . "
      SET
        user_id = :user_id,
        image_id = :image_id";
        //prepare statement
        $stmt = $this->conn->prepare($query);
        //make sure id is absolute number
        $user_id = abs((int)$this->user_id);
        $image_id = abs((int)$this->image_id);
        //bind data
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':image_id', $image_id);
        //execute query
        if($stmt->execute()){
          return true;
        }
        //printing error for debugging
        printf("Error: %s.\n", $stmt->error);
        //return false if proplem occur during adding user on database
        return false;
  }

}
