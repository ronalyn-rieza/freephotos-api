<?php
class Image{
  //db stuff
  private $conn;
  private $table = 'images';

  //image properties
  public $image_id;
  public $standard_name;
  public $thumbnail_name;
  public $image_likes;

  //constructor with DB
  public function __construct($db){
    $this->conn = $db;
  }

  //get images
  public function read(){
    //create query
    $query = "SELECT image_id, standard_name, thumbnail_name, image_likes FROM ". $this->table ." ORDER BY image_id DESC";
    //Prepare statment
    $stmt = $this->conn->prepare($query);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }

  //get categorized images
  public function categorized_images(){
    //Create query
    $query = "SELECT image_id, standard_name, thumbnail_name, image_likes FROM ". $this->table ." WHERE image_cat_title = :image_category AND image_post_date != '' ORDER BY image_id DESC";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //bind category name
    $stmt->bindParam(':image_category', $this->category);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }

  //get single image
  public function read_single_image(){
    //create query
    $query = "SELECT image_id, standard_name, thumbnail_name, image_likes FROM ". $this->table ." WHERE image_id = :id AND image_post_date != ''";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $image_id = abs((int)$this->id);
    //bind id
    $stmt->bindParam(':id', $image_id);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }

  //search images
  public function search_images(){
    //create query
    $query = "SELECT image_id, standard_name, thumbnail_name, image_likes FROM ". $this->table ." WHERE image_tags LIKE :search ORDER BY image_id DESC";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //add % to search image tags before binding it
    $search = "%".$this->search."%";
    //bind search
    $stmt->bindParam(':search', $search);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }

  //download image
  public function get_image_name_to_download(){
    //create query
    $query = "SELECT standard_name FROM ". $this->table ." WHERE image_id = :id AND image_post_date != ''";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $image_id = abs((int)$this->id);
    //bind id
    $stmt->bindParam(':id', $image_id);
    //execute query
    $stmt->execute();
    //return result
    return $stmt;
  }

  //update image likes
  public function add_image_likes(){
    $query ="UPDATE ". $this->table . " SET image_likes = image_likes + 1 WHERE image_id = :id";
    //prepare statement
    $stmt = $this->conn->prepare($query);
    //make sure id is absolute number
    $image_id = abs((int)$this->id);
    //bind id
    $stmt->bindParam(':id', $image_id);
    //execute query
    if($stmt->execute()){
      //return true if it is updated succesfully
      return true;
    }
      //print error for debugging
      printf("Error: %s.\n", $stmt->error);
      return false;
  }

}
