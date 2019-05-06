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
$data = json_decode(file_get_contents("php://input"));
if(
    !empty($data->user_email) &&
    !empty($data->user_password)
){
    //connecting input data to user object
    $user->user_email = htmlentities(strip_tags($data->user_email));
    //$user->user_password = htmlentities(strip_tags($data->user_password));
    $remember = htmlentities(strip_tags($data->remember));
    $user_data_password = htmlentities(strip_tags($data->user_password));
    $email = htmlentities(strip_tags($data->user_email));

    //call check user login to get user password, id and user role of user with activated account
    $result = $user->check_user_login();
    //get row count
    $num = $result->rowcount();

    if($num > 0){
      //if one user found gat assc array
       $row = $result->fetch(PDO::FETCH_ASSOC);
       //user dp password
       $db_password = $row['user_password'];
       //db user role
       $db_userrole = $row['user_role'];
       //db user id
       $db_user_id  = $row['user_id'];
       //check if db password is the same as password from input data
       if(password_verify($user_data_password, $db_password)) {
          //connect db user id to user object user id
           $user->user_id = $db_user_id;
           //call time oline function to update user time info to know how many users r online
           $user->update_time_online();

           if($remember == 'on'){
             //if remenber me is on set cookie
             setcookie('user', $db_user_id, time() + 60*60*24, '/', '', FALSE, FALSE);
             setcookie('role', $db_userrole, time() + 60*60*24, '/', '', FALSE, FALSE);
           }
           //ini_set('session.cookie_secure',1);
           //ini_set('session.cookie_httponly',1);
           //ini_set('session.use_only_cookies',1);
           //start session
           session_start();
           //set session user role
           $_SESSION['user_role'] = $db_userrole;
           //set session user
           $_SESSION['user'] = $db_user_id;

           if($db_userrole === 'Admin'){
             //if user is admin send json data message to confirm that the user is admin
             echo json_encode(
                 array('message' => 'User is Admin')
               );
           }else{
             //if user is Subscriber send json data message to confirm that the user is Subscriber
             echo json_encode(
                 array('message' => 'User is Subcriber')
               );
           }//user role = admin else closing brackets

       } else {
         //if password from data base is not the same as user input data send json data message for warning
         echo json_encode(
             array('message' => 'Email or Password that you\'ve entered is incorrect')
           );
       }//password verify else closing brackets
    }else{
      //check if there is user on data base with email the same as input data email
      //if user is found  get user code one and code two to activate account
      $get_codes = $user->get_code_one_and_two();

      //get row count
      $user_num = $get_codes->rowcount();

        if($user_num === 1){
          //get assoc info
          $code_row = $get_codes->fetch(PDO::FETCH_ASSOC);
          // user activation code
          $db_code_one = $code_row['code_one'];
          $db_code_two = $code_row['code_two'];
          //email subject
          $subject = "Activate Account";
          //email headers
          $headers = "MIME-Version: 1.0" . "\r\n";
          $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
          $headers .= "From: noreply@freephotos.io". "\r\n";
          //email template
          $template = "<div style='padding:50px;'><p>Please click the link below to activate your Account</p><a href='https://www.freephotos.io/index.php?code=$db_code_one&valcode=$db_code_two'>https://www.freephotos.io/index.php?code=$db_code_one&valcode=$db_code_two</a></div>";
          $sendmessage = "<div>" . $template . "</div>";
          //send email
          mail($email, $subject, $sendmessage, $headers);
          //send json data message info to activate user account
          echo json_encode(
              array('message' => 'Your account is not activated yet - Check your email for activation link')
            );
        }else{
          //if no user is found with input email send json data for warning
          echo json_encode(
              array('message' => 'No account found - Please sign up')
            );
          //then stop processing data
            die();
        }
    }

}else{
  //if input data is empty stop processing data
  die();
}
