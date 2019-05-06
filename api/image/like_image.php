<?php
//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
//including database file and user model
include_once '../../config/Database.php';
include_once '../../models/Image.php';
include_once '../../models/Likes.php';
//instantiate DB & connect
$database = new Database();
$db = $database->connect();
//instantiate image object
$image = new Image($db);
//instantiate like object
$like = new Like($db);
  //check if user and image id is not set
  if(!isset($_GET['user']) && !isset($_GET['image'])) {
    //stop processing if it is true
    die();
    //check if user and image is empty
  } else if (empty($_GET['user']) || empty($_GET['image'])) {
      //stop processing if it is true
      die();
  } else {
    //if they are set and not empty clean the data
    $like->user_id = htmlentities(strip_tags($_GET['user']));
    $like->image_id = htmlentities(strip_tags($_GET['image']));
    //check if user already liked this image
    $like_result = $like->check_if_user_liked_an_image();
    //get row count
    $num_row = $like_result->rowcount();
    if($num_row < 1){
        //if user doesn't like this image yet save user id and image id on likes table on database
        if($like->save_image_liked_on_likes_table()){
            //clean get image before connection to images table on database
            $image->id = htmlentities(strip_tags($_GET['image']));
            //if save_image_liked_on_likes_table returned true add like to this image
            if($image->add_image_likes()){
            //if adding likes to this image is succesfull send json message to confirm
            echo json_encode(
                array('message' => 'You have liked this image')
              );
          }
      }
    }else{
      //if user is already liked this image send json data message to confirm
      echo json_encode(
          array('message' => 'You Already Like this Image')
        );
      //then stop processing data
      die();
    }
  }
