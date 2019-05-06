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
$images = new Image($db);
//instantiate like object
$likes = new Like($db);

//get search
$images->search = isset($_GET['search']) && $_GET['search'] != '' ? htmlentities(strip_tags($_GET['search'])) : die();
//image query
$result = $images->search_images();
//get row count
$num = $result->rowcount();
//check if any images
if($num > 0){
  //image array
  $images_arr = array();
  $images_arr['images'] = array();
  $images_arr['user_liked'] = array();
    //check if session user is set
    if(isset($_SESSION['user'])){
      //clean session user before connecting to database
      $likes->user_id = htmlentities(strip_tags($_SESSION['user']));
      //get ids of images that user liked
      $like_result = $likes->check_if_user_liked_image();
      //get row count
      $num_row = $like_result->rowcount();
      if($num_row > 0){
          //loop through all images
          while($row = $like_result->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            //save user id and image id to an array
            $liked_info = array(
                'user_id' => $user_id,
                'liked_image_id' => $image_id
            );
            //push 'data' to user liked array
            array_push($images_arr['user_liked'], $liked_info);
          }
      }else{
        //if no images found stop proccesing data
        die();
      }
    //check if cookie user is set
    }else if(isset($_COOKIE['user'])){
      //clean cookie user before connecting to database
      $likes->user_id = htmlentities(strip_tags($_COOKIE['user']));
      //get ids of images that user liked
      $like_result = $likes->check_if_user_liked_image();
      //get row count
      $num_row = $like_result->rowcount();
      if($num_row > 0){
        //loop through all images
        while($row = $like_result->fetch(PDO::FETCH_ASSOC)){
          extract($row);
          //save user id and image id to an array
          $liked_info = array(
              'user_id' => $user_id,
              'liked_image_id' => $image_id
          );
          //push 'data' to user liked array
          array_push($images_arr['user_liked'], $liked_info);
        }
      }else{
        //if no images found stop processing data
        die();
      }
    }
  //loop through images and get their info
  while($row = $result->fetch(PDO::FETCH_ASSOC)){
    extract($row);
    //image info array
    $image_item = array(
        'image_id' => $image_id,
        'standard_name' => $standard_name,
        'thumbnail_name' => $thumbnail_name,
        'image_likes' => $image_likes
    );
    //push image info  to images array
    array_push($images_arr['images'], $image_item);
  }
  //turn turn images array to JSON & output
  echo json_encode($images_arr);
}else{
  //no images
  echo json_encode(
    array('message' => 'No Images Found')
  );
}
