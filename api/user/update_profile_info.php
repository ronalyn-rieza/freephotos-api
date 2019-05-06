<?php
//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/User.php';

//instantiate DB & connect
$database = new Database();
$db = $database->connect();
//instantiate user object
$user = new User($db);
//get raw posted data
$data = json_decode(file_get_contents("php://input"));
//check if input data is not empty
if(
    !empty($data->first_name) &&
    !empty($data->last_name) &&
    !empty($data->email) &&
    !empty($data->id)
){
  //clean and connect input data to user object
  $user->first_name = htmlentities(strip_tags($data->first_name));
  $user->last_name = htmlentities(strip_tags($data->last_name));
  $user->email = htmlentities(strip_tags($data->email));
  $user->id = htmlentities(strip_tags($data->id));

  //call profile info function to find user and check if its online
  $result = $user->check_user_is_online();
  //get row count of result
  $num = $result->rowcount();
    if($num > 0){
      //if user is found call update profile info
      if($user->update_profile_info()){
          //if upadate profile returned true check if profile is succesfully updated
          $update_result = $user->check_profile_is_updated();
          //get row count
          $num = $result->rowcount();
            if($num > 0){
              //if profile  is updated get assoc row
              $row = $result->fetch(PDO::FETCH_ASSOC);
              //database profile info
              $db_name = $row['user_firstname'];
              $db_lastname = $row['user_lastname'];
              $db_email = $row['user_email'];
              //input data
              $firstName = htmlentities(strip_tags($data->first_name));
              $lastname = htmlentities(strip_tags($data->last_name));
              $email = htmlentities(strip_tags($data->email));
              //check if database profile info is the same as input profile data
              if(
                $db_name = $firstName &&
                $db_lastname = $lastname &&
                $db_email = $email
              ){
                //if returned true send jason data message that profile is been updated
                echo json_encode(
                  array('message' => 'Your profile has been updated')
                );
              }else{
                //if returned false send jason data message that profile is not been updated
                echo json_encode(
                  array('message' => 'Something went wrong, Please try again')
                );
                die();
              }
          }else{
            //check profile is updated returned false, stop processing data
            die();
          }
      }else{
        //Update profile returned false, stop processing data
        die();
      }
  }else{
    //No online user found, stop processing data
    die();
  }
}else{
  //input data is empty, stop processing data
  die();
}
