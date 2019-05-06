<?php
//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: multipart/form-data');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/User.php';

//instantiate DB & connect
$database = new Database();
$db = $database->connect();
//instantiate user object
$user = new User($db);
//get and clean raw posted data
$data = htmlentities(strip_tags($_POST['user-id']));
$user->user_id = $data;
$user_id = $data;
//array for file error
$error = [];
//image file info
$profile_photo = $_FILES['profile_image']['name'];
$profile_photo_temp = $_FILES['profile_image']['tmp_name'];
$fileSize = $_FILES["profile_image"]["size"]; // File size in bytes
$split_file_name = explode(".", $profile_photo); // Split file name into an array using the dot
$fileExt = end($split_file_name); //get file extension
//check if file is gif, jpg, png or jpeg
if (!preg_match("/.(gif|jpg|png|jpeg)$/i", $profile_photo) ) {
    //save error mesage on error array
    $errors[] = "Your image was not .gif, .jpg, .jpeg, or .png - Please try again";
     // Remove the uploaded file from the PHP temp folder
    unlink($profile_photo_temp);
}
//check if file is larger than 2MB
if($fileSize > 2097152) {
    // if file size is larger than 2 Megabytes, save error message on error array
    $errors[] = "Your image was larger than 2 Megabytes in size - Please try again";
    // Remove the uploaded file from the PHP temp folder
    unlink($profile_photo_temp);
}
//check if error array is empty
if(!empty($errors)){
    //if is not empty loop through
    foreach ($errors as $error){
        //send json data error message
        echo json_encode(
            array('message' => $error)
        );
    }
}else{
    //if there is no error move uploaded file user profile folder
    $moveResult = move_uploaded_file($profile_photo_temp, "../../user-profile-photo/org_".$user_id.".".$fileExt);
    //check if file is successfully save to user profile folder
    if ($moveResult != true) {
        //send json data warning message
        echo json_encode(
            array('message' => 'Problem occur on uploading image - Please try again.')
        );
    }else{
        //if it successfully save to user profile folder
        //create new file name
        $new_name = $user_id.".".$fileExt;
        $user->new_profile_photo = $new_name;
        //temporary file path
        $target_file = "../../user-profile-photo/org_".$new_name;
        //resized file path
        $resize_file = "../../user-profile-photo/".$new_name;
        // set standard profile img size
        $w = 500;
        $h = 450;
        //resized image
        resize_image($target_file,$w,$h,$fileExt,$resize_file);
        //delete original image
        unlink($target_file);
        //getting and deleting the old profile pic if user image doesn't have the same file extention as the new uploaded profile image
        $result = $user->get_profile_pic_name_to_delete_image_from_profile_photo_folder();
        //get row count
        $num = $result->rowcount();
        //if one user found
        if($num  == 1){
            //get user image row
            $row = $result->fetch(PDO::FETCH_ASSOC);
            //user image on database
            $old_user_image = $row['user_image'];
            //check if user has profile image name on database
            if($old_user_image != ' '){
              //check if img name on database has the same extension as the new uploaded img
              if($new_name != $old_user_image){
                //setting image path to delete
                $target_path = "../../user-profile-photo/".$old_user_image;
                //delete image on user profile photo folder
                unlink($target_path);
              }
            }

        }else{
            //no user found after executing get_profile_pic_name_to_delete_image_from_profile_photo_folder function
            die('');
        }
        //update profile image file name  on database
        if($user->change_profile_pic()){
            //check if user image is updated
            $updated_img_result = $user->check_if_user_image_is_updated();
            //get row count
            $num_row = $updated_img_result->rowcount();
            //if one user found
            if($num_row  == 1){
            //get user image row
            $updated_img_row = $updated_img_result->fetch(PDO::FETCH_ASSOC);
            //user image on database
            $new_added_img = $updated_img_row['user_image'];
                //check if image name on data base is the same as new uploaded image
                if($new_added_img == $new_name){
                    //send json data mesage to confirm that changing profile pic is successfully uploaded
                    echo json_encode(
                        array('message' => 'Your profile photo has been changed')
                    );
                }
            }else{
                //no user found after executing get_profile_pic_name_to_delete_image_from_profile_photo_folder function
                die('');
            }
        }
    }
}

//function to resize image
function resize_image($target_file,$w,$h,$fileExt,$resized_file){

    list($w_orig, $h_orig) = getimagesize($target_file);

    $scale_ratio = $w_orig / $h_orig;

    if (($w / $h) > $scale_ratio) {

        $w = floor($h * $scale_ratio);

    } else {

        $h = floor($w / $scale_ratio);
    }

    //checking the ext of the file
    $ext = strtolower($fileExt);

    if ($ext == "gif"){

        $img = imagecreatefromgif($target_file);

    } else if($ext == "png"){

        $img = imagecreatefrompng($target_file);

    } else{

        $img = imagecreatefromjpeg($target_file);
    }

    //create the resized file
    $tci = imagecreatetruecolor($w, $h);

    // imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
    imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);

    //save the resized file to the target path
    if ($ext == "gif"){

        $resized_image = imagegif($tci, $resized_file);

    } else if($ext =="png"){

        $resized_image = imagepng($tci, $resized_file);

    } else {

        $resized_image = imagejpeg($tci, $resized_file, 80);

    }

    imagedestroy($img);
    imagedestroy($tci);

}
