<?php
//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Image.php';

//instantiate DB & connect
$database = new Database();
$db = $database->connect();

//instantiate image object
$images = new Image($db);

//get file id
$images->id = isset($_GET['file']) && $_GET['file'] != '' ? htmlentities(strip_tags($_GET['file'])) : die();
//image query
$result = $images->get_image_name_to_download();
//get row count
$num = $result->rowcount();
//check if any images
if($num == 1){
  //get image info
  $row = $result->fetch(PDO::FETCH_ASSOC);
  //image name on database
  $image_filename = $row['standard_name'];
  //set image file path to download
  $image_filepath ="../../img/".$image_filename;
  //download file
  download_file($image_filepath);

}else{
  //no images
  echo json_encode(
    array('message' => 'No Image Found')
  );
}


function download_file( $fullPath ){

    if(ini_get('zlib.output_compression')){

        ini_set('zlib.output_compression', 'Off');

    }

    if( file_exists($fullPath) ){

        $fsize = filesize($fullPath);
        $path_parts = pathinfo($fullPath);
        $ext = strtolower($path_parts["extension"]);

        switch ($ext) {
            case "pdf": $ctype="application/pdf"; break;
            case "exe": $ctype="application/octet-stream"; break;
            case "zip": $ctype="application/zip"; break;
            case "doc": $ctype="application/msword"; break;
            case "xls": $ctype="application/vnd.ms-excel"; break;
            case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
            case "gif": $ctype="image/gif"; break;
            case "png": $ctype="image/png"; break;
            case "jpeg":
            case "jpg": $ctype="image/jpg"; break;
            default: $ctype="application/octet-stream";
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: $ctype");
        header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" );
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".$fsize);
        ob_clean();
        flush();
        readfile( $fullPath );

    }else{

        die();
    }
}// end of download_file function
