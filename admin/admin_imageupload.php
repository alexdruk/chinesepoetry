<?php
require_once dirname(__DIR__).'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["title"] ='Upload Image';
$success = false;
$error =false;
$target_dir = dirname(__DIR__).'/images/';


if(isset($_POST["submit"])) {
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if (file_exists($target_file)) {
        $error =  "Sorry, file already exists or was not choosen.";
    }
    elseif ($_FILES["fileToUpload"]["size"] > 200000) {
        $error =  "Sorry, your file is too large.";
    }
    elseif ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "svg" ) {
        $error =  "Sorry, only JPG, JPEG, PNG, GIF, SVG files are allowed.";
    }
    else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $success = "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
            echo '<script>history.pushState({}, "", "")</script>';
        } else {
          $error = "Sorry, there was an error uploading your file.".$error;
        }
    }      
}

$template_info["error"] = $error;
$template_info["success"] = $success;
$template = $twig->load('upload_image.html.twig');
echo $template->display($template_info);