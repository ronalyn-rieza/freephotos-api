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

//check if get logout is set
if(isset($_GET['logout'])){
  //if it is set check if it is not empty
  if(
      !empty($_GET['logout'])
  ){
    //if it is not empty, clean it before connecting to database
    $user->user_id = htmlentities(strip_tags($_GET['logout']));
    //check if logout id is same as user session id or user cookie id
    if($_GET['logout'] == $_SESSION['user'] || $_GET['logout'] == $_COOKIE['user']){
      //check if session user is set
      if(isset($_SESSION['user'])){
        //update time online on databse to 0
        if($user->update_time_online_logout()){
          //check if time online has been updated
          $result = $user->check_time_online_updated();
          //get row count
          $num = $result->rowcount();
          //user is found
          if($num  === 1){
            //get time online row
            $row = $result->fetch(PDO::FETCH_ASSOC);
            //time online on database
            $db_time_online = $row['time_online'];
            //check if db time online is 0
            if($db_time_online < 1){
            echo json_encode(
                //confirmed user
                array('message' => 'you logged out')
              );
            }else{
              //if time online is not succesfully updated send json data message for warning
              echo json_encode(
                  array('message' => 'Something went wrong while logging out, Please try again')
                );
            }
          }else{
            //no user found after calling check_time_online_updated
            die();
          }
        }else{
          //update time online returned false
          die();
        }
      //check if user cookie is set
      }else if(isset($_COOKIE['user'])){
        //update time online on databse to 0
        if($user->update_time_online_logout()){
          //check if time online has been updated
          $result = $user->check_time_online_updated();
          //get row count
          $num = $result->rowcount();
          //user is found
          if($num  === 1){
            //get time online row
            $row = $result->fetch(PDO::FETCH_ASSOC);
            //time online on database
            $db_time_online = $row['time_online'];
            //check if db time online is 0
            if($db_time_online < 1){
            echo json_encode(
                //confirmed user
                array('message' => 'you logged out')
              );
            }else{
              //if time online is not succesfully updated send json data message for warning
              echo json_encode(
                  array('message' => 'Something went wrong while logging out, Please try again')
                );
            }
          }else{
            //no user found after calling check_time_online_updated
            die();
          }
        }else{
          //update time online returned false
          die();
        }
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
    //if get logout is set but empty stop processing data
    die();
  }

}else{
  //if get logout is not set stop processing data
  die();
}
