<?php
require_once dirname(__DIR__).'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["title"] ='Delete record';
$success = false;
$error =false;

if ($_GET['table'] == 'poems') {
    $template_info["type"] = 'poems';
    if ((array_key_exists('posted', $_GET)) && (array_key_exists('recordID', $_POST))) {
        $record_id = $_POST['recordID'];
        $rd = deleteRecordFromPoems($record_id );
        if ($rd > 0) {
            $success = "Record ".$record_id." was succcesfully deleted";
            $error = false;
        }
        else {
            $success = false;
            $error = "An error occur.";
        }
    }
}
elseif ($_GET['table'] == 'originals') {
    $template_info["type"] = 'originals';
    if ((array_key_exists('posted', $_GET)) && (array_key_exists('recordID', $_POST))) {
        $record_id = $_POST['recordID'];
        $rd = deleteRecordFromOriginals($record_id );
        if ($rd > 0) {
            $success = "Record ".$record_id." was succcesfully deleted";
            $error = false;
        }
        else {
            $success = false;
            $error = "An error occur.";
        }
    }
}
$template_info["error"] = $error;
$template_info["success"] = $success;
$template = $twig->load('admin_delete_record.html.twig');
echo $template->display($template_info);