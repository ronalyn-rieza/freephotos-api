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

//check if code is set
if(isset($_GET['code']) && isset($_GET['valcode'])) {
  //if it is set check if it is not empty
  if(
      !empty($_GET['code']) &&
      !empty($_GET['valcode'])
  ){
      //if it is not empty, clean it before connecting to database
      $user->code_one = htmlentities(strip_tags($_GET['code']));
      $user->code_two = htmlentities(strip_tags($_GET['valcode']));
      //get user with valid aactivation codes
      $result = $user->check_code_one_and_two_activate_user();
      //get row count
      $num = $result->rowcount();

      if($num  === 1){
        //if one user is found call activate user account function
        if($user->activate_user_account()){
          //if it is succesfully activated send json data message for confirmation
          echo json_encode(
              array('message' => 'Your account has been activated please login')
            );
        }else{
          //if it is not succesfully activated send json data message for warning
          echo json_encode(
              array('message' => 'Something went wrong, Please try agin.')
            );
        }
      }else{
        //if no user found with activation code send json data message for warning
        echo json_encode(
            array('message' => 'Activation code is invalid - Check your email for validation code and Try again')
          );
      }

  }else{
    //if get code is set but empty stop processing data
    die();
  }

}else{
  //if get code and val code is not set stop processing data
  die();
}
