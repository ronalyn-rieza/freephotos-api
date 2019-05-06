<?php
//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/User.php';

//instantiate DB & connect
$database = new Database();
$db = $database->connect();

//instantiate user object
$user = new User($db);

//get raw posted data
$data = json_decode(file_get_contents('php://input'));
//check if data is not empty
if(
    !empty($data->user_firstname) &&
    !empty($data->user_lastname) &&
    !empty($data->user_email) &&
    !empty($data->user_password)
){
    //if input data is not empty connect it to user object data
    $user->user_firstname = $data->user_firstname;
    $user->user_lastname = $data->user_lastname;
    $user->user_email = $data->user_email;
    $user->user_password = $data->user_password;

    //call and check if create_user fuction is succesfully executed
    if($user->create_user()){
      //check if email_exist
      $result = $user->check_new_user_is_added();
      //get row count
      $num = $result->rowcount();

      if($num === 1){
        //if user account is added get assoc row
            $row = $result->fetch(PDO::FETCH_ASSOC);
            //user info on database
            $db_user_fistname = $row['user_fistname'];
            $db_user_lastname = $row['user_lastname'];
            $db_user_email = $row['user_email'];
            $db_code_one = $row['code_one'];
            $db_code_two = $row['code_two'];

            //setting email to send after user has been added to data base
            $subject = "Activate Account";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@freephotos.io". "\r\n";
            //mesage template
            $template = "<div style='padding:50px;'><p>Hello $db_user_fistname</p><p>Thank you...! For Signing Up to our Website.</p><p>Please click the link below to activate your Account</p><a href='https://www.freephotos.io/index.php?code=$db_code_one&valcode=$db_code_two'>https://www.freephotos.io/index.php?code=$db_code_one&valcode=$db_code_two</a></div>";
            $sendmessage = "<div>" . $template . "</div>";
            //sending email
            mail($db_user_email, $subject, $sendmessage, $headers);
        //if there is no problem adding user send json data confirming that new user has been added
        echo json_encode(
          array('message' => 'Thank you...! For Signing Up to our Website. Please check your email to activate your account.')
        );
      }else{
        //if user is not added to datatbase send json data warning massage
        echo json_encode(
            array('message' => 'Something went wrong, Please try agin.')
          );
        //then stop proccesing data
          die();
      }

    }else{
      //if create user returned false
      die();
    }

}else{
  //if data is empty stop processing it
  die();
}
