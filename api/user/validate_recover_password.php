<?php
//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
//including database file and user model
include_once '../../config/Database.php';
include_once '../../models/User.php';
//instantiate DB & connect
$database = new Database();
$db = $database->connect();
//instantiate user object
$recover_password = new User($db);
//check if temp acces code cookie is set
//if(isset($_COOKIE['temp_access_code'])) {
  //check if code and reset code is not set
  if(!isset($_GET['code']) && !isset($_GET['resetpcode'])) {
    //stop processing if it is true
    die();
    //check if code and reset code is empty
  } else if (empty($_GET['code']) || empty($_GET['resetpcode'])) {
      //stop processing if it is true
      die();

  } else {
    //if they are set and not empty clean the data
    $recover_password->code = htmlentities(strip_tags($_GET['code']));
    $recover_password->reset_code = htmlentities(strip_tags($_GET['resetpcode']));
    //get user id with the valid code
    $result = $recover_password->get_id_with_valid_recovery_password_code();
    //check how many user has the valid code
    $num = $result->rowcount();
    if($num === 1){
      //if one user is found, get the user id
      $row = $result->fetch(PDO::FETCH_ASSOC);
      $user_id = $row['user_id'];
      //send json data with succes message and user id
      echo json_encode(
          array('message' => 'Code to reset your password is valid - Create your new password',
                'user_id' => $user_id
              )
        );
    }else{
      //if no user found send jason data with message that code is invalid
      echo json_encode(
          array('message' => 'Code to reset your password is invalid - Please try again')
        );
      //then stop processing data
      die();
    }
  }

//}else{
  //if temp acces code is not set or has expired send json date with message of code to reset is not valid
//  echo json_encode(
//      array('message' => '>Code to reset your password is expired - Please try again')
//    );
  //then stop processing data
//  die();
//}
