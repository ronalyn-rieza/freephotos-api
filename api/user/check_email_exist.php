<?php
//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
//including database and user object
include_once '../../config/Database.php';
include_once '../../models/User.php';
//instantiate DB & connect
$database = new Database();
$db = $database->connect();
//instantiate user object
$email_exist = new User($db);
//get, check and clean email before sending query to database
$email_exist->user_email = isset($_GET['email']) && $_GET['email'] != '' ? htmlentities(strip_tags($_GET['email'])) : die();
//check if email_exist
$result = $email_exist->check_email_exist();
//get row count
$num = $result->rowcount();

if($num > 0){
  //if row count is more than 0 send json data message of confirming email is already registered
  echo json_encode(
      array('message' => 'E-mail is already registered. Please use different E-mail')
    );
}else{
  //if row count is 0 send json data message of confirming email is not yet registered
  echo json_encode(
      array('message' => 'E-mail is not yet registered.')
    );
}
