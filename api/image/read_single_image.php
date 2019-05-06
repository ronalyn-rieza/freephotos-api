<?php
//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Image.php';
include_once '../../models/Likes.php';

//instantiate DB & connect
$database = new Database();
$db = $database->connect();

//instantiate image object
$image = new Image($db);
//instantiate like object
$likes = new Like($db);

//get id
$image->id = isset($_GET['id']) && $_GET['id'] != '' ? htmlentities(strip_tags($_GET["id"])) : die();

//get image
$result = $image->read_single_image();

//get row count
$num = $result->rowcount();

//check if any images
if($num > 0){
  //image array
  $images_arr = array();
  $images_arr['image'] = array();
  //$images_arr['user_liked'] = array();
    //check if session user is set
    if(isset($_SESSION['user'])){
      //clean session user before connecting to database
      $likes->user_id = htmlentities(strip_tags($_SESSION['user']));
      $likes->image_id = htmlentities(strip_tags($_GET["id"]));
      //get ids of images that user liked
      $like_result = $likes->check_if_user_liked_an_image();
      //get row count
      $num_row = $like_result->rowcount();
      if($num_row > 0){
          //loop through all images
          while($row = $like_result->fetch(PDO::FETCH_ASSOC)){
            $user_id = $row['user_id'];
            $liked_image_id = $row['image_id'];
          }
      }
    //check if cookie user is set
    }else if(isset($_COOKIE['user'])){
      //clean cookie user before connecting to database
      $likes->user_id = htmlentities(strip_tags($_COOKIE['user']));
      $likes->image_id = htmlentities(strip_tags($_GET["id"]));
      //get ids of images that user liked
      $like_result = $likes->check_if_user_liked_an_image();
      //get row count
      $num_row = $like_result->rowcount();
      if($num_row > 0){
        //loop through all images
        while($row = $like_result->fetch(PDO::FETCH_ASSOC)){
          $user_id = $row['user_id'];
          $liked_image_id = $row['image_id'];
        }
      }
    }
    //loop through images and get their info
    while($row = $result->fetch(PDO::FETCH_ASSOC)){
      $image_id = $row['image_id'];
      $standard_name = $row['standard_name'];
      $thumbnail_name = $row['thumbnail_name'];
      $image_likes = $row['image_likes'];
    }

    if($image_id == $liked_image_id){
      $image_item = array(
          'image_id' => $image_id,
          'standard_name' => $standard_name,
          'thumbnail_name' => $thumbnail_name,
          'image_likes' => $image_likes,
          'user_liked' => $user_id
      );
      //push image info  to images array
      array_push($images_arr['images'], $image_item);
    }else{
      $image_item = array(
          'image_id' => $image_id,
          'standard_name' => $standard_name,
          'thumbnail_name' => $thumbnail_name,
          'image_likes' => $image_likes,
          'user_liked' => ' '
      );
      //push image info  to images array
      array_push($images_arr['image'], $image_item);
    }
    //turn turn images array to JSON & output
    echo json_encode($images_arr);

}else{
  //no images
  echo json_encode(
    array('message' => 'No Images Found')
  );
}
