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

//check if get delete is set
if(isset($_GET['delete'])){
  //if it is set check if it is not empty
  if(
      !empty($_GET['delete'])
  ){
    //if it is not empty, clean it before connecting to database
    $user->user_id = htmlentities(strip_tags($_GET['delete']));
    //check if delete id is same as user session id or user cookie id
    if($_GET['delete'] == $_SESSION['user'] || $_GET['delete'] == $_COOKIE['user']){
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
        //after deleting user image, call delete account function to delete user from database
        if($user->delete_user_account()){
          //check if user account is deleted
          $result_account_deleted = $user->check_user_account_is_deleted();
          //get row count
          $num = $result_account_deleted->rowcount();
          //if it is succesfully activated send json data message for confirmation
          if($num  == 0){
            //if no user found, send confirmation to user that thier account has been deleted
            echo json_encode(
                array('message' => 'We\'re Sorry to say Goodbye')
              );
          }else{
            //if account is not succesfully deleted send json data message for warning
            echo json_encode(
                array('message' => 'Account is not been deleted, Please try again')
              );
          }
        }else{
          //delete_user_account returned false
          die();
        }
        //unset cookie user if it set
        if(isset($_COOKIE['user'])) {
            unset($_COOKIE['user']);
            setcookie('user', '', time()-60*60*24, '/', 'freephotos.io', TRUE, TRUE);
        }
        //unset cookie role if it set
        if(isset($_COOKIE['role'])) {
          unset($_COOKIE['role']);
          setcookie('role', '', time()-60*60*24, '/', 'freephotos.io', TRUE, TRUE);
        }
        //unset and destroy session
        if(isset($_COOKIE[session_name()])){
          session_destroy();
          $_SESSION = array();
          setcookie(session_name(), '', time()-3600, '/', 'freephotos.io', TRUE, TRUE);
        }

    }else{
      //if session or cookie is not set stop processing data
     die();
    }

  }else{
    //if get delete is set but empty stop processing data
    die();
  }

}else{
  //if get delete is not set stop processing data
  die();
}
