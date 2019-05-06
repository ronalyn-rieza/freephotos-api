<?php
//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
//including database and user object
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
if(!empty($data->user_email)){
    //clean input data
    $user->user_email = htmlentities(strip_tags($data->user_email));
    //clean data to before using it to generate code two for recovering password and use it to send confirmation email
    $user_email = htmlentities(strip_tags($data->user_email));
    //check if email exist on database
    $check_user_email_registered = $user->check_email_exist();
    //get row count
    $num = $check_user_email_registered->rowcount();

    if($num > 0){
      //if email exist get user id and code one from databse
      $result = $user->get_user_id_and_code_one_to_recover_password();
      //check how many user has it
      $recover_email_num = $result->rowcount();

      if($recover_email_num === 1){
        //if user account is activated get assoc row
        $row = $result->fetch(PDO::FETCH_ASSOC);
        //user code one on database
        $db_code_one = $row['code_one'];
        //generate new code two
        $code_two = md5($user_email . microtime());
        //set cookie for recovering password
        //setcookie('temp_access_code', $code_two, time() + 60*60, '/', '', FALSE, FALSE);
        //update code two  to use later to access updating paswword
        $update_code_result = $user->update_code_two_to_recover_password($code_two);

        if($update_code_result){
          //if code code is succesfully updated send and email to user for the link to change password
          $subject = "Reset Password";

           $headers = "MIME-Version: 1.0" . "\r\n";
           $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
           $headers .= "From: noreply@freephotos.io". "\r\n";
           //email template
           $template = "<div style='padding:50px;'><p>Please click the link below to reset your password</p><a href='https://www.freephotos.io/reset_password.php?code=$db_code_one&resetpcode=$code_two'>https://www.freephotos.io/reset_password.php?code=$db_code_one&resetpcode=$code_two</a></div>";
           $sendmessage = "<div>" . $template . "</div>";
           //send email
           mail($user_email, $subject, $sendmessage, $headers);
           //send json data with succes message
           echo json_encode(
               array('message' => 'Please check your email to reset your password')
             );
        }else{
          //if code code is  unsuccesfully updated stop proccesing data
          die();
        }
      }else{
        //if user account is not activated yet get user code one and two
        $get_user_codes = $user->get_code_one_and_two();
        //get row count
        $row_count = $get_user_codes->rowcount();

        if($row_count === 1){
          //get assoc info
          $code_row = $result->fetch(PDO::FETCH_ASSOC);
          //user code one and two to activate account
          $dbCodeOne = $code_row['code_one'];
          $dbCodeTwo = $code_row['code_two'];

          $subject = "Activate Account";

          $headers = "MIME-Version: 1.0" . "\r\n";
          $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
          $headers .= "From: noreply@freephotos.io". "\r\n";
          //email template
          $template = "<div style='padding:50px;'><p>Please click the link below to activate your Account</p><a href='https://www.freephotos.io/index.php?code=$dbCodeOne&valcode=$dbCodeTwo'>https://www.freephotos.io/index.php?code=$dbCodeOne&valcode=$dbCodeTwo</a></div>";
          $sendmessage = "<div>" . $template . "</div>";
          //send email with activation link
          mail($user_email, $subject, $sendmessage, $headers);
          //send json data massage
          echo json_encode(
              array('message' => '>Please activate your account to reset your password - Check your e-mail for activation link.')
            );
        }else{
          //if there a problem getting activation code stop proccesing data
          die();
        }
      }

    }else{
      //send json data message that the email doesn't exist on database
      echo json_encode(
          array('message' => 'The e-mail you\'ve entered is not registered - Please try again')
        );
    //stop proccesing data
      die();
    }

}else{
  //if input data is empty stop processing data
    die();
}
