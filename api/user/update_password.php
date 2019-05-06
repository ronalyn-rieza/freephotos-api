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
//check if data is not empty
if(
    !empty($data->user_id) &&
    !empty($data->user_password)
){
  //if input data is not empty clean and connect it to user object data
    $user->id = htmlentities(strip_tags($data->user_id));
    $user->new_password = htmlentities(strip_tags($data->user_password));

     if($user->check_user_is_online()){
      $num = $user->check_user_is_online()->rowcount();
        if($num > 0){
          if($user->update_password()){
            //if change password returned true check if password is updated on databse
            $result = $user->check_password_is_updated();
            //get row count
            $num = $result->rowcount();

            if($num > 0){
              //if password is updated get assoc row
                  $row = $result->fetch(PDO::FETCH_ASSOC);
                  //user password on database
                  $db_password = $row['user_password'];
                  //new password from input data
                  $user_data_password = htmlentities(strip_tags($data->user_password));
                  //check if input data password is the same as user password on database
                  if(password_verify($user_data_password, $db_password)) {
                    //if password has been succesfully updated send jason data message that password is been changed
                    echo json_encode(
                      array('message' => 'Your password has been updated')
                    );
                  }else{
                    //if not send json data message to let user know
                    echo json_encode(
                        array('message' => 'Something went wrong on updating password, Please try agin.')
                      );
                      // then stop processing data
                      die();
                  }
              }else{
                //check password is upadated returned false
                die();
              }
          }else{
            //update password returned false
            die();
          }
        }else{

            if($user->change_forgot_password()){
              //if change password returned true check if password is updated on databse
              $result = $user->check_password_is_updated();
              //get row count
              $num = $result->rowcount();

              if($num > 0){
                //if password is updated get assoc row
                    $row = $result->fetch(PDO::FETCH_ASSOC);
                    //user password on database
                    $db_password = $row['user_password'];
                    //new password from input data
                    $user_data_password = htmlentities(strip_tags($data->user_password));
                    //check if input data password is the same as user password on database
                    if(password_verify($user_data_password, $db_password)) {
                      //if password has been succesfully updated send jason data message that password is been changed
                      echo json_encode(
                        array('message' => 'Forgot password has been updated')
                      );
                    }else{
                      //if not send json data message to let user know
                      echo json_encode(
                          array('message' => 'Something went wrong on changing forgot password, Please try agin.')
                        );
                        // then stop processing data
                        die();
                    }
                }else{
                  //check password is upadated returned false on changing forgot password
                  die();
                }
            }else{
              //change pasword returned false
              die();
            }
        }//closing bracket for else statement of checking if user is online
    }else{
      //check online user function returned false
      die();
    }
}else{
  //if input data is empty stop proccesing data
  die();
}
