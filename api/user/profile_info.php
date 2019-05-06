<?php
//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/User.php';
//instantiate DB & connect
$database = new Database();
$db = $database->connect();
//instantiate user object
$user = new User($db);
//get user id
$user->id = isset($_GET['user_id']) && $_GET['user_id'] != '' ? htmlentities(strip_tags($_GET["user_id"])) : die();
//call profile info function to connect to database
$result = $user->profile_info();
//get row count of result
$num = $result->rowcount();
//check if user found
if($num > 0){
  //user_info array
  //$user_arr = array();
  //$user_arr['data'] = array();
  //while($row = $result->fetch(PDO::FETCH_ASSOC)){

  //get the info
  $row = $result->fetch(PDO::FETCH_ASSOC);
  extract($row);
  //save info to an array
  $profile_info = array(
    'user_id' => $user_id,
    'user_firstname' => $user_firstname,
    'user_lastname' => $user_lastname,
    'user_email' => $user_email,
    'user_image' => $user_image
  );

    //push to 'data'
    //array_push($user_arr['data'], $profile_info);
  //}

  //turn to JSON & output
  echo json_encode($profile_info);
}else{
  //no user, send json data message for warning
  echo json_encode(
    array('message' => 'No User Found')
  );
  //stop proccesing data
  die()
}
