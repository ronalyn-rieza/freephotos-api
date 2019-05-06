<?php
//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

//get raw posted data
$data = json_decode(file_get_contents('php://input'));
//check if data is not empty
if(
    !empty($data->contact_name) &&
    !empty($data->contact_email) &&
    !empty($data->contact_message)
){
    //clean if input data is not empty
    $name = htmlentities(strip_tags($data->contact_name));
    $email = htmlentities(strip_tags($data->contact_email));
    $message = htmlentities(strip_tags($data->contact_message));

    //getting mail from user query
    $to = "downloadfreephotosio@gmail.com";
    $subjects = "Query from freephotos.io";

    $header = "MIME-Version: 1.0" . "\r\n";
    $header .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $header .= "From: " . $name . " <" . $email . ">". "\r\n";

    $templates = "<div style='padding:50px;'><p>". $message . "</p></div>";
    $sendmsg = "<div>" . $templates . "</div>";
    //send query email to freephotos email
    mail($to,$subjects,$sendmsg,$header);

    echo json_encode(
      array('message' => 'Your Query has been sent, We will contact you As Soon As Possible')
    );

}else{
  //if data is empty stop processing it
  die();
}
