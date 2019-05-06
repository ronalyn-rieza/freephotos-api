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

//check if get delete image is set
if(isset($_GET['deletepimage'])){
  //if it is set check if it is not empty
  if(
      !empty($_GET['deletepimage'])
  ){
    //if it is not empty, clean it before connecting to database
    $user->user_id = htmlentities(strip_tags($_GET['deletepimage']));
    //check if delete image id is same as user session id or user cookie id
    if($_GET['deletepimage'] == $_SESSION['user'] || $_GET['deletepimage'] == $_COOKIE['user']){
        //call get_profile_pic_name_to_delete_image_from_profile_photo_folder function and check if returned true
        if($user->get_profile_pic_name_to_delete_image_from_profile_photo_folder()){

          $result = $user->get_profile_pic_name_to_delete_image_from_profile_photo_folder();
          //get row count
          $num = $result->rowcount();
          //if one user found
          if($num  === 1){
            //get user image row
            $row = $result->fetch(PDO::FETCH_ASSOC);
            //user image on database
            $db_profile_pic_name = $row['user_image'];
            //set image path to delete
            $target_path = '../../user-profile-photo/' . $db_profile_pic_name;
            //delete user image from user profile photo folder
            unlink($target_path);
          }else{
            //no user found after executing get_profile_pic_name_to_delete_image_from_profile_photo_folder function
            die();
          }
        }
        //after deleting user image from user profile photo folder, call update profile pic to null function to delete user image from database
        if($user->update_profile_pic_to_null()){
          //check if profile pic is deleted
          $result_profile_pic_update = $user->check_if_user_image_is_updated();
          //get row count
          $num = $result_profile_pic_update->rowcount();
          //user is found
          if($num  === 1){
            //get user image row
            $row = $result_profile_pic_update->fetch(PDO::FETCH_ASSOC);
            //user image on database
            $db_profile_pic_name = $row['user_image'];
            //check if db profile pic is empty
            if($db_profile_pic_name = ' '){
            echo json_encode(
                //confirmed user that profile is succesfully deleted
                array('message' => 'Profile Pic Has Been Deleted')
              );
            }else{
              //if profile pic is not succesfully deleted send json data message for warning
              echo json_encode(
                  array('message' => 'Account is not been deleted, Please try again')
                );
            }
          }else{
            //no user found after calling check if user image is null
            die();
          }
        }else{
          //update_profile_pic returned false
          die();
        }

   }else{
      //if session or cookie is not set stop processing data
    die();
   }

  }else{
    //if get delete image is set but empty stop processing data
    die();
  }

}else{
  //if get delete image is not set stop processing data
  die();
}
